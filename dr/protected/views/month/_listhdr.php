<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('城市'))
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
        <?php echo TbHtml::link($this->getLabelName('销售部分数'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('外勤部分数'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('财务部分数'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('营运部分数'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('人事部分数'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('总分'))
        ;
        ?>
    </th>
</tr>
