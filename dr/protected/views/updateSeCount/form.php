<?php
$this->pageTitle=Yii::app()->name . ' - UpdateSeCount Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'UpdateSeCount-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .tooltip-inner{ text-align: left;}
    .click-th,.click-tr{ cursor: pointer;}
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
        <strong><?php echo Yii::t('app','Update Service Count'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('UpdateSeCount/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('UpdateSeCount/downExcel',array("searchType"=>$model->searchType))));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'start_date',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-2">
                                <?php echo $form->textField($model, 'start_date',
                                    array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                ); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'end_date',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-2">
                                <?php echo $form->textField($model, 'end_date',
                                    array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                ); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-10" id="report_look_city">
                                <?php
                                $item = General::getCityListWithCityAllow(Yii::app()->user->city_allow());
                                if (empty($model->city)) {
                                    $model->city = array();
                                    foreach ($item as $key=>$value) {$model->city[] = $key;}
                                }
                                echo $form->inlineCheckBoxList($model,'city', $item,
                                    array('readonly'=>true,'class'=>'look_city'));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row">
                            <ul class="nav nav-tabs" id="capacityMenu">
                                <li <?php if (empty($model->searchType)){ echo " class='active'";}?>>
                                    <?php
                                    echo TbHtml::btn("linkButton",Yii::t("summary","staff update Count"),array(
                                        "class"=>"btn btn-link",
                                        "submit"=>Yii::app()->createUrl('updateSeCount/view',array("searchType"=>0)),
                                    ));
                                    ?>
                                </li>
                                <li <?php if (!empty($model->searchType)){ echo " class='active'";}?>>
                                    <?php
                                    echo TbHtml::btn("linkButton",Yii::t("summary","contract update Count"),array(
                                        "class"=>"btn btn-link",
                                        "submit"=>Yii::app()->createUrl('updateSeCount/view',array("searchType"=>1)),
                                    ));
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-12" style="padding-top: 15px;">
                        <?php if (!empty($model->searchType)): ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label">表格内搜索:</label>
                                        <div class="col-lg-5">
                                            <div class="input-group">
                                                <input name="pageSearchText" id="pageSearchText" class="form-control" type="text" value="" placeholder="城市、系统来源、合约ID、客户编号及名称、客户类别">
                                                <span class="input-group-btn"><button id="pageSearchBtn" class="btn btn-default"  type="button"><span class="fa fa-search"></span> 搜寻</button></span>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">
                                    <?php
                                    if(empty($model->searchType)){
                                        echo Yii::t('summary','staff update Count');
                                    }else{
                                        echo Yii::t('summary','contract update Count');
                                    }
                                    ?>
                                    <small>(<?php echo $model->start_date." ~ ".$model->end_date;?>)</small>
                                </h3>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <?php echo $model->updateSeCountHtml();?>
                                <?php echo TbHtml::hiddenField("excel",$model->downJsonText);?>
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

<?php
$js="
$('#capacityMenu a').click(function(){
    Loading.show();
});

";
$ajaxUrl = Yii::app()->createUrl('updateSeCount/ajaxDetail');
$js.= "
$('.td_detail').on('click',function(){
    var tdOne = $(this).data('titleexp');
    $('#detailDialog').find('.modal-title').text($(this).data('title')+' - '+tdOne);
    $('#detailDialog').find('.modal-body').html('<p>加载中....</p>');
    $('#detailDialog').modal('show');
    $.ajax({
        type: 'GET',
        url: '{$ajaxUrl}',
        data: {
            'search':$(this).data('search'),
            'table':$(this).data('table'),
            'type':$(this).data('type'),
            'startDate':'{$model->start_date}',
            'endDate':'{$model->end_date}',
            'searchType':'{$model->searchType}'
        },
        dataType: 'json',
        success: function(data) {
            $('#detailDialog').find('.modal-body').html(data['html']);
            $('[data-toggle=\"tooltip\"]').tooltip({ html:true});
        },
        error: function(data) { // if error occured
            alert('Error occured.please try again');
        }
    });
});

$('.clickPage>a').click(function(){
    var objLi = $(this).parent('li');
    var pageNum = objLi.data('page');
    var pageMax = objLi.data('max');
    var startNum = (pageNum-1)*pageMax;
    var endNum = pageNum*pageMax;
    objLi.siblings('li').removeClass('active');
    objLi.addClass('active');
    $('.pageTr').addClass('hide');
    $('.pageTr').slice(startNum,endNum).removeClass('hide');
});

$('#pageSearchBtn').click(function(){
    Loading.show();
    var searchText = $('#pageSearchText').val();
    if(searchText!=''){
        $('#paginationID').addClass('hide');
        $('.pageTr').addClass('hide');
        $('.searchText').each(function(){
            if($(this).text().indexOf(searchText)>-1){
                $(this).parents('.pageTr').eq(0).removeClass('hide');
            }
        });
    }else{
        $('#paginationID').removeClass('hide');
        $('.clickPage').eq(0).find('a:first').trigger('click');
    }
    $('#loading').hide();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


