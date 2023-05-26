<tr class='clickable-row' data-href='<?php echo $this->getLink('G11', 'lifeline/edit', 'lifeline/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('G11', 'lifeline/edit', 'lifeline/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['life_date']; ?></td>
	<td><?php echo $this->record['life_num']; ?></td>
</tr>
