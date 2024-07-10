<?php
$this->pageTitle=Yii::app()->name . ' - CrossAudit';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'crossAudit-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Cross Audit'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                if (Yii::app()->user->validRWFunction('CD02'))
                    echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('service','Audit Full'), array(
                            'submit'=>Yii::app()->createUrl('crossAudit/auditFull'))
                    );
                ?>
            </div>
        </div>
    </div>

	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('app','Cross Apply'),
			'model'=>$model,
				'viewhdr'=>'//crossAudit/_listhdr',
				'viewdtl'=>'//crossAudit/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>array(
                    'contract_no',
                    'company_name',
                    'apply_date',
                    'old_city',
                    'cross_city'
                ),
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
	echo TbHtml::hiddenField("attrStr",'',array("id"=>"attrStr"));

echo TbHtml::button("aa",array("submit"=>"#","class"=>"hide"));
?>
<?php $this->endWidget(); ?>

<?php
$js = "
$('.che').on('click', function(e){
    e.stopPropagation();
});

$('body').on('click','#all',function() {
	var val = $(this).prop('checked');
	$('.che').children('input[type=checkbox]').prop('checked',val);
});
$('form').on('submit',function(){
    var list = [];
    var confirmHtml='';
    $('input[type=checkbox]:checked').each(function(){
        var id = $(this).val();
        if(id!=''&&list.indexOf(id)==-1&&$(this).parent('td.che').length==1){
            list.push(id);
        }
    });
    list = list.join(',');
    $('#attrStr').val(list);
});
";
Yii::app()->clientScript->registerScript('selectAll',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
