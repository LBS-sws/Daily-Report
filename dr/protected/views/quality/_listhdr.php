<tr>
<!--	<th></th>-->
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('quality-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('dt').$this->drawOrderArrow('dt'),'#',$this->createOrderLink('quality-list','dt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('job_staff').$this->drawOrderArrow('job_staff'),'#',$this->createOrderLink('quality-list','job_staff'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('result').$this->drawOrderArrow('result'),'#',$this->createOrderLink('quality-list','result'))
			;
		?>
	</th>

</tr>
