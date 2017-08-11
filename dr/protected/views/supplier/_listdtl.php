<tr class='clickable-row' data-href='<?php echo $this->getLink('A10', 'supplier/edit', 'supplier/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('A10', 'supplier/edit', 'supplier/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['full_name']; ?></td>
	<td><?php echo $this->record['cont_name']; ?></td>
	<td><?php echo $this->record['cont_phone']; ?></td>
</tr>
