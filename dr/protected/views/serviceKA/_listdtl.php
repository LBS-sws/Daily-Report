<tr class='clickable-row' data-href='<?php echo $this->getLink('A13', 'serviceKA/edit', 'serviceKA/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('A13', 'serviceKA/edit', 'serviceKA/view', array('index'=>$this->record['id']));?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['service_no']; ?></td>
	<td><?php echo $this->record['company_name']; ?></td>
	<td><?php echo $this->record['type_desc']; ?></td>
	<td><?php echo $this->record['nature_desc']; ?></td>
	<td><?php echo $this->record['service']; ?></td>
	<td><?php echo $this->record['cont_info']; ?></td>
	<td><?php echo $this->record['status']; ?></td>
	<td><?php echo $this->record['status_dt']; ?></td>
	<td><?php echo ($this->record['no_of_attm'] > 0) ? '<span class="fa fa-paperclip"></span>' : '&nbsp;';?></td>
</tr>
