<?php
$this->pageTitle=Yii::app()->name . ' - Customer Form';
?>
<div class="form frame">
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'report-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
)); ?>

<div class="grid_16">
	<h2 class="page-heading"><?php echo Yii::t('customer','Customer Form'); ?></h2>
</div>
<div class="grid_6 prefix_2" style="text-align:right;"><h2 class="page-heading">
	<?php echo CHtml::Button(Yii::t('misc','Back'), array(
		'submit'=>Yii::app()->createUrl('report/index'))
	); ?>
<?php if ($model->scenario!='view'): ?>
	<?php echo CHtml::Button(Yii::t('misc','Save'), array(
		'submit'=>Yii::app()->createUrl('report/save'))
	); ?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
	<?php echo CHtml::Button(Yii::t('misc','Delete'), array(
		'name'=>'btnDelete','id'=>'btnDelete')
	); ?>
<?php endif ?>
</div>
<div class="clear"></div>

<?php echo $form->hiddenField($model, 'scenario'); ?>
<?php echo $form->hiddenField($model, 'id'); ?>

<div class="grid_24 block">
	<fieldset>
		<div class="grid_3">
			<?php echo $form->label($model,'code'); ?>
		</div>
		<div class="grid_8">
			<?php echo $form->textField($model, 'code', array('size'=>10,'readonly'=>($model->scenario=='view'),)); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->label($model,'name'); ?>
		</div>
		<div class="grid_8">
			<?php echo $form->textField($model, 'name', 
				array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->label($model,'type'); ?>
		</div>
		<div class="grid_8">
			<?php echo $form->dropDownList($model, 'type', 
				array('AI'=>Yii::t('customer','AI'),'BI'=>Yii::t('customer','BI')),
				array('disabled'=>($model->scenario=='view'))
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->label($model,'nature'); ?>
		</div>
		<div class="grid_8">
			<?php echo $form->dropDownList($model, 'nature', 
				array('A'=>Yii::t('customer','Restaurant'),'B'=>Yii::t('customer','Non-restaurant')),
				array('disabled'=>($model->scenario=='view'))
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->label($model,'cont_name'); ?>
		</div>
		<div class="grid_8">
			<?php echo $form->textField($model, 'cont_name', 
				array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
			); ?>
		</div>
		<div class="grid_3">
			<?php echo $form->label($model,'cont_phone'); ?>
		</div>
		<div class="grid_8">
			<?php echo $form->textField($model, 'cont_phone', 
				array('size'=>15,'maxlength'=>50,'readonly'=>($model->scenario=='view'))
			); ?>
		</div>
		<div class="clear"></div>
	</fieldset>
</div>
<div class="clear"></div>

<div class="grid_24 box" style="padding:0;">
	<div class="grid_24 omega alpha" style="background:#333;">
		<h2>
		<?php echo Yii::t('customer','Service'); ?>
		</h2>
	</div>
	<div class="clear"></div>
	<div class="block" id="callresult">
		<?php $this->widget('ext.layout.TableView2Widget', array(
				'model'=>$model,
				'attribute'=>'service',
				'viewhdr'=>'//report/_formhdr',
				'viewdtl'=>'//report/_formdtl',
				'gridsize'=>'24',
				'height'=>'200',
			));
		?>
		<div class="clear"></div>
	</div>
</div>
<div class="clear"></div>

<?php $this->renderPartial('//site/savedialog'); ?>
<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = "
$('#btnDelete').on('click',function() {
	$('#removedialog').dialog('open');
});

function deletedata() {
	var elm=$('#btnDelete');
	jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('report/delete')."',{});
}
";
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

