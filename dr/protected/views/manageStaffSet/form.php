<?php
$this->pageTitle=Yii::app()->name . ' - Customer Type Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'code-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
    .select2-container .select2-selection--single{ height: 34px;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Management Staff Setting'); ?></strong>
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
        <?php echo CHtml::hiddenField('dtltemplate'); ?>
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('manageStaffSet/new')));
                echo TbHtml::button('<span class="fa fa-copy"></span> '.Yii::t('misc','Copy'), array(
                    'submit'=>Yii::app()->createUrl('manageStaffSet/new',array("index"=>$model->id))));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('manageStaffSet/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('manageStaffSet/save'))); 
			?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city',array("id"=>"city")); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'start_date',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'start_date',
						array('id'=>'start_date','readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php
                    echo $form->textField($model, 'employee_name',
                        array('readonly'=>true,'class'=>'employeeName',
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('summary','Employee Name'),array('class'=>'searchUser','disabled'=>($model->scenario=='view'))),
                        ));
                    ?>
                    <?php echo $form->hiddenField($model, 'employee_id',array("class"=>"employeeID")); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city_allow',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-10">
                    <?php
                    $allCityList = UserForm::getCityListForCity();
                    echo $form->inlineCheckBoxList($model, 'city_allow', $allCityList,
                        array('disabled'=>($model->scenario=='view'),'class'=>'city_allow'));
                    ?>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'job_key',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
                    $jobList = ManageStaffSetForm::getJobList();
                    echo $form->dropDownList($model, 'job_key',$jobList["list"],
						array('id'=>'job_key','options'=>$jobList["options"],'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'condition_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'condition_type',array("1"=>"城市合计月新签金额不低于以下目标"),
                        array('id'=>'condition_type','readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'condition_money',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'condition_money',
                        array('id'=>'condition_money','min'=>0,'readonly'=>($model->scenario=='view'),'append'=>'<span>元</span>')
                    ); ?>
                </div>
            </div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'team_rate',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'team_rate',
						array('id'=>'team_rate','min'=>0,'max'=>100,'readonly'=>($model->scenario=='view'),'append'=>'<span>%</span>')
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'person_type',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->dropDownList($model, 'person_type',array("1"=>"固定金额","2"=>"销售提成"),
						array('id'=>'person_type','readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'person_money',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'person_money',
						array('id'=>'person_money','min'=>0,'readonly'=>($model->scenario=='view'),'append'=>'<span>元</span>')
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'max_bonus',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'max_bonus',
						array('id'=>'max_bonus','min'=>0,'readonly'=>($model->scenario=='view'),'append'=>'<span>元</span>')
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'z_index',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'z_index',
						array('readonly'=>($model->scenario=='view'),'min'=>0)
					); ?>
				</div>
                <div class="col-sm-8">
                    <p class="form-control-static">数值越大，显示顺序越靠前</p>
                </div>
			</div>
        </div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('//site/lookup'); ?>
<?php
$js="
$('#job_key').on('change',function(){
    var optionSelect = $(this).children('option:selected');
    $('#team_rate').val(optionSelect.data('team_rate'));
    $('#person_type').val(optionSelect.data('person_type')).trigger('change');
    $('#condition_money').val(optionSelect.data('condition_money'));
    $('#max_bonus').val(optionSelect.data('max_bonus'));
});

$('#person_type').on('change',function(){
    if($(this).val()==1){
        $('#person_money').val(0).prop('readonly',false);
    }else{
        $('#person_money').val('').prop('readonly',true);
    }
}).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


$js = Script::genDatePicker(array(
    'start_date',
));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);

/*
switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = ($model->scenario!='view') ? 'false' : 'true';
$js="
$('#city').select2({
    multiple: false,
    maximumInputLength: 10,
    language: '$lang',
    disabled: $disabled
});
function formatState(state) {
	var rtn = $('<span style=\"color:black\">'+state.text+'</span>');
	return rtn;
}
";
Yii::app()->clientScript->registerScript('searchCityInput',$js,CClientScript::POS_READY);
*/
$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$multiflag = 'false';
$js = <<<EOF
$('body').on('click','.searchUser',function() {
	var value = $(this).parents('.input-group:first').children('.employeeName').attr("id");
	var code = $(this).parents('.input-group:first').next('.employeeID').attr("id");
	var title = '员工查询';
	$('#lookuptype').val('employeeAll');
	$('#lookupcodefield').val(code);
	$('#lookupvaluefield').val(value);
	$('#lookupotherfield').val('city,city');
	$('#lookupparamfield').val('');
	if ($multiflag) $('#lstlookup').attr('multiple','multiple');
	if (!($multiflag)) $('#lookup-label').attr('style','display: none');
	$('#lookupdialog').find('.modal-title').text(title);
	$('#lookupdialog').modal('show');
	$(this).parents('.input-group:first').next('.employeeID').trigger('change');
});
$('#city').change(function(){
    var city = $(this).val();
    $('.city_allow').prop('checked',false);
    $('.city_allow[value="'+city+'"]').prop('checked',true);
});
EOF;
Yii::app()->clientScript->registerScript('lookupEmployee',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);
?>
<?php
$js = Script::genDeleteData(Yii::app()->createUrl('manageStaffSet/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

?>

<?php $this->endWidget(); ?>
