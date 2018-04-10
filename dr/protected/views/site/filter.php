<?php
	$ftrbtn = array(
				TbHtml::button(Yii::t('dialog','Clear'), array('id'=>'btnSrchClear',
																'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
															)),
				TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnSrchOk',
																'data-dismiss'=>'modal',
																'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
															)),
				TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnSrchClose',
																'data-dismiss'=>'modal',
																'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
															)),
			);
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'filterdialog',
					'header'=>Yii::t('lead','Filter'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<?php 
// Dummy Button for include jQuery.yii.submitForm
echo TbHtml::button('dummyButton', array('style'=>'display:none','disabled'=>true,'submit'=>'#',));
?>		

<?php
	$fieldlist = $model->getFilterFieldList();
	
	$operlist = array(
					'like'=>Yii::t('misc','contain'),
					'='=>Yii::t('misc','equal'),
					'<>'=>Yii::t('misc','not equal'),
					'>='=>Yii::t('misc','greater or equal'),
					'>'=>Yii::t('misc','greater than'),
					'<='=>Yii::t('misc','less or equal'),
					'<'=>Yii::t('misc','less than'),
				);

	$session = Yii::app()->session;
	$filter = isset($session['criteria_a02']) ? json_decode($session['criteria_a02']) : array();

	for ($i=0; $i < 5; $i++) {
		$listdef = empty($filter) ? 'NA' : (isset($filter[$i]->field_id) ? $filter[$i]->field_id : 'NA');
		$operdef = empty($filter) ? 'like' : (isset($filter[$i]->operator) ? $filter[$i]->operator : 'like');
		$textdef = empty($filter) ? '' : (isset($filter[$i]->srchval) ? $filter[$i]->srchval : '');
		
		$list = TbHtml::dropDownList('srchfield_'.$i, $listdef, $fieldlist);
		$oper = TbHtml::dropDownList('srchoper_'.$i, $operdef, $operlist);
		$text = TbHtml::textField('srchvalue_'.$i, $textdef);
		
		$line = <<<EOF
<div class="row">
	<div class="col-md-4">$list</div>
	<div class="col-md-4">$oper</div>
	<div class="col-md-4">$text</div>
</div>
EOF;
		echo ($i==0 ? '' : '<hr>').$line;
	}
?>

<?php
	$this->endWidget(); 
?>

<?php
$link = Yii::app()->createAbsoluteUrl('entry/storecriteria');
$url = Yii::app()->createAbsoluteUrl('entry/'.(Yii::app()->user->validRWFunction('A02') ? 'edit' : 'view'),
		array('batch'=>$model->batch_code,));
$js = <<<EOF
$('#btnSrchOk').on('click', function() {
	var filter = [];
	var v = $('#last_resp').val();
	filter[filter.length] = {field_id:'last_resp', operator:'=', srchval:v};
	for (var i=1; i<5; i++) {
		var fld = $('#srchfield_'+i).val();
		var opr = $('#srchoper_'+i).val();
		var val = $('#srchvalue_'+i).val();
		if (fld!='NA' && val!='') {
			filter[filter.length] = {field_id:fld, operator:opr, srchval:val};
		}
	}
	$('#EntryForm_filter').val(JSON.stringify(filter));

	var formdata = $('#entry-form').serialize();
	$.ajax({
		type: 'POST',
		url: '$link',
		data: formdata,
		dataType: 'html',
		success: function(data) {
			if (data=='success') {
				window.location.href='$url';
			} else {
				alert('Error occured.');
			}
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});
EOF;
Yii::app()->clientScript->registerScript('filterok',$js,CClientScript::POS_READY);
?>

<?php
$link = Yii::app()->createAbsoluteUrl('entry/clearcriteria');
$js = <<<EOF
$('#btnSrchClear').on('click', function() {
	$.ajax({
		type: 'GET',
		url: '$link',
		dataType: 'html',
		success: function(data) {
			if (data=='success') {
				$('[id^="srchfield_"]').val('NA');
				$('[id^="srchoper_"]').val('like');
				$('[id^="srchvalue_"]').val('');
			} else {
				alert('Error occured.');
			}
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});
EOF;
Yii::app()->clientScript->registerScript('filterclear',$js,CClientScript::POS_READY);
?>

