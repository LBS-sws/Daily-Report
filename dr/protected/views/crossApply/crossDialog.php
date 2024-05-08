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
?>

<div class="form-group">
    <?php echo Tbhtml::hiddenField('CrossApply[service_id]','',array('id'=>'cross_service_id')); ?>
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
    <?php echo Tbhtml::label(Yii::t("service","Cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::dropDownList('CrossApply[cross_city]','',CrossApplyForm::getCityList(),array('id'=>'cross_cross_city','empty'=>'')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Monthly"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[month_amt]','',array('id'=>'cross_month_amt','prepend'=>"<span class='fa fa-cny'></span>")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Rate number"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::numberField('CrossApply[rate_num]','',array('id'=>'cross_rate_num','min'=>0,'max'=>100,'append'=>"%")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Rate For Amt"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[rate_amt]','',array('id'=>'cross_rate_amt','readonly'=>true,'prepend'=>"<span class='fa fa-cny'></span>")); ?>
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
	    var month_amt = $('#ServiceForm_amt_paid').val();
	    //month_amt = $('#ServiceForm_paid_type').val()=='M'?month_amt:(month_amt/12);
	    $('#cross_service_id').val($('#ServiceForm_id').val());
	    $('#cross_contract_no').val($('#ServiceForm_contract_no').val());
	    $('#cross_apply_date').val('{$nowDate}');
	    $('#cross_month_amt').val(month_amt);
	});
	$('#cross_rate_num,#cross_month_amt').on('change keyup',function(){
	    var rate_num= $('#cross_rate_num').val();
	    var month_amt= $('#cross_month_amt').val();
	    var rate_amt= 0;
	    if(rate_num!=''&&month_amt!=''){
	        rate_amt = month_amt*(rate_num/100);
	        rate_amt = rate_amt.toFixed(2);
	        $('#cross_rate_amt').val(rate_amt);
	    }else{
	        $('#cross_rate_amt').val('');
	    }
	});
	";
Yii::app()->clientScript->registerScript('crossDialog',$js,CClientScript::POS_READY);
?>

