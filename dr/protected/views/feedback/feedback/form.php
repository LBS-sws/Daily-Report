<?php
$this->pageTitle=Yii::app()->name . ' - Feedback Form';
?>
<div class="form frame">
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'feedback-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
)); ?>

<div class="grid_14">
	<h2 class="page-heading"><?php echo Yii::t('feedback','Feedback Form'); ?></h2>
</div>
<div class="grid_8 prefix_2" style="text-align:right;"><h2 class="page-heading">
	<?php echo CHtml::Button(Yii::t('misc','Back'), array(
		'submit'=>Yii::app()->createUrl('feedback/index'))
	); ?>
<?php if ($model->scenario!='view'): ?>
	<?php echo CHtml::Button(Yii::t('misc','Save'), array(
		'submit'=>Yii::app()->createUrl('feedback/save'))
	); ?>
<?php endif ?>
</div>
<div class="clear"></div>

<?php echo $form->hiddenField($model, 'scenario'); ?>
<?php echo $form->hiddenField($model, 'id'); ?>
<?php echo $form->hiddenField($model, 'status'); ?>

<div class="grid_24 block">
	<fieldset>
<div class='grid_23'>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'request_dt'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->textField($model, 'request_dt', 
				array('size'=>15,'maxlength'=>50,'readonly'=>true)
			); ?>
		</div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'status_desc'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->textField($model, 'status_desc', 
				array('size'=>10,'maxlength'=>10,'readonly'=>true)
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'feedback_dt'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->textField($model, 'feedback_dt', 
				array('size'=>15,'maxlength'=>50,'readonly'=>true)
			); ?>
		</div>
		<div class="clear"></div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'to'); ?>
		</div>
		<div class="grid_7">
			<?php echo $form->textArea($model, 'to', 
				array('rows'=>4,'cols'=>30,'maxlength'=>200,'readonly'=>true)
			); ?>
		</div>
		<div class="grid_3">
			<?php echo $form->labelEx($model,'cc'); ?>
		</div>
		<div class="grid_7">
			<?php 
				echo $form->listbox($model, 'cc', General::getEmailListboxData(), 
					array('size'=>4,'multiple'=>'multiple','disabled'=>($model->scenario=='view'))
				); 
			?>
		</div>
		<div class="clear"></div>
</div>
<div class="clear"></div>
<div class="grid_3">&nbsp;</div>
<div class="clear"></div>
<div class='grid_23 body_panel' style='height:500px;'>
<?php
	$cnt = 0;
	foreach ($model->cats as $cat=>$desc) {
		$cnt++;
		$cat_field = 'cat_'.$cnt;
		$fb_field = 'feedback_'.$cnt;
		echo '<div class="grid_3">';
		echo $form->checkBox($model,$cat_field, array('value'=>'Y','uncheckValue'=>'N','disabled'=>($model->scenario=='view')));
		echo $form->labelEx($model,$cat_field);
		echo '</div>';
		echo '<div class="grid_7">';
		echo $form->textArea($model, $fb_field, 
				array('rows'=>5,'cols'=>80,'maxlength'=>5000,'readonly'=>($model->scenario=='view' || $model->$cat_field!='Y'))
			);		
		echo '</div>';
		echo '<div class="clear"></div>';
	}
?>
		<div class="clear"></div>
</div>
		<div class="clear"></div>
	</fieldset>
</div>
<div class="clear"></div>

<div style="display: none">
<?php $this->renderPartial('//site/savedialog'); ?>
</div>

<?php
$js = "";
$cnt = 0;
foreach ($model->cats as $cat=>$desc) {
	$cnt++;
	$cfield = 'FeedbackForm_cat_'.$cnt;
	$ffield = 'FeedbackForm_feedback_'.$cnt;
	$js .= "
$('#".$cfield."').on('change',function() {
	if ($(this).is(':checked')) {
		$('#".$ffield."').removeAttr('readonly');
		$('#".$ffield."').removeClass('readonly');
	} else {
		$('#".$ffield."').prop('readonly',true);
		$('#".$ffield."').addClass('readonly');
	}
});
";
}
Yii::app()->clientScript->registerScript('feedbackReadonly',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

