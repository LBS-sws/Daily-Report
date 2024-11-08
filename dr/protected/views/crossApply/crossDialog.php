<style>
    .select2.select2-container{ width: 100%!important;}
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }
</style>
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

<div class="form-group" style="margin-top: -10px;margin-bottom: 0px;">
    <div class="col-lg-12">
        <p class="form-control-static text-danger">
            <?php $this->renderPartial('//crossApply/crossNote',array('typeText'=>'dialog')); ?>
        </p>
    </div>
</div>

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
        <?php echo Tbhtml::textField('CrossApply[apply_date]','',array('id'=>'cross_apply_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Monthly"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[month_amt]','',array('id'=>'cross_month_amt','autocomplete'=>'off','prepend'=>"<span class='fa fa-cny'></span>",'data-amt'=>$endCrossList?$endCrossList["month_amt"]:"")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","apply category"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php
        $apply_category = empty($endCrossList)?2:1;
        ?>
        <?php echo Tbhtml::dropDownList('CrossApply[apply_category]',$apply_category,CrossApplyForm::getApplyCategoryList(),array("id"=>"apply_category",'readonly'=>$apply_category==2)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Cross type"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::dropDownList('CrossApply[cross_type]','',empty($endCrossList)?CrossApplyForm::getCrossTypeList():CrossApplyForm::getCrossTypeThreeList(),array('empty'=>'',"id"=>"cross_type",'data-type'=>$endCrossList?$endCrossList["cross_type"]:"")); ?>
    </div>
</div>
<div class="qualification-div" style="display: none">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Qualification city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[qualification_city]','',CrossApplyForm::getCityList(),array('id'=>'qualification_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["qualification_city"]:"")); ?>
        </div>
    </div>
    <div>
        <div class="form-group">
            <?php echo Tbhtml::label(Yii::t("service","Qualification ratio"),'',array('class'=>"col-lg-3 control-label")); ?>
            <div class="col-lg-5">
                <?php echo Tbhtml::numberField('CrossApply[qualification_ratio]','',array('id'=>'qualification_ratio','autocomplete'=>'off','min'=>0,'max'=>100,'append'=>"%",'data-val'=>$endCrossList?$endCrossList["qualification_ratio"]:"")); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo Tbhtml::label(Yii::t("service","Qualification Amt"),'',array('class'=>"col-lg-3 control-label")); ?>
            <div class="col-lg-5">
                <?php echo Tbhtml::textField('CrossApply[qualification_amt]','',array('id'=>'qualification_amt','autocomplete'=>'off','readonly'=>true,'prepend'=>"<span class='fa fa-cny'></span>")); ?>
            </div>
        </div>
    </div>
</div>
<div class="accept-div">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[cross_city]','',CrossApplyForm::getCityList(),array('id'=>'cross_cross_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["cross_city"]:"",'data-old'=>$endCrossList?$endCrossList["old_city"]:"")); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","accept rate"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::numberField('CrossApply[rate_num]','',array('id'=>'cross_rate_num','autocomplete'=>'off','min'=>0,'max'=>100,'append'=>"%",'data-val'=>$endCrossList?$endCrossList["rate_num"]:"")); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","accept amt"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::textField('CrossApply[rate_amt]','',array('id'=>'cross_rate_amt','autocomplete'=>'off','readonly'=>true,'prepend'=>"<span class='fa fa-cny'></span>")); ?>
        </div>
    </div>
</div>
<div class="form-group" id="effective_div">
    <?php echo Tbhtml::label(Yii::t("service","effective date"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[effective_date]','',array('id'=>'effective_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")); ?>
    </div>
</div>
<div class="form-group" id="send_city_div" style="display:none;" >
    <?php echo Tbhtml::label(Yii::t("service","send cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::dropDownList('CrossApply[send_city]','',CrossApplyForm::getCityList(),array('id'=>'send_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["cross_city"]:"")); ?>
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
    $nowDateOne = date_format(date_create(),"Y/m/01");
	$js="
	$('#crossDialog').on('show.bs.modal', function (event) {
	    var month_amt = $('#{$modelForm}_amt_paid').val();
	    //month_amt = $('#{$modelForm}_paid_type').val()=='M'?month_amt:(month_amt/12);
	    $('#cross_service_id').val($('#{$modelForm}_id').val());
	    $('#cross_contract_no').val($('#{$modelForm}_contract_no').val());
	    $('#cross_apply_date').attr('value','{$nowDate}').trigger('change');
	    $('#effective_date').attr('value','{$nowDateOne}').trigger('change');
	    $('#cross_month_amt').attr('value',month_amt);
	    $('#apply_category').trigger('change');
	});
	$('#apply_category').on('change',function(){
	    var apply_category=$('#apply_category').val();
	    var pre_cross_month_amt=$('#cross_month_amt').data('amt');
	    var pre_cross_city=$('#cross_cross_city').data('city');
	    var pre_old_city=$('#cross_cross_city').data('old');
	    var pre_qualification_city=$('#qualification_city').data('city');
	    var pre_cross_type=$('#cross_type').data('type');
	    var pre_qualification_ratio=$('#qualification_ratio').data('val');
	    var pre_cross_rate_num=$('#cross_rate_num').data('val');
	    switch(apply_category){
	        case '1'://合约金额调整
                if(pre_cross_city!=''&&pre_cross_city!=undefined){
                    $('#cross_cross_city').attr('readonly','readonly').addClass('readonly').prop('disabled',true).val(pre_cross_city).trigger('change');
                }
                if(pre_qualification_city!=''&&pre_qualification_city!=undefined){
                    $('#qualification_city').attr('readonly','readonly').addClass('readonly').prop('disabled',true).val(pre_qualification_city).trigger('change');
                }
                if(pre_cross_type!=''&&pre_cross_type!=undefined){
                    $('#cross_type').attr('readonly','readonly').addClass('readonly').val(pre_cross_type).trigger('change');
                }
                if(pre_qualification_ratio!=''&&pre_qualification_ratio!=undefined){
                    $('#qualification_ratio').attr('readonly','readonly').addClass('readonly').val(pre_qualification_ratio);
                }
                if(pre_cross_rate_num!=''&&pre_cross_rate_num!=undefined){
                    $('#cross_rate_num').attr('readonly','readonly').addClass('readonly').val(pre_cross_rate_num);
                }
                $('#cross_month_amt').removeAttr('readonly').removeClass('readonly');
	            break;
	        case '3'://调整合约内容
                if(pre_cross_type!=''&&pre_cross_type!=undefined){
                    $('#cross_type').attr('readonly','readonly').addClass('readonly').val(pre_cross_type).trigger('change');
                }
                if(pre_cross_month_amt!=''&&pre_cross_month_amt!=undefined){
                    $('#cross_month_amt').attr('readonly','readonly').addClass('readonly').val(pre_cross_month_amt).trigger('change');
                }
                $('#cross_cross_city').removeAttr('readonly').removeClass('readonly').prop('disabled',false);
                $('#qualification_city').removeAttr('readonly').removeClass('readonly').prop('disabled',false);
                $('#qualification_ratio').removeAttr('readonly').removeClass('readonly');
                $('#cross_rate_num').removeAttr('readonly').removeClass('readonly');
	            break;
            default:
                $('#cross_month_amt').removeAttr('readonly').removeClass('readonly');
                $('#cross_cross_city').removeAttr('readonly').removeClass('readonly').prop('disabled',false);
                $('#qualification_city').removeAttr('readonly').removeClass('readonly').prop('disabled',false);
                $('#cross_type').removeAttr('readonly').removeClass('readonly');
                $('#qualification_ratio').removeAttr('readonly').removeClass('readonly');
                $('#cross_rate_num').removeAttr('readonly').removeClass('readonly');
	            break;
	    }
	    $('#cross_type').trigger('change');
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
	    var pre_cross_rate_num=$('#cross_rate_num').data('val');
	    var pre_cross_city=$('#cross_cross_city').data('city');
	    var send_city=$('#send_city').data('city');
	    var pre_old_city=$('#cross_cross_city').data('old');
	    var pre_qualification_city=$('#qualification_city').data('city');
	    var pre_qualification_ratio=$('#qualification_ratio').data('val');
        $('#send_city').val('');
        $('#send_city_div').hide();
        if(['11','12','0','1','5'].indexOf(cross_type)>=0){
            $('#send_city_div').show();
            if(send_city!=''&&send_city!=undefined){
                $('#send_city').val(send_city);
            }
        }
	    if(['5','6','7','8'].indexOf(cross_type)>=0){
	        $('.qualification-div').slideDown(100);
	    }else{
	        $('#qualification_ratio').val('');
	        $('#qualification_amt').val('');
	        $('.qualification-div').slideUp(100);
	    }
        if(['5','0','1'].indexOf(cross_type)>=0){
            $('.accept-div').slideUp(100);
        }else{
	        $('.accept-div').slideDown(100);
        }
        if(cross_type=='11'||cross_type=='12'){
            if(pre_cross_rate_num!=''&&pre_cross_rate_num!=undefined){
	            $('.accept-div').slideDown(100);
                $('#cross_rate_num').attr('readonly','readonly').addClass('readonly').val(0);
            }
            if(pre_old_city!=''&&pre_old_city!=undefined){
                $('#cross_cross_city').attr('readonly','readonly').addClass('readonly').prop('disabled',true).val(pre_old_city).trigger('change');
            }
            if(pre_qualification_city!=''&&pre_qualification_city!=undefined){
	            $('.qualification-div').slideDown(100);
                $('#qualification_city').attr('readonly','readonly').addClass('readonly').prop('disabled',true).val(pre_qualification_city).trigger('change');
            }
            if(pre_qualification_ratio!=''&&pre_qualification_ratio!=undefined){
                $('#qualification_ratio').attr('readonly','readonly').addClass('readonly').val(pre_qualification_ratio);
            }
        }else if(!$(this).hasClass('readonly')){
            $('#cross_rate_num').removeAttr('readonly').removeClass('readonly');
            $('#cross_cross_city').removeAttr('readonly').removeClass('readonly').prop('disabled',false);
            if($('#apply_category').val()!=1){
                $('#qualification_city').removeAttr('readonly').removeClass('readonly').prop('disabled',false);
                $('#qualification_ratio').removeAttr('readonly').removeClass('readonly');
            }
        }
	    $('#cross_rate_num').trigger('change');
	});
	
$('#effective_date').datepicker({autoclose: false,language: 'zh_cn', format: 'yyyy/mm/01', minViewMode: 1});
	";
Yii::app()->clientScript->registerScript('crossDialog',$js,CClientScript::POS_READY);

$js="
$('#qualification_city,#cross_cross_city,#send_city').select2({
    dropdownParent: $('#crossDialog'),
    multiple: false,
    maximumInputLength: 10,
    language: 'zh-CN'
});
";
Yii::app()->clientScript->registerScript('searchCityInput',$js,CClientScript::POS_READY);
?>

