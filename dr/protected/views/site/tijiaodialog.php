<?php
	$content = "<p>".Yii::t('dialog','Confirmation Ti')."</p>";
	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'removedialog',
					'header'=>Yii::t('dialog','TiJiao'),
					'content'=>$content,
					'footer'=>array(
						TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnDeleteData','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
						TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
					),
					'show'=>false,
				));
?>