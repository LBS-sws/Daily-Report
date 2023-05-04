<?php
$this->pageTitle=Yii::app()->name . ' - ServiceCount Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ServiceCount-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Customer Service Count'); ?></strong>
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
                <?php echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('enquiry','Enquiry'), array(
                    'submit'=>Yii::app()->createUrl('serviceCount/edit')));
                ?>
            </div>
        </div></div>

	<div class="box box-info">
		<div class="box-body">
            <div class="form-group">
                <?php echo $form->labelEx($model,'status',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'status',ServiceCountForm::getStatusList(),
                        array('class'=>'form-control',));
                    ?>
                </div>
                <?php echo $form->labelEx($model,'cust_type',array('class'=>"col-sm-1 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'cust_type',ServiceCountForm::getServiceTypeList(),
                        array('class'=>'form-control'));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'search_year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'search_year',ServiceCountForm::getYearList(),
                        array('class'=>'form-control',));
                    ?>
                </div>
                <?php echo $form->labelEx($model,'city_allow',array('class'=>"col-sm-1 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'city_allow',ServiceCountForm::getCityList(),
                        array('class'=>'form-control'));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'company_name',
                        array('class'=>'form-control',));
                    ?>
                </div
            </div>
		</div>
	</div>
</section>


<?php
$js="
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


