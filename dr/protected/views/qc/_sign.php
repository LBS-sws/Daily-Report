<?php
	$button = TbHtml::button(Yii::t('qc','Clear'), array('name'=>'btnClearSignature','id'=>'btnClearSignature','class'=>'btn-sm',));
	$hidden = TbHtml::hiddenField('sign_target_field','');
	$content = <<<EOF
<div class='row'>
	<div class='col-sm-7'>
		$hidden
		<canvas id='qc-signature' class='signature-pad' style='border:1px solid black; width:330px; height:200px'></canvas>
		$button
	</div>
</div>
EOF;
					
	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'signdialog',
					'header'=>Yii::t('qc','Signatures'),
					'content'=>$content,
					'footer'=>array(
						TbHtml::button(Yii::t('dialog','OK'), 
								array(
									'id'=>'btnOkSignature',
									'data-dismiss'=>'modal',
									'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
								)
							),
						TbHtml::button(Yii::t('dialog','Close'), 
								array(
									'id'=>'btnCloseSignature',
									'data-dismiss'=>'modal',
									'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
								)
							),
					),
					'show'=>false,
				));
?>

<?php
$js = <<<EOF
var signaturePad = new SignaturePad(document.getElementById('qc-signature'));
$('#btnClearSignature').on('click',function(){
	signaturePad.clear();
});
$('#btnOkSignature').on('click',function(){
	var inputid = $('#sign_target_field').val();
	var data = signaturePad.toDataURL('image/png');
	$('#'+inputid).val(data);
	$('#'+inputid+'_img').show();
	$('#'+inputid+'_img').attr('src',data);
	signaturePad.clear();
});
EOF;
Yii::app()->clientScript->registerScript('signatureDialog',$js,CClientScript::POS_READY);
?>
