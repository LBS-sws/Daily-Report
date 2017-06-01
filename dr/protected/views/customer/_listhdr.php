<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('customer-list','city_name'))
			;
		?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('code'),'#',$this->createOrderLink('customer-list','code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('customer-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('cont_name').$this->drawOrderArrow('cont_name'),'#',$this->createOrderLink('customer-list','cont_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('cont_phone').$this->drawOrderArrow('cont_phone'),'#',$this->createOrderLink('customer-list','cont_phone'))
			;
		?>
	</th>
</tr>
