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
	<th>
		<?php echo TbHtml::link($this->getLabelName('u_id').$this->drawOrderArrow('u_id'),'#',$this->createOrderLink('code-list','u_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_display').$this->drawOrderArrow('z_display'),'#',$this->createOrderLink('code-list','z_display'))
			;
		?>
	</th>
</tr>
