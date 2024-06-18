<tr class='clickable-row <?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink('CD02', 'crossAudit/edit', 'crossAudit/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('CD02', 'crossAudit/edit', 'crossAudit/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['table_type']; ?></td>
	<td><?php echo $this->record['contract_no']; ?></td>
	<td><?php echo $this->record['apply_date']; ?></td>
	<td><?php echo $this->record['month_amt']; ?></td>
	<td><?php echo $this->record['rate_num']; ?></td>
	<td><?php echo $this->record['rate_amt']; ?></td>
	<td><?php echo $this->record['old_city']; ?></td>
	<td><?php echo $this->record['cross_city']; ?></td>
	<td><?php echo $this->record['status_str']; ?></td>
</tr>
