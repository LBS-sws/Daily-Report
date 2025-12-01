<?php
$this->pageTitle=Yii::app()->name . ' - QC Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'qc-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('qc','QC Form'); ?></strong>
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
					'name'=>'btnAdd','id'=>'btnAdd','data-toggle'=>'modal','data-target'=>'#addrecdialog',)
				); 
//				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
//					'id'=>'btnAddNew'));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('qc/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php if($model->readonly()){} else{echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('qc/save'))); }
			?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
            <?php if($model->readonly()&&Yii::app()->user->validFunction('CN03')==false){}else{
                echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Remove'), array(
                    'submit'=>Yii::app()->createUrl('qc/remove')));
                echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );}
            ?>
<?php endif ?>
	</div>
	<div class="btn-group pull-right" role="group">
        <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','xiazai'), array(
            'data-href'=>Yii::app()->createUrl('qc/downs'),'id'=>'xiazai'));
        ?>
<?php 
		$counter = ($model->no_of_attm['qc'] > 0) ? ' <span id="docqc" class="label label-info">'.$model->no_of_attm['qc'].'</span>' : ' <span id="docqc"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
			'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadqc',)
		);
?>
<?php 
		$counter = ($model->no_of_attm['qcphoto'] > 0) ? ' <span id="docqcphoto" class="label label-info">'.$model->no_of_attm['qcphoto'].'</span>' : ' <span id="docqcphoto"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('qc','Photo with Cust.').$counter, array(
			'name'=>'btnFileQP','id'=>'btnFileQP','data-toggle'=>'modal','data-target'=>'#fileuploadqcphoto',)
		);
?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'service_type'); ?>
			<?php echo $form->hiddenField($model, 'entry_dt'); ?>
			<?php echo $form->hiddenField($model, 'team'); ?>
			<?php echo $form->hiddenField($model, 'month'); ?>
			<?php echo $form->hiddenField($model, 'new_form'); ?>
            <?php echo $form->hiddenField($model, 'lcu'); ?>
            <?php echo $form->hiddenField($model, 'luu'); ?>
            <?php echo $form->hiddenField($model, 'lcd'); ?>
            <?php echo $form->hiddenField($model, 'lud'); ?>
			<?php echo TbHtml::hiddenField('QcForm[info][sign_cust]', $model->info['sign_cust']); ?>
			<?php echo TbHtml::hiddenField('QcForm[info][sign_tech]', $model->info['sign_tech']); ?>
			<?php echo TbHtml::hiddenField('QcForm[info][sign_qc]', $model->info['sign_qc']); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
                    echo $form->dropDownList($model,'city',QcForm::getCityList(),array('readonly'=>$model->readonly()||$model->ltNowDate));

					?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'qc_staff',array('class'=>"col-sm-2 control-label")); ?>

				<div class="col-sm-7">
					<?php
						echo $form->textField($model, 'qc_staff',
							array('size'=>50,'maxlength'=>500,'readonly'=>true,
							'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('qc','QC Staff'),
											array('name'=>'btnStaffQc','id'=>'btnStaffQc',
												'disabled'=>($model->readonly()||$model->ltNowDate)
											))
						));
					?>
				</div>
			</div>


			<div class="form-group">
				<?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->hiddenField($model, 'company_id'); ?>
					<?php echo $form->textField($model, 'company_name', 
						array('maxlength'=>500,'readonly'=>'readonly',
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('qc','Customer'),array('name'=>'btnCompany','id'=>'btnCompany','disabled'=>($model->readonly()||$model->ltNowDate)))
					)); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'job_staff',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'job_staff',
						array('maxlength'=>500,'readonly'=>true,
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('qc','Resp. Staff'),array('name'=>'btnStaffResp','id'=>'btnStaffResp','disabled'=>($model->readonly()||$model->ltNowDate)))
					)); ?>
				</div>
			</div>
			
			<div class="form-group">
				<?php echo $form->labelEx($model,'service_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php 
							echo TbHtml::textField('QcForm[info][service_dt]',$model->info['service_dt'], 
								array('class'=>'form-control pull-right','readonly'=>($model->readonly()||$model->ltNowDate),));
						?>
					</div>
				</div>

				<?php echo $form->labelEx($model,'qc_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'qc_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->readonly()||$model->ltNowDate),));
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php 
					echo $form->labelEx($model,'env_grade',
						array('class'=>"col-sm-2 control-label",
							'data-toggle'=>'tooltip',
							'data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('env_grade'),
						)
					); 
				?>
				<div class="col-sm-3">
					<?php 
						$listenv = array(
										'A'=>'A-'.Yii::t('qc','Normal'),
										'B'=>'B-'.Yii::t('qc','Poor'),
										'C'=>'C-'.Yii::t('qc','Worst')
									);
						echo $form->dropDownList($model, 'env_grade', $listenv,array('disabled'=>($model->readonly()))) 
					?>
				</div>
			</div>
			
			<div class="form-group">
				<?php echo $form->labelEx($model,'score_machine',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_machine'),)); ?>
				<div class="col-sm-1">
					<?php 
//						echo TbHtml::dropDownList('QcForm[info][score_fan]', $model->info['score_fan'], General::getServiceTypeList(true),array('disabled'=>($model->scenario=='view')))
						echo TbHtml::numberField('QcForm[info][score_machine]', $model->info['score_machine'], 
							array('min'=>0,'max'=>14,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_sink',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_sink'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_sink]', $model->info['score_sink'], 
							array('min'=>0,'max'=>6,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_toilet',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_toilet'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_toilet]', $model->info['score_toilet'], 
							array('min'=>0,'max'=>50,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_sticker',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_sticker'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_sticker]', $model->info['score_sticker'], 
							array('min'=>0,'max'=>10,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'sticker_clno',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
<!--					--><?php //
//						$listtype = array('1'=>Yii::t('qc','Missing'), '2'=>Yii::t('qc','Broken'));
//						echo TbHtml::dropDownList('QcForm[info][sticker_cltype]',$model->info['sticker_cltype'], $listtype,array('disabled'=>($model->readonly())));
//					?>
                    <?php
                    $listtype = array('1'=>Yii::t('qc','Missing'), '2'=>Yii::t('qc','Broken'));
                    if ($model->readonly()||$model->ltNowDate) {
                        echo TbHtml::hiddenField('QcForm[info][sticker_cltype]',$model->info['sticker_cltype'], array('id'=>'QcForm_info_sticker_cltype'));
                        echo TbHtml::textField('QcForm[info][sticker_cltype]', $listtype[$model->info['sticker_cltype']], array('readonly'=>true,'id'=>'QcForm_info_sticker_cltype'));
                    } else {
                        echo TbHtml::dropDownList('QcForm[info][sticker_cltype]',$model->info['sticker_cltype'], $listtype,array('disabled'=>($model->readonly()||$model->ltNowDate)));
                    }
                    ?>
				</div>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][sticker_clno]', $model->info['sticker_clno'], 
							array('min'=>0,'max'=>999,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'sticker_mano',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php
                    if ($model->readonly()||$model->ltNowDate) {
                        echo TbHtml::hiddenField('QcForm[info][sticker_matype]',$model->info['sticker_matype'], array('id'=>'QcForm_info_sticker_matype'));
                        echo TbHtml::textField('QcForm[info][sticker_matype]', $listtype[$model->info['sticker_matype']], array('readonly'=>true,'id'=>'QcForm_info_sticker_matype'));
                    } else {
                        echo TbHtml::dropDownList('QcForm[info][sticker_matype]',$model->info['sticker_matype'], $listtype,array('disabled'=>($model->readonly()||$model->ltNowDate)));
                    }

					?>
				</div>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][sticker_mano]', $model->info['sticker_mano'], 
							array('min'=>0,'max'=>999,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'sticker_bgno',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php
                    if ($model->readonly()||$model->ltNowDate) {
                        echo TbHtml::hiddenField('QcForm[info][sticker_bgtype]',$model->info['sticker_bgtype'], array('id'=>'QcForm_info_sticker_bgtype'));
                        echo TbHtml::textField('QcForm[info][sticker_bgtype]', $listtype[$model->info['sticker_bgtype']], array('readonly'=>true,'id'=>'QcForm_info_sticker_bgtype'));
                    } else {
                        echo TbHtml::dropDownList('QcForm[info][sticker_bgtype]',$model->info['sticker_bgtype'], $listtype,array('disabled'=>($model->readonly())));
                    }
					?>
				</div>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][sticker_bgno]', $model->info['sticker_bgno'], 
							array('min'=>0,'max'=>999,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo TbHtml::label(Yii::t('qc','According to customer request on hand-washing/flushing sticker'),false,array('class'=>'col-sm-4',)); ?>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'sticker_reqno',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'title'=>$model->getDescription('sticker_reqno'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][sticker_reqno]', $model->info['sticker_reqno'], 
							array('min'=>0,'max'=>999,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
				<?php echo $form->labelEx($model,'sticker_actno',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'title'=>$model->getDescription('sticker_actno'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][sticker_actno]', $model->info['sticker_actno'], 
							array('min'=>0,'max'=>999,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_enzyme',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'title'=>$model->getDescription('score_enzyme'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_enzyme]', $model->info['score_enzyme'], 
							array('min'=>0,'max'=>5,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
				<?php echo $form->labelEx($model,'score_bluecard',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'title'=>$model->getDescription('score_bluecard'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_bluecard]', $model->info['score_bluecard'], 
							array('min'=>0,'max'=>5,'readonly'=>($model->readonly()||$model->ltNowDate),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'cust_score1',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-1">
					<?php echo $form->numberField($model, 'cust_score', 
						array('min'=>0,'max'=>10,'readonly'=>($model->readonly()||$model->ltNowDate))
					); ?>
				</div>

				<?php echo $form->labelEx($model,'service_score',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-1">
					<?php echo $form->numberField($model, 'service_score', 
						array('readonly'=>true)
					); ?>
				</div>

				<?php echo $form->labelEx($model,'qc_result',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-1">
					<?php echo $form->numberField($model, 'qc_result', 
						array('readonly'=>true)
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'cust_comment',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'cust_comment', 
						array('rows'=>3,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->readonly()))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'qc_comment',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'remarks', 
						array('rows'=>3,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->readonly()))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'improve',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo TbHtml::textArea('QcForm[info][improve]',$model->info['improve'],
						array('rows'=>3,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->readonly()))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'praise',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo TbHtml::textArea('QcForm[info][praise]',$model->info['praise'],
						array('rows'=>3,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->readonly()))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'signature',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-8">


					<div class="col-sm-7">
<?php if (empty($model->info['sign_cust']) && $model->scenario!='view'): ?>
					<?php 
						echo TbHtml::button(Yii::t('qc','Customer Signature'), array('name'=>'btnSignCust','id'=>'btnSignCust',));
						echo TbHtml::image($model->info['sign_cust'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>200,'height'=>100,'style'=>'display:none')); 
					?>
<?php else: ?>
					<?php 
						echo $form->labelEx($model,'sign_cust');
						echo TbHtml::image($model->info['sign_cust'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>200,'height'=>100,)); 
					?>
<?php endif ?>
					</div>




<!--					<div class="col-sm-7">-->
<?php //if (empty($model->info['sign_tech']) && $model->scenario!='view'): ?>
<!--					--><?php //
//						echo TbHtml::button(Yii::t('qc','Technician Signature'), array('name'=>'btnSignTech','id'=>'btnSignTech',));
//						echo TbHtml::image($model->info['sign_tech'],'QcForm_info_sign_tech_img',array('id'=>'QcForm_info_sign_tech_img','width'=>200,'height'=>100,'style'=>'display:none'));
//					?>
<?php //else: ?>
<!--					--><?php //
//						echo $form->labelEx($model,'sign_tech');
//						echo TbHtml::image($model->info['sign_tech'],'QcForm_info_sign_tech_img',array('id'=>'QcForm_info_sign_tech_img','width'=>200,'height'=>100,));
//					?>
<?php //endif ?>
<!--					</div>-->



					<div class="col-sm-7">
<?php if (empty($model->info['sign_qc']) && $model->scenario!='view'): ?>
					<?php
						echo TbHtml::button(Yii::t('qc','QC Signature'), array('name'=>'btnSignQc','id'=>'btnSignQc',));
						echo TbHtml::image($model->info['sign_qc'],'QcForm_info_sign_qc_img',array('id'=>'QcForm_info_sign_qc_img','width'=>200,'height'=>100,'style'=>'display:none'));
					?>
<?php else: ?>
					<?php
						echo $form->labelEx($model,'sign_qc');
						echo TbHtml::image($model->info['sign_qc'],'QcForm_info_sign_qc_img',array('id'=>'QcForm_info_sign_qc_img','width'=>200,'height'=>100,));
					?>
<?php endif ?>
					</div>


				</div>
			</div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('//site/lookup2'); ?>
<?php //$this->renderPartial('//site/fileupload',array('model'=>$model,'form'=>$form)); ?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'QC',
													'header'=>Yii::t('dialog','File Attachment'),
													'ronly'=>(""),
                                                    'nodelete'=>$model->readonlys(),
													)); 
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'QCPHOTO',
													'header'=>Yii::t('qc','Photo Attachment'),
													'ronly'=>(""),
                                                    'nodelete'=>$model->readonlys(),
													)); 
?>
<?php $this->renderPartial('//qc/_type',array('model'=>$model)); ?>
<?php $this->renderPartial('//qc/_sign'); ?>
<?php $this->renderPartial('//qc/_type',array('model'=>$model)); ?>

<?php
$baseUrl = Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/signature_pad.min.js',CClientScript::POS_HEAD);

Script::genFileUpload($model,$form->id,'QC');
Script::genFileUpload($model,$form->id,'QCPHOTO');

//Script::genFileUpload(get_class($model),$form->id, 'qc');

Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/dms-lookup.js', CClientScript::POS_HEAD);

$js = <<<EOF
$('#btnStaffResp').on('click',function() {
	opendialog('staff', '', 'job_staff', false, {}, { city:$('#QcForm_city').val()});
});

$('#btnCompany').on('click',function() {
	opendialog('company', 'company_id', 'company_name', false, {}, { city:$('#QcForm_city').val()});
});

$('#btnStaffQc').on('click',function() {
	opendialog('staff', '', 'qc_staff', false, {}, { city:$('#QcForm_city').val()});
});


EOF;
Yii::app()->clientScript->registerScript('lookup',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('qc/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if (!$model->readonly()) {
	$js = Script::genDatePicker(array(
			'QcForm_entry_dt',
			'QcForm_qc_dt',
			'QcForm_info_service_dt',
		));
	Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = <<<EOF
$('#btnSignCust').on('click',function(){
	$('#sign_target_field').val('QcForm_info_sign_cust');
	$('#signdialog').modal('show');
});

$('#btnSignTech').on('click',function(){
	$('#sign_target_field').val('QcForm_info_sign_tech');
	$('#signdialog').modal('show');
});

$('#btnSignQc').on('click',function(){
	$('#sign_target_field').val('QcForm_info_sign_qc');
	$('#signdialog').modal('show');
});
EOF;
Yii::app()->clientScript->registerScript('signature',$js,CClientScript::POS_READY);

$js = <<<EOF
$('.popover-dismiss').popover({
  trigger: 'focus'
});
EOF;
Yii::app()->clientScript->registerScript('popover',$js,CClientScript::POS_READY);

$js = <<<EOF
$('#QcForm_cust_score, #QcForm_info_score_machine, #QcForm_info_score_sink, #QcForm_info_score_toilet, #QcForm_info_score_sticker, #QcForm_info_score_enzyme, #QcForm_info_score_bluecard').focusout(function() {
	$('#QcForm_info_score_machine').val(parseInt(+$('#QcForm_info_score_machine').val() || 0));
	$('#QcForm_info_score_sink').val(parseInt(+$('#QcForm_info_score_sink').val() || 0));
	$('#QcForm_info_score_toilet').val(parseInt(+$('#QcForm_info_score_toilet').val() || 0));
	$('#QcForm_info_score_sticker').val(parseInt(+$('#QcForm_info_score_sticker').val() || 0));
	$('#QcForm_info_score_enzyme').val(parseInt(+$('#QcForm_info_score_enzyme').val() || 0));
	$('#QcForm_info_score_bluecard').val(parseInt(+$('#QcForm_info_score_bluecard').val() || 0));
	$('#QcForm_cust_score').val(parseInt(+$('#QcForm_cust_score').val() || 0));
	var svcscore = parseInt(document.getElementById('QcForm_info_score_machine').value)
		+ parseInt(document.getElementById('QcForm_info_score_sink').value)
		+ parseInt(document.getElementById('QcForm_info_score_toilet').value)
		+ parseInt(document.getElementById('QcForm_info_score_sticker').value)
		+ parseInt(document.getElementById('QcForm_info_score_enzyme').value)
		+ parseInt(document.getElementById('QcForm_info_score_bluecard').value);
	var total = parseInt(document.getElementById('QcForm_cust_score').value) + svcscore;
	$('#QcForm_service_score').val(svcscore);
	$('#QcForm_qc_result').val(total);
});

$('#yt1').on('click',function(){
document.getElementById('yt1').removeAttribute("style");
});

	$('#xiazai').on('click',function(){
	    var href = $(this).data('href');
	    $('#qc-form').attr('action',href).submit();
	});

EOF;
Yii::app()->clientScript->registerScript('calculate',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

