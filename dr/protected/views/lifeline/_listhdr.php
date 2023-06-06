<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('code-list','city_name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('life_date').$this->drawOrderArrow('a.life_date'),'#',$this->createOrderLink('code-list','a.life_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('life_num').$this->drawOrderArrow('a.life_num'),'#',$this->createOrderLink('code-list','a.life_num'))
			;
		?>
	</th>
</tr>
