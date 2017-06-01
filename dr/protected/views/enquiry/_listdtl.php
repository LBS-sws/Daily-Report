<tr class='clickable-row' data-href='<?php echo $this->getLink('A04', 'enquiry/edit', 'enquiry/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('A04', 'enquiry/edit', 'enquiry/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['contact_dt']; ?></td>
	<td><?php echo $this->record['customer']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
	<td><?php echo $this->record['source']; ?></td>
</tr>
