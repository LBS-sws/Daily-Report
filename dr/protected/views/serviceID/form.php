<?php
$this->pageTitle=Yii::app()->name . ' - Service Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'serviceID-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data'),
)); ?>

<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
    .text-nowrap.control-label{ min-width: 135px;}
</style>
<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Customer Service ID'); ?></strong>
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
                            'name'=>'btnAdd','id'=>'btnAdd')
                    );
                    ?>
                    <?php echo TbHtml::button('<span class="fa fa-clone"></span> '.Yii::t('misc','Copy'), array(
                            'name'=>'btnCopy','id'=>'btnCopy')
                    );
                    ?>
                <?php endif ?>
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                        'submit'=>Yii::app()->createUrl('serviceID/index'))
                ); ?>
                <?php if ($model->scenario!='view'): ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                            'submit'=>Yii::app()->createUrl('serviceID/save'))
                    ); ?>
                <?php endif ?>
                <?php if ($model->scenario=='edit'): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                    ?>
                <?php endif ?>
                <?php
                $counter = ($model->no_of_attm['serviceid'] > 0) ? ' <span id="docserviceid" class="label label-info">'.$model->no_of_attm['serviceid'].'</span>' : ' <span id="docserviceid"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadserviceid',)
                );
                ?>
            </div>
            <?php if ($model->scenario!='new'&&!empty($model->service_new_id)): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-map-o"></span> '.Yii::t('service','Service List'), array(
                            'data-toggle'=>'modal','data-target'=>'#historydialog')
                    );
                    ?>
                </div>
            <?php endif ?>
        </div></div>

    <?php
    $currcode = City::getCurrency($model->city);
    $sign = Currency::getSign($currcode);//货币单位
    $model->sign = $sign;
    ?>
    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'status'); ?>
            <?php echo TbHtml::hiddenField("dtltemplate"); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'status',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'status',$model->getStatusList(),
                        array('class'=>'form-control','readonly'=>true));
                    ?>
                </div>
                <?php if ($model->scenario!='new'): ?>
                    <?php echo $form->labelEx($model,'service_no',array('class'=>"col-sm-1 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'service_no',
                            array('class'=>'form-control','maxlength'=>30,'readonly'=>true));
                        ?>
                    </div>
                <?php endif ?>
            </div>
            <?php if ($model->status!='N'&&$model->scenario=='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'service_new_id',array('class'=>"col-sm-2 control-label"));   ?>
                    <div class="col-sm-7">
                        <?php echo $form->dropDownList($model, 'service_new_id',$model->getServiceAllForNew(),
                            array('readonly'=>($model->scenario=='view'),"id"=>'service_new_id')
                        ); ?>
                    </div>
                </div>
                <script>
                    $(function () {
                        $("#service_new_id").on("change",function () {
                            if($(this).val()!=""){
                                var url = "<?php echo Yii::app()->createUrl('serviceID/new',array('type'=>$model->status));?>";
                                window.location = url+"&id="+$(this).val();
                            }
                        });
                    });
                </script>
            <?php endif ?>
            <div class="form-group">
                <?php
                $dt_name = "new_dt";
                switch ($model->status) {
                    case 'N': $dt_name = 'new_dt'; break;
                    case 'C': $dt_name = 'renew_dt'; break;
                    case 'A': $dt_name = 'amend_dt'; break;
                    case 'S': $dt_name = 'suspend_dt'; break;
                    case 'R': $dt_name = 'resume_dt'; break;
                    case 'T': $dt_name = 'terminate_dt'; break;
                }
                echo $form->labelEx($model,$dt_name,array('class'=>"col-sm-2 control-label"));
                ?>
                <div class="col-sm-3">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'status_dt',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),'autocomplete'=>'off'));
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
                    echo $form->hiddenField($model, 'company_id');
                    echo $form->textField($model, 'company_name',
                        array('class'=>'form-control','maxlength'=>15,'readonly'=>true,
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Customer'),
                                array('name'=>'btnCompany','id'=>'btnCompany','disabled'=>($model->scenario=='view'))),
                        ));
                    ?>
                </div>
            </div>
            <div class="form-group" id="custTypeDiv">
                <?php echo $form->labelEx($model,'cust_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php
                    echo $form->dropDownList($model, 'cust_type', CustomertypeIDForm::getCustTypeRow(), array('readonly'=>true));
                    ?>
                </div>
                <div class="col-sm-2" data-num="<?php echo $model->cust_type_name;?>">
                    <?php
                    echo $form->dropDownList($model, 'cust_type_name', array(), array('readonly'=>$model->scenario!='new'));
                    ?>
                </div>
                <div class="col-sm-2" data-num="<?php echo $model->cust_type_three;?>">
                    <?php
                    echo $form->dropDownList($model, 'cust_type_three', array(), array('readonly'=>$model->scenario!='new'));
                    ?>
                </div>
                <div class="col-sm-2" data-num="<?php echo $model->cust_type_four;?>">
                    <?php
                    echo $form->dropDownList($model, 'cust_type_four', array(), array('readonly'=>$model->scenario!='new'));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'nature_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'nature_type', General::getNatureList(), array('disabled'=>($model->scenario=='view')));
                    ?>
                </div>
                <?php if ($model->status!='A'): ?>
                    <?php echo $form->labelEx($model,'pieces',array('class'=>"col-sm-1 control-label"));   ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'pieces',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,"service",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php
                    echo $form->hiddenField($model, 'product_id');
                    echo $form->textField($model, 'service',
                        array('size'=>60,'maxlength'=>1000,'readonly'=>(true),
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Service'),array('name'=>'btnService','id'=>'btnService','disabled'=>($model->scenario=='view'))),
                        ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'pay_week',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->dropDownList($model, 'pay_week',PayWeekForm::getPayWeekForId(), array('disabled'=>($model->scenario=='view'))
                    );
                    ?>
                </div>
            </div>
            <?php if ($model->status=='A'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,"b4_amt_paid",array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php
                        echo $form->numberField($model, 'b4_amt_paid',
                            array('size'=>6,'min'=>0,'readonly'=>($model->scenario=='view'),
                                'prepend'=>'<span class="fa '.$sign.'"></span>')
                        );
                        ?>
                    </div>
                    <?php echo $form->labelEx($model,"b4_amt_money",array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-2">
                        <?php
                        echo $form->numberField($model, 'b4_amt_money',
                            array('size'=>6,'min'=>0,'readonly'=>($model->scenario=='view'),
                                'prepend'=>'<span class="fa '.$sign.'"></span>')
                        );
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'b4_pieces',array('class'=>"col-sm-2 control-label"));   ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'b4_pieces',
                            array('size'=>4,'min'=>0,'readonly'=>(true))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'b4_cust_type_end',array('class'=>"col-sm-2 control-label"));   ?>
                    <div class="col-sm-2">
                        <?php echo $form->textField($model, 'b4_cust_type_end',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,"amt_paid",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php
                    echo $form->numberField($model, 'amt_paid',
                        array('size'=>6,'min'=>0,'readonly'=>($model->scenario=='view'),
                            'prepend'=>'<span class="fa '.$sign.'"></span>',"class"=>"changeOutMoney")
                    );
                    ?>
                </div>
                <?php if ($model->status!='A'): ?>
                    <?php echo $form->labelEx($model,'ctrt_period',array('class'=>"col-sm-1 control-label text-nowrap")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'ctrt_period',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'),'class'=>"changeOutMonth",'autocomplete'=>'off')
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,"amt_money",array('class'=>"col-sm-1 control-label text-nowrap")); ?>
                    <?php else: ?>
                    <?php echo $form->labelEx($model,"amt_money",array('class'=>"col-sm-2 control-label")); ?>
                <?php endif ?>
                <div class="col-sm-2">
                    <?php
                    echo $form->numberField($model, 'amt_money',
                        array('size'=>6,'min'=>0,'readonly'=>($model->scenario=='view'),
                            'prepend'=>'<span class="fa '.$sign.'"></span>')
                    );
                    ?>
                </div>
            </div>
            <?php if ($model->status=='A'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'pieces',array('class'=>"col-sm-2 control-label"));   ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'pieces',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'cust_type_end',array('class'=>"col-sm-2 control-label"));   ?>
                    <div class="col-sm-2">
                        <?php echo $form->textField($model, 'cust_type_end',
                            array('readonly'=>(true),"id"=>"cust_type_end")
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($model->status=="T"): ?>
                <div class="form-group">
                    <?php echo TbHtml::label(Yii::t("service","put month"),"",array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
                    <div class="col-sm-2">
                        <?php
                        echo $form->numberField($model, 'all_number',
                            array('size'=>6,'min'=>0,'readonly'=>($model->scenario=='view'))
                        );
                        ?>
                    </div>
                    <?php echo TbHtml::label(Yii::t("service","surplus month"),"",array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
                    <div class="col-sm-2">
                        <?php
                        echo $form->numberField($model, 'surplus',
                            array('size'=>6,'min'=>0,'id'=>"surplus",'readonly'=>($model->scenario=='view'),
                                "append"=>"<span>天</span>")
                        );
                        ?>
                    </div>
                    <div class="col-sm-2" style="padding-left: 0px;">
                        <p id="surplus_label" class="form-control-static text-primary"></p>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'need_install',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'need_install', array(''=>Yii::t('misc','No'),'Y'=>Yii::t('misc','Yes')),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'amt_install',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'amt_install',
                        array('size'=>6,'min'=>0,'readonly'=>($model->scenario=='view'),
                            'prepend'=>'<span class="fa '.$sign.'"></span>')
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'sign_dt',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'sign_dt',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),'autocomplete'=>'off'));
                        ?>
                    </div>
                </div>
                <?php if ($model->status=='A'): ?>
                    <?php echo $form->labelEx($model,'ctrt_period',array('class'=>"col-sm-1 control-label text-nowrap")); ?>
                    <div class="col-sm-2">
                        <?php echo $form->numberField($model, 'ctrt_period',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'),'class'=>"changeOutMonth",'autocomplete'=>'off')
                        ); ?>
                    </div>
                    <?php echo $form->labelEx($model,'ctrt_end_dt',array('class'=>"col-sm-1 control-label text-nowrap")); ?>
                <?php else: ?>
                    <?php echo $form->labelEx($model,'ctrt_end_dt',array('class'=>"col-sm-2 control-label")); ?>
                <?php endif ?>
                <div class="col-sm-2">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'ctrt_end_dt',
                            array('class'=>'form-control pull-right','readonly'=>$model->scenario=='view','autocomplete'=>'off'));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'equip_install_dt',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'equip_install_dt',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),'autocomplete'=>'off'));
                        ?>
                    </div>
                </div>
                <?php echo $form->labelEx($model,'first_dt',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'first_dt',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),'autocomplete'=>'off'));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'salesman',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php
                    echo $form->textField($model, 'salesman',
                        array('size'=>60,'maxlength'=>1000,'readonly'=>true,
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Resp. Sales'),array('name'=>'btnSalesman','id'=>'btnSalesman','disabled'=>($model->scenario=='view'))),
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
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Resp. Sales'),array('name'=>'btnOtherSalesman','id'=>'btnOtherSalesman','disabled'=>($model->scenario=='view'))),
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
                        array('size'=>60,'maxlength'=>1000,'readonly'=>true,
                            'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('service','Resp. Tech.'),array('name'=>'btnTechnician','id'=>'btnTechnician','disabled'=>($model->scenario=='view'))),
                        ));
                    ?>
                    <?php echo $form->hiddenField($model, 'technician_id'); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'cont_info',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textField($model, 'cont_info',
                        array('size'=>60,'maxlength'=>500,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
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
                        array('rows'=>3,'cols'=>60,'maxlength'=>2000,'readonly'=>(true))
                    ); ?>
                </div>
            </div>


            <?php if (!in_array($model->status,array("S","R","T"))): ?>
                <div class="box">
                    <div class="box-body table-responsive">
                        <?php
                        $this->widget('ext.layout.TableView2Widget', array(
                            'model'=>$model,
                            'attribute'=>'service_info',
                            'viewhdr'=>'//serviceID/_formhdr',
                            'viewdtl'=>'//serviceID/_formdtl',
                        ));
                        ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</section>
<script>
    $("#surplus").on("keyup",function () {
        var day = $(this).val();
        day = parseInt(day,10);
        var month = Math.floor(day/30);
        var str = "";
        if(month>0){
            str+=month+"月";
        }
        if(day%30>0){
            str+=(day%30)+"天";
        }
        $("#surplus_label").html(str);
    });
    $("#surplus").trigger("keyup")
    function changeOutMonth() {
        var maxMonth = $("#ServiceIDForm_ctrt_period").val();
        var list = [];
        if(maxMonth!=""){
            $("#tblDetail>tbody>tr").each(function (index,tr) {
                var backDate = $(this).find("[id*=\"_back_date\"]").val();
                var putMonth = $(this).find("[id*=\"_put_month\"]").val();
                var uflag = $(this).find("[id*=\"_uflag\"]").val();
                if(backDate!=""&&putMonth!=""&&uflag!="D"){
                    var obj = {
                        "backDate":backDate,
                        "putMonth":putMonth,
                        "index":index
                    };
                    var bool = true;
                    $.each(list,function (key, val) {
                        if(val["backDate"]>backDate){
                            list.splice(key,0,obj);
                            bool = false;
                            return false;
                        }
                    });
                    if(bool){
                        list.push(obj);
                    }
                }
            });
        }
        $.each(list,function (key, row) {
            var trObj = $("#tblDetail>tbody>tr").eq(row["index"]);
            maxMonth-=row["putMonth"];
            trObj.find("[id*=\"_out_month\"]").val(maxMonth);
        });
    }
    function changeOutMoney() {
        var money = $("#ServiceIDForm_amt_paid").val();
        if(money!=""&&money!=0&&!isNaN(money)){
            money = parseInt(money,10);
            $("#tblDetail>tbody>tr").each(function (index,tr) {
                var backMoney = $(this).find("[id*=\"_back_money\"]").val();
                backMoney = parseInt(backMoney,10);
                var month = Math.floor(backMoney/money);
                if(month>1){
                    $(this).find("[id*=\"_put_month\"]").val(month);
                }
            });
        }
        changeOutMonth();
    }
    $("body").delegate(".changeOutMonth","change keyup",changeOutMonth);
    $("body").delegate(".changeOutMoney","change keyup",changeOutMoney);
    $("#ServiceIDForm_amt_paid,#ServiceIDForm_ctrt_period").on("change keyup",function () {
        var money = $("#ServiceIDForm_amt_paid").val();
        var month = $("#ServiceIDForm_ctrt_period").val();
        if(money&&month&&!isNaN(money)&&!isNaN(month)){
            money = parseInt(money,10);
            month = parseInt(month,10);
            $("#ServiceIDForm_amt_money").val(money*month);
        }
    });
</script>
<?php
$language = Yii::app()->language;
$js = "
$('table').on('change','[id^=\"ServiceIDForm\"]',function() {
	var n=$(this).attr('id').split('_');
	$('#ServiceIDForm_'+n[1]+'_'+n[2]+'_uflag').val('Y');
});
";
Yii::app()->clientScript->registerScript('setFlag',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = <<<EOF
$('table').on('click','#btnDelRow', function() {
	$(this).closest('tr').find('[id*=\"_uflag\"]').val('D');
	$(this).closest('tr').hide();
	changeOutMonth();
});
EOF;
    Yii::app()->clientScript->registerScript('removeRow',$js,CClientScript::POS_READY);

    $js = <<<EOF
$(document).ready(function(){
	var ct = $('#tblDetail>tbody>tr:first').html();
	$('#dtltemplate').attr('value',ct);
	$('.deadline').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: '$language'});
});

$('#btnAddRow').on('click',function() {
	var r = $('#tblDetail tr').length;
	if (r>0) {
		var nid = '';
		var ct = $('#dtltemplate').val();
		$('#tblDetail tbody:last').append('<tr>'+ct+'</tr>');
		$('#tblDetail tr').eq(-1).find('[id*=\"ServiceIDForm_\"]').each(function(index) {
			var id = $(this).attr('id');
			var name = $(this).attr('name');

			var oi = 0;
			var ni = r;
			id = id.replace('_'+oi.toString()+'_', '_'+ni.toString()+'_');
			$(this).attr('id',id);
			name = name.replace('['+oi.toString()+']', '['+ni.toString()+']');
			$(this).attr('name',name);

		
			if (id.indexOf('_back_date') != -1){
			    $(this).attr('value','');
				$(this).datepicker({autoclose: true, format: 'yyyy/mm/dd',language: '$language'});
			}
			if (id.indexOf('_uflag') != -1) $(this).attr('value','Y');
			if (id.indexOf('_back_money') != -1) $(this).attr('value','');
			if (id.indexOf('_put_month') != -1) $(this).attr('value','');
			if (id.indexOf('_out_month') != -1) $(this).attr('value','');
			if (id.indexOf('_id') != -1) $(this).attr('value',0);
		});
		if (nid != '') {
			var topos = $('#'+nid).position().top;
			$('#tbl_detail').scrollTop(topos);
		}
	}
});
EOF;
    Yii::app()->clientScript->registerScript('addRow',$js,CClientScript::POS_READY);
}
?>
<?php
$buttons = array(
    TbHtml::button(Yii::t('service','New Service'),
        array(
            'name'=>'btnNew',
            'id'=>'btnNew',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"N"))
        )),
    TbHtml::button(Yii::t('service','Renew Service'),
        array(
            'name'=>'btnRenew',
            'id'=>'btnRenew',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"C"))
        )),
    TbHtml::button(Yii::t('service','Amend Service'),
        array(
            'name'=>'btnAmend',
            'id'=>'btnAmend',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"A"))
        )),
    TbHtml::button(Yii::t('service','Suspend Service'),
        array(
            'name'=>'btnSuspend',
            'id'=>'btnSuspend',
            'class'=>'btn btn-block btnChangeAdd',
            'data-type'=>'S',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"S"))
        )),
    TbHtml::button(Yii::t('service','Resume Service'),
        array(
            'name'=>'btnResume',
            'id'=>'btnResume',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"R"))
        )),
    TbHtml::button(Yii::t('service','Terminate Service'),
        array(
            'name'=>'btnTerminate',
            'id'=>'btnTerminate',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"T"))
        )),
);

$content = "";
foreach ($buttons as $button) {
    $content .= "<div class=\"row\"><div class=\"col-sm-10\">$button</div></div>";
}
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'addrecdialog',
    'header'=>Yii::t('service','Add Record'),
    'content'=>$content,
//					'footer'=>array(
//						TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
//					),
    'show'=>false,
));
?>

<?php if ($model->scenario!='new'&&!empty($model->service_new_id)): ?>
    <?php $this->renderPartial('//serviceID/historydialog',array('model'=>$model)); ?>
<?php endif ?>
<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('//site/lookup'); ?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'SERVICEID',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'),
));
?>

<?php
Script::genFileUpload($model,$form->id,'SERVICEID');
$link3 = Yii::app()->createAbsoluteUrl("service/getcusttypelist");
$js = <<<EOF
EOF;
Yii::app()->clientScript->registerScript('select2_1',$js,CClientScript::POS_READY);

$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$fields = ($model->status=='N' || $model->status=='C') ? array('contact'=>'ServiceFormID_cont_info',) : array();
$js = Script::genLookupButtonEx('btnCompany', 'company', 'company_id', 'company_name', $fields);
Yii::app()->clientScript->registerScript('lookupCompany',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnServiceB4', 'product', 'ServiceIDForm_b4_product_id', 'ServiceIDForm_b4_service');
Yii::app()->clientScript->registerScript('lookupServiceB4',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnService', 'product', 'ServiceIDForm_product_id', 'ServiceIDForm_service');
Yii::app()->clientScript->registerScript('lookupService',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnSalesman', 'staff', 'ServiceIDForm_salesman_id', 'ServiceIDForm_salesman');
Yii::app()->clientScript->registerScript('lookupSalesman',$js,CClientScript::POS_READY);
/*
$js = Script::genLookupButtonEx('btnOtherSalesman', 'staff', 'ServiceIDForm_othersalesman_id', 'ServiceIDForm_othersalesman');
Yii::app()->clientScript->registerScript('lookupOtherSalesman',$js,CClientScript::POS_READY);
*/
$js = Script::genLookupButtonEx('btnTechnician', 'staff', 'ServiceIDForm_technician_id', 'ServiceIDForm_technician');
Yii::app()->clientScript->registerScript('lookupTechnician',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnFirstTech', 'staff', 'ServiceIDForm_first_tech_id', 'ServiceIDForm_first_tech', array(), true);
Yii::app()->clientScript->registerScript('lookupFirstTech',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);

$js = "
$('#ServiceIDForm_ctrt_period,#ServiceIDForm_sign_dt').on('change',function() {
	var sign_dt = $('#ServiceIDForm_sign_dt').val();
	var p = $('#ServiceIDForm_ctrt_period').val();
	if (sign_dt && p) {
		var m = parseInt(p,10);
		var x = sign_dt.replace(/\//g,'-')
		var sd = new Date(x);
		$('#ServiceIDForm_ctrt_end_dt').val(addMonth(sd,m));
	}
});

function addMonth(d, m) {
    if (isNaN(d)) return '';
    d.setMonth(d.getMonth() + m);
    d.setDate(d.getDate() - 1);
    var Year=d.getFullYear();
    var Month=d.getMonth()+1;
    var Date=d.getDate();
    var result = Year+'/';
	result+=Month<10?'0'+Month:Month;
	result+='/';
	result+=Date<10?'0'+Date:Date;
	return result;
}
//addrecdialog
$('.btnChangeAdd').on('click',function(){
    var url = $(this).data('href');
    var id = $('#addrecdialog').data('id');
    window.document.location = url+'&id='+id;
});
$('#btnAdd').on('click',function(){
    $('#addrecdialog').data('id','');
	$('#addrecdialog').modal('show');
});
$('#btnCopy').on('click',function(){
    $('#addrecdialog').data('id','{$model->id}');
	$('#addrecdialog').modal('show');
});
";
Yii::app()->clientScript->registerScript('addRecord',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('serviceID/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'ServiceIDForm_status_dt',
        'ServiceIDForm_sign_dt',
        'ServiceIDForm_ctrt_end_dt',
        'ServiceIDForm_first_dt',
        'ServiceIDForm_equip_install_dt'
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js='
    var custTypeJson = '.CustomertypeIDForm::getCustTypeJson().';
    function changeCustTypeDiv(key,parentId){
        var selectText = $("#custTypeDiv>div").eq(key).find("select>option:selected").text();
        $("#custTypeDiv>div").each(function(index){
            if(index>key){
                var dataNum = $(this).data("num");
                var html ="";
                var num = index+1;
                $(this).data("num","");
                $.each(custTypeJson,function(key,val){
                    if(val["index_num"]==num&&val["cust_type_id"]==parentId){
                        html+="<option value="+val["id"];
                        if(val["id"]==dataNum){
                            html+=" selected ";
                        }
                        html+=" >"+val["name"]+"</option>";
                    }
                });
                $(this).children("select:first").html(html);
                if(html==""){
                    $(this).hide();
                    return false;
                }else{
                    $(this).show();
                    parentId = $(this).children("select:first").val();
                    selectText = $(this).children("select:first").children("option:selected").text();
                }
            }
        });
        $("#cust_type_end").val(selectText);
    }
    $("#custTypeDiv select").on("change",function(){
        var key = $("#custTypeDiv>div").index($(this).parent("div"));
        changeCustTypeDiv(key,$(this).val());
    });
    changeCustTypeDiv(0,$("#ServiceIDForm_cust_type").val());
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>
<?php $this->endWidget(); ?>


