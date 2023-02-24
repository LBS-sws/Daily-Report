<?php
$this->beginWidget('bootstrap.widgets.TbModal', array(
				'id'=>'helpdialog',
				'show'=>false,
			));
?>

	<div class="form-group">
		<div class="col-sm-12">
<?php
			echo CHtml::image(Yii::app()->request->baseUrl.'/images/help02.png','image',array('width'=>'100%','height'=>'auto','class'=>'responsive-image'));
?>
		</div>
	</div>
<?php
$this->endWidget(); 
?>

