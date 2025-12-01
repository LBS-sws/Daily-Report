<?php
$this->pageTitle=Yii::app()->name . ' - CheckInWeek Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'CheckInWeek-form',
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
        <strong><?php echo Yii::t('app','Check In Week'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('checkInWeek/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('checkInWeek/downExcel')));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-5">
                        <div id="search_div">
                            <div data-id="3">
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'start_date',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->textField($model, 'start_date',
                                            array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo $form->labelEx($model,'end_date',array('class'=>"col-lg-5 control-label")); ?>
                                    <div class="col-lg-5">
                                        <?php echo $form->textField($model, 'end_date',
                                            array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">
                                    <?php echo Yii::t('app','Check In Week'); ?>
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

<!--功能說明-->

<?php
$js="
    $('.click-th').click(function(){
        var startNum=0;
        var thStartNum=0;
        var endNum = $(this).attr('colspan');
        var thEndNum=endNum;
        $(this).prevAll('th').each(function(){
            var colspan = $(this).attr('colspan');
            var rowspan = $(this).attr('rowspan');
            colspan = parseInt(colspan,10);
            startNum += colspan;
            thStartNum += colspan;
            if(rowspan!=undefined&&rowspan>1){
                thStartNum--;
            }
        });
        endNum = parseInt(endNum,10)+startNum;
        thEndNum = parseInt(thEndNum,10)+thStartNum;
        if($(this).hasClass('active')){
            $(this).children('span').text($(this).data('text'));
            $(this).removeClass('active');
            $('#comparison>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = $(this).data('width')+'px';
                $(this).width(width);
            });
            $('#comparison>thead>tr').eq(2).children().slice(thStartNum,thEndNum).each(function(){
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
            $('#comparison>thead>tr').eq(2).children().slice(thStartNum,thEndNum).each(function(){
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
    
    $('td.changeOffice').on('click',function(){
        var city = $(this).parent('tr').eq(0).data('city');
        console.log(city);
        console.log($('tr.office-city-tr[data-city=\"'+city+'\"]').length);
        if($(this).find('i:first').hasClass('fa-plus')){ //展开
            $(this).find('i:first').removeClass('fa-plus').addClass('fa-minus');
            $('tr.office-city-tr[data-city=\"'+city+'\"]').slideDown(100);
        }else if($(this).find('i:first').hasClass('fa-minus')){ //收缩
            $(this).find('i:first').removeClass('fa-minus').addClass('fa-plus');
            $('tr.office-city-tr[data-city=\"'+city+'\"]').slideUp(100);
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


