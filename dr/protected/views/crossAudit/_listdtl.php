<tr class='clickable-row <?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink('CW02', 'crossAudit/edit', 'crossAudit/view', array('index'=>$this->record['id']));?>'>
    <td class="che">
        <?php if ($this->record['cross_bool']): ?>
            <input value="<?php echo $this->record['id']; ?>"  type="checkbox" class="checkOne">
        <?php endif ?>
    </td>
    <td><?php echo $this->drawEditButton('CW02', 'crossAudit/edit', 'crossAudit/view', array('index'=>$this->record['id'])); ?></td>

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
