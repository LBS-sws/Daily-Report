<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('serviceID-list','city_name'));?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_no').$this->drawOrderArrow('a.service_no'),'#',$this->createOrderLink('serviceID-list','a.service_no'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('serviceID-list','f.name'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('type_desc').$this->drawOrderArrow('type_desc'),'#',$this->createOrderLink('serviceID-list','type_desc'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('nature_desc').$this->drawOrderArrow('nature_desc'),'#',$this->createOrderLink('serviceID-list','nature_desc'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service').$this->drawOrderArrow('a.service'),'#',$this->createOrderLink('serviceID-list','a.service'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('cont_info').$this->drawOrderArrow('a.cont_info'),'#',$this->createOrderLink('serviceID-list','a.cont_info'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('serviceID-list','a.status'));?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status_dt').$this->drawOrderArrow('a.status_dt'),'#',$this->createOrderLink('serviceID-list','a.status_dt'));?>
	</th>
	<th></th>
</tr>
