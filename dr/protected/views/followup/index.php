<?php
$this->pageTitle=Yii::app()->name . ' - Complaint Cases';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'followup-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('followup','Complaint Cases'); ?></strong>
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
			if (Yii::app()->user->validRWFunction('A03'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','New Record'), array(
					'submit'=>Yii::app()->createUrl('followup/new'), 
				)); 
			 echo TbHtml::button('dummyButton', array('style'=>'display:none','disabled'=>true,'submit'=>'#',));
		?>
	</div>
	</div></div>
	<?php 
		$search = array(
						'type',
						'company_name',
						'content',
						'cont_info',
						'resp_staff',
						'resp_tech',
						'fp_comment',
					);
		if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('followup','Complaint Cases List'),
			'model'=>$model,
				'viewhdr'=>'//followup/_listhdr',
				'viewdtl'=>'//followup/_listdtl',
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
?>
<?php $this->endWidget(); ?>

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

