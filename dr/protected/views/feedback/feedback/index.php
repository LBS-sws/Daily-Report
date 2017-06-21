<?php
$this->pageTitle=Yii::app()->name . ' - Feedback';
?>

<div class="form frame">
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'feedback-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
)); ?>

<div class="grid_17">
	<h2 class="page-heading"><?php echo Yii::t('feedback','Feedback'); ?></h2>
</div>
<div class="grid_5 prefix_2" style="text-align:right;"><h2 class="page-heading">
	<?php 
//		if (Yii::app()->user->validRWFunction('A08'))
//			echo CHtml::Button(Yii::t('misc','Add Record'), array(
//				'submit'=>Yii::app()->createUrl('feedback/new'))
//			); 
	?>
	&nbsp;
</div>
<div class="clear"></div>

<div class="grid_24 box" style="padding:0;">
	<div class="grid_24 omega alpha" style="background:#333;">
		<h2>
		<?php echo Yii::t('feedback','Feedback List'); ?>
		</h2>
	</div>
		<?php $this->widget('ext.layout.ListPageWidget', array(
				'model'=>$model,
				'viewhdr'=>'//feedback/_listhdr',
				'viewdtl'=>'//feedback/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>array(
							'request_dt',
							'feedback_dt',
							'status',
							'feedback_cat',
							'feedbacker',
						),
			));
		?>
</div>
<div class="clear"></div>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

</div><!-- form -->
