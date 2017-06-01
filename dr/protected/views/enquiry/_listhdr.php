<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('enquiry-list','city_name'))
			;
		?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('contact_dt').$this->drawOrderArrow('contact_dt'),'#',$this->createOrderLink('enquiry-list','contact_dt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('customer').$this->drawOrderArrow('customer'),'#',$this->createOrderLink('enquiry-list','customer'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('type').$this->drawOrderArrow('type'),'#',$this->createOrderLink('enquiry-list','type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('source').$this->drawOrderArrow('source'),'#',$this->createOrderLink('enquiry-list','source'))
			;
		?>
	</th>
</tr>
