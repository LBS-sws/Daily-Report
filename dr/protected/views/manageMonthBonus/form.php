<?php
$this->pageTitle=Yii::app()->name . ' - Bonus Month Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ManageMonthBonus-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .click-th,.click-tr,.td_detail{ cursor: pointer;}
    .click-tr>.fa:before{ content: "\f062";}
    .click-tr.show-tr>.fa:before{ content: "\f063";}
    .table-fixed{ table-layout: fixed;}
    .radio-inline,select{ opacity: 0.6;pointer-events: none;}
    .form-group{ margin-bottom: 0px;}
    .table-fixed>thead>tr>th,.table-fixed>tfoot>tr>td,.table-fixed>tbody>tr>td{ text-align: center;vertical-align: middle;font-size: 12px;border-color: #333;}
    .table-fixed>tfoot>tr>td,.table-fixed>tbody>tr>td{ text-align: right;}
    .table-fixed>thead>tr>th.header-width{ height: 0px;padding: 0px;overflow: hidden;border-width: 0px;line-height: 0px;}
</style>

<section class="content-header">
	<h1>
        <strong><?php echo Yii::t('app','Management Month Bonus'); ?></strong>
        <?php $this->renderPartial('//site/uLoadData',array("model"=>$model)); ?>
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
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('manageMonthBonus/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php if (Yii::app()->user->validFunction('CN31')): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> 强制刷新', array(
                        'id'=>"saveCache"));
                    ?>
                <?php endif ?>
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('manageMonthBonus/downExcel')));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <?php echo $form->hiddenField($model, 'update_user'); ?>
                    <?php echo $form->hiddenField($model, 'update_date'); ?>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'search_year',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-2">
                                <?php echo $form->dropDownList($model, 'search_year',ManageMonthBonusForm::getYearList(),
                                    array('readonly'=>true,"id"=>"search_year")
                                ); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'search_month',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-2">
                                <?php echo $form->dropDownList($model, 'search_month',ManageMonthBonusForm::getMonthList(),
                                    array('readonly'=>true,"id"=>"search_month")
                                ); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'status_type',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-2">
                                <?php
                                $statusStr = $model->status_type==1?"已固定":"未固定";
                                echo TbHtml::textField("status_type",$statusStr,array('readonly'=>true))
                                ?>
                            </div>
                            <div class="col-lg-8">
                                <p class="form-control-static">每月五号晚上10点系统自动固定上月的月度奖金，如果数据异常请与管理员联系</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">
                                    <?php echo Yii::t('app','Management Month Bonus'); ?>
                                    <small>(<?php echo $model->start_date." ~ ".$model->end_date?>)</small>
                                </h3>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <?php echo $model->manageMonthBonusHtml();?>
                                <?php echo $form->hiddenField($model,"downJsonText",array("name"=>"excel"));?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>

<!--詳情彈窗-->
<div class="modal fade" tabindex="-1" role="dialog" id="detailDialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">强制刷新中...</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">请耐心等待五分钟左右，正在强制刷新中...</p>
                <p class="text-center">刷新后，页面会自动跳转</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$saveCacheUrl = Yii::app()->createUrl('manageMonthBonus/saveCache');

$js="
    $('.click-tr').click(function(){
    console.log(1);
        var show = $(this).hasClass('show-tr');
        if(show){
            $(this).removeClass('show-tr');
        }else{
            $(this).addClass('show-tr');
        }
        $(this).prevAll('tr').each(function(){
            if($(this).hasClass('tr-end')||$(this).children('td:first').hasClass('click-tr')){
                return false;
            }else{
                if(show){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            }
        });
    });
";
$ajaxUrl = Yii::app()->createUrl('manageMonthBonus/ajaxDetail');
$js.= "
$('.td_detail').on('click',function(){
    var city_name = $(this).parent('tr').children('td').eq(0).text();
    $('#detailDialog').find('.modal-title').text($(this).data('title')+' - '+city_name);
    $('#detailDialog').find('.modal-body').html('<p>加载中....</p>');
    $('#detailDialog').modal('show');
    $.ajax({
        type: 'GET',
        url: '{$ajaxUrl}',
        data: {
            'city':$(this).data('city'),
            'type':$(this).data('type'),
            'startDate':'{$model->start_date}',
            'endDate':'{$model->end_date}'
        },
        dataType: 'json',
        success: function(data) {
            $('#detailDialog').find('.modal-body').html(data['html']);
        },
        error: function(data) { // if error occured
            alert('Error occured.please try again');
        }
    });
});

resetEndBonus();
function resetEndBonus(){
    var jsonData = $('#excel').val();
    if(jsonData!=''){
        jsonData = JSON.parse(jsonData);
        jsonData = jsonData['excel'];
        $.each(jsonData,function(staff_id,row){
            if(row['end_bonus']!=''){
                $('td[data-type=\"end_bonus\"][data-id=\"'+staff_id+'\"]').html('<span>'+row['end_bonus']+'</span>');
            }
        });
    }
}

$('#saveCache').click(function(){
    $('#detailDialog').modal('show');
    $('*').css('pointer-events','none');
    jQuery.yii.submitForm(this,'{$saveCacheUrl}',{});
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


