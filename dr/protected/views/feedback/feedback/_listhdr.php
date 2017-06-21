<tr>
	<th width=15% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('request_dt'),'#',$this->createOrderLink('feedback-list','request_dt'))
			.$this->drawOrderArrow('request_dt');
		?>
	</th>
	<th width=20% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('feedbacker'),'#',$this->createOrderLink('feedback-list','feedbacker'))
			.$this->drawOrderArrow('feedbacker');
		?>
	</th>
	<th width=15% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('feedback_dt'),'#',$this->createOrderLink('feedback-list','feedback_dt'))
			.$this->drawOrderArrow('feedback_dt');
		?>
	</th>
	<th width=10% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('status'),'#',$this->createOrderLink('feedback-list','status'))
			.$this->drawOrderArrow('status');
		?>
	</th>
	<th width=40% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('feedback_cat'),'#',$this->createOrderLink('feedback-list','feedback_cat'))
			.$this->drawOrderArrow('feedback_cat');
		?>
	</th>
</tr>
