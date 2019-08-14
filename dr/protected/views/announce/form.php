<?php
$this->pageTitle=Yii::app()->name . ' - Announcement Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'code-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('code','Announcement Form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('announce/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('announce/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('announce/save'))); 
			?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'image_type'); ?>
			<?php echo $form->hiddenField($model, 'imageA_old'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'name', 
						array('maxlength'=>100,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'start_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'start_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'end_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'end_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'priority',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php
						echo $form->numberField($model, 'priority', 
							array('min'=>0,'readonly'=>($model->scenario=='view'),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'content',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'content', 
						array('rows'=>3,'cols'=>60,'maxlength'=>5000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'image_caption',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'image_caption', 
						array('rows'=>3,'cols'=>60,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			

			<div class="form-group">
				<?php echo $form->labelEx($model,'imageA',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->fileField($model, 'imageA'); ?>
				</div>
				<div class="col-sm-4">
					<?php echo 
							(empty($model->imageA)) 
							? '&nbsp;' 
							: CHtml::image($model->getImageString(),'imageA',array('width'=>100,'height'=>100));
					?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('announce/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
	$js = "
	$('#AnnounceForm_start_dt').datepicker({autoclose: true, format: 'yyyy/mm/dd'});
	$('#AnnounceForm_end_dt').datepicker({autoclose: true, format: 'yyyy/mm/dd'});
	";
	Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


