<?php
$doc = new DocMan($doctype,$model->id,get_class($model));
if (isset($model->uploadMaxSize) && $model->uploadMaxSize > 0) $doc->docMaxSize = $model->uploadMaxSize;
$doc->masterId = $model->docMasterId[strtolower($doc->docType)];

$ftrbtn = array();
if (!$ronly) {$ftrbtn[] = TbHtml::button(Yii::t('dialog','Upload'), array('id'=>$doc->uploadButtonName,));}
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>$doc->closeButtonName,'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));

$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>$doc->widgetName,
    'header'=>$header,
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>
<div class="box" id="file-list" style="max-height: 300px; overflow-y: auto;">
    <table id="<?php echo $doc->tableName; ?>" class="table table-hover">
        <thead>
        <tr><th></th><th><?php echo Yii::t('dialog','File Name');?></th><th><?php echo Yii::t('dialog','Date');?></th></tr>
        </thead>
        <tbody>
        <?php
        if (isset($nodelete)) {
            echo $doc->genTableFileList($ronly, $nodelete);
        } else {
            echo $doc->genTableFileList($ronly);
        }
        ?>
        </tbody>
    </table>
</div>
<?php
echo CHtml::hiddenField(get_class($model).'[removeFileId]['.strtolower($doc->docType).']',$model->removeFileId[strtolower($doc->docType)], array('id'=>get_class($model).'_removeFileId_'.strtolower($doc->docType),));
?>

<?php
if (!$ronly) echo TbHtml::fileField($doc->inputName, '', array('multiple'=>true));
?>

<?php
$this->endWidget();
?>

<?php
if (!$ronly) {
	$modelname = get_class($model);
	$formId = $form->id;
	$typeid = strtolower($doctype);
	$ctrlname = Yii::app()->controller->id;
	$link = Yii::app()->createAbsoluteUrl($ctrlname."/fileupload",array('doctype'=>$doctype));

	$warning1 = Yii::t('dialog','Exceeds file upload limit.');
	$warning2 = Yii::t('dialog','Invalid file type.');

	$tblid = $doc->tableName;
	$buttonId = '#'.$doc->inputName;
	$maxSize = $doc->docMaxSize;

	$jscript = <<<EOF
$('$buttonId').on('change',function() {
	var errmsg = '';
	$(this).files.forEach(file => {
		if (file.size > $maxSize) errmsg += '$warning1 ($maxSize Bytes) -' + file.name + "\\n";
		let elm = file.name.split('.');
		if ('jpeg|jpg|gif|png|xlsx|xls|docx|doc|pdf|tif|'.indexOf(elm[elm.length - 1].toLowerCase()+'|')==-1)
			errmsg += '$warning2 -' + file.name + "\\n";
	});
	if (errmsg!='') {
		alert(errmsg);
	} else {
		var form = document.getElementById('$formId');
		var formdata = new FormData(form);
		$.ajax({
			type: 'POST',
			url: '$link',
		data: formdata,
		mimeType: 'multipart/form-data',
		contentType: false,
		processData: false,
		success: function(data) {
			if (data!='NIL') {
				$('#$tblid').find('tbody').empty().append(data);
				$('input:file').MultiFile('reset')
				attmno = '$modelname'+'_no_of_attm_'+'$typeid';
				counter = $('#'+attmno).val();
				var d = $('#doc$typeid');
				if (counter==undefined || counter==0) {
					d.removeClass();
					d.html('');
				} else {
					d.removeClass().addClass('label').addClass('label-info');
					d.html(counter);
				}
			}
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
		
	}
});
EOF;
	Yii::app()->clientScript->registerScript('fileUpload2'.$doctype,$jscript,CClientScript::POS_READY);
}
?>