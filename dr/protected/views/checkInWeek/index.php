<?php
$this->pageTitle=Yii::app()->name . ' - CheckInWeek Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'CheckInWeek-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Check In Week'); ?></strong>
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
        <?php echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('summary','Enquiry'), array(
            'submit'=>Yii::app()->createUrl('checkInWeek/view')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
            <div id="search_div">
                <div data-id="3">
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'start_date',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-lg-2">
                            <?php
                            echo $form->textField($model,"start_date",array(
                                "id"=>"start_date",
                                "readonly"=>true,
                                'prepend'=>"<span class='fa fa-calendar'></span>"
                            ));
                             ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'end_date',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-lg-2">
                            <?php
                            echo $form->textField($model,"end_date",array(
                                "id"=>"end_date",
                                "readonly"=>true,
                                'prepend'=>"<span class='fa fa-calendar'></span>"
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--查询說明-->
		</div>
	</div>
</section>


<?php

$language = Yii::app()->language;
$js="
    $('button').click(function(){
        Loading.show();
    });
    var datePicker = $('#start_date,#end_date').datepicker({
        'language':'{$language}',
        'format':'yyyy/mm/dd',
        'weekStart':1
    });
    $('#start_date,#end_date').on('changeDate',function(e){
        var trActive = $('.datepicker td.day.active').eq(0).parent('tr');
        var startDay = trActive.find('td').eq(0).data('date');
        startDay = new Date(startDay);
        $('#start_date').datepicker('update',startDay);
        var endDay = trActive.find('td').eq(-1).data('date');
        endDay = new Date(endDay);
        $('#end_date').datepicker('update',endDay);
        $('.datepicker').find('td').removeClass('in-range');
        trActive.find('td').removeClass('active').addClass('in-range');
        trActive.find('td').eq(0).addClass('active');
        trActive.find('td').eq(-1).addClass('active');
    }).on('show change',function(){
        $('.datepicker').addClass('daterangepicker');
        var trActive = $('.datepicker td.day.active').eq(0).parent('tr');
        trActive.find('td').removeClass('active').addClass('in-range');
        trActive.find('td').eq(0).addClass('active');
        trActive.find('td').eq(-1).addClass('active');
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


