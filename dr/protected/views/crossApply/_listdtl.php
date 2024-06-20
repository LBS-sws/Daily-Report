<tr class='clickable-row <?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink('CD01', 'crossApply/edit', 'crossApply/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('CD01', 'crossApply/edit', 'crossApply/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['old_city']; ?></td>
    <td><?php echo $this->record['table_type']; ?></td>
	<td><?php echo $this->record['company_name']; ?></td>
	<td><?php echo $this->record['apply_date']; ?></td>
	<td><?php echo $this->record['month_amt']; ?></td>
	<td><?php echo $this->record['cross_type_name']; ?></td>
	<td><?php echo $this->record['qualification_city']; ?></td>
	<td><?php echo $this->record['qualification_ratio']; ?></td>
	<td><?php echo $this->record['cross_city']; ?></td>
    <td><?php echo $this->record['rate_num']; ?></td>
	<td><?php echo $this->record['status_str']; ?></td>
</tr>
