<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('qc-list','city_name'))
			;
		?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_dt').$this->drawOrderArrow('entry_dt'),'#',$this->createOrderLink('qc-list','entry_dt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_name').$this->drawOrderArrow('company_name'),'#',$this->createOrderLink('qc-list','company_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('总分').$this->drawOrderArrow('qc_result'),'#',$this->createOrderLink('qc-list','qc_result'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('job_staff').$this->drawOrderArrow('job_staff'),'#',$this->createOrderLink('qc-list','job_staff'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('team').$this->drawOrderArrow('team'),'#',$this->createOrderLink('qc-list','team'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('qc_dt').$this->drawOrderArrow('qc_dt'),'#',$this->createOrderLink('qc-list','qc_dt'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('qc_staff').$this->drawOrderArrow('qc_staff'),'#',$this->createOrderLink('qc-list','qc_staff'))
			;
		?>
	</th>
	<th></th>
</tr>
