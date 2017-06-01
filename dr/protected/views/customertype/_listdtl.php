<tr class='clickable-row' data-href='<?php echo $this->getLink('C02', 'customertype/edit', 'customertype/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('C02', 'customertype/edit', 'customertype/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['description']; ?></td>
</tr>
