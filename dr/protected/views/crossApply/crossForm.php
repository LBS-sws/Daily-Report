<style>
    .select2.select2-container{ width: 100%!important;}
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }
</style>
<?php
$table_type_name = CrossApplyForm::getCrossTableTypeNameForKey($model->table_type);


$endCrossList = CrossApplyForm::getEndCrossListForTypeAndId($model->table_type,$model->service_id,$model->id);
?>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="#cross" aria-controls="cross" role="tab" data-toggle="tab"><?php echo Yii::t("app","Cross Apply");?></a>
    </li>
    <li role="presentation">
        <a href="#service" aria-controls="service" role="tab" data-toggle="tab"><?php echo $table_type_name;?></a>
    </li>
    <li role="presentation">
        <a href="#crossTable" aria-controls="crossTable" role="tab" data-toggle="tab"><?php echo Yii::t("service","Cross Table");?></a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <!-- cross -->
    <div role="tabpanel" class="tab-pane active" id="cross">
        <p>&nbsp;</p>
        <?php if ($model->status_type==2): ?>
            <div class="form-group has-error">
                <?php echo $form->labelEx($model,'reject_note',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-7">
                    <?php echo $form->textArea($model, 'reject_note',
                        array('readonly'=>true,'rows'=>4)
                    ); ?>
                </div>
            </div>
        <?php endif ?>
        <div class="form-group">
            <?php echo $form->labelEx($model,'table_type',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->hiddenField($model, 'cross_num'); ?>
                <?php echo $form->hiddenField($model, 'status_type'); ?>
                <?php
                echo TbHtml::textField("table_type",$table_type_name,array('readonly'=>true));
                 ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'contract_no',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->textField($model, 'contract_no',
                    array('readonly'=>true)
                ); ?>
            </div>
            <?php echo Tbhtml::label(Yii::t("service","status type"),'',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-2">
                <?php
                echo Tbhtml::textField('status_type',CrossApplyList::getStatusStrForStatusType($model),array('readonly'=>true));
                ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->textField($model, 'apply_date',
                    array('readonly'=>$model->readonly(),'id'=>'cross_apply_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'month_amt',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->textField($model, 'month_amt',
                    array('readonly'=>$model->readonly(),'id'=>'cross_month_amt','autocomplete'=>'off','prepend'=>"<span class='fa fa-cny'></span>")
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'apply_category',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php
                $model->apply_category = empty($endCrossList)?2:$model->apply_category;
                ?>
                <?php echo $form->dropDownList($model, 'apply_category',CrossApplyForm::getApplyCategoryList(),
                    array('readonly'=>true,'id'=>'apply_category','empty'=>'')
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'cross_type',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->hiddenField($model, 'cross_type',array("id"=>"cross_type")); ?>
                <?php
                echo TbHtml::textField("cross_type",CrossApplyForm::getCrossTypeStrToKey($model->cross_type),array('readonly'=>true));
                ?>
            </div>
        </div>
        <?php
        if(in_array($model->cross_type,array('5','6','7','8'))){
            $qualificationBool = false;
        }elseif(in_array($model->cross_type,array('11','12'))&&!empty($model->qualification_city)){
            $qualificationBool = false;
        }else{
            $qualificationBool = true;
        }
        ?>
        <div class="qualification-div" style="<?php if($qualificationBool){ echo 'display: none;';} ?>">
            <div class="form-group">
                <?php echo $form->labelEx($model,'qualification_city',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'qualification_city',CrossApplyForm::getCityOnlyList($model->qualification_city),
                        array('empty'=>'','id'=>'qualification_city','readonly'=>$model->readonly())
                    ); ?>
                </div>
            </div>
            <div class="form-group" >
                <?php echo $form->labelEx($model,'qualification_ratio',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->numberField($model, 'qualification_ratio',
                        array('readonly'=>true,'id'=>'qualification_ratio','autocomplete'=>'off','append'=>"%","min"=>0,"max"=>"100")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'qualification_amt',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'qualification_amt',
                        array('id'=>'qualification_amt','readonly'=>true,'autocomplete'=>'off','prepend'=>"<span class='fa fa-cny'></span>")
                    ); ?>
                </div>
            </div>
        </div>
        <div class="accept-div" style="<?php if(in_array($model->cross_type,array(0,1))){ echo 'display: none;';} ?>">
            <div class="form-group">
                <?php echo $form->labelEx($model,'cross_city',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'cross_city',CrossApplyForm::getCityOnlyList($model->cross_city),
                        array('empty'=>'','id'=>'cross_cross_city','readonly'=>$model->readonly())
                    ); ?>
                </div>
            </div>
            <div class="form-group" style="<?php if($model->cross_type==13){ echo 'display: none;';} ?>">
                <?php echo $form->labelEx($model,'rate_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->numberField($model, 'rate_num',
                        array('readonly'=>true,'id'=>'cross_rate_num','autocomplete'=>'off','append'=>"%","min"=>0,"max"=>"100")
                    ); ?>
                </div>
            </div>
            <div class="form-group" style="<?php if($model->cross_type==13){ echo 'display: none;';} ?>">
                <?php echo $form->labelEx($model,'cross_amt',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'cross_amt',
                        array('id'=>'cross_rate_amt','readonly'=>true,'autocomplete'=>'off','prepend'=>"<span class='fa fa-cny'></span>")
                    ); ?>
                </div>
            </div>
        </div>
        <div class="form-group" id="effective_div" >
            <?php echo $form->labelEx($model,'effective_date',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->textField($model, 'effective_date',
                    array('readonly'=>$model->readonly(),'id'=>'effective_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")
                ); ?>
            </div>
        </div>
        <div class="form-group" id="send_city_div" >
            <?php echo Tbhtml::label(Yii::t("service","send cross city"),'',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->dropDownList($model, 'send_city',CrossApplyForm::getCityOnlyList($model->send_city),
                    array('empty'=>'','id'=>'send_city','readonly'=>$model->readonly())
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'remark',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-7">
                <?php echo $form->textArea($model, 'remark',
                    array('readonly'=>$model->readonly(),'id'=>'cross_textArea','rows'=>4)
                ); ?>
            </div>
        </div>
        <?php if (in_array($model->status_type,array(2,3,5,6))): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_user',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo $form->hiddenField($model, 'audit_user');
                    $audit_user = RptCross::getStaffNameForUsername($model->audit_user);
                    echo TbHtml::textField('audit_user', $audit_user,
                        array('readonly'=>true)
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'audit_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'audit_date',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <?php if (in_array($model->status_type,array(5,6))): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'u_update_user',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-2">
                        <?php echo $form->textField($model, 'u_update_user',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'u_update_date',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-2">
                        <?php echo $form->textField($model, 'u_update_date',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>
    </div>

    <!-- service -->
    <div role="tabpanel" class="tab-pane" id="service">
        <p>&nbsp;</p>
        <?php $this->renderPartial('//crossApply/serviceForm',array("model"=>$model,"form"=>$form)); ?>
    </div>

    <!-- service -->
    <div role="tabpanel" class="tab-pane" id="crossTable">
        <p>&nbsp;</p>
        <?php $this->renderPartial('//crossApply/crossTable',array("model"=>$model,"form"=>$form)); ?>
    </div>
</div>

<?php
if($model->status_type==2){
    $js="
	
	";
    Yii::app()->clientScript->registerScript('crossDialog',$js,CClientScript::POS_READY);

    $js="
$('#qualification_city,#cross_cross_city,#send_city').select2({
    multiple: false,
    maximumInputLength: 10,
    language: 'zh-CN'
});
";
    Yii::app()->clientScript->registerScript('searchCityInput',$js,CClientScript::POS_READY);
}
?>