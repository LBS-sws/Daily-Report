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
                    array('readonly'=>$model->readonly(),'id'=>'apply_category','empty'=>'')
                ); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'cross_type',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->dropDownList($model, 'cross_type',empty($endCrossList)?CrossApplyForm::getCrossTypeList():CrossApplyForm::getCrossTypeThreeList(),
                    array('empty'=>'','readonly'=>$model->readonly(),'id'=>'cross_type')
                ); ?>
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
        <div class="qualification-div" style="<?php if($qualificationBool){ echo 'display: none';} ?>">
            <div class="form-group">
                <?php echo $form->labelEx($model,'qualification_city',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'qualification_city',CrossApplyForm::getCityList(),
                        array('empty'=>'','id'=>'qualification_city','readonly'=>$model->readonly())
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'qualification_ratio',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->numberField($model, 'qualification_ratio',
                        array('readonly'=>$model->readonly(),'id'=>'qualification_ratio','autocomplete'=>'off','append'=>"%","min"=>0,"max"=>"100")
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
        <div class="accept-div" style="<?php if($model->cross_type==5){ echo 'display: none';} ?>">
            <div class="form-group">
                <?php echo $form->labelEx($model,'cross_city',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'cross_city',CrossApplyForm::getCityList(),
                        array('empty'=>'','id'=>'cross_cross_city','readonly'=>$model->readonly())
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'rate_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->numberField($model, 'rate_num',
                        array('readonly'=>$model->readonly(),'id'=>'cross_rate_num','autocomplete'=>'off','append'=>"%","min"=>0,"max"=>"100")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'cross_amt',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'cross_amt',
                        array('id'=>'cross_rate_amt','readonly'=>true,'autocomplete'=>'off','prepend'=>"<span class='fa fa-cny'></span>")
                    ); ?>
                </div>
            </div>
        </div>
        <div class="form-group" id="effective_div" style="<?php if($model->apply_category!=2){ echo 'display: none';} ?>">
            <?php echo $form->labelEx($model,'effective_date',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->textField($model, 'effective_date',
                    array('readonly'=>$model->readonly(),'id'=>'effective_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")
                ); ?>
            </div>
        </div>
        <div class="form-group" id="send_city_div" style="<?php echo in_array($model->cross_type,array(11,12))?"":"display:none;";?>" >
            <?php echo Tbhtml::label(Yii::t("service","send cross city"),'',array('class'=>"col-lg-2 control-label")); ?>
            <div class="col-lg-3">
                <?php echo $form->dropDownList($model, 'send_city',CrossApplyForm::getCityList(),
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
	$('#cross_rate_num,#cross_month_amt,#qualification_ratio').on('change keyup',function(){
	    var qualification_ratio= $('#qualification_ratio').val();
	    var rate_num= $('#cross_rate_num').val();
	    var month_amt= $('#cross_month_amt').val();
	    var rate_amt= 0;
	    if(qualification_ratio!=''&&month_amt!=''){
	        $('#qualification_amt').val(month_amt*(qualification_ratio/100));
	    }else{
	        $('#qualification_amt').val('');
	    }
	    if(rate_num!=''&&month_amt!=''){
	        qualification_ratio = qualification_ratio==''?0:qualification_ratio;
	        month_amt = month_amt*((100-qualification_ratio)/100);
	        rate_amt = month_amt*(rate_num/100);
	        rate_amt = rate_amt.toFixed(2);
	        $('#cross_rate_amt').val(rate_amt);
	    }else{
	        $('#cross_rate_amt').val('');
	    }
	});
	
	$('#cross_type').change(function(){
	    var cross_type = $(this).val();
	    if(['5','6','7','8'].indexOf(cross_type)>=0){
	        $('.qualification-div').slideDown(100);
	    }else{
	        $('#qualification_ratio').val('');
	        $('#qualification_amt').val('');
	        $('.qualification-div').slideUp(100);
	    }
        if(cross_type=='5'){
            $('.accept-div').slideUp(100);
        }else{
	        $('.accept-div').slideDown(100);
        }
        if(cross_type=='11'||cross_type=='12'){
            $('#send_city_div').show();
        }else{
            $('#send_city_div').hide();
        }
	    $('#cross_rate_num').trigger('change');
	});
	";
    Yii::app()->clientScript->registerScript('crossDialog',$js,CClientScript::POS_READY);
}
?>