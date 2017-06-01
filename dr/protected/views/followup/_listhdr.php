<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('follow-list','city_name'))
			;
		?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_dt').$this->drawOrderArrow('entry_dt'),'#',$this->createOrderLink('followup-list','entry_dt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('type').$this->drawOrderArrow('type'),'#',$this->createOrderLink('followup-list','type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_name').$this->drawOrderArrow('company_name'),'#',$this->createOrderLink('followup-list','company_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('content').$this->drawOrderArrow('content'),'#',$this->createOrderLink('followup-list','content'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('cont_info').$this->drawOrderArrow('cont_info'),'#',$this->createOrderLink('followup-list','cont_info'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('resp_staff').$this->drawOrderArrow('resp_staff'),'#',$this->createOrderLink('followup-list','resp_staff'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('resp_tech').$this->drawOrderArrow('resp_tech'),'#',$this->createOrderLink('followup-list','resp_tech'))
			;
		?>
	</th>
</tr>
