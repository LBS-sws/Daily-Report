<div class="form-group">
	<div class="col-sm-2">
<?php 
	$fieldid = get_class($model).'_rights_'.$idx.'_XX01';
	$fieldname = get_class($model)."[rights][".$idx."][XX01]";
	$fieldvalue = $model->rights[$idx]['XX01'];
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
	$fieldvalue = $model->extfields['onlibrole']['value'];
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
	$fieldvalue = $model->extfields['onlibrole']['type'];
	echo TbHtml::hiddenField($fieldname, $fieldvalue, array('id'=>$fieldid));
?>
<?php
	$fieldid = get_class($model).'_extfields_onlibrole_sysid';
	$fieldname = get_class($model)."[extfields][onlibrole][sysid]";
	$fieldvalue = $model->extfields['onlibrole']['sysid'];
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
$('#GroupForm_extfields_onlibrole_value').select2({
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