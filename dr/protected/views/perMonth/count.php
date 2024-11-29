<?php
$this->pageTitle=Yii::app()->name . ' - PerMonthCount Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'perMonthCount-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
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
        <strong><?php echo Yii::t('app','Lost orders rate'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('perMonth/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('perMonth/downExcel',array('type'=>'count'))));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-5">
                        <div class="form-group">
                            <?php echo TbHtml::hiddenField("month_type",$model->month_type);?>
                            <?php echo $form->labelEx($model,'search_date',array('class'=>"col-sm-5 control-label")); ?>
                            <div class="col-sm-7">
                                <?php echo $form->textField($model, 'search_date',
                                    array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                ); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'day_num',array('class'=>"col-sm-5 control-label")); ?>
                            <div class="col-sm-7">
                                <p class="form-control-static">
                                    <?php echo $model->start_date." ~ ".$model->end_date."&nbsp; (".$model->day_num."天)";?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row">
                            <ul class="nav nav-tabs" id="perMonthMenu">
                                <li>
                                    <?php
                                    echo TbHtml::link(Yii::t("summary","Per Month Add"),Yii::app()->createUrl('perMonth/add'));
                                    ?>
                                </li>
                                <li>
                                    <?php
                                    echo TbHtml::link(Yii::t("summary","Per Month Stop"),Yii::app()->createUrl('perMonth/stop'));
                                    ?>
                                </li>
                                <li>
                                    <?php
                                    echo TbHtml::link(Yii::t("summary","Per Month Recover"),Yii::app()->createUrl('perMonth/recover'));
                                    ?>
                                </li>
                                <li>
                                    <?php
                                    echo TbHtml::link(Yii::t("summary","Per Month Net"),Yii::app()->createUrl('perMonth/net'));
                                    ?>
                                </li>
                                <li class="active">
                                    <?php
                                    echo TbHtml::link(Yii::t("summary","Per Month Count"),Yii::app()->createUrl('perMonth/count'));
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">
                                    <?php echo Yii::t('summary','Per Month Count'); ?>
                                    <small>(<?php echo $model->start_date." ~ ".$model->end_date;?>)</small>
                                </h3>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <?php echo $model->perMonthCountHtml();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>


<?php
$js="
    $('.click-th').click(function(){
        var contNum = 1;
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
            $('#perMonthCount>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = $(this).data('width')+'px';
                $(this).width(width);
            });
            $('#perMonthCount>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).children('span').text($(this).data('text'));
            });
            $('#perMonthCount>tbody>tr').each(function(){
                $(this).children().slice(startNum,endNum).each(function(){
                    $(this).children('span').text($(this).data('text'));
                });
            });
        }else{
            $(this).data('text',$(this).text());
            $(this).children('span').text('.');
            $(this).addClass('active');
            $('#perMonthCount>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = '15px';
                $(this).width(width);
            });
            $('#perMonthCount>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).data('text',$(this).text());
                $(this).children('span').text('');
            });
            $('#perMonthCount>tbody>tr').each(function(){
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


