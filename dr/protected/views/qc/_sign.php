<style>
	.ratio-2-1{ position: relative;width: 100%;padding-top: 50%;}
	.ratio-2-1>div{ position: absolute;width: 100%;height: 100%;left: 0px;top: 0px;}
</style>

<?php
	$button = TbHtml::button(Yii::t('qc','Clear'), array('name'=>'btnClearSignature','id'=>'btnClearSignature','class'=>'btn-sm',));
	$button .= TbHtml::button(Yii::t('qc','Adjust'), array('name'=>'btnAlign','id'=>'btnAlign','class'=>'btn-sm',));
	$hidden = TbHtml::hiddenField('sign_target_field','');
	$content = <<<EOF
<div class='row'>
	<div class='col-lg-12'>
		<div class='ratio-2-1'>
			<div>
				<canvas id='qc-signature' class='signature-pad' style='border:1px solid black; width:100%; height:100%'></canvas>
			</div>
		</div>
		<div>
		$hidden
		$button
		</div>
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
function resizeCanvas() {
	var canvas = document.getElementById('qc-signature');
    var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    signaturePad.clear(); // otherwise isEmpty() might return incorrect value
}

$('#btnClearSignature').on('click',function(){
	signaturePad.clear();
});
$('#btnAlign').on('click',function(){
	resizeCanvas();
});
$('#btnOkSignature').on('click',function(){
	var inputid = $('#sign_target_field').val();
	var data = signaturePad.toDataURL('image/png');
	$('#'+inputid).val(data);
	$('#'+inputid+'_img').show();
	$('#'+inputid+'_img').attr('src',data);
	signaturePad.clear();
});

//window.addEventListener("resize", resizeCanvas);
//resizeCanvas();
//window.onresize = resizeCanvas;

$('#signdialog').on('shown.bs.modal', function (e) {
  $('#btnAlign').trigger('click');
})
EOF;
Yii::app()->clientScript->registerScript('signatureDialog',$js,CClientScript::POS_READY);
?>
