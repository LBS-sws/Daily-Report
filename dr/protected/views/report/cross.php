<?php
$this->pageTitle=Yii::app()->name . ' - Report';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'report-form',
    'action'=>Yii::app()->createUrl('report/chain'),
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('report',$model->name); ?></strong>
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
                <?php echo TbHtml::button(Yii::t('misc','Submit'), array(
                    'submit'=>Yii::app()->createUrl('report/cross')));
                ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'name'); ?>
            <?php echo $form->hiddenField($model, 'fields'); ?>

            <?php if ($model->showField('city') && !Yii::app()->user->isSingleCity()): ?>
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
            <?php else: ?>
                <?php echo $form->hiddenField($model, 'city'); ?>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'company_status',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    $item = CrossApplyList::getCrossStatusList();
                    if (empty($model->company_status)) {
                        $model->company_status = array();
                        foreach ($item as $key=>$value) {$model->company_status[] = $key;}
                    }
                    echo $form->listbox($model, 'company_status', $item,
                        array('displaySize'=>6,'multiple'=>'multiple')
                    );
                    ?>
                </div>
            </div>

            <?php if ($model->showField('start_dt')): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'start_dt',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'start_dt',
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),'id'=>'start_dt'));
                            ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php echo $form->hiddenField($model, 'start_dt'); ?>
            <?php endif ?>

            <?php if ($model->showField('end_dt')): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'end_dt',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'end_dt',
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),'id'=>'end_dt'));
                            ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php echo $form->hiddenField($model, 'end_dt'); ?>
            <?php endif ?>


        </div>
    </div>
</section>

<?php
$datefields = array();
if ($model->showField('start_dt')) $datefields[] = 'start_dt';
if ($model->showField('end_dt')) $datefields[] = 'end_dt';
if (!empty($datefields)) {
    $js = Script::genDatePicker($datefields);
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
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

</div><!-- form -->

