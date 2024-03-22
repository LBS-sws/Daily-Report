<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('code'),'#',$this->createOrderLink('code-list','code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('code-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('ka_bool').$this->drawOrderArrow('ka_bool'),'#',$this->createOrderLink('code-list','ka_bool'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('region_name').$this->drawOrderArrow('region_name'),'#',$this->createOrderLink('code-list','region_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('incharge').$this->drawOrderArrow('incharge'),'#',$this->createOrderLink('code-list','incharge'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('SARANK').$this->drawOrderArrow('f.field_value'),'#',$this->createOrderLink('code-list','f.field_value'))
			;
		?>
	</th>
</tr>
