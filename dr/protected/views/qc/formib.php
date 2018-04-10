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
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('qc/save'))); 
			?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	<div class="btn-group pull-right" role="group">
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
			<?php echo $form->hiddenField($model, 'qc_staff'); ?>
			<?php echo $form->hiddenField($model, 'month'); ?>
			<?php echo $form->hiddenField($model, 'new_form'); ?>
			<?php echo TbHtml::hiddenField('QcForm[info][sign_cust]', $model->info['sign_cust']); ?>
			<?php echo TbHtml::hiddenField('QcForm[info][sign_tech]', $model->info['sign_tech']); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->hiddenField($model, 'company_id'); ?>
					<?php echo $form->textField($model, 'company_name', 
						array('maxlength'=>500,'readonly'=>($model->readonly()),
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('qc','Customer'),array('name'=>'btnCompany','id'=>'btnCompany','disabled'=>($model->readonly())))
					)); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'job_staff',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'job_staff', 
						array('maxlength'=>500,'readonly'=>($model->readonly()),
						'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('qc','Resp. Staff'),array('name'=>'btnStaffResp','id'=>'btnStaffResp','disabled'=>($model->readonly())))
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
								array('class'=>'form-control pull-right','readonly'=>($model->readonly()),)); 
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
							array('class'=>'form-control pull-right','readonly'=>($model->readonly()),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php 
					echo $form->labelEx($model,'env_grade',
						array('class'=>"col-sm-2 control-label",
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

			<legend></legend>
			<div class="form-group">
				<div class="col-sm-5">
					<h4>
						<b><?php echo Yii::t('qc','QC Score');?></b>
						<small><?php echo Yii::t('qc','(60% of Total Score)'); ?></small>
					</h4>
					
				</div>
				<div class="col-sm-2">
					<?php 
						echo TbHtml::numberField('QcForm[info][qc_score]', $model->info['qc_score'], 
							array('readonly'=>true)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo TbHtml::label(Yii::t('qc','Rat'),false,array('class'=>'col-sm-2',)); ?>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'freq_rat',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php 
						$listtype = array('H'=>Yii::t('qc','High'), 'M'=>Yii::t('qc','Medium'), 'L'=>Yii::t('qc','Low'));
						echo TbHtml::dropDownList('QcForm[info][freq_rat]',$model->info['freq_rat'], $listtype,array('disabled'=>($model->readonly()))); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_ratcheck',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_ratcheck'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_ratcheck]', $model->info['score_ratcheck'], 
							array('min'=>0,'max'=>$model->maxscore('score_ratcheck'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_ratdispose',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_ratdispose'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_ratdispose]', $model->info['score_ratdispose'], 
							array('min'=>0,'max'=>$model->maxscore('score_ratdispose'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_ratboard',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_ratboard'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_ratboard]', $model->info['score_ratboard'], 
							array('min'=>0,'max'=>$model->maxscore('score_ratboard'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_rathole',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_rathole'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_rathole]', $model->info['score_rathole'], 
							array('min'=>0,'max'=>$model->maxscore('score_rathole'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_ratwarn',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_ratwarn'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_ratwarn]', $model->info['score_ratwarn'], 
							array('min'=>0,'max'=>$model->maxscore('score_ratwarn'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_ratdrug',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_ratdrug'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_ratdrug]', $model->info['score_ratdrug'], 
							array('min'=>0,'max'=>$model->maxscore('score_ratdrug'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo TbHtml::label(Yii::t('qc','Cockroach'),false,array('class'=>'col-sm-2',)); ?>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'freq_roach',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php 
						$listtype = array('H'=>Yii::t('qc','High'), 'M'=>Yii::t('qc','Medium'), 'L'=>Yii::t('qc','Low'));
						echo TbHtml::dropDownList('QcForm[info][freq_roach]',$model->info['freq_roach'], $listtype,array('disabled'=>($model->readonly()))); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_roachcheck',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_roachcheck'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_roachcheck]', $model->info['score_roachcheck'], 
							array('min'=>0,'max'=>$model->maxscore('score_roachcheck'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_roachdrug',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_roachdrug'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_roachdrug]', $model->info['score_roachdrug'], 
							array('min'=>0,'max'=>$model->maxscore('score_roachdrug'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_roachexdrug',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_roachexdrug'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_roachexdrug]', $model->info['score_roachexdrug'], 
							array('min'=>0,'max'=>$model->maxscore('score_roachexdrug'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_roachtoxin',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_roachtoxin'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_roachtoxin]', $model->info['score_roachtoxin'], 
							array('min'=>0,'max'=>$model->maxscore('score_roachtoxin'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo TbHtml::label(Yii::t('qc','Fly'),false,array('class'=>'col-sm-2',)); ?>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'freq_fly',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php 
						$listtype = array('H'=>Yii::t('qc','High'), 'M'=>Yii::t('qc','Medium'), 'L'=>Yii::t('qc','Low'));
						echo TbHtml::dropDownList('QcForm[info][freq_fly]',$model->info['freq_fly'], $listtype,array('disabled'=>($model->readonly()))); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_flycup',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_flycup'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_flycup]', $model->info['score_flycup'], 
							array('min'=>0,'max'=>$model->maxscore('score_flycup'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_flylamp',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_flylamp'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_flylamp]', $model->info['score_flylamp'], 
							array('min'=>0,'max'=>$model->maxscore('score_flylamp'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_flycntl',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_flycntl'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_flycntl]', $model->info['score_flycntl'], 
							array('min'=>0,'max'=>$model->maxscore('score_flycntl'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_flyspray',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_flyspray'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_flyspray]', $model->info['score_flyspray'], 
							array('min'=>0,'max'=>$model->maxscore('score_flyspray'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<legend></legend>
			<div class="form-group">
				<div class="col-sm-5">
					<h4>
						<b><?php echo Yii::t('qc','Service Score');?></b>
						<small><?php echo Yii::t('qc','(40% of Total Score)'); ?></small>
					</h4>
					
				</div>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'service_score', 
						array('readonly'=>true)
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo TbHtml::label(Yii::t('qc','Personal Image'),false,array('class'=>'col-sm-2',)); ?>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_uniform',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_uniform'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_uniform]', $model->info['score_uniform'], 
							array('min'=>0,'max'=>$model->maxscore('score_uniform'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_tools',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_tools'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_tools]', $model->info['score_tools'], 
							array('min'=>0,'max'=>$model->maxscore('score_tools'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo TbHtml::label(Yii::t('qc','Communication Skill'),false,array('class'=>'col-sm-2',)); ?>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_greet',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_greet'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_greet]', $model->info['score_greet'], 
							array('min'=>0,'max'=>$model->maxscore('score_greet'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>

				<?php echo $form->labelEx($model,'score_comm',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_comm'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_comm]', $model->info['score_comm'], 
							array('min'=>0,'max'=>$model->maxscore('score_comm'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo TbHtml::label(Yii::t('qc','Safety'),false,array('class'=>'col-sm-4',)); ?>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_safety',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_safety'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_safety]', $model->info['score_safety'], 
							array('min'=>0,'max'=>$model->maxscore('score_safety'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
				<?php echo $form->labelEx($model,'score_afterwork',array('class'=>"col-sm-2 control-label",'data-toggle'=>'tooltip','data-placement'=>'bottom',
							'data-html'=>true,
							'title'=>$model->getDescription('score_afterwork'),)); ?>
				<div class="col-sm-1">
					<?php 
						echo TbHtml::numberField('QcForm[info][score_afterwork]', $model->info['score_afterwork'], 
							array('min'=>0,'max'=>$model->maxscore('score_afterwork'),'readonly'=>($model->readonly()),)
						); 
					?>
				</div>
			</div>

			<legend></legend>
			<div class="form-group">
				<div class="col-sm-5">
					<h4>
						<b><?php echo Yii::t('qc','Total Score').'(100)';?></b>
					</h4>
					
				</div>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'qc_result', 
						array('readonly'=>true)
					); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-5">
					<h4>
						<b>
							<?php 
								$lbl = $model->attributeLabels();
								echo $lbl['cust_score1'];
							?>
						</b>
					</h4>
					
				</div>
				<div class="col-sm-1">
					<?php echo $form->numberField($model, 'cust_score', 
						array('min'=>0,'max'=>10,'readonly'=>($model->readonly()))
					); ?>
				</div>
			</div>

			<legend></legend>
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
				<?php echo $form->labelEx($model,'signature',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-8">
					<div class="col-sm-7">
<?php if ((empty($model->info['sign_cust']) && $model->scenario!='view')): ?>
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
					<div class="col-sm-7">
<?php if ((empty($model->info['sign_tech']) && $model->scenario!='view')): ?>
					<?php 
						echo TbHtml::button(Yii::t('qc','Technician Signature'), array('name'=>'btnSignTech','id'=>'btnSignTech',));
						echo TbHtml::image($model->info['sign_tech'],'QcForm_info_sign_tech_img',array('id'=>'QcForm_info_sign_tech_img','width'=>200,'height'=>100,'style'=>'display:none')); 
					?>
<?php else: ?>
					<?php 
						echo $form->labelEx($model,'sign_tech');
						echo TbHtml::image($model->info['sign_tech'],'QcForm_info_sign_tech_img',array('id'=>'QcForm_info_sign_tech_img','width'=>200,'height'=>100,)); 
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
													'ronly'=>($model->scenario=='view'),
													)); 
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'QCPHOTO',
													'header'=>Yii::t('qc','Photo Attachment'),
													'ronly'=>($model->scenario=='view'),
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

Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/dms-lookup.js', CClientScript::POS_HEAD);

$js = <<<EOF
$('#btnStaffResp').on('click',function() {
	opendialog('staff', '', 'job_staff', false, {}, {});
});

$('#btnCompany').on('click',function() {
	opendialog('company', 'company_id', 'company_name', false, {}, {});
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
EOF;
Yii::app()->clientScript->registerScript('signature',$js,CClientScript::POS_READY);

$js = <<<EOF
$('.popover-dismiss').popover({
  trigger: 'focus'
});
EOF;
Yii::app()->clientScript->registerScript('popover',$js,CClientScript::POS_READY);

$js = <<<EOF
$('[id^="QcForm_info_score_"]').focusout(function() {
	var ratscore = 0;
	var roachscore = 0;
	var flyscore = 0;
	var svcscore = 0;
	var qcscore = 0;
	
	$('[id^="QcForm_info_score"]').each(function() {
		var id = $(this).attr('id');
		var itemscore = parseInt(+$(this).val() || 0);
		$(this).val(itemscore);
		
		var b_rat = (id.indexOf("QcForm_info_score_rat")!=-1);
		var b_roach = (id.indexOf("QcForm_info_score_roach")!=-1);
		var b_fly = (id.indexOf("QcForm_info_score_fly")!=-1);
		
		ratscore += b_rat ? itemscore : 0;
		roachscore += b_roach ? itemscore : 0;
		flyscore += b_fly ? itemscore : 0;
		svcscore += (!b_rat && !b_roach && !b_fly) ? itemscore : 0;
	});
	
	var choice = (ratscore!=0 | 0) + (roachscore!=0 | 0) +(flyscore!=0 | 0);
	
	if (choice > 0) {
		qcscore = (ratscore + roachscore + flyscore) / choice * 0.6;
		$('#QcForm_info_qc_score').val(qcscore.toFixed(2));
	}	
	
	svcscore = (svcscore * 0.4);
	var total = qcscore + svcscore;
	$('#QcForm_service_score').val(svcscore.toFixed(2));
	$('#QcForm_qc_result').val(total.toFixed(2));
});
EOF;
Yii::app()->clientScript->registerScript('calculate',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

