<?php
$this->pageTitle=Yii::app()->name . ' - Activation Form';
?>
<div class="form frame">
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'activate-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
)); ?>

<?php echo $form->hiddenField($model, 'req_key'); ?>
<?php echo $form->hiddenField($model, 'station_name'); ?>
<?php echo $form->hiddenField($model, 'hash1'); ?>
<?php echo $form->hiddenField($model, 'hash2'); ?>
<?php echo $form->hiddenField($model, 'timestamp'); ?>

<div class="grid_24 block">
	<fieldset>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'email'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->textField($model, 'email', array('size'=>30,'maxlength'=>100)); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'city'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->dropDownList($model, 'city', General::getCityList()); ?>
		</div>
		<div class="clear"></div>
		<div class="clear"></div>
		<div class="grid_10">
				<?php echo CHtml::submitButton(Yii::t('misc','Submit'),array('class'=>'confirm button',)); ?>
		</div>
		<div class="clear"></div>
	</fieldset>
</div>
<div class="clear"></div>

<?php $this->endWidget(); ?>

</div><!-- form -->

