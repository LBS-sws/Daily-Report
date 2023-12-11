<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('pest_name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('pestType-list','pest_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('z_index'),'#',$this->createOrderLink('pestType-list','z_index'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('display_num').$this->drawOrderArrow('display_num'),'#',$this->createOrderLink('pestType-list','display_num'))
			;
		?>
	</th>
</tr>
