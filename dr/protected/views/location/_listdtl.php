<tr class='clickable-row' data-href='<?php echo $this->getLink('C03', 'location/edit', 'location/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('C03', 'location/edit', 'location/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['description']; ?></td>
</tr>
