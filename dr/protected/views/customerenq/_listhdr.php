<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('c.name'),'#',$this->createOrderLink('customer-enq','c.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('customer-enq','a.code'))
			;
		?>
	</th>
	<th colspan=4>
		<?php echo TbHtml::link($this->getLabelName('company_name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('customer-enq','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('customer-enq','a.status'))
			;
		?>
	</th>
</tr>
