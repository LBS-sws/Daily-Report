<tr class='clickable-row' data-href='<?php echo $this->getLink('C04', 'task/edit', 'task/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('C04', 'task/edit', 'task/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['description']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
	<td><?php echo $this->record['sales_products']; ?></td>
</tr>
