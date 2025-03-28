<?php
$this->pageTitle=Yii::app()->name . ' - Comparison Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'Comparison-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .changeOffice{ cursor: pointer;}
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
        <strong><?php echo Yii::t('app','Comparison'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('comparison/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('comparison/downExcel')));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-5">
                        <div class="form-group">
                            <?php echo $form->hiddenField($model,"month_type");?>
                            <?php echo $form->labelEx($model,'search_type',array('class'=>"col-lg-5 control-label")); ?>
                            <div class="col-lg-7">
                                <?php echo $form->inlineRadioButtonList($model, 'search_type',SummarySetList::getSelectType(),
                                    array('readonly'=>true,'id'=>'search_type')
                                ); ?>
                            </div>
                        </div>
                        <div id="search_div">
                            <div data-id="1" <?php if ($model->search_type!=1){ echo "style='display:none'"; } ?>>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_year',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->dropDownList($model, 'search_year',SummarySetList::getSelectYear(),
                                            array('readonly'=>true,'id'=>'year_one')
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_quarter',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->dropDownList($model, 'search_quarter',SummarySetList::getSummaryMonthList(),
                                            array('readonly'=>true)
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                            <div data-id="2" <?php if ($model->search_type!=2){ echo "style='display:none'"; } ?>>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_year',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->dropDownList($model, 'search_year',SummarySetList::getSelectYear(),
                                            array('readonly'=>true,'id'=>'year_two')
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_month',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <?php echo $form->dropDownList($model, 'search_month',SummarySetList::getSelectMonth(),
                                                    array('readonly'=>true)
                                                ); ?>
                                            </div>
                                            <div class="col-lg-1" style="width: 0px;overflow: visible;padding: 0px;">
                                                <span class="form-control-static">-</span>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php echo $form->dropDownList($model, 'search_month_end',SummarySetList::getSelectMonth(),
                                                    array('readonly'=>true)
                                                ); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div data-id="3" <?php if ($model->search_type!=3){ echo "style='display:none'"; } ?>>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_start_date',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->textField($model, 'search_start_date',
                                            array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_end_date',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->textField($model, 'search_end_date',
                                            array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'day_num',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->textField($model, 'day_num',
                                            array('readonly'=>true,'append'=>Yii::t("summary","day"))
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--颜色說明-->
                    <?php $this->renderPartial('//comparison/colorNote'); ?>

                    <div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">
                                    <?php echo Yii::t('app','Comparison'); ?>
                                    <small>(<?php echo $model->start_date." ~ ".$model->end_date?>)</small>
                                </h3>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <?php echo $model->comparisonHtml();?>
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
    <div class="modal-dialog" role="document" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <p>加载中....</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--功能說明-->
<?php $this->renderPartial('//comparison/rankingNote',array("model"=>$model)); ?>

<?php
$js="
    $('.click-th').click(function(){
        var contNum = 2;
        var startNum=contNum;
        var endNum = $(this).attr('colspan');
        $(this).prevAll('.click-th').each(function(){
            var colspan = $(this).attr('colspan');
            startNum += parseInt(colspan,10);
        });
        endNum = parseInt(endNum,10)+startNum;
        if($(this).hasClass('active')){
            $(this).children('span').text($(this).data('text'));
            $(this).removeClass('active');
            $('#comparison>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = $(this).data('width')+'px';
                $(this).width(width);
            });
            $('#comparison>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).children('span').text($(this).data('text'));
            });
            $('#comparison>tbody>tr').each(function(){
                $(this).children().slice(startNum,endNum).each(function(){
                    $(this).children('span').text($(this).data('text'));
                });
            });
        }else{
            $(this).data('text',$(this).text());
            $(this).children('span').text('.');
            $(this).addClass('active');
            $('#comparison>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = '15px';
                $(this).width(width);
            });
            $('#comparison>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).data('text',$(this).text());
                $(this).children('span').text('');
            });
            $('#comparison>tbody>tr').each(function(){
                $(this).children().slice(startNum,endNum).each(function(){
                    $(this).data('text',$(this).text());
                    $(this).children('span').text('');
                });
            });
        }
    });
    
    $('.click-tr').click(function(){
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
$ajaxUrl = Yii::app()->createUrl('comparison/ajaxDetail');
$js.= "
$('.td_detail').on('click',function(){
    var tdOne = $(this).parent('tr').children('td').eq(0).text();
    $('#detailDialog').find('.modal-title').text($(this).data('title')+' - '+tdOne);
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
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


$ajaxUrl = Yii::app()->createUrl('comparison/ajaxOffice');
$js="
$(function(){
    var cityList=[];
    $('td.changeOffice').each(function(){
        cityList.push($(this).data('city'));
    });
    $.ajax({
        type: 'GET',
        url: '{$ajaxUrl}',
        data: {
            'cityList':cityList,
            'searchType':'{$model->search_type}',
            'startDate':'{$model->start_date}',
            'endDate':'{$model->end_date}'
        },
        dataType: 'json',
        success: function(data) {
            var dataList = data.list.cityHtml;
            $('form:first').prepend(data.list.hideHtml);
            $('td.changeOffice').each(function(){
                var city = $(this).data('city');
                if(typeof dataList[city] !== undefined){
                    $(this).parent('tr').after(dataList[city]);
                }
                //fa-minus
                $(this).find('i:first').removeClass('fa-spinner fa-pulse').addClass('fa-plus');
            });
        },
        error: function(data) { // if error occured
            alert('Error occured.please try again');
        }
    });
    
    $('td.changeOffice').on('click',function(){
        var city = $(this).data('city');
        if($(this).find('i:first').hasClass('fa-plus')){ //展开
            $(this).find('i:first').removeClass('fa-plus').addClass('fa-minus');
            $('tr.office-city-tr[data-city=\"'+city+'\"]').slideDown(100);
        }else if($(this).find('i:first').hasClass('fa-minus')){ //收缩
            $(this).find('i:first').removeClass('fa-minus').addClass('fa-plus');
            $('tr.office-city-tr[data-city=\"'+city+'\"]').slideUp(100);
        }
    });
});
";
Yii::app()->clientScript->registerScript('changeOffice',$js,CClientScript::POS_READY);

$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


