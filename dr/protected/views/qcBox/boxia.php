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
                'submit'=>Yii::app()->createUrl('qcBox/save'))); }
			?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
            <?php if($model->readonly()&&Yii::app()->user->validFunction('CN03')==false){}else{
                echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Remove'), array(
                    'submit'=>Yii::app()->createUrl('qcBox/remove')));
                echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );}
            ?>
<?php endif ?>
	</div>
	<div class="btn-group pull-right" role="group">
        <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','xiazai'), array(
            'data-href'=>Yii::app()->createUrl('qcBox/downs',array("index"=>$model->id)),'id'=>'xiazai'));
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
			<?php echo TbHtml::hiddenField('QcBoxForm[info][sign_cust]', $model->info['sign_cust']); ?>
            <?php echo TbHtml::hiddenField('QcBoxForm[info][sign_qc]', $model->info['sign_qc']); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
                    echo $form->dropDownList($model,'city',QcForm::getCityList(),array('readonly'=>$model->readonly()));

					?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'qc_staff',array('class'=>"col-sm-2 control-label")); ?>

				<div class="col-sm-7">
					<?php
						echo $form->textField($model, 'qc_staff',
							array('size'=>50,'maxlength'=>500,'readonly'=>'',
							'append'=>TbHtml::Button('<span class="fa fa-search"></span> '.Yii::t('qc','QC Staff'),
											array('name'=>'btnStaffQc','id'=>'btnStaffQc',
												'disabled'=>($model->readonly())
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
							echo TbHtml::textField('QcBoxForm[info][service_dt]',$model->info['service_dt'],
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
                <?php echo $form->labelEx($model,'qc_result',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'qc_result',
                        array('readonly'=>true,'class'=>'total_amt')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="table-responsive">
                        <?php
                        echo $model->printInfoHtml();
                        ?>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'cust_sfn',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php
                    echo TbHtml::inlineRadioButtonList('QcBoxForm[info][cust_sfn]',$model->info['cust_sfn'],QcBoxForm::CustSfnList(),
                        array('readonly'=>($model->readonly()),));
                     ?>
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
				<?php echo $form->labelEx($model,'signature',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-8">


					<div class="col-sm-7">
<?php if (empty($model->info['sign_cust']) && $model->scenario!='view'): ?>
					<?php 
						echo TbHtml::button(Yii::t('qc','Customer Signature'), array('name'=>'btnSignCust','id'=>'btnSignCust',));
						echo TbHtml::image($model->info['sign_cust'],'QcBoxForm_info_sign_cust_img',array('id'=>'QcBoxForm_info_sign_cust_img','width'=>200,'height'=>100,'style'=>'display:none'));
					?>
<?php else: ?>
					<?php 
						echo $form->labelEx($model,'sign_cust');
						echo TbHtml::image($model->info['sign_cust'],'QcBoxForm_info_sign_cust_img',array('id'=>'QcBoxForm_info_sign_cust_img','width'=>200,'height'=>100,));
					?>
<?php endif ?>
					</div>

                    <div class="col-sm-7">
                        <?php if (empty($model->info['sign_qc']) && $model->scenario!='view'): ?>
                            <?php
                            echo TbHtml::button(Yii::t('qc','QC Signature'), array('name'=>'btnSignQc','id'=>'btnSignQc',));
                            echo TbHtml::image($model->info['sign_qc'],'QcBoxForm_info_sign_qc_img',array('id'=>'QcBoxForm_info_sign_qc_img','width'=>200,'height'=>100,'style'=>'display:none'));
                            ?>
                        <?php else: ?>
                            <?php
                            echo $form->labelEx($model,'sign_qc');
                            echo TbHtml::image($model->info['sign_qc'],'QcBoxForm_info_sign_qc_img',array('id'=>'QcBoxForm_info_sign_qc_img','width'=>200,'height'=>100,));
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

<?php
$baseUrl = Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/signature_pad.min.js',CClientScript::POS_HEAD);

Script::genFileUpload($model,$form->id,'QC');
Script::genFileUpload($model,$form->id,'QCPHOTO');

//Script::genFileUpload(get_class($model),$form->id, 'qc');

Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/dms-lookup.js', CClientScript::POS_HEAD);

$js = <<<EOF
$('#btnStaffResp').on('click',function() {
	opendialog('staff', '', 'job_staff', false, {}, { city:$('#QcBoxForm_city').val()});
});

$('#btnCompany').on('click',function() {
	opendialog('company', 'company_id', 'company_name', false, {}, { city:$('#QcBoxForm_city').val()});
});

$('#btnStaffQc').on('click',function() {
	opendialog('staff', '', 'qc_staff', false, {}, { city:$('#QcBoxForm_city').val()});
});


EOF;
Yii::app()->clientScript->registerScript('lookup',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('qcBox/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if (!$model->readonly()) {
	$js = Script::genDatePicker(array(
			'QcBoxForm_entry_dt',
			'QcBoxForm_qc_dt',
			'QcBoxForm_info_service_dt',
		));
	Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = <<<EOF
$('#btnSignCust').on('click',function(){
	$('#sign_target_field').val('QcBoxForm_info_sign_cust');
	$('#signdialog').modal('show');
});

$('#btnSignQc').on('click',function(){
	$('#sign_target_field').val('QcBoxForm_info_sign_qc');
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
$('.changeAll').on('change',function(){
    var strKey = $(this).data('name');
    if($(this).val()==1){//包含
        $(this).parent('td').next('td').text('');
        $('.'+strKey+'_val').data('off',0).prop('readonly',false);
        $('.'+strKey+'_rmk').prop('readonly',false);
    }else{
        $('.'+strKey+'_val').val('').data('off',1).prop('readonly',true);
        $('.'+strKey+'_rmk').val('').prop('readonly',true);
        $(this).parent('td').next('td').text($(this).data('sum'));
    }
    changeAmt();
});

$('.changeAmt').on('change keyup',function(){
    changeAmt();
});

function changeAmt(){
    var totalAmt = 0;
    $('.changeAmt').each(function(){
        if($(this).attr('readonly')=='readonly'&&$(this).data('off')==1){
            var num = $(this).attr('max');
        }else{
            var num = $(this).val();
        }
        num = num==''?0:parseFloat(num);
        totalAmt+=num;
    });
    $(".totalAmtText").text(totalAmt);
    $(".total_amt").val(totalAmt);
}


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

