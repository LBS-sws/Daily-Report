<?php
$this->pageTitle=Yii::app()->name . ' - OutBusiness Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'OutBusiness-form',
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
        <strong><?php echo Yii::t('app','Out Business'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('outBusiness/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('outBusiness/downExcel')));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-4">
                        <div class="form-group hide">
                            <?php
                            $outCityJson = json_encode($model->outCity,JSON_UNESCAPED_UNICODE);
                            echo TbHtml::hiddenField("outCity",$outCityJson,array("id"=>"outCity"));
                            ?>
                            <?php echo $form->hiddenField($model,"month_type");?>
                            <?php echo $form->labelEx($model,'search_type',array('class'=>"col-lg-5 control-label")); ?>
                            <div class="col-lg-7">
                                <?php echo $form->inlineRadioButtonList($model, 'search_type',OutBusinessForm::getSelectType(),
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
                                        <?php echo $form->dropDownList($model, 'search_month',SummarySetList::getSelectMonth(),
                                            array('readonly'=>true)
                                        ); ?>
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

                    <div class="col-lg-8">
                        <!--查询說明-->
                        <?php $this->renderPartial('//outBusiness/indexNote'); ?>
                    </div>
                    <div class="col-lg-12">
                        <p>&nbsp;</p>
                    </div>

                    <?php
                    $model->downJsonText = array();
                    $contentHead='<div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">{:head:}<small>('.$model->start_date ." ~ ".$model->end_date.')</small>
                                </h3>
                            </div>
                            <!-- Table -->
                            <div class="table-responsive">';

                    $contentEnd='</div></div></div>';
                    $tabs =array();
                    $contentTable = str_replace("{:head:}",Yii::t("app",'Out Business'),$contentHead);
                    $contentTable.=$model->outBusinessHtml();
                    $contentTable.=$contentEnd;
                    $tabs[] = array(
                        'label'=>Yii::t("app",'Out Business'),
                        'content'=>$contentTable,
                        'active'=>true,
                    );//外包数据分析
                    $contentTable = str_replace("{:head:}",Yii::t("summary","OutBusiness productivity"),$contentHead);
                    $contentTable.=$model->outBusinessHtml(2);
                    $contentTable.=$contentEnd;
                    //$contentTable.=TbHtml::hiddenField("excel[two]",$areaModel->downJsonText);
                    $tabs[] = array(
                        'label'=>Yii::t("summary","OutBusiness productivity"),
                        'content'=>$contentTable,
                        'active'=>false,
                    );//外包人员生产力
                    echo TbHtml::tabbableTabs($tabs);
                    ?>
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

<div class="hide" id="tableDiv">
    <table class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th width="100px">区域</th>
            <th width="100px">城市</th>
            <th width="100px">工号 (文本)</th>
            <th width="100px">员工 (姓名)</th>
            <th width="100px">服务金额</th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <tr>
            <td colspan="2" class="text-right">汇总数量：</td>
            <td id="tableDiv_sum"></td>
            <td class="text-right">汇总金额：</td>
            <td id="tableDiv_amt"></td>
        </tr>
        <tr class="noExl">
            <td colspan="6" class="text-right">
                <button class="table2excel btn btn-default" name="yt0" type="button">下载</button>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<?php
$js="
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
$ajaxUrl = Yii::app()->createUrl('outBusiness/ajaxDetail');
$js.= "
var outCityJson='';
$('.td_detail').on('click',function(){
    if(outCityJson==''){
        outCityJson=$('#outCity').val();
        outCityJson=JSON.parse(outCityJson);
    }
    var tdOne = $(this).parent('tr').children('td').eq(0).text();
    $('#detailDialog').find('.modal-title').text($(this).data('title')+' - '+tdOne);
    var html = $('#tableDiv').html();
    var cityCode = $(this).data('city');
    var dataType = $(this).data('type');
    if(outCityJson[cityCode]!==undefined){
        var htmlObj = $(html);
        var amt=0;
        var sum=0;
        $.each(outCityJson[cityCode],function(key,item){
            if(dataType=='all'||item['table_type']==4){
                sum++;
                amt+=parseFloat(item['service_money']);
                var temp='<tr>';
                temp+='<td>'+item['region_name']+'</td>';
                temp+='<td>'+item['city_name']+'</td>';
                temp+='<td>'+item['staff_code']+'</td>';
                temp+='<td>'+item['staff_name']+'</td>';
                temp+='<td>'+item['service_money']+'</td>';
                temp+='</tr>';
                htmlObj.find('tbody').append(temp);
            }
        });
        amt = amt.toFixed(2);
        htmlObj.find('#tableDiv_sum').text(sum);
        htmlObj.find('#tableDiv_amt').text(amt);
        $('#detailDialog').find('.modal-body').html(htmlObj);
    }else{
        $('#detailDialog').find('.modal-body').html('<p>没有数据</p>');
    }
    $('#detailDialog').modal('show');
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


