<?php
	$idx = $this->recordptr + ($this->model->pageNum - 1) * $this->model->noOfItem;
	if (Yii::app()->user->validRWFunction('D04') && $this->record['status']==Yii::t('register','Pending')) {
		$lnk=Yii::app()->createUrl('register/edit',array('index'=>$this->record['req_key'],'timestamp'=>strtotime($this->record['lud2'])));
		$icon = "glyphicon glyphicon-pencil";
	} else {
		$lnk=Yii::app()->createUrl('register/view',array('index'=>$this->record['req_key']));
		$icon = "glyphicon glyphicon-eye-open";	
	}
?>
<tr>
	<td width=15% class='widget-leftalign'><?php echo CHtml::link($this->record['station_name'],$lnk); ?></td>
	<td width=20% class='widget-leftalign'><?php echo CHtml::link($this->record['email'],$lnk); ?></td>
	<td width=15% class='widget-leftalign'><?php echo CHtml::link($this->record['city'],$lnk); ?></td>
	<td width=10% class='widget-leftalign'><?php echo CHtml::link($this->record['status'],$lnk); ?></td>
	<td width=15% class='widget-leftalign'><?php echo CHtml::link($this->record['lcd'],$lnk); ?></td>
	<td width=15% class='widget-leftalign'><?php echo CHtml::link($this->record['lud'],$lnk); ?></td>
	<td width=10% class='widget-leftalign'>&nbsp;</td>
</tr>
<tr class='clickable-row' data-href='<?php echo $lnk;?>'>
	<td><?php echo "<a href=\"$lnk\"><span class=\"$icon\"></span></a>";?></td>
	<td><?php echo $this->record['station_name']; ?></td>
	<td><?php echo $this->record['email']; ?></td>
	<td><?php echo $this->record['city']; ?></td>
	<td><?php echo $this->record['status']; ?></td>
	<td><?php echo $this->record['lcd']; ?></td>
	<td><?php echo $this->record['lud']; ?></td>
</tr>
