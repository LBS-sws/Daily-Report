<?php
$this->pageTitle=Yii::app()->name . ' - Report';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'report-form',
'action'=>Yii::app()->createUrl('report/generate'),
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('report',$model->name); ?></strong>
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
		<?php echo TbHtml::button(Yii::t('misc','Submit'), array(
				'submit'=>Yii::app()->createUrl('report/generate'))); 
		?>
	</div>
	<div class="btn-group pull-right" role="group">
        <?php echo TbHtml::button(Yii::t('misc','Save'), array(
            'submit'=>Yii::app()->createUrl('report/allsave')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'name'); ?>
			<?php echo $form->hiddenField($model, 'fields'); ?>
			<?php echo $form->hiddenField($model, 'target_dt'); ?>
			<?php echo $form->hiddenField($model, 'email'); ?>
			<?php echo $form->hiddenField($model, 'emailcc'); ?>
			<?php echo $form->hiddenField($model, 'form'); ?>

		<?php if ($model->showField('city') && !Yii::app()->user->isSingleCity()): ?>
            <div class="form-group">
                <?php
                echo TbHtml::label(Yii::t("user","Fast City"),"",array('class'=>"col-sm-2 control-label"));
                ?>
                <div class="col-sm-10">
                    <?php
                    echo TbHtml::checkBox("0",false,array('label'=>"全部","class"=>"fastChange",'data-city'=>"",'labelOptions'=>array("class"=>"checkbox-inline")));
                    $fastCityList = UserForm::getCityListForArea();
                    foreach ($fastCityList as $row){
                        echo TbHtml::checkBox($row["code"],false,array('label'=>$row["name"],"class"=>"fastChange",'data-city'=>$row["city"],'labelOptions'=>array("class"=>"checkbox-inline")));
                    }
                    ?>
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
                        array('id'=>'look_city'));
                    ?>
                </div>
            </div>
		<?php else: ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
		<?php endif ?>
		
			<div class="form-group">
				<?php echo $form->labelEx($model,'start_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'start_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'end_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'end_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'format',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php 
						$item = array('EXCEL'=>'Excel');
						if (Yii::app()->user->isSingleCity()) $item['FEED']=Yii::t('report','For Feedback');
						echo $form->dropDownList($model, 'format', 
							$item, array('disabled'=>($model->scenario=='view'))
						); 
					?>
				</div>
			</div>
			
			<div id="feedback_div" style="display: none">
				<div class="form-group">
					<?php echo $form->labelEx($model,'touser',array('class'=>"col-sm-2 control-label")); ?>
					<div class="col-sm-5">
						<?php echo $form->dropDownList($model, 'touser', 
							General::getMgrFeedbackList(), array('disabled'=>($model->scenario=='view'))
						); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'ccuser',array('class'=>"col-sm-2 control-label")); ?>
					<div class="col-sm-5">
						<?php 
							echo $form->listbox($model, 'ccuser', General::getEmailListboxData(),
								array('size'=>6,'multiple'=>'multiple')
							); 
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
$js = "
showEmailField();

$('#ReportForm_format').on('change',function() {
	showEmailField();
});

function showEmailField() {
	$('#feedback_div').css('display','none');
	if ($('#ReportForm_format').val()=='FEED') $('#feedback_div').css('display','');
}
";
Yii::app()->clientScript->registerScript('changestyle',$js,CClientScript::POS_READY);

$js = Script::genDatePicker(array(
			'ReportForm_start_dt',
			'ReportForm_end_dt',
		));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
?>

<?php
$js="
$('.fastChange').change(function(){
    var cityStr = ','+$(this).data('city')+',';
    console.log(cityStr);
    var checkBool = $(this).is(':checked')?true:false;
    $('#report_look_city').find('input[type=\"checkbox\"]').each(function(){
        var city = ','+$(this).val()+',';
        if(cityStr==',,'||cityStr.indexOf(city)>-1){
            $(this).prop('checked',checkBool);
        }
    });
});
";
Yii::app()->clientScript->registerScript('fastChange',$js,CClientScript::POS_READY);
?>
<?php $this->endWidget(); ?>

</div><!-- form -->

