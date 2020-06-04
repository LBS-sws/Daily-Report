<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('year_no').$this->drawOrderArrow('year_no'),'#',$this->createOrderLink('','year_no'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('month_no').$this->drawOrderArrow('month_no'),'#',$this->createOrderLink('monthly-list','month_no'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('sales dep'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('tech. dep'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('acc. dep'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('ops. dep'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('hr. dep'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('total score'))
        ;
        ?>
    </th>
</tr>
