<?php
$this->pageTitle=Yii::app()->name . ' - Station Form';
?>
<div class="form frame">
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'station-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
)); ?>

<div class="grid_14">
	<h2 class="page-heading"><?php echo Yii::t('station','Station Form'); ?></h2>
</div>
<div class="grid_8 prefix_2" style="text-align:right;"><h2 class="page-heading">
	<?php echo CHtml::Button(Yii::t('misc','Back'), array(
		'submit'=>Yii::app()->createUrl('station/index'))
	); ?>
<?php if ($model->scenario!='view'): ?>
	<?php echo CHtml::Button(Yii::t('misc','Save'), array(
		'submit'=>Yii::app()->createUrl('station/save'))
	); ?>
<?php endif ?>
</div>
<div class="clear"></div>

<?php echo $form->hiddenField($model, 'scenario'); ?>

<div class="grid_24 block">
	<fieldset>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'station_id'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->textField($model, 'station_id', 
				array('size'=>30,'maxlength'=>30,'readonly'=>true,)
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'station_name'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->textField($model, 'station_name', 
				array('size'=>30,'maxlength'=>30,'readonly'=>true)
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'city'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->dropDownList($model, 'city', General::getCityList(),
				array('disabled'=>true)
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'status'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->dropDownList($model, 'status', 
				array('A'=>Yii::t('misc','Active'),'I'=>Yii::t('misc','Inactive')),
				array('disabled'=>($model->scenario=='view'))
			); ?>
		</div>
		<div class="clear"></div>
	</fieldset>
</div>
<div class="clear"></div>

<div style="display: none">
<?php $this->renderPartial('//site/savedialog'); ?>
</div>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

