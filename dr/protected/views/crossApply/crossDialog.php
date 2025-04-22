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

<div class="form-group hide" style="margin-top: -10px;margin-bottom: 0px;">
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
    <?php echo Tbhtml::label(Yii::t("service","Cross type"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::dropDownList('CrossApply[cross_type]','',CrossApplyForm::getCrossTypeEndList(),array('empty'=>'',"id"=>"cross_type",'data-type'=>"")); ?>
    </div>
</div>
<div class="qualification-div">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Qualification city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[qualification_city]','',CrossApplyForm::getCityOnlyList(),array('id'=>'qualification_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["qualification_city"]:"")); ?>
        </div>
    </div>
</div>
<div class="accept-div">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[cross_city]','',CrossApplyForm::getCityOnlyList(),array('id'=>'cross_cross_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["cross_city"]:"",'data-old'=>$endCrossList?$endCrossList["old_city"]:"")); ?>
        </div>
    </div>
</div>
<div class="form-group" id="effective_div">
    <?php echo Tbhtml::label(Yii::t("service","effective date"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[effective_date]','',array('id'=>'effective_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")); ?>
    </div>
</div>
<div class="form-group" id="send_city_div" >
    <?php echo Tbhtml::label(Yii::t("service","send cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::dropDownList('CrossApply[send_city]','',CrossApplyForm::getCityOnlyList(),array('id'=>'send_city','empty'=>'','data-city'=>$endCrossList?$endCrossList["send_city"]:"")); ?>
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
	    
	    changeCity();
	});
	
	function changeCity(){
	    if($('#qualification_city').val()==''){
	        $('#qualification_city').val($('#qualification_city').data('city')).trigger('change');
	    }
	    if($('#cross_cross_city').val()==''){
	        $('#cross_cross_city').val($('#cross_cross_city').data('city')).trigger('change');
	    }
	    if($('#send_city').val()==''){
	        $('#send_city').val($('#send_city').data('city')).trigger('change');
	    }
	}
	
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

