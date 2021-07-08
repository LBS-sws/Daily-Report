<div class="form-group">
	<div class="col-sm-2">
<?php 
	$fieldid = get_class($model).'_rights_'.$idx.'_XX01';
	$fieldname = get_class($model)."[rights][".$idx."][XX01]";
	$fieldvalue = isset($model->rights[$idx]['XX01']) ? $model->rights[$idx]['XX01'] : '';
	echo TbHtml::label($model->functionLabels('drs','System Use'), $fieldid);
?>
	</div>
	<div class="col-sm-2">
<?php
	echo TbHtml::dropDownList($fieldname, $fieldvalue, array('CN'=>Yii::t('misc','On'), 'NA'=>Yii::t('misc','Off'),),
								array('disabled'=>($model->scenario=='view')));
?>
	</div>
</div>
<div class="form-group">
	<div class="col-sm-2">
<?php
	$fieldid = get_class($model).'_extfields_onlibrole_value';
	$fieldname = get_class($model)."[extfields][onlibrole][value][]";
	$fieldvalue = isset($model->extfields['onlibrole']['value']) ? $model->extfields['onlibrole']['value'] : '';
	echo TbHtml::label(Yii::t('external','User Role'), $fieldid);
?>
	</div>
	<div class="col-sm-4">
<?php
	$roletype = UserFormEx::roleListOnlib();
	echo TbHtml::dropDownList($fieldname, $fieldvalue, $roletype,
								array('class'=>'select2','multiple'=>'multiple','style'=>'width:100%'));
?>
<?php
	$fieldid = get_class($model).'_extfields_onlibrole_type';
	$fieldname = get_class($model)."[extfields][onlibrole][type]";
	$fieldvalue = isset($model->extfields['onlibrole']['type']) ? $model->extfields['onlibrole']['type'] : '';
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_extfields_onlibuser_value';
	$fieldname = get_class($model)."[extfields][onlibuser][value]";
	$fieldvalue = isset($model->extfields['onlibuser']['value']) ? $model->extfields['onlibuser']['value'] : '';
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_extfields_onlibuser_type';
	$fieldname = get_class($model)."[extfields][onlibuser][type]";
	$fieldvalue = isset($model->extfields['onlibuser']['type']) ? $model->extfields['onlibuser']['type'] : '';
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_oriextfields_onlibuser_type';
	$fieldname = get_class($model)."[oriextfields][onlibuser][type]";
	$fieldvalue = isset($model->oriextfields['onlibuser']['type']) ? $model->oriextfields['onlibuser']['type'] : '';
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_oriextfields_onlibuser_value';
	$fieldname = get_class($model)."[oriextfields][onlibuser][value]";
	$fieldvalue = isset($model->oriextfields['onlibuser']['value']) ? $model->oriextfields['onlibuser']['value'] : '';
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_oriextfields_onlibrole_type';
	$fieldname = get_class($model)."[oriextfields][onlibrole][type]";
	$fieldvalue = isset($model->oriextfields['onlibrole']['type']) ? $model->oriextfields['onlibrole']['type'] : '';
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_oriextfields_onlibrole_value';
	$fieldname = get_class($model)."[oriextfields][onlibrole][value]";
	$fieldvalue = json_encode((isset($model->oriextfields['onlibrole']['value']) ? $model->oriextfields['onlibrole']['value'] : ''));
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_oriextrights_onlib_XX01';
	$fieldname = get_class($model)."[oriextrights][onlib][XX01]";
	$fieldvalue = isset($model->oriextrights['onlib']['XX01']) ? $model->oriextrights['onlib']['XX01'] : '';
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
	</div>
</div>

<?php
switch(Yii::app()->language) {
	case 'zh_cn': $lang = 'zh-CN'; break;
	case 'zh_tw': $lang = 'zh-TW'; break;
	default: $lang = Yii::app()->language;
}
$disabled = ($model->scenario!='view') ? 'false' : 'true';
	$js = <<<EOF
$('#UserForm_extfields_onlibrole_value').select2({
	tags: true,
	multiple: true,
	maximumInputLength: 0,
	maximumSelectionLength: 10,
	allowClear: true,
	language: '$lang',
	disabled: $disabled
});
EOF;
Yii::app()->clientScript->registerScript('select2_onlib',$js,CClientScript::POS_READY);
?>