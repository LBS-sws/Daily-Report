<tr class='clickable-row' data-href='<?php echo $this->getLink('A09', 'month/view', 'month/view', array('index'=>$this->record['id'],'city'=>$this->record['cityname']));?>'>
	<td><?php echo $this->drawEditButton('A09', 'month/view', 'month/view', array('index'=>$this->record['id'],'city'=>$this->record['cityname']));?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['year_no']; ?></td>
	<td><?php echo $this->record['month_no']; ?></td>
    <td><?php echo $this->record['f74']; ?></td>
    <td><?php echo $this->record['f86']; ?></td>
    <td><?php echo $this->record['f94']; ?></td>
    <td><?php echo $this->record['f100']; ?></td>
    <td><?php echo $this->record['f115']; ?></td>
    <td><?php echo $this->record['f73']; ?></td>
</tr>
