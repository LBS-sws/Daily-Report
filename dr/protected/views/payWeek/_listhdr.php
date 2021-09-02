<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('code'),'#',$this->createOrderLink('code-list','code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('description').$this->drawOrderArrow('description'),'#',$this->createOrderLink('code-list','description'))
			;
		?>
	</th>
</tr>
