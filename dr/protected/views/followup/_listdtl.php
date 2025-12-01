<tr class='clickable-row' data-href='<?php echo $this->getLink('A03', 'followup/edit', 'followup/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('A03', 'followup/edit', 'followup/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['entry_dt']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
	<td><?php echo $this->record['company_name']; ?></td>
	<td><?php echo $this->record['content']; ?></td>
	<td><?php echo $this->record['cont_info']; ?></td>
	<td><?php echo $this->record['resp_staff']; ?></td>
	<td><?php echo $this->record['resp_tech']; ?></td>
	<td><?php echo $this->record['fp_fin_dt']; ?></td>
</tr>
