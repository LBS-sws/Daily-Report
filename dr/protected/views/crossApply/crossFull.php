<style>
    .select2.select2-container{ width: 100%!important;}
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }
</style>
<form class="form-horizontal" id="crossForm" method="post">
<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,'class'=>"pull-left"));
	$ftrbtn[] = TbHtml::button(Yii::t('misc','Submit'), array('id'=>'crossFullOk','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'crossFull',
					'header'=>Yii::t('app','Cross dispatch'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
$modelForm = get_class($model);
$table_type = $modelForm=="ServiceList"?0:1;

?>

    <div class="form-group" style="margin-top: -10px;margin-bottom: 0px;">
        <div class="col-lg-12">
            <p class="form-control-static text-danger">
                <?php $this->renderPartial('//crossApply/crossNote',array('typeText'=>'dialog')); ?>
            </p>
        </div>
    </div>
<div class="form-group">
    <?php echo Tbhtml::hiddenField('CrossApply[table_type]',$table_type); ?>
    <?php echo Tbhtml::hiddenField('CrossApply[attrStr]','',array("id"=>"attrStr")); ?>
    <?php echo Tbhtml::label(Yii::t("service","Apply date"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::textField('CrossApply[apply_date]','',array('id'=>'cross_apply_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label(Yii::t("service","Cross type"),'',array('class'=>"col-lg-3 control-label")); ?>
    <div class="col-lg-5">
        <?php echo Tbhtml::dropDownList('CrossApply[cross_type]','',CrossApplyForm::getCrossTypeList(),array('empty'=>'',"id"=>"cross_type")); ?>
    </div>
</div>
<div class="qualification-div" style="display: none">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Qualification city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[qualification_city]','',CrossApplyForm::getCityList(),array('id'=>'qualification_city','empty'=>'')); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Qualification ratio"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::numberField('CrossApply[qualification_ratio]','',array('id'=>'qualification_ratio','autocomplete'=>'off','min'=>0,'max'=>100,'append'=>"%")); ?>
        </div>
    </div>
</div>
<div class="accept-div">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","Cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[cross_city]','',CrossApplyForm::getCityList(),array('id'=>'cross_cross_city','empty'=>'')); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","accept rate"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::numberField('CrossApply[rate_num]','',array('id'=>'cross_rate_num','autocomplete'=>'off','min'=>0,'max'=>100,'append'=>"%")); ?>
        </div>
    </div>
</div>
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("service","effective date"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::textField('CrossApply[effective_date]','',array('id'=>'effective_date','autocomplete'=>'off','prepend'=>"<span class='fa fa-calendar'></span>")); ?>
        </div>
    </div>
    <div class="form-group" id="send_city_div" style="display:none;" >
        <?php echo Tbhtml::label(Yii::t("service","send cross city"),'',array('class'=>"col-lg-3 control-label")); ?>
        <div class="col-lg-5">
            <?php echo Tbhtml::dropDownList('CrossApply[send_city]','',CrossApplyForm::getCityList(),array('id'=>'send_city','empty'=>'')); ?>
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
	$('#crossFullBtn').on('click', function (event) {
	    $('#cross_apply_date').val('{$nowDate}');
	    $('#effective_date').val('{$nowDateOne}');
	    if($('.checkOne:checked').length>=1){
	        $('#crossFull').modal('show');
	    }else{
	        $('#errorMessageBody').html('<p>请选择服务单</p>');
	        $('#errorMessage').modal('show');
	    }
	});
	
	$('#cross_type').change(function(){
	    var cross_type = $(this).val();
	    if(['5','6','7','8'].indexOf(cross_type)>=0){
	        $('.qualification-div').slideDown(100);
	    }else{
	        $('#qualification_ratio').val('');
	        $('.qualification-div').slideUp(100);
	    }
        if(cross_type=='5'){
            $('.accept-div').slideUp(100);
	        $('#send_city_div').show(100);
        }else{
	        $('.accept-div').slideDown(100);
            $('#send_city_div').hide(100);
        }
	});
	";
Yii::app()->clientScript->registerScript('crossFull',$js,CClientScript::POS_READY);
$link = Yii::app()->createUrl('crossApply/ajaxCross');
$js = "
$('.che').on('click', function(e){
    e.stopPropagation();
});

$('body').on('click','#all',function() {
	var val = $(this).prop('checked');
	$('.che').children('input[type=checkbox]').prop('checked',val);
});

$('#qualification_ratio,#cross_rate_num').on('change',function(){
    var num_str = $(this).val();
    if(num_str!=''){
        var num = parseFloat(num_str).toFixed(2);
        num = parseFloat(num);
        $(this).val(num);
    }
});

$('#crossFullOk').on('click',function(){
    var list = [];
    var confirmHtml='';
    $('input[type=checkbox]:checked').each(function(){
        var id = $(this).val();
        if(id!=''&&list.indexOf(id)==-1&&$(this).parent('td.che').length==1){
            list.push(id);
        }
    });
    list = list.join(',');
    $('#attrStr').val(list);
    var cross_type = $('#cross_type').val();
    var cross_city = $('#cross_cross_city').val();
    var rate_num = $('#cross_rate_num').val();
    var qualification_city = $('#qualification_city').val();
    var qualification_ratio = $('#qualification_ratio').val();
    var html='';
    if(cross_type==''){
        html+='<p>业务场景不能为空</p>';
    }
    if(['5','6','7','8'].indexOf(cross_type)>=0){
        if(qualification_city==''){
            html+='<p>资质方不能为空</p>';
        }
        if(qualification_ratio==''){
            html+='<p>资质方比例不能为空</p>';
        }else if(qualification_ratio<0||qualification_ratio>100){
            html+='<p>资质方比例的范围：0 ~ 100</p>';
        }
    }
    if(cross_type!='5'){
        if(cross_city==''){
            html+='<p>承接城市不能为空</p>';
        }
        if(rate_num==''){
            html+='<p>承接比例不能为空</p>';
        }else if(rate_num<0||rate_num>100){
            html+='<p>承接比例的范围：0 ~ 100</p>';
        }
    }
    
    if(html!=''){
        $('#errorMessageBody').html(html);
        $('#errorMessage').modal('show');
        $('#crossFullOk').removeAttr('style');
        return false;
    }else{
        var formdata = $('#crossForm').serializeArray();
        $.ajax({
            type: 'POST',
            url: '$link',
            data: formdata,
            dataType: 'json',
            success: function(data) {
                $('#confirmBody').html(data.html);
                $('#confirmDiv').modal('show');
            },
            error: function(data) { // if error occured
                alert('Error occured.please try again');
            }
        });
    }
});

$('#effective_date').datepicker({autoclose: true,language: 'zh_cn', format: 'yyyy/mm/01', minViewMode: 1});
";
Yii::app()->clientScript->registerScript('selectAll',$js,CClientScript::POS_READY);

$js="
$('#qualification_city,#cross_cross_city,#send_city').select2({
    dropdownParent: $('#crossFull'),
    multiple: false,
    maximumInputLength: 10,
    language: 'zh-CN'
});
";
Yii::app()->clientScript->registerScript('searchCityInput',$js,CClientScript::POS_READY);
?>

    <div id="confirmDiv" role="dialog" tabindex="-1" class="modal fade">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal" type="button">×</button>
                    <h4 class="modal-title">确认交叉派单</h4>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>城市</th>
                            <th>客户名称</th>
                            <th>申请日期</th>
                            <th>月金额</th>
                            <th>业务场景</th>
                            <th>资质方</th>
                            <th>资质方比例</th>
                            <th>承接城市</th>
                            <th>承接比例</th>
                            <th>说明</th>
                        </tr>
                        </thead>
                        <tbody id="confirmBody"></tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <?php
                    echo TbHtml::button("确定", array('color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit'=>Yii::app()->createUrl('crossApply/newFull')));
                    ?>
                </div>
            </div>
        </div>
    </div>
</form>

<div id="errorMessage" role="dialog" tabindex="-1" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">验证信息</h4>
            </div>

            <div class="modal-body" id="errorMessageBody">
                <p>。。。</p>
            </div>

            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary" type="button">确定</button>
            </div>
        </div>
    </div>
</div>