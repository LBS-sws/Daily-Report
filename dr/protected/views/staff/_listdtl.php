<tr class='clickable-row' data-href='<?php echo $this->getLink('A07', 'staff/edit', 'staff/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('A07', 'staff/edit', 'staff/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['position']; ?></td>
	<td><?php echo $this->record['email']; ?></td>
</tr>
