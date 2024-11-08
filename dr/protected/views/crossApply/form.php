<?php
$this->pageTitle=Yii::app()->name . ' - CrossApply Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'CrossApply-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    select[readonly]{ pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('service','My Cross Apply'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('crossApply/index')));
		?>
<?php if ($model->status_type==2): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Submit'), array(
				'submit'=>Yii::app()->createUrl('crossApply/save')));
			?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
<?php endif ?>
	</div>
	</div></div>

    <div class="box">
        <div class="box-body">
            <p class="text-danger">
                <?php $this->renderPartial('//crossApply/crossNote'); ?>
            </p>
        </div>
    </div>
	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'table_type'); ?>
			<?php echo $form->hiddenField($model, 'service_id'); ?>
            <?php echo $form->hiddenField($model, 'status_type'); ?>

            <?php $this->renderPartial('//crossApply/crossForm',array("model"=>$model,"form"=>$form)); ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
if (!$model->readonly()) {
    $js = Script::genDatePicker(array(
        'cross_apply_date',
    ));
    $js.="	
$('#effective_date').datepicker({autoclose: true,language: 'zh_cn', format: 'yyyy/mm/01', minViewMode: 1});";
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('crossApply/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


