<tr>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_code').$this->drawOrderArrow('a.log_code'),'#',$this->createOrderLink('systemLog-list','a.log_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_date').$this->drawOrderArrow('a.log_date'),'#',$this->createOrderLink('systemLog-list','a.log_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_user').$this->drawOrderArrow('a.log_user'),'#',$this->createOrderLink('systemLog-list','a.log_user'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('systemLog-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_type_name').$this->drawOrderArrow('a.log_type_name'),'#',$this->createOrderLink('systemLog-list','a.log_type_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('option_str').$this->drawOrderArrow('a.option_str'),'#',$this->createOrderLink('systemLog-list','a.option_str'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('option_text').$this->drawOrderArrow('a.option_text'),'#',$this->createOrderLink('systemLog-list','a.option_text'))
			;
		?>
	</th>
    <th>&nbsp;</th>
</tr>
