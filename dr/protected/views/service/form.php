<?php
$this->pageTitle=Yii::app()->name . ' - Service Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'service-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
'htmlOptions'=>array('enctype' => 'multipart/form-data'),
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('service','Service Form'); ?></strong>
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
<?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
	<?php
		echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
			'name'=>'btnAdd','id'=>'btnAdd','data-toggle'=>'modal','data-target'=>'#addrecdialog',)
		);
	?>
	<?php echo TbHtml::button('<span class="fa fa-clone"></span> '.Yii::t('misc','Copy'), array(
			'name'=>'btnCopy','id'=>'btnCopy')
		);
	?>
<?php endif ?>
	<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
		'submit'=>Yii::app()->createUrl('service/index'))
	); ?>
<?php if ($model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
		'submit'=>Yii::app()->createUrl('service/save'))
	); ?>
<?php endif ?>
<?php if ($model->scenario=='edit'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	<?php
		$counter = ($model->no_of_attm['service'] > 0) ? ' <span id="docservice" class="label label-info">'.$model->no_of_attm['service'].'</span>' : ' <span id="docservice"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadservice',)
		);

	?>
<?php if ($model->status=='T'|| $model->status=='S'): ?>
  <?php   if ($model->send=='Y'){
        echo TbHtml::button('<span class="fa fa-send"></span> '."重新发送", array('name'=>'btnSendemail','id'=>'btnSendemail','data-toggle'=>'modal','data-target'=>'#sendemail','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
    }else{
        echo TbHtml::button('<span class="fa fa-send"></span> '.Yii::t('misc','Send Email'), array('name'=>'btnSendemail','id'=>'btnSendemail','data-toggle'=>'modal','data-target'=>'#sendemail',));
    }

    ?>
<?php endif ?>

	</div>
            <?php if ($model->scenario!='new'): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-list"></span> '.Yii::t('service','Flow Info'), array(
                            'data-toggle'=>'modal','data-target'=>'#flowinfodialog',)
                    );
                    ?>
                </div>
            <?php endif ?>
	</div></div>

<?php
	$currcode = City::getCurrency($model->city);
	$sign = Currency::getSign($currcode);
?>
	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
			<?php echo $form->hiddenField($model, 'company_id'); ?>
			<?php echo $form->hiddenField($model, 'backlink'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'commission'); ?>
			<?php echo $form->hiddenField($model, 'other_commission'); ?>
			<?php echo $form->hiddenField($model, 'lcu'); ?>
			<?php echo $form->hiddenField($model, 'luu'); ?>
			<?php echo $form->hiddenField($model, 'lcd'); ?>
			<?php echo $form->hiddenField($model, 'lud'); ?>
			<?php echo TbHtml::hiddenField('copy_index',0,array('id'=>'copy_index')); ?>
			<?php
				if ($model->status!='A') {
					echo $form->hiddenField($model, 'b4_service');
					echo $form->hiddenField($model, 'b4_paid_type');
					echo $form->hiddenField($model, 'b4_amt_paid');
				}

				if (($model->status!='N') && ($model->status!='C')) {
					echo $form->hiddenField($model, 'equip_install_dt');
				}

				if (($model->status!='S') && ($model->status!='T')) {
					echo $form->hiddenField($model, 'org_equip_qty');
					echo $form->hiddenField($model, 'rtn_equip_qty');
				}
			?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'status',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'status_desc',
						array('class'=>'form-control','maxlength'=>15,'readonly'=>true,));
					?>
				</div>
                <?php echo $form->labelEx($model,'contract_no',array('class'=>"col-sm-1 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'contract_no',
                        array('class'=>'form-control','maxlength'=>30,));
                    ?>
                </div>
			</div>
			<div class="form-group">
				<?php
					switch ($model->status) {
						case 'N': $dt_name = 'new_dt'; break;
						case 'C': $dt_name = 'renew_dt'; break;
						case 'A': $dt_name = 'amend_dt'; break;
						case 'S': $dt_name = 'suspend_dt'; break;
						case 'R': $dt_name = 'resume_dt'; break;
						case 'T': $dt_name = 'terminate_dt'; break;
                        default:
                            $dt_name='new_dt';
                            echo "<span>异常：$model->status</span>";
					}
					echo $form->labelEx($model,$dt_name,array('class'=>"col-sm-2 control-label"));
				?>
				<div class="col-sm-3">
					<div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'status_dt',
                            array('class'=>'form-control pull-right','readonly'=>($model->getReadonly()),));
                        ?>
                    </div>
				</div>
                <div class="form-group" style="height: 20px;">
                    <?php echo $form->labelEx($model,'prepay_month',array('class'=>"col-sm-1 control-label")); ?>
                    <div class="col-sm-1">
                        <?php echo $form->numberField($model, 'prepay_month',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'prepay_start',array('class'=>"col-sm-1 control-label")); ?>
                    <div class="col-sm-1">
                        <?php echo $form->numberField($model, 'prepay_start',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>

			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php
						echo $form->textField($model, 'company_name',
							array('class'=>'form-control','maxlength'=>15,'readonly'=>true,
								'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Customer'),
									array('name'=>'btnCompany','id'=>'btnCompany','disabled'=>($model->getReadonly()))),
						));
					?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'cust_type',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->dropDownList($model, 'cust_type', General::getCustTypeList(), array('disabled'=>($model->getReadonly())));
					?>
				</div>
                <div class="col-sm-2">
                    <?php
                    $typelist = $model->getCustTypeList((empty($model->cust_type) ? 1 : $model->cust_type));
                    echo $form->dropDownList($model, 'cust_type_name', $typelist, array('disabled'=>($model->getReadonly())));

                    ?>
                </div>
                <?php echo $form->labelEx($model,'pieces',array('class'=>"col-sm-1 control-label"));   ?>
                <div class="col-sm-2">
                     <?php echo $form->numberField($model, 'pieces',
                        array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'nature_type',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->dropDownList($model, 'nature_type', NatureForm::getNatureList(), array('disabled'=>($model->scenario=='view'),'id'=>'nature_type'));
					?>
				</div>
                <div class="col-sm-2">
                    <?php
                    $natureTwoList = NatureForm::getNatureTwoList();
                    echo $form->dropDownList($model, 'nature_type_two', $natureTwoList["select"], array('disabled'=>($model->scenario=='view'),'options'=>$natureTwoList["options"],'id'=>'nature_type_two'));
                    ?>
				</div>
			</div>
<?php if ($model->status=='A') : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'b4_service',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php
						echo $form->hiddenField($model, 'b4_product_id');
						echo $form->textField($model, 'b4_service',
							array('size'=>60,'maxlength'=>1000,'readonly'=>($model->scenario=='view'),
								'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Service'),array('name'=>'btnServiceB4','id'=>'btnServiceB4','disabled'=>($model->scenario=='view'))),
						));
					?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'b4_amt_paid',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
						echo $form->dropDownList($model, 'b4_paid_type',
							array('M'=>Yii::t('service','Monthly'),
								'Y'=>Yii::t('service','Yearly'),
								'1'=>Yii::t('service','One time'),
							),
							array('disabled'=>($model->getReadonly())));
					?>
				</div>
				<div class="col-sm-2">
					<?php
						echo $form->numberField($model, 'b4_amt_paid',
							array('size'=>6,'min'=>0,'readonly'=>($model->getReadonly()),
							'prepend'=>'<span class="fa '.$sign.'"></span>')
						);
					?>
				</div>
			</div>
<?php endif; ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,(($model->status=='A') ? 'af_service' : 'service'),array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php
						echo $form->hiddenField($model, 'product_id');
						echo $form->textField($model, 'service',
							array('size'=>60,'maxlength'=>1000,'readonly'=>($model->scenario=='view'),
								'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Service'),array('name'=>'btnService','id'=>'btnService','disabled'=>($model->scenario=='view'))),
							));
					?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,(($model->status=='A') ? 'af_amt_paid' : 'amt_paid'),array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
						echo $form->dropDownList($model, 'paid_type',
							array('M'=>Yii::t('service','Monthly'),
								'Y'=>Yii::t('service','Yearly'),
								'1'=>Yii::t('service','One time'),
							), array('disabled'=>($model->getReadonly()))
						);
					?>
				</div>

				<div class="col-sm-2">
					<?php
						echo $form->numberField($model, 'amt_paid',
							array('size'=>6,'min'=>0,'readonly'=>($model->getReadonly()),
							'prepend'=>'<span class="fa '.$sign.'"></span>')
						);
					?>
				</div>

			</div>
<?php if (($model->status!='S') && ($model->status!='T')) : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'amt_install',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'amt_install',
							array('size'=>6,'min'=>0,'readonly'=>($model->getReadonly()),
							'prepend'=>'<span class="fa '.$sign.'"></span>')
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'need_install',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->dropDownList($model, 'need_install', array(''=>Yii::t('misc','No'),'Y'=>Yii::t('misc','Yes')),
								array('disabled'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
<?php endif; ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'all_number',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'all_number',
                        array('size'=>4,'min'=>0,'readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
                <?php if (($model->status=='A') || ($model->status=='T') || ($model->status=='S')) : ?>
                <?php echo $form->labelEx($model,'surplus',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'surplus',
                        array('size'=>4,'min'=>0,'readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($model->status=='T') : ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'all_number_edit0',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'all_number_edit0',
                        array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                    <?php echo $form->labelEx($model,'surplus_edit0',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'surplus_edit0',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
            </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'all_number_edit1',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'all_number_edit1',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'surplus_edit1',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'surplus_edit1',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'all_number_edit2',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'all_number_edit2',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'surplus_edit2',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'surplus_edit2',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'all_number_edit3',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'all_number_edit3',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'surplus_edit3',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'surplus_edit3',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'salesman',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php
						echo $form->textField($model, 'salesman',
							array('size'=>60,'maxlength'=>1000,'readonly'=>true,
							'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Resp. Sales'),array('name'=>'btnSalesman','id'=>'btnSalesman','disabled'=>($model->getReadonly()))),
						));
					?>
                    <?php echo $form->hiddenField($model, 'salesman_id'); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'othersalesman',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php
                    echo $form->textField($model, 'othersalesman',
                        array('size'=>60,'maxlength'=>1000,'readonly'=>true,
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Resp. Sales'),array('name'=>'btnOtherSalesman','id'=>'btnOtherSalesman','disabled'=>($model->getReadonly()))),
                        ));
                    ?>
                    <?php echo $form->hiddenField($model, 'othersalesman_id'); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'technician',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php
                    echo $form->textField($model, 'technician',
                        array('size'=>60,'maxlength'=>1000,'readonly'=>($model->scenario=='view'),
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Resp. Tech.'),array('name'=>'btnTechnician','id'=>'btnTechnician','disabled'=>($model->scenario=='view'))),
                        ));
                    ?>
                    <?php echo $form->hiddenField($model, 'technician_id'); ?>
                </div>
            </div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'sign_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'sign_dt',
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'ctrt_period',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->numberField($model, 'ctrt_period',
							array('size'=>4,'min'=>0,'readonly'=>($model->getReadonly()))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'ctrt_end_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'ctrt_end_dt',
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
						?>
					</div>
				</div>
			</div>
<?php if ($model->status=='N'|| $model->status=='C') : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'cont_info',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'cont_info',
						array('size'=>60,'maxlength'=>500,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
<?php endif; ?>
<?php if ($model->status=='N' || $model->status=='C' || $model->status=='A') : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'first_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'first_dt',
							array('class'=>'form-control pull-right','readonly'=>($model->getReadonly()),));
						?>
					</div>
				</div>
			</div>
<?php endif; ?>
<?php if ($model->status=='N' || $model->status=='C') : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'first_tech',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textField($model, 'first_tech',
						array('size'=>80,'maxlength'=>1000,'readonly'=>($model->scenario=='view'),
						'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','First Service Tech.'),array('name'=>'btnFirstTech','id'=>'btnFirstTech','disabled'=>($model->scenario=='view')))
					)); ?>
                    <?php echo $form->hiddenField($model, 'first_tech_id'); ?>
				</div>
			</div>
<?php endif; ?>
<?php if (($model->status=='S') || ($model->status=='T')) : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'reason',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'reason',
						array('rows'=>3,'cols'=>60,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'tracking',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'tracking',
						array('rows'=>3,'cols'=>60,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'org_equip_qty',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->numberField($model, 'org_equip_qty',
						array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'rtn_equip_qty',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->numberField($model, 'rtn_equip_qty',
						array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
<?php endif; ?>
<?php if ($model->status=='N' || $model->status=='C') : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'equip_install_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'equip_install_dt',
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
						?>
					</div>
				</div>
			</div>
<?php endif; ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'remarks2',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'remarks2',
						array('rows'=>3,'cols'=>60,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'remarks',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'remarks',
						array('rows'=>3,'cols'=>60,'maxlength'=>2000,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

            <?php if ($model->scenario!='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'lcu',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->textField($model, 'lcu',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->textField($model, 'lcd',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'luu',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->textField($model, 'luu',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'lud',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->textField($model, 'lud',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
		</div>
	</div>
</section>

<?php
	$buttons = array(
			TbHtml::button(Yii::t('service','New Service'),
				array(
					'name'=>'btnNew',
					'id'=>'btnNew',
					'class'=>'btn btn-block',
				)),
			TbHtml::button(Yii::t('service','Renew Service'),
				array(
					'name'=>'btnRenew',
					'id'=>'btnRenew',
					'class'=>'btn btn-block',
				)),
			TbHtml::button(Yii::t('service','Amend Service'),
				array(
					'name'=>'btnAmend',
					'id'=>'btnAmend',
					'class'=>'btn btn-block',
				)),
			TbHtml::button(Yii::t('service','Suspend Service'),
				array(
					'name'=>'btnSuspend',
					'id'=>'btnSuspend',
					'class'=>'btn btn-block',
				)),
			TbHtml::button(Yii::t('service','Resume Service'),
				array(
					'name'=>'btnResume',
					'id'=>'btnResume',
					'class'=>'btn btn-block',
				)),
			TbHtml::button(Yii::t('service','Terminate Service'),
				array(
					'name'=>'btnTerminate',
					'id'=>'btnTerminate',
					'class'=>'btn btn-block',
				)),
		);

	$content = "";
	foreach ($buttons as $button) {
		$content .= "<div class=\"row\"><div class=\"col-sm-10\">$button</div></div>";
	}
	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'addrecdialog',
					'header'=>Yii::t('misc','Add Record'),
					'content'=>$content,
//					'footer'=>array(
//						TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
//					),
					'show'=>false,
				));
?>
<?php $this->renderPartial('//site/sendemail'); ?>
<?php $this->renderPartial('//service/historylist',array("model"=>$model)); ?>
<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('//site/lookup'); ?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'SERVICE',
													'header'=>Yii::t('dialog','File Attachment'),
													'ronly'=>($model->scenario=='view'),
													));
?>


<?php
Script::genFileUpload($model,$form->id,'SERVICE');
$link3 = Yii::app()->createAbsoluteUrl("service/getcusttypelist");
$js = <<<EOF
$('#ServiceForm_cust_type').on('change',function() {
	var group = $(this).val();
	var data = "group="+group;
	
	$.ajax({
		type: 'GET',
		url: '$link3',
		data: data,
		success: function(data) {
			$('#ServiceForm_cust_type_name').html(data);
		},
		error: function(data) { // if error occured
			var x = 1;
		},
		dataType:'html'
	});
});	
EOF;
Yii::app()->clientScript->registerScript('select2_1',$js,CClientScript::POS_READY);

$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$fields = ($model->status=='N' || $model->status=='C') ? array('contact'=>'ServiceForm_cont_info',) : array();
$js = Script::genLookupButtonEx('btnCompany', 'company', 'company_id', 'company_name', $fields);
Yii::app()->clientScript->registerScript('lookupCompany',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnServiceB4', 'product', 'b4_product_id', 'ServiceForm_b4_service');
Yii::app()->clientScript->registerScript('lookupServiceB4',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnService', 'product', 'product_id', 'ServiceForm_service');
Yii::app()->clientScript->registerScript('lookupService',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnSalesman', 'staff', 'salesman_id','salesman');
Yii::app()->clientScript->registerScript('lookupSalesman',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnOtherSalesman', 'staff', 'othersalesman_id', 'othersalesman');
Yii::app()->clientScript->registerScript('lookupOtherSalesman',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnTechnician', 'staff', 'technician_id', 'technician');
Yii::app()->clientScript->registerScript('lookupTechnician',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnFirstTech', 'staff', 'first_tech_id', 'first_tech', array(), true);
Yii::app()->clientScript->registerScript('lookupFirstTech',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);

$js = "
$('#ServiceForm_ctrt_period').on('change',function() {
	var end_dt = $('#ServiceForm_ctrt_end_dt').val();
	var sign_dt = $('#ServiceForm_sign_dt').val();
	var p = $(this).val();
	if (!end_dt && sign_dt && p) {
		var m = parseInt($('#ServiceForm_ctrt_period').val());
		var x = sign_dt.replace(/\//g,'-')
		var sd = new Date(x);
		$('#ServiceForm_ctrt_end_dt').val(addMonth(sd,parseInt(m)));
	}
});

$('#ServiceForm_sign_dt').on('change',function() {
	var end_dt = $('#ServiceForm_ctrt_end_dt').val();
	var period = $('#ServiceForm_ctrt_period').val();
	if (!end_dt && period) {
		var sd = new Date($('#ServiceForm_sign_dt').val().replace(/\//g,'-'));
		$('#ServiceForm_ctrt_end_dt').val(addMonth(sd,parseInt(period)));
	}
});

function addMonth(d, m) {
	var t = new Date(d);
	if (isNaN(t)) return '';
	var result = new Date(t.setMonth(t.getMonth()+m));;
	if (d.getDate()>28) {
		var t1 = new Date(d.getFullYear()+'-'+(d.getMonth()+1)+'-1');
		var t2 = new Date(t1.setMonth(t1.getMonth()+m));
		if (t2.getMonth()!=result.getMonth()) {
			result = new Date(result.setDate(0));
		}
	}
	return (result.getFullYear()+'/'+(result.getMonth()+1)+'/'+result.getDate());
}

$('#btnAdd').on('click',function() {
	$('#copy_index').val(0);
});

$('#btnCopy').on('click',function() {
	var id = $('#ServiceForm_id').val();
	$('#copy_index').val(id);
	var tst = $('#copy_index').val();
	$('#addrecdialog').modal('show');
});

$('#btnNew').on('click',function() {
	$('#addrecdialog').modal('hide');
	redirection('N');
});

$('#btnRenew').on('click',function() {
	$('#addrecdialog').modal('hide');
	redirection('C');
});

$('#btnAmend').on('click',function() {
	$('#addrecdialog').modal('hide');
	redirection('A');
});

$('#btnSuspend').on('click',function() {
	$('#addrecdialog').modal('hide');
	redirection('S');
});

$('#btnResume').on('click',function() {
	$('#addrecdialog').modal('hide');
	redirection('R');
});

$('#btnTerminate').on('click',function() {
	$('#addrecdialog').modal('hide');
	redirection('T');
});

function redirection(arg) {
	var index = $('#copy_index').val();
	var elm=$('#btnAdd');
	switch (arg) {
		case 'N':
			if (index==0)
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/new')."',{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/new')."?index='+index,{});
			break;
		case 'A':
			if (index==0)
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/amend')."',{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/amend')."?index='+index,{});
			break;
		case 'S':
			if (index==0)
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/suspend')."',{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/suspend')."?index='+index,{});
			break;
		case 'R':
			if (index==0)
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/resume')."',{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/resume')."?index='+index,{});
			break;
		case 'T':
			if (index==0)
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/terminate')."',{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/terminate')."?index='+index,{});
			break;
		case 'C':
			if (index==0)
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/renew')."',{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('service/renew')."?index='+index,{});
			break;
	}
}
";
Yii::app()->clientScript->registerScript('addRecord',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('service/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
	$js = "
	$('#ServiceForm_status_dt').datepicker({autoclose: true, format: 'yyyy/mm/dd'});
	$('#ServiceForm_sign_dt').datepicker({autoclose: true, format: 'yyyy/mm/dd'});
	$('#ServiceForm_ctrt_end_dt').datepicker({autoclose: true, format: 'yyyy/mm/dd'});
	$('#ServiceForm_first_dt').datepicker({autoclose: true, format: 'yyyy/mm/dd'});
	$('#ServiceForm_equip_install_dt').datepicker({autoclose: true, format: 'yyyy/mm/dd'});
	";
	Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);

if ($model->status=='T') {
    $js = "
	 document.getElementById('ServiceForm_all_number').addEventListener('input',function(event){
           event.target.value = event.target.value.replace(/\-/g,''); 
        });
    document.getElementById('ServiceForm_surplus').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_all_number_edit0').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_all_number_edit1').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_all_number_edit2').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_all_number_edit3').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_surplus_edit0').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_surplus_edit1').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_surplus_edit2').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
    document.getElementById('ServiceForm_surplus_edit3').addEventListener('input',function(event){
       event.target.value = event.target.value.replace(/\-/g,''); 
    });
	";
    Yii::app()->clientScript->registerScript('surplus',$js,CClientScript::POS_READY);
}else{
    $js = "
	 document.getElementById('ServiceForm_all_number').addEventListener('input',function(event){
           event.target.value = event.target.value.replace(/\-/g,''); 
        }); 
	";
    Yii::app()->clientScript->registerScript('surplus',$js,CClientScript::POS_READY);
}
$js = "
$('#nature_type_two').change(function(){
    if($(this).val()!=''){
        var nature_id = $('#nature_type_two>option:selected').data('nature');
        $('#nature_type').val(nature_id);
        $('#nature_type').trigger('change');
    }
});
$('#nature_type').change(function(){
    var nature_id = $(this).val();
    if(nature_id!=''){
        $('#nature_type_two>option').slice(1).hide();
        $('#nature_type_two>option[data-nature='+nature_id+']').show();
    }else{
        $('#nature_type_two>option').show();
    }
});
	";
Yii::app()->clientScript->registerScript('changeNatureType',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


