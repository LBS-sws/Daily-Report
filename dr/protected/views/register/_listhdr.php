<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('station_name').$this->drawOrderArrow('station_name'),'#',$this->createOrderLink('register-list','station_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('email').$this->drawOrderArrow('email'),'#',$this->createOrderLink('register-list','email'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('register-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('register-list','status'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('lcd'),'#',$this->createOrderLink('register-list','lcd'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('lud').$this->drawOrderArrow('lud'),'#',$this->createOrderLink('register-list','lud'))
			;
		?>
	</th>
</tr>
