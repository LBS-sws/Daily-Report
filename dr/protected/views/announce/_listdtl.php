<tr class='clickable-row' data-href='<?php echo $this->getLink('D05', 'announce/edit', 'announce/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('D05', 'announce/edit', 'announce/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['start_dt']; ?></td>
	<td><?php echo $this->record['end_dt']; ?></td>
	<td><?php echo $this->record['priority']; ?></td>
</tr>

