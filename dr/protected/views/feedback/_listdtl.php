<?php
	$idx = $this->recordptr + ($this->model->pageNum - 1) * $this->model->noOfItem;
	if (Yii::app()->user->validRWFunction('A08') && Yii::app()->user->id==$this->record['username']) {
		$lnk=Yii::app()->createUrl('feedback/edit',array('index'=>$this->record['id']));
		$icon = "glyphicon glyphicon-pencil";
	} else {
		$lnk=Yii::app()->createUrl('feedback/view',array('index'=>$this->record['id']));
		$icon = "glyphicon glyphicon-eye-open";	
	}
?>
<tr class='clickable-row' data-href='<?php echo $lnk;?>'>
	<td><?php echo "<a href=\"$lnk\"><span class=\"$icon\"></span></a>";?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['request_dt']; ?></td>
	<td><?php echo $this->record['feedbacker']; ?></td>
	<td><?php echo $this->record['feedback_dt']; ?></td>
	<td><?php echo $this->record['status']; ?></td>
	<td><?php echo $this->record['feedback_cat']; ?></td>
</tr>
