<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('code-list','city_name'))
			;
		?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('description').$this->drawOrderArrow('description'),'#',$this->createOrderLink('code-list','description'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('type').$this->drawOrderArrow('type'),'#',$this->createOrderLink('code-list','type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('sales_products').$this->drawOrderArrow('sales_products'),'#',$this->createOrderLink('code-list','sales_products'))
			;
		?>
	</th>
</tr>
