<?php
$this->pageTitle=Yii::app()->name . ' - UpdateSeCount Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'UpdateSeCount-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Update Service Count'); ?></strong>
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
            'submit'=>Yii::app()->createUrl('updateSeCount/view')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'start_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'start_date',
                        array('readonly'=>false,'prepend'=>"<span class='fa fa-calendar'></span>")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'end_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'end_date',
                        array('readonly'=>false,'prepend'=>"<span class='fa fa-calendar'></span>")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php
                echo TbHtml::label(Yii::t("user","Fast City"),"",array('class'=>"col-sm-2 control-label"));
                ?>
                <div class="col-sm-10">
                    <?php
                    echo TbHtml::checkBox("0",false,array('label'=>"全部","class"=>"fastChange",'data-city'=>"",'labelOptions'=>array("class"=>"checkbox-inline")));
                    $fastCityList = UserForm::getCityListForArea();
                    foreach ($fastCityList as $row){
                        echo TbHtml::checkBox($row["code"],false,array('label'=>$row["name"],"class"=>"fastChange hide",'data-city'=>$row["city"],'labelOptions'=>array("class"=>"checkbox-inline hide")));
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-10" id="report_look_city">
                    <?php
                    $item = General::getCityListWithCityAllow(Yii::app()->user->city_allow());
                    if (empty($model->city)) {
                        $model->city = array();
                        foreach ($item as $key=>$value) {$model->city[] = $key;}
                    }
                    echo $form->inlineCheckBoxList($model,'city', $item,
                        array('class'=>'look_city'));
                    ?>
                </div>
            </div>
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
    'UpdateSeCountForm_start_date',
    'UpdateSeCountForm_end_date',
));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php
$js="
$('.fastChange').change(function(){
    var cityStr = ','+$(this).data('city')+',';
    var checkBool = $(this).is(':checked')?true:false;
    $('.look_city').each(function(){
        var city = ','+$(this).val()+',';
        if(cityStr==',,'||cityStr.indexOf(city)>-1){
            $(this).prop('checked',checkBool);
        }
    });
});

$('.look_city').each(function(){
    var city = ','+$(this).val()+',';
    $('.fastChange.hide').each(function(){
        var cityStr = ','+$(this).data('city')+',';
        if(cityStr.indexOf(city)>-1){
            $(this).removeClass('hide').parent('label').removeClass('hide');
        }
    });
});
";
Yii::app()->clientScript->registerScript('fastChange',$js,CClientScript::POS_READY);
?>
<?php $this->endWidget(); ?>


