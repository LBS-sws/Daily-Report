<tr class='clickable-row' data-href='<?php echo $this->getLink('C12', 'pestType/edit', 'pestType/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('C12', 'pestType/edit', 'pestType/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['pest_name']; ?></td>
	<td><?php echo $this->record['z_index']; ?></td>
	<td><?php echo $this->record['display_num']; ?></td>
</tr>
