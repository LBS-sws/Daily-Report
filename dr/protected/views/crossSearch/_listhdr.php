<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('old_city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('crossSearch-list','b.name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('table_type').$this->drawOrderArrow('a.table_type'),'#',$this->createOrderLink('crossSearch-list','a.table_type'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('company_name'),'#')
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('crossSearch-list','a.apply_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('month_amt').$this->drawOrderArrow('a.month_amt'),'#',$this->createOrderLink('crossSearch-list','a.month_amt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('cross_type').$this->drawOrderArrow('a.cross_type'),'#',$this->createOrderLink('crossSearch-list','a.cross_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('qualification_city').$this->drawOrderArrow('a.qualification_city'),'#',$this->createOrderLink('crossSearch-list','a.qualification_city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('qualification_ratio').$this->drawOrderArrow('a.qualification_ratio'),'#',$this->createOrderLink('crossSearch-list','a.qualification_ratio'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('cross_city').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('crossSearch-list','f.name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('rate_num').$this->drawOrderArrow('a.rate_num'),'#',$this->createOrderLink('crossSearch-list','a.rate_num'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status_type').$this->drawOrderArrow('a.status_type'),'#',$this->createOrderLink('crossSearch-list','a.status_type'))
        ;
        ?>
    </th>
</tr>
