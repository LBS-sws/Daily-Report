<tr class='clickable-row' data-href='<?php echo $this->getLink('G14', 'citySet/edit', 'citySet/view', array('index'=>$this->record['code']));?>'>
	<td><?php echo $this->drawEditButton('G14', 'citySet/edit', 'citySet/view', array('index'=>$this->record['code'])); ?></td>

    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['show_type']; ?></td>
	<td><?php echo $this->record['region_name']; ?></td>
	<td><?php echo $this->record['add_type']; ?></td>
	<td><?php echo $this->record['z_index']; ?></td>
</tr>
