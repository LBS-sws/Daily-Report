
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

$content = '<div class="form-horizontal">';
$content.= '<div class="form-group">';
$content.= Tbhtml::label(Yii::t('misc','City'),'',array('class'=>"col-lg-4 control-label"));
$content.= '<div class="col-lg-4">';
$content.= Tbhtml::dropDownList("dialog_city", Yii::app()->user->city(),General::getCityListWithCityAllow(Yii::app()->user->city_allow()),
    array('id'=>"dialog_city",'empty'=>'')
);
$content.= '</div></div>';
$content.= '<div class="form-group">';
foreach ($buttons as $button) {
    $content .= "<div class=\"col-sm-10 col-sm-offset-1\">$button</div>";
}
$content.= '</div></div>';
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'addrecdialog',
    'header'=>Yii::t('misc','Add Record'),
    'content'=>$content,
//					'footer'=>array(
//						TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
//					),
    'show'=>false,
));

$actionStr=isset($actionStr)?$actionStr:"service";
$js="
$('#btnAdd').on('click',function() {
	$('#copy_index').val(0);
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
    var city = $('#dialog_city').val();
    var elm=$('#btnAdd');
    switch (arg) {
        case 'N':
            if (index==0||index==undefined)
                jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/new')."?city='+city,{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/new')."?city='+city+'&index='+index,{});
			break;
        case 'A':
            if (index==0||index==undefined)
                jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/amend')."?city='+city,{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/amend')."?city='+city+'&index='+index,{});
			break;
        case 'S':
            if (index==0||index==undefined)
                jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/suspend')."?city='+city,{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/suspend')."?city='+city+'&index='+index,{});
			break;
        case 'R':
            if (index==0||index==undefined)
                jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/resume')."?city='+city,{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/resume')."?city='+city+'&index='+index,{});
			break;
        case 'T':
            if (index==0||index==undefined)
                jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/terminate')."?city='+city,{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/terminate')."?city='+city+'&index='+index,{});
			break;
        case 'C':
            if (index==0||index==undefined)
                jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/renew')."?city='+city,{});
			else
				jQuery.yii.submitForm(elm,'".Yii::app()->createUrl($actionStr.'/renew')."?city='+city+'&index='+index,{});
			break;
    }
}
";
Yii::app()->clientScript->registerScript('addCityRecordBtn',$js,CClientScript::POS_READY);
?>

