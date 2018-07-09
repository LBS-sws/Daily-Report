<tr class='clickable-row' data-href='<?php echo $this->getLink('A10', 'supplier/edits', 'supplier/views', array('index'=>$this->record['id']));?>'>
    <td><?php echo $this->drawEditButton('A10', 'supplier/edits', 'supplier/views', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['req_dt']; ?></td>
    <td><?php echo $this->record['ref_no']; ?></td>
    <td><?php echo $this->record['trans_type_desc']; ?></td>
    <td><?php echo $this->record['bank']; ?></td>
    <td><?php echo $this->record['amount']; ?></td>
    <td><?php echo $this->record['item_desc']; ?></td>
</tr>
