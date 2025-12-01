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
        <?php
        if($model->scenario!='new'){
            echo TbHtml::button('<span class="fa fa-download"></span> 下载pdf', array(
                'data-href'=>Yii::app()->createUrl('qcBox/downs',array("index"=>$model->id)),'class'=>'xiazai'));
            echo TbHtml::button('<span class="fa fa-download"></span> 下载excel', array(
                'data-href'=>Yii::app()->createUrl('qcBox/downExcel',array("index"=>$model->id)),'class'=>'xiazai'));
        }
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
						array('maxlength'=>500,'readonly'=>(true),
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
							echo TbHtml::textField('QcBoxForm[info][service_dt]',$model->info['service_dt'],
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
                <?php echo $form->labelEx($model,'qc_result',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'qc_result',
                        array('readonly'=>true,'class'=>'total_amt')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class=""><!--table-responsive-->
                        <?php
                        echo $model->printInfoHtml();
                        ?>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'cust_sfn',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-2">
					<?php
                    $model->info['cust_sfn'] = isset($model->info['cust_sfn'])?$model->info['cust_sfn']:null;
                    echo TbHtml::numberField('QcBoxForm[info][cust_sfn]',$model->info['cust_sfn'],
                        array('readonly'=>$model->readonly(),'min'=>0,'max'=>10,'id'=>'cust_sfn'));
                     ?>
				</div>
				<div class="col-lg-8">
                    <p style="margin-bottom: 0px;"><?php echo Yii::t("qc","cust_sfn_note");?></p>
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

<!-- Modal -->
<div class="modal fade" id="visibleXSModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">评分弹窗 - <span id="visibleXSSmall"></span></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-lg-2 control-label">评分说明：</label>
                    <div class="col-lg-10">
                        <p class="form-control-static" id="visibleXSName"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">最大分值：<span id="visibleXSMax"></span></label>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">评分：</label>
                    <div class="col-lg-10">
                        <input type="number" min="0" class="form-control" id="visibleXSVal">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">备注：</label>
                    <div class="col-lg-10">
                        <textarea rows="3" class="form-control" id="visibleXSRemark"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="visibleXSOK">确定</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="numberErrorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">验证</h4>
            </div>
            <div class="modal-body" id="numberErrorBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>

<?php
$baseUrl = Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/signature_pad.min.js',CClientScript::POS_HEAD);

Script::genFileUpload($model,$form->id,'QC');
Script::genFileUpload($model,$form->id,'QCPHOTO');

//Script::genFileUpload(get_class($model),$form->id, 'qc');

Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/dms-lookup.js', CClientScript::POS_HEAD);

$js = <<<EOF
$('#btnStaffResp').on('click',function() {
	opendialog('staff', '', 'job_staff', false, {}, { city:''});
});

$('#btnCompany').on('click',function() {
	opendialog('company', 'company_id', 'company_name', false, {}, { city:$('#QcBoxForm_city').val()});
});

$('#btnStaffQc').on('click',function() {
	opendialog('staff', '', 'qc_staff', false, {}, { city:''});
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

	$('.xiazai').on('click',function(){
	    var href = $(this).data('href');
	    $('#qc-form').attr('action',href).submit();
	});

$('.click-xs').click(function(){
    if($(this).children(".visible-xs:first").is(':visible')){
        var visibleXSName = $(this).children('td').eq(0).text();
        var visibleXSMax = $(this).children('td').eq(1).text();
        var visibleXSVal = $(this).find('.changeAmt:first').val();
        var visibleXSRemark = $(this).find('textarea:first').val();
        var dataOff = $(this).find('.changeAmt:first').attr('readonly');//=='readonly'
        $('#visibleXSName').text(visibleXSName);
        $('#visibleXSMax').text(visibleXSMax);
        $('#visibleXSVal').val(visibleXSVal).attr('max',visibleXSMax);
        $('#visibleXSRemark').val(visibleXSRemark);
        $('#visibleXSSmall').text($(this).data('title'));
        $('#visibleXSOK').data('id',$(this).data('id'));
        if(dataOff=='readonly'){
            $('#visibleXSVal').prop('readonly',true);
            $('#visibleXSRemark').prop('readonly',true);
        }else{
            $('#visibleXSVal').prop('readonly',false);
            $('#visibleXSRemark').prop('readonly',false);
        }
        $('#visibleXSModal').modal('show');
    }
});

$('#visibleXSOK').click(function(){
    var id = $(this).data('id');
    var visibleXSVal = $('#visibleXSVal').val();
    var visibleXSRemark = $('#visibleXSRemark').val();
    var trObj = $('.click-xs[data-id="'+id+'"]');
    var text = '';
    if(visibleXSVal!=''){
        text+='<b>评分：'+visibleXSVal+'</b>';
    }
    if(visibleXSRemark!=''){
        text+='<br/><b>备注：</b>'+visibleXSRemark;
    }
    trObj.find('.mark-text').html(text);
    trObj.find('.changeAmt:first').val(visibleXSVal);
    trObj.find('textarea:first').val(visibleXSRemark);
    
    $('#visibleXSModal').modal('hide');
    changeAmt();
});

$('.changeAmt,#visibleXSVal,#cust_sfn').blur(function(){
    var maxNum = $(this).attr('max');
    var minNum = $(this).attr('min');
    var thisVal = $(this).val();
    if($(this).val()!=''){
        thisVal = parseFloat(thisVal);
        maxNum = parseFloat(maxNum);
        minNum = minNum==''||minNum==undefined?0:parseFloat(minNum);
        if(thisVal>maxNum){
            $('#numberErrorBody').html('<p>数值不能大于'+maxNum+'</p>');
            $('#numberErrorModal').data('max',maxNum).data('obj',$(this).attr('id')).modal('show');
        }
        if(thisVal<minNum){
            $('#numberErrorBody').html('<p>数值不能小于'+minNum+'</p>');
            $('#numberErrorModal').data('max',minNum).data('obj',$(this).attr('id')).modal('show');
        }
    }
});
$('#numberErrorModal').on('hidden.bs.modal', function (e) {
    var maxNum = $(this).data('max');
    var obj = $(this).data('obj');
    $('#'+obj).val(maxNum).trigger('change');
    if(obj=='visibleXSVal'){
        $('#visibleXSOK').trigger('click');
    }
})
EOF;
Yii::app()->clientScript->registerScript('calculate',$js,CClientScript::POS_READY);
if(!empty($model->errorID)){
    $js=" 
    setTimeout(function(){
        $('#{$model->errorID}').focus();
    },500);
    ";
    Yii::app()->clientScript->registerScript('errorIDShow',$js,CClientScript::POS_READY);
}

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>
