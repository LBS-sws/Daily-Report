<?php
$this->pageTitle=Yii::app()->name . ' - Service';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'service-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Customer Service KA'); ?></strong>
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
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if (Yii::app()->user->validRWFunction('A13'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','New Record'), array(
					'name'=>'btnAdd','id'=>'btnAdd','data-toggle'=>'modal','data-target'=>'#addrecdialog',)
				); 
		?>
	</div>

            <?php if (Yii::app()->user->validRWFunction('CD01')): ?>
                <div class="btn-group pull-right" role="group">
                    <?php
                    //交叉派单
                    echo TbHtml::button('<span class="fa fa-superpowers"></span> '.Yii::t('app','Cross dispatch'), array(
                            'id'=>'crossFullBtn')
                    );
                    ?>
                </div>
            <?php endif ?>
	</div></div>
	
	<div class="box">
        <div class="box-body">
            <div class="form-group">
                <label><?php echo "归属：";?></label>
                <div class="btn-group" role="group">
                    <?php
                    $modelName = get_class($model);
                    $officeList=GetNameToId::getStaticOfficeType();
                    foreach ($officeList as $key=>$value){
                        $class = $key===$model->office_type?" btn-primary active":"";
                        echo TbHtml::button($value,array("class"=>"btn_submit".$class,"data-key"=>$key));
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
	<?php 
		$search = array(
						'service_no',
						'company_name',
						'type_desc',
						'nature_desc',
						'service',
						'cont_info',
						'status',
					);
		if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('service','Service List'),
			'model'=>$model,
			'viewhdr'=>'//serviceKA/_listhdr',
			'viewdtl'=>'//serviceKA/_listdtl',
			'search'=>$search,
			'hasDateButton'=>true,
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
	echo $form->hiddenField($model,'office_type');
?>

<?php
$url = Yii::app()->createUrl('serviceKA/index',array("pageNum"=>1));

$js = "
$('.clickable-row').click(function() {
	window.document.location = $(this).data('href');
});

    $('.btn_submit').on('click',function(){
        var key=$(this).data('key');
        $('#ServiceKAList_orderField').val('');
        $('#ServiceKAList_office_type').val(key);
        jQuery.yii.submitForm(this,'{$url}',{});
    });
";
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);

?>

<?php $this->endWidget(); ?>

<?php
$this->renderPartial('//site/cityServiceBtn',array("actionStr"=>"serviceKA"));
if (Yii::app()->user->validRWFunction('CD01')){ //交叉派单
    $this->renderPartial('//crossApply/crossFull',array("model"=>$model));
}
?>