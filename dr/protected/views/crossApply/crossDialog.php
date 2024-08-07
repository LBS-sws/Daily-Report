<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,'class'=>"pull-left"));
	$ftrbtn[] = TbHtml::button(Yii::t('misc','Submit'), array('color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit'=>Yii::app()->createUrl('crossApply/newSave')));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'crossDialog',
					'header'=>Yii::t('app','Cross dispatch'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
$modelForm = get_class($model);
$table_type = $modelForm=="ServiceForm"?0:1;

$endCrossList = CrossApplyForm::getEndCrossListForTypeAndId($table_type,$model->id);
?>

<div class="form-group">
    <?php echo Tbhtml::hiddenField('CrossApply[service_id]','',array('id'=>'cross_service_id')); ?>
    <?php echo Tbhtml::hiddenField('CrossApply[table_type]',$table_type); ?>
    <?php echo Tbhtml::label(Yii::t("service","Contract No"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('CrossApply[contract_no]','',array('id'=>'cross_contract_no','readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Apply date"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[apply_date]','',array('id'=>'cross_apply_date','prepend'=>"<span class='fa fa-calendar'></span>")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Monthly"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[month_amt]','',array('id'=>'cross_month_amt','prepend'=>"<span class='fa fa-cny'></span>")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Cross type"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::dropDownList('CrossApply[cross_type]','',CrossApplyForm::getCrossTypeList(),array('empty'=>'',"id"=>"cross_type",'data-type'=>$endCrossList?$endCrossList["cross_type"]:"")); ?>
    </div>
</div>
<div class="qualification-div" style="display: none">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Qualification city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[qualification_city]','',CrossApplyForm::getCityList(),array('id'=>'qualification_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["qualification_city"]:"")); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Qualification ratio"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::numberField('CrossApply[qualification_ratio]','',array('id'=>'qualification_ratio','min'=>0,'max'=>100,'append'=>"%")); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Qualification Amt"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::textField('CrossApply[qualification_amt]','',array('id'=>'qualification_amt','readonly'=>true,'prepend'=>"<span class='fa fa-cny'></span>")); ?>
        </div>
    </div>
</div>
<div class="accept-div">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[cross_city]','',CrossApplyForm::getCityList(),array('id'=>'cross_cross_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["cross_city"]:"")); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","accept rate"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::numberField('CrossApply[rate_num]','',array('id'=>'cross_rate_num','min'=>0,'max'=>100,'append'=>"%")); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","accept amt"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::textField('CrossApply[rate_amt]','',array('id'=>'cross_rate_amt','readonly'=>true,'prepend'=>"<span class='fa fa-cny'></span>")); ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Remarks"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textArea('CrossApply[remark]','',array('id'=>'cross_remark','rows'=>4)); ?>
    </div>
</div>

<?php
	$this->endWidget();
    $nowDate = date_format(date_create(),"Y/m/d");
	$js="
	$('#crossDialog').on('show.bs.modal', function (event) {
	    var month_amt = $('#{$modelForm}_amt_paid').val();
	    //month_amt = $('#{$modelForm}_paid_type').val()=='M'?month_amt:(month_amt/12);
	    $('#cross_service_id').val($('#{$modelForm}_id').val());
	    $('#cross_contract_no').val($('#{$modelForm}_contract_no').val());
	    $('#cross_apply_date').val('{$nowDate}');
	    $('#cross_month_amt').val(month_amt);
	    var pre_cross_city=$('#cross_cross_city').data('city');
	    var pre_qualification_city=$('#qualification_city').data('city');
	    var pre_cross_type=$('#cross_type').data('type');
	    if(pre_cross_city!=''&&pre_cross_city!=undefined){
	        $('#cross_cross_city').attr('readonly','readonly').addClass('readonly').val(pre_cross_city);
	    }
	    if(pre_qualification_city!=''&&pre_qualification_city!=undefined){
	        $('#qualification_city').val(pre_qualification_city);
	    }
	    if(pre_cross_type!=''&&pre_cross_type!=undefined){
	        $('#cross_type').val(pre_cross_type).trigger('change');
	    }
	});
	$('#cross_rate_num,#cross_month_amt,#qualification_ratio').on('change',function(){
	    var qualification_ratio= $('#qualification_ratio').val();
	    var rate_num= $('#cross_rate_num').val();
	    var month_amt= $('#cross_month_amt').val();
	    var rate_amt= 0;
	    if(qualification_ratio!=''&&month_amt!=''){
            qualification_ratio = parseFloat(qualification_ratio).toFixed(2);
            qualification_ratio = parseFloat(qualification_ratio);
            $('#qualification_ratio').val(qualification_ratio);
            var qualification_amt = month_amt*(qualification_ratio/100);
	        qualification_amt = qualification_amt.toFixed(2);
	        $('#qualification_amt').val(qualification_amt);
	    }else{
	        $('#qualification_amt').val('');
	    }
	    if(rate_num!=''&&month_amt!=''){
            rate_num = parseFloat(rate_num).toFixed(2);
            rate_num = parseFloat(rate_num);
            $('#cross_rate_num').val(rate_num);
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
	    $('#cross_rate_num').trigger('change');
	});
	";
Yii::app()->clientScript->registerScript('crossDialog',$js,CClientScript::POS_READY);
?>

