<?php
$this->pageTitle=Yii::app()->name . ' - CheckInStaff Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'CheckInStaff-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Check In Staff'); ?></strong>
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
        <?php echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('summary','Enquiry'), array(
            'submit'=>Yii::app()->createUrl('checkInStaff/view')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
            <div id="search_div">
                <div data-id="3">
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_start_date',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->textField($model, 'search_start_date',
                                array('readonly'=>false,'prepend'=>"<span class='fa fa-calendar'></span>")
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_end_date',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->textField($model, 'search_end_date',
                                array('readonly'=>false,'prepend'=>"<span class='fa fa-calendar'></span>")
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--查询說明-->
		</div>
	</div>
</section>


<?php
$js="
    $('button').click(function(){
        Loading.show();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDatePicker(array(
    'CheckInStaffForm_search_start_date',
    'CheckInStaffForm_search_end_date'
));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


