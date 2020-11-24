<?php
$this->pageTitle=Yii::app()->name . ' - Task Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'code-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('code','Task Form'); ?></strong>
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
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('task/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('task/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('task/save'))); 
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

			<div class="form-group">
				<?php echo $form->labelEx($model,'description',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'description', 
						array('size'=>50,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'task_type',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->dropDownList($model, 'task_type', 
								array(
									'NIL'=>Yii::t('code','Nil'),
									'PAPER'=>Yii::t('code','Paper'),
									'SOAP'=>Yii::t('code','Soap'),
									'FLOOR'=>Yii::t('code','Floor Cleaner'),
									'MAINT'=>Yii::t('code','Maintenance'),
									'UNINS'=>Yii::t('code','Uninstallion'),
									'RELOC'=>Yii::t('code','Relocation'),
									'REPLA'=>Yii::t('code','Replacement'),
									'PURIS'=>Yii::t('code','Puriscent'),
									'PERFU'=>Yii::t('code','Perfume'),
									'OTHER'=>Yii::t('code','Other'),
								),
								array('disabled'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'sales_products',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'sales_products',
                        array(
                            'paper'=>Yii::t('code','Paper'),//纸
                            'disinfectant'=>Yii::t('code','Disinfectant'),//消毒液
                            'purification'=>Yii::t('code','Purification'),//空气净化
                            'chemical'=>Yii::t('code','Chemical'),//化学剂
                            'aromatherapy'=>Yii::t('code','Aromatherapy'),//香薰
                            'pestcontrol'=>Yii::t('code','Pest control'),//虫控
                            'other'=>Yii::t('code','Other'),//其他
                        ),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('task/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


