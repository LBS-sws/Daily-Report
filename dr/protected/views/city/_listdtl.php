<tr class='clickable-row' data-href='<?php echo $this->getLink('C01', 'city/edit', 'city/view', array('index'=>$this->record['code']));?>'>
	<td><?php echo $this->drawEditButton('C01', 'city/edit', 'city/view', array('index'=>$this->record['code'])); ?></td>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['ka_bool']; ?></td>
	<td><?php echo $this->record['region_name']; ?></td>
	<td><?php echo $this->record['incharge']; ?></td>
	<td><?php echo $this->record['SARANK']; ?></td>
	<td><?php echo $this->record['JD_city']; ?></td>
</tr>
