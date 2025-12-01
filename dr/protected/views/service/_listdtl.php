<tr class='clickable-row' data-href='<?php echo $this->getLink('A02', 'service/edit', 'service/view', array('index'=>$this->record['id']));?>'>
    <?php if (Yii::app()->user->validRWFunction('CW01')): ?>
        <!--交叉派单-->
        <td class="che">
            <?php if ($this->record['cross_bool']): ?>
                <input value="<?php echo $this->record['id']; ?>"  type="checkbox" class="checkOne">
            <?php endif ?>
        </td>
    <?php endif ?>

    <td><?php echo $this->drawEditButton('A02', 'service/edit', 'service/view', array('index'=>$this->record['id']));?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['office_name']; ?></td>
	<td><?php echo $this->record['company_name']; ?></td>
	<td><?php echo $this->record['type_desc']; ?></td>
	<td><?php echo $this->record['nature_desc']; ?></td>
	<td><?php echo $this->record['service']; ?></td>
	<td><?php echo $this->record['cont_info']; ?></td>
	<td><?php echo $this->record['status']; ?></td>
	<td><?php echo $this->record['status_dt']; ?></td>
	<td><?php echo ($this->record['no_of_attm'] > 0) ? '<span class="fa fa-paperclip"></span>' : '&nbsp;';?></td>
</tr>
