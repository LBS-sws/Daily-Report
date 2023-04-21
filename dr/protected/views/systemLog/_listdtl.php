<tr class='clickable-row' data-href='<?php echo $this->getLink('D06', 'systemLog/edit', 'systemLog/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->record['log_code']; ?></td>
	<td><?php echo $this->record['log_date']; ?></td>
	<td><?php echo $this->record['log_user']; ?></td>
	<td><?php echo $this->record['city']; ?></td>
	<td><?php echo $this->record['log_type_name']; ?></td>
	<td><?php echo $this->record['option_str']; ?></td>
	<td>
        <div  data-toggle="tooltip" data-placement="left" title="<?php echo $this->record['option_text']; ?>">

            <?php echo $this->record['option_text_min']; ?>
        </div>
    </td>

    <td class="click-doc" data-id="<?php echo $this->record['id']; ?>" data-code="<?php echo $this->record['log_code']; ?>" data-num="<?php echo $this->record['doc_num']; ?>">
        <span class="badge"><?php echo $this->record['doc_num']; ?></span>
    </td>
</tr>
