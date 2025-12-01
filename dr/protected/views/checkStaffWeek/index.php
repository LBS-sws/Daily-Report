<?php
$this->pageTitle=Yii::app()->name . ' - CheckStaffWeek Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'CheckStaffWeek-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Check Week Staff'); ?></strong>
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
            'submit'=>Yii::app()->createUrl('checkStaffWeek/view')));
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
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-4">
                            <?php
                            echo $form->textArea($model, 'city_desc',
                                array('rows'=>2,'cols'=>80,'maxlength'=>1000,'readonly'=>true)
                            );
                            ?>
                        </div>
                        <div class="col-sm-2">
                            <?php
                            echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('dialog','Select'),
                                array('name'=>'btnCity','id'=>'btnCity',)
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
                                array('readonly'=>false,'min'=>0)
                            ); ?>
                        </div>
                        <div class="pull-left text-center">
                            <p class="form-control-static"> 至 </p>
                        </div>
                        <div class="col-sm-2">
                            <?php echo $form->numberField($model, 'seniority_max',
                                array('readonly'=>false,'min'=>0)
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'staff_type',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-10">
                            <?php echo $form->inlineRadioButtonList($model, 'staff_type',UServiceForm::getStaffType(),
                                array('readonly'=>false,'id'=>'staff_type')
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--查询說明-->
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/lookup'); ?>

<?php
$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonText('btnCity', 'citySearch', 'city', 'city_desc',
    array(),
    true
);
Yii::app()->clientScript->registerScript('lookupCity',$js,CClientScript::POS_READY);

$js = Script::genLookupSelectText();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);


$language = Yii::app()->language;
$js="
$('#condition').select2({
	tags: false,
	multiple: true,
	maximumInputLength: 0,
	maximumSelectionLength: 10,
	allowClear: true,
	language: '$language',
	disabled: false,
	templateSelection: formatState
});
function formatState(state) {
	var rtn = $('<span style=\"color:black\">'+state.text+'</span>');
	return rtn;
}
    $('#yt0').click(function(){
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


