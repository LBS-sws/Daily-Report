<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('description').$this->drawOrderArrow('description'),'#',$this->createOrderLink('code-list','description'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('sales_rate').$this->drawOrderArrow('sales_rate'),'#',$this->createOrderLink('code-list','sales_rate'))
			;
		?>
	</th>
</tr>
