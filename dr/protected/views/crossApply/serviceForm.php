
<?php
if($model->table_type==0){
    $serviceModel = new ServiceForm("view");
    $serviceModel->retrieveData($model->service_id,false);
    $serviceModel->cust_type=GetNameToId::getCustOneNameForId($serviceModel->cust_type);
    $serviceModel->cust_type_name=GetNameToId::getCustTwoNameForId($serviceModel->cust_type_name);
}else{
    $serviceModel = new ServiceKAForm("view");
    $serviceModel->retrieveData($model->service_id,false);
}
$serviceModel->amt_paid=$model->old_month_amt;
$serviceModel->nature_type=GetNameToId::getNatureOneNameForId($serviceModel->nature_type);
$serviceModel->nature_type_two=GetNameToId::getNatureTwoNameForId($serviceModel->nature_type_two);
?>

<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('status'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::hiddenField('service[id]',$serviceModel->id); ?>
        <?php echo Tbhtml::textField('service[status]',GetNameToId::getServiceStatusForKey($serviceModel->status),array('readonly'=>true)); ?>
    </div>
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('contract_no'),'',array('class'=>"col-lg-1 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[contract_no]',$serviceModel->contract_no,array('readonly'=>true)); ?>
    </div>
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('office_id'),'',array('class'=>"col-lg-1 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[office_id]',GetNameToId::getOfficeNameForID($serviceModel->office_id),array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('status_dt'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[status_dt]',$serviceModel->status_dt,array('readonly'=>true,'prepend'=>'<span class="fa fa-calendar"></span>')); ?>
    </div>
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('prepay_month'),'',array('class'=>"col-lg-1 control-label")); ?>
    <div class="col-lg-1">
        <?php echo Tbhtml::textField('service[prepay_month]',$serviceModel->prepay_month,array('readonly'=>true)); ?>
    </div>
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('prepay_start'),'',array('class'=>"col-lg-1 control-label")); ?>
    <div class="col-lg-1">
        <?php echo Tbhtml::textField('service[prepay_start]',$serviceModel->prepay_start,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('company_name'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('service[company_name]',$serviceModel->company_name,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('cust_type'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[cust_type]',$serviceModel->cust_type,array('readonly'=>true)); ?>
    </div>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[cust_type_name]',$serviceModel->cust_type_name,array('readonly'=>true)); ?>
    </div>
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('pieces'),'',array('class'=>"col-lg-1 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[pieces]',$serviceModel->pieces,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('nature_type'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[nature_type]',$serviceModel->nature_type,array('readonly'=>true)); ?>
    </div>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[nature_type_two]',$serviceModel->nature_type_two,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('service'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('service[service]',$serviceModel->service,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('paid_type'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[paid_type]',GetNameToId::getPaidTypeForId($serviceModel->paid_type),array('readonly'=>true)); ?>
    </div>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[amt_paid]',$serviceModel->amt_paid,array('readonly'=>true,'prepend'=>'<span class="fa fa-cny"></span>')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('amt_install'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[amt_install]',$serviceModel->amt_install,array('readonly'=>true,'prepend'=>'<span class="fa fa-cny"></span>')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('need_install'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[need_install]',GetNameToId::getNeedInstallForId($serviceModel->need_install),array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('all_number'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[all_number]',$serviceModel->all_number,array('readonly'=>true,'prepend'=>'<span class="fa fa-cny"></span>')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('salesman'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('service[salesman]',$serviceModel->salesman,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('othersalesman'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('service[othersalesman]',$serviceModel->othersalesman,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('technician'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('service[technician]',$serviceModel->technician,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('sign_dt'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[sign_dt]',$serviceModel->sign_dt,array('readonly'=>true,'prepend'=>'<span class="fa fa-calendar"></span>')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('ctrt_period'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[ctrt_period]',$serviceModel->ctrt_period,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('ctrt_end_dt'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[ctrt_end_dt]',$serviceModel->ctrt_end_dt,array('readonly'=>true,'prepend'=>'<span class="fa fa-calendar"></span>')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('cont_info'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('service[cont_info]',$serviceModel->cont_info,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('first_dt'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[first_dt]',$serviceModel->first_dt,array('readonly'=>true,'prepend'=>'<span class="fa fa-calendar"></span>')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('first_tech'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textField('service[first_tech]',$serviceModel->first_tech,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('equip_install_dt'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-3">
        <?php echo Tbhtml::textField('service[equip_install_dt]',$serviceModel->equip_install_dt,array('readonly'=>true,'prepend'=>'<span class="fa fa-calendar"></span>')); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('remarks2'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textArea('service[remarks2]',$serviceModel->remarks2,array('readonly'=>true,'rows'=>4)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('remarks'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-7">
        <?php echo Tbhtml::textArea('service[remarks]',$serviceModel->remarks,array('readonly'=>true,'rows'=>4)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('u_system_id'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[u_system_id]',$serviceModel->u_system_id,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('lcu'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[lcu]',$serviceModel->lcu,array('readonly'=>true)); ?>
    </div>
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('lcd'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[lcd]',$serviceModel->lcd,array('readonly'=>true)); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('luu'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[luu]',$serviceModel->luu,array('readonly'=>true)); ?>
    </div>
    <?php echo Tbhtml::label($serviceModel->getAttributeLabel('lud'),'',array('class'=>"col-lg-2 control-label")); ?>
    <div class="col-lg-2">
        <?php echo Tbhtml::textField('service[lud]',$serviceModel->lud,array('readonly'=>true)); ?>
    </div>
</div>