<tr class='clickable-row' data-href='<?php echo $this->getLink('C06', 'product/edit', 'product/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('C06', 'product/edit', 'product/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['description']; ?></td>
</tr>
