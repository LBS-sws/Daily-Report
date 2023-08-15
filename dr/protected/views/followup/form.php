<?php
$this->pageTitle=Yii::app()->name . ' - Complaint Case Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'followup-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    option:disabled{ background: #F1F1F1;cursor: not-allowed;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('followup','Complaint Case Form'); ?></strong>
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
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('followup/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('followup/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('followup/save'))); 
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

			<div class="form-group">
				<?php echo $form->labelEx($model,'entry_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'entry_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'type',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php 
						echo $form->dropDownList($model, 'type', General::getServiceTypeList(true), array('disabled'=>($model->scenario=='view')));
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->hiddenField($model, 'company_id'); ?>
					<?php echo $form->textField($model, 'company_name', 
						array('size'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'),
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('followup','Customer'),array('name'=>'btnCompany','id'=>'btnCompany','disabled'=>($model->scenario=='view')))
					)); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'content',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'content', 
						array('rows'=>3,'cols'=>50,'maxlength'=>5000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
	
			<div class="form-group">
				<?php echo $form->labelEx($model,'cont_info',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'cont_info', 
						array('size'=>50,'maxlength'=>500,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'resp_staff',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'resp_staff', 
						array('size'=>30,'maxlength'=>500,'readonly'=>($model->scenario=='view'),
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('followup','Sales'),array('name'=>'btnStaffResp','id'=>'btnStaffResp','disabled'=>($model->scenario=='view')))
					)); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'resp_tech',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'resp_tech', 
						array('size'=>30,'maxlength'=>500,'readonly'=>($model->scenario=='view'),
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('followup','Technician'),array('name'=>'btnStaffTech','id'=>'btnStaffTech','disabled'=>($model->scenario=='view')))
					)); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'mgr_notify',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->dropDownList($model, 'mgr_notify', array(''=>Yii::t('misc','No'),'Y'=>Yii::t('misc','Yes')),
								array('disabled'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'sch_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'sch_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'follow_staff',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-8">
                    <?php echo $form->textField($model, 'follow_staff',
                        array('size'=>30,'rows'=>3,'readonly'=>($model->scenario=='view'),
                            'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('followup','Technician'),array('name'=>'btnStaffFollow','id'=>'btnStaffFollow','disabled'=>($model->scenario=='view'))
                            )
                        )
                    );
                    ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'leader',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->dropDownList($model, 'leader', array(''=>Yii::t('misc','No'),'Y'=>Yii::t('misc','Yes')),
								array('disabled'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'follow_tech',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'follow_tech', 
						array('size'=>30,'maxlength'=>500,'readonly'=>($model->scenario=='view'),
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('followup','Technician'),array('name'=>'btnStaffFollowTech','id'=>'btnStaffFollowTech','disabled'=>($model->scenario=='view')))
					)); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'fin_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'fin_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'follow_action',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'follow_action', 
						array('rows'=>2,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'mgr_talk',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->dropDownList($model, 'mgr_talk', array(''=>Yii::t('misc','No'),'Y'=>Yii::t('misc','Yes')),
								array('disabled'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'change',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'change', 
						array('size'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'tech_notify',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'tech_notify', 
						array('size'=>30,'maxlength'=>500,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'mcard_remarks',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'mcard_remarks', 
						array('size'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'mcard_staff',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'mcard_staff', 
						array('size'=>30,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

		<legend><?php echo Yii::t('followup','Follow Up After Complaint'); ?></legend>

			<div class="form-group">
				<?php echo $form->labelEx($model,'fp_fin_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'fp_fin_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
				<?php echo $form->labelEx($model,'fp_cust_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textField($model, 'fp_cust_name', 
						array('size'=>30,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'fp_call_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'fp_call_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
				<?php echo $form->labelEx($model,'fp_comment',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textField($model, 'fp_comment', 
						array('size'=>30,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>


		<legend><?php echo Yii::t('followup','Follow Up After Service'); ?></legend>

			<div class="form-group">
				<?php echo $form->labelEx($model,'svc_next_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'svc_next_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
				<?php echo $form->labelEx($model,'svc_cust_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textField($model, 'svc_cust_name', 
						array('size'=>30,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'svc_call_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'svc_call_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
				<?php echo $form->labelEx($model,'svc_comment',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textField($model, 'svc_comment', 
						array('size'=>30,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('//site/lookup'); ?>
<input disabled>

<?php
$js = Script::genLookupSearchEx();
$js.="
$('#lstlookup').change(function(){
    var maxCount = $(this).attr('maxCount');
    if(maxCount>1){
        if($(this).find('option:selected').length==maxCount){
            $(this).find('option').not('option:selected').prop('disabled',true);
        }else{
            $(this).find('option').prop('disabled',false);
        }
    }
});
";
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnCompany', 'company', 'company_id', 'company_name');
Yii::app()->clientScript->registerScript('lookupCompany',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnStaffResp', 'staff', '', 'resp_staff');
Yii::app()->clientScript->registerScript('lookupStaffREsp',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnStaffTech', 'staff', '', 'resp_tech');
Yii::app()->clientScript->registerScript('lookupStaffTech',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnStaffFollow', 'staff', '', 'follow_staff',
    array(),
    true,
    array("maxCount"=>5)
);
Yii::app()->clientScript->registerScript('lookupStaffFollow',$js,CClientScript::POS_READY);

$js = Script::genLookupButton('btnStaffFollowTech', 'staff', '', 'follow_tech');
Yii::app()->clientScript->registerScript('lookupStaffFollowTech',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('followup/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
	$js = Script::genDatePicker(array(
			'FollowupForm_entry_dt',
			'FollowupForm_sch_dt',
			'FollowupForm_fin_dt',
			'FollowupForm_fp_fin_dt',
			'FollowupForm_fp_call_dt',
			'FollowupForm_svc_next_dt',
			'FollowupForm_svc_call_dt',
		));
	Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

