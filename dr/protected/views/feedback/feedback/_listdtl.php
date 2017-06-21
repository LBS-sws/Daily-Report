<?php
	$idx = $this->recordptr + ($this->model->pageNum - 1) * $this->model->noOfItem;
	if (Yii::app()->user->validRWFunction('A08') && Yii::app()->user->id==$this->record['username']) 
		$lnk=Yii::app()->createUrl('feedback/edit',array('index'=>$this->record['id']));
	else
		$lnk=Yii::app()->createUrl('feedback/view',array('index'=>$this->record['id']));
?>
<tr>
	<td width=15% class='widget-leftalign'><?php echo CHtml::link($this->record['request_dt'],$lnk); ?></td>
	<td width=20% class='widget-leftalign'><?php echo CHtml::link($this->record['feedbacker'],$lnk); ?></td>
	<td width=15% class='widget-leftalign'><?php echo CHtml::link($this->record['feedback_dt'],$lnk); ?></td>
	<td width=10% class='widget-leftalign'><?php echo CHtml::link($this->record['status'],$lnk); ?></td>
	<td width=40% class='widget-leftalign'><?php echo CHtml::link($this->record['feedback_cat'],$lnk); ?></td>
</tr>
