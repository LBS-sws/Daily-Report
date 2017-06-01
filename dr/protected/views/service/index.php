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
		<strong><?php echo Yii::t('service','Service'); ?></strong>
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
			if (Yii::app()->user->validRWFunction('A02'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Record'), array(
					'name'=>'btnAdd','id'=>'btnAdd','data-toggle'=>'modal','data-target'=>'#addrecdialog',)
				); 
		?>
	</div>
	</div></div>
	<?php 
		$search = array(
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
			'viewhdr'=>'//service/_listhdr',
			'viewdtl'=>'//service/_listdtl',
			'gridsize'=>'24',
			'height'=>'600',
			'search'=>$search,
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>

<?php
	$buttons = array(
			TbHtml::button(Yii::t('service','New Service'), 
				array(
					'name'=>'btnNew',
					'id'=>'btnNew',
					'class'=>'btn btn-block',
					'submit'=>Yii::app()->createUrl('service/new'), 
					'data-dismiss'=>'modal',
				)),
			TbHtml::button(Yii::t('service','Renew Service'), 
				array(
					'name'=>'btnRenew',
					'id'=>'btnRenew',
					'class'=>'btn btn-block',
					'submit'=>Yii::app()->createUrl('service/renew'), 
					'data-dismiss'=>'modal',
				)),
			TbHtml::button(Yii::t('service','Amend Service'), 
				array(
					'name'=>'btnAmend',
					'id'=>'btnAmend',
					'class'=>'btn btn-block',
					'submit'=>Yii::app()->createUrl('service/amend'), 
					'data-dismiss'=>'modal',
				)),
			TbHtml::button(Yii::t('service','Suspend Service'), 
				array(
					'name'=>'btnSuspend',
					'id'=>'btnSuspend',
					'class'=>'btn btn-block',
					'submit'=>Yii::app()->createUrl('service/suspend'), 
					'data-dismiss'=>'modal',
				)),
			TbHtml::button(Yii::t('service','Resume Service'), 
				array(
					'name'=>'btnResume',
					'id'=>'btnResume',
					'class'=>'btn btn-block',
					'submit'=>Yii::app()->createUrl('service/resume'), 
					'data-dismiss'=>'modal',
				)),
			TbHtml::button(Yii::t('service','Terminate Service'), 
				array(
					'name'=>'btnTerminate',
					'id'=>'btnTerminate',
					'class'=>'btn btn-block',
					'submit'=>Yii::app()->createUrl('service/terminate'), 
					'data-dismiss'=>'modal',
				)),
		);
	
	$content = "";
	foreach ($buttons as $button) {
		$content .= "<div class=\"row\"><div class=\"col-sm-10\">$button</div></div>";
	}
	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'addrecdialog',
					'header'=>Yii::t('misc','Add Record'),
					'content'=>$content,
//					'footer'=>array(
//						TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
//					),
					'show'=>false,
				));

$js = "
$('.clickable-row').click(function() {
	window.document.location = $(this).data('href');
});
";
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);

?>

<?php $this->endWidget(); ?>
