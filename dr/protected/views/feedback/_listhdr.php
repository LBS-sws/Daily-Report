<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('feedback-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('request_dt').$this->drawOrderArrow('request_dt'),'#',$this->createOrderLink('feedback-list','request_dt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('feedbacker').$this->drawOrderArrow('feedbacker'),'#',$this->createOrderLink('feedback-list','feedbacker'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('feedback_dt').$this->drawOrderArrow('feedback_dt'),'#',$this->createOrderLink('feedback-list','feedback_dt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('feedback-list','status'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('feedback_cat').$this->drawOrderArrow('feedback_cat'),'#',$this->createOrderLink('feedback-list','feedback_cat'))
			;
		?>
	</th>
</tr>
