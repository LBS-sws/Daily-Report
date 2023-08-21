<?php
$this->pageTitle=Yii::app()->name . ' - SystemLog';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'systemLog-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>
<style>
    .tooltip>.tooltip-inner{ text-align: left;}
    .active .badge {
        color: #337ab7;
        background-color: #fff;
    }
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','System Log'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
    <?php
    TbHtml::button('submit',array("class"=>"hide",'submit'=>"1"));
    ?>
	<?php
    $className = get_class($model);
    $search_add_html= TbHtml::dropDownList($className.'[optionType]',$model->optionType,SystemLogList::getOptionTypeList(),
        array("class"=>"form-control submitBtn"));
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('log','System Log List'),
        'model'=>$model,
        'viewhdr'=>'//systemLog/_listhdr',
        'viewdtl'=>'//systemLog/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'city',
            'log_type_name',
            'log_code',
            'option_str',
            'option_text',
        ),
    ));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
	echo $form->hiddenField($model,'id');
?>

<?php
$this->renderPartial('//site/fileupload',array(
    'model'=>$model,
    'form'=>$form,
    'doctype'=>'SLOG',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>(!Yii::app()->user->validRWFunction('D06')),
));
//$model->getInputBool()
?>
<?php $this->endWidget(); ?>

<?php
Script::genFileUploadList($model,$form->id,'SLOG');
$js = "
$('.submitBtn').change(function(){
    $('form:first').submit();
});

$('[data-toggle=\"tooltip\"]').tooltip({ html:true});
$('.click-doc').click(function(){
    var code = $(this).data('code');
    var id = $(this).data('id');
    $('#SystemLogList_id').val(id);
    if($('#fileuploadslog .modal-title>small').length>0){
        $('#fileuploadslog .modal-title>small').text('('+code+')');
    }else{
        $('#fileuploadslog .modal-title').eq(0).append('<small>('+code+')</small>');
    }
    $('#tblFileslog>tbody').html('');
    getFileListAll();
    $('#fileuploadslog').modal('show');
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

//$js = Script::genTableRowClick();
	//Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
