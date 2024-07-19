<?php
$this->pageTitle=Yii::app()->name . ' - CrossSearch Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'CrossSearch-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    select[readonly]{ pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Cross Search'); ?></strong>
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
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('crossSearch/index')));
		?>
<?php if ($model->status_type==1): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Approve'), array(
				'submit'=>Yii::app()->createUrl('crossSearch/audit')));
			?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Deny'), array(
                    'data-toggle'=>'modal','data-target'=>'#denyDialog',)
            );
            ?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'service_id'); ?>
            <?php echo $form->hiddenField($model, 'table_type'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>

            <?php $this->renderPartial('//crossApply/crossForm',array("model"=>$model,"form"=>$form)); ?>
		</div>
	</div>
</section>

<?php
$content="<div class=\"form-group\">";
$content.=$form->labelEx($model,'reject_note',array('class'=>"col-lg-3 control-label"));
$content.="<div class=\"col-lg-8\">";
$content.=$form->textArea($model, 'reject_note',
    array('readonly'=>false,'id'=>'reject_note','rows'=>4)
);
$content.="</div></div>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'denyDialog',
    'header'=>Yii::t('misc','Deny'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit'=>Yii::app()->createUrl('crossSearch/reject'))),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));

?>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


