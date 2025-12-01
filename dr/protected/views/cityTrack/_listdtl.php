<tr class='clickable-row' data-href='<?php echo $this->getLink('G32', 'cityTrack/edit', 'cityTrack/view', array('index'=>$this->record['code']));?>'>
	<td><?php echo $this->drawEditButton('G32', 'cityTrack/edit', 'cityTrack/view', array('index'=>$this->record['code'])); ?></td>

    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['show_type']; ?></td>
	<td><?php echo $this->record['end_name']; ?></td>
	<td><?php echo $this->record['z_index']; ?></td>
</tr>
