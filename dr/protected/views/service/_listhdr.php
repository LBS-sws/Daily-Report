<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('service-list','city_name'));?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_name').$this->drawOrderArrow('company_name'),'#',$this->createOrderLink('service-list','company_name'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('type_desc').$this->drawOrderArrow('type_desc'),'#',$this->createOrderLink('service-list','type_desc'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('nature_desc').$this->drawOrderArrow('nature_desc'),'#',$this->createOrderLink('service-list','nature_desc'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service').$this->drawOrderArrow('service'),'#',$this->createOrderLink('service-list','service'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('cont_info').$this->drawOrderArrow('cont_info'),'#',$this->createOrderLink('service-list','cont_info'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('service-list','status'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status_dt').$this->drawOrderArrow('status_dt'),'#',$this->createOrderLink('service-list','status_dt'));?>
	</th>
	<th></th>
</tr>
