<?php
	$typelist = General::getServiceTypeList(true);
	$listbox = TbHtml::dropDownList('lsttypelookup', '', $typelist);
	$label = TbHtml::label(Yii::t('qc','Service Type'),false,array('class'=>"col-sm-2 control-label"));
	$content = "<div class=\"row\">$label<div class=\"col-sm-10\">$listbox</div></div>";
	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'addrecdialog',
					'header'=>Yii::t('misc','Add Record'),
					'content'=>$content,
					'footer'=>array(
						TbHtml::button(Yii::t('dialog','OK'), 
								array(
									'id'=>'btnOk',
									'data-dismiss'=>'modal',
									'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
								)
							),
					),
					'show'=>false,
				));
?>

<?php
$url = Yii::app()->createAbsoluteUrl('qc/new');
$js = <<<EOF
	$('#btnOk').on('click', function() {
		var type = $('#lsttypelookup').val();
		window.location.href = '$url?type='+type;
	});
EOF;
Yii::app()->clientScript->registerScript('okClick',$js,CClientScript::POS_READY);
?>
