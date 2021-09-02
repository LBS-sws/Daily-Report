<tr class='clickable-row' data-href='<?php echo $this->getLink('C09', 'payWeek/edit', 'payWeek/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('C09', 'payWeek/edit', 'payWeek/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['description']; ?></td>
</tr>
