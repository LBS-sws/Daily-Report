<tr class='clickable-row' data-href='<?php echo $this->getLink('MM03', 'manageStopSet/edit', 'manageStopSet/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('MM03', 'manageStopSet/edit', 'manageStopSet/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['start_date']; ?></td>
	<td><?php echo $this->record['set_name']; ?></td>
</tr>
