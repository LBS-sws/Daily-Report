<?php
$this->pageTitle=Yii::app()->name . ' - RetentionKARate Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'RetentionKARate-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .margin-p-0>p{margin-bottom: 2px;}
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
        <strong><?php echo Yii::t('app','Retention KA rate'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('retentionKARate/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('retentionKARate/downExcel')));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-5">
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'search_year',array('class'=>"col-lg-5 control-label")); ?>
                            <div class="col-lg-7">
                                <?php echo $form->dropDownList($model, 'search_year',RetentionKARateForm::getRetentionYear(),
                                    array('readonly'=>true)
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 margin-p-0 visible-lg">
                        <p>1、长约：服务表单金额为非一次性金额的客户</p>
                        <p>2、长约每月显示金额即销售当月“更改+恢复+暂停+终止”总和</p>
                        <p>3、长约保留率 =  1- ((长约YTD综合停单金额 / YTD 月份数）*12)/生效中合同总金额</p>

                        <p>生效中合同金额不包括一次性服务金额</p>

                        <p>4、一次性：- 派单系统服务类型为“灭虫（一次性）、清洁（一次性）、白蚁、甲醛、雾化消毒、灭虫喷焗即为一次性</p>
                        <p>5、一次性每个月显示的金额即派单系统已完工的服务工单金额</p>
                        <p>6、一次性保留率 = 1-（上月一次性金额 -本月一次性金额） / 本月一次性服务生意额</p>
                        <p>7、每年1月份将去年12月一次性服务金额显示出来</p>
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
                    $contentTable = str_replace("{:head:}",Yii::t('summary','Long Service Rate'),$contentHead);
                    $contentTable.=$model->retentionKARateHtml(0);
                    $contentTable.=$contentEnd;
                    $tabs[] = array(
                        'label'=>Yii::t("summary","Long Service Rate"),
                        'content'=>$contentTable,
                        'active'=>true,
                    );
                    //长约保留率
                    $contentTable = str_replace("{:head:}",Yii::t("summary","One Service Rate"),$contentHead);
                    $contentTable.=$model->retentionKARateHtml(1);
                    $contentTable.=$contentEnd;
                    $tabs[] = array(
                        'label'=>Yii::t("summary","One Service Rate"),
                        'content'=>$contentTable,
                        'active'=>false,
                    );
                    //一次性保留率
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
$ajaxUrl = Yii::app()->createUrl('retentionKARate/ajaxDetail');
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

$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


