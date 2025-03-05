<tr class='clickable-row' data-href='<?php echo $this->getLink('MM02', 'manageStaffSet/edit', 'manageStaffSet/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('MM02', 'manageStaffSet/edit', 'manageStaffSet/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['start_date']; ?></td>
	<td><?php echo $this->record['employee_code']; ?></td>
	<td><?php echo $this->record['employee_name']; ?></td>
	<td><?php echo $this->record['city_allow_name']; ?></td>
	<td><?php echo $this->record['job_key']; ?></td>
</tr>
