<?php
$this->pageTitle=Yii::app()->name . ' - UService Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'UService-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .select2-container.select2-container-disabled .select2-choice {
        background-color: #ddd;
        border-color: #a8a8a8;
    }
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
        <strong><?php echo Yii::t('app','U Service Amount'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('uService/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('uService/downExcel')));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?php echo $form->hiddenField($model,"month_type");?>
                            <?php echo $form->labelEx($model,'search_type',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-10">
                                <?php echo $form->inlineRadioButtonList($model, 'search_type',UServiceForm::getSelectType(),
                                    array('readonly'=>true,'id'=>'search_type')
                                ); ?>
                            </div>
                        </div>
                        <div id="search_div">
                            <div data-id="1" <?php if ($model->search_type!=1){ echo "style='display:none'"; } ?>>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_year',array('class'=>"col-sm-2 control-label")); ?>
                                    <div class="col-sm-2">
                                        <?php echo $form->dropDownList($model, 'search_year',SummarySetList::getSelectYear(),
                                            array('readonly'=>true,'id'=>'year_one')
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_quarter',array('class'=>"col-sm-2 control-label")); ?>
                                    <div class="col-sm-2">
                                        <?php echo $form->dropDownList($model, 'search_quarter',SummarySetList::getSummaryMonthList(),
                                            array('readonly'=>true)
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                            <div data-id="2" <?php if ($model->search_type!=2){ echo "style='display:none'"; } ?>>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_year',array('class'=>"col-sm-2 control-label")); ?>
                                    <div class="col-sm-2">
                                        <?php echo $form->dropDownList($model, 'search_year',SummarySetList::getSelectYear(),
                                            array('readonly'=>true,'id'=>'year_two')
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_month',array('class'=>"col-sm-2 control-label")); ?>
                                    <div class="col-sm-2">
                                        <?php echo $form->dropDownList($model, 'search_month',SummarySetList::getSelectMonth(),
                                            array('readonly'=>true)
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                            <div data-id="3" <?php if ($model->search_type!=3){ echo "style='display:none'"; } ?>>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_start_date',array('class'=>"col-sm-2 control-label")); ?>
                                    <div class="col-sm-2">
                                        <?php echo $form->textField($model, 'search_start_date',
                                            array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'search_end_date',array('class'=>"col-sm-2 control-label")); ?>
                                    <div class="col-sm-2">
                                        <?php echo $form->textField($model, 'search_end_date',
                                            array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo $form->hiddenField($model, 'city'); ?>
                            <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-4">
                                <?php
                                echo $form->textArea($model, 'city_desc',
                                    array('rows'=>2,'cols'=>80,'maxlength'=>1000,'readonly'=>true)
                                );
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'condition',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-8">
                                <?php echo $form->dropDownList($model, 'condition',UServiceForm::getConditionList(),
                                    array('class'=>'select2 de_class','multiple'=>'multiple','de_type'=>'select2','id'=>'condition')
                                );
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'seniority_min',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-2">
                                <?php echo $form->numberField($model, 'seniority_min',
                                    array('readonly'=>true,'min'=>0)
                                ); ?>
                            </div>
                            <div class="pull-left text-center">
                                <p class="form-control-static"> 至 </p>
                            </div>
                            <div class="col-sm-2">
                                <?php echo $form->numberField($model, 'seniority_max',
                                    array('readonly'=>true,'min'=>0)
                                ); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'staff_type',array('class'=>"col-sm-2 control-label")); ?>
                            <div class="col-sm-10">
                                <?php echo $form->inlineRadioButtonList($model, 'staff_type',UServiceForm::getStaffType(),
                                    array('readonly'=>true,'id'=>'staff_type')
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">
                                    <?php echo Yii::t('app','U Service Amount'); ?>
                                    <small>(<?php echo $model->start_date." ~ ".$model->end_date?>)</small>
                                </h3>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <?php echo $model->uServiceHtml();?>
                                <?php echo $form->hiddenField($model,"downJsonText",array("name"=>"excel"));?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>


<?php
switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = 'true';
$js="
$('#condition').select2({
	tags: false,
	multiple: true,
	maximumInputLength: 0,
	maximumSelectionLength: 10,
	allowClear: true,
	language: '$lang',
	disabled: $disabled,
	templateSelection: formatState
});
function formatState(state) {
	var rtn = $('<span style=\"color:black\">'+state.text+'</span>');
	return rtn;
}
    $('.click-th').click(function(){
        var contNum = 0;
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
            $('#uService>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = $(this).data('width')+'px';
                $(this).width(width);
            });
            $('#uService>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).children('span').text($(this).data('text'));
            });
            $('#uService>tbody>tr').each(function(){
                $(this).children().slice(startNum,endNum).each(function(){
                    $(this).children('span').text($(this).data('text'));
                });
            });
        }else{
            $(this).data('text',$(this).text());
            $(this).children('span').text('.');
            $(this).addClass('active');
            $('#uService>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = '15px';
                $(this).width(width);
            });
            $('#uService>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).data('text',$(this).text());
                $(this).children('span').text('');
            });
            $('#uService>tbody>tr').each(function(){
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
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


