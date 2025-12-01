<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('code-list','a.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('code-list','a.name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('show_type').$this->drawOrderArrow('b.show_type'),'#',$this->createOrderLink('code-list','b.show_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_name').$this->drawOrderArrow('b.end_name'),'#',$this->createOrderLink('code-list','b.end_name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('b.z_index'),'#',$this->createOrderLink('code-list','b.z_index'))
        ;
        ?>
    </th>
</tr>
