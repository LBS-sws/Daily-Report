<tr class='clickable-row' data-href='<?php echo $this->getLink('A06', 'qc/edit', 'qc/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('A06', 'qc/edit', 'qc/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['entry_dt']; ?></td>
	<td><?php echo $this->record['company_name']; ?></td>
	<td><?php echo $this->record['job_staff']; ?></td>
	<td><?php echo $this->record['team']; ?></td>
	<td><?php echo $this->record['qc_dt']; ?></td>
	<td><?php echo $this->record['qc_staff']; ?></td>
	<td><?php echo ($this->record['no_of_attm'] > 0) ? '<span class="fa fa-paperclip"></span>' : '&nbsp;';?></td>
</tr>
