<?php
$this->pageTitle=Yii::app()->name . ' - Report';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'kaRetention-form',
    'action'=>Yii::app()->createUrl('report/kaRetention'),
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','KA Retention report'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('report/kaRetention')));
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
                <?php echo $form->labelEx($model,'year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'year', ReportKaRetentionForm::getYearList(),
                        array('disabled'=>($model->scenario=='view'),'id'=>"year")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo TbHtml::label("快捷操作","all",array('class'=>"col-sm-2 control-label"));?>
                <div class="col-sm-3">
                    <?php echo TbHtml::checkBox("all",true,array("label"=>"全选"));?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'chain_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <?php
                    echo $model->getKaManBoxHtml($model->year);
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

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

$('#all').change(function(){
    var checkBool = $(this).is(':checked');
    $('.changeCheck').prop('checked',checkBool);
});

$('#year').change(function(){
    window.location.href='".Yii::app()->createUrl('report/kaRetention')."?year='+$(this).val();
});
";
Yii::app()->clientScript->registerScript('fastChange',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

