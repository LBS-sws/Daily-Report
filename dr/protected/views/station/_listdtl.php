<?php
	$idx = $this->recordptr + ($this->model->pageNum - 1) * $this->model->noOfItem;
	$flow = Yii::app()->user->validRWFunction('D03') ? 'station/edit' : 'station/view';
	$lnk=Yii::app()->createUrl($flow,array('index'=>$this->record['station_id']));
?>
<tr>
	<td width=20% class='widget-leftalign'><?php echo CHtml::link($this->record['station_id'],$lnk); ?></td>
	<td width=20% class='widget-leftalign'><?php echo CHtml::link($this->record['station_name'],$lnk); ?></td>
	<td width=15% class='widget-leftalign'><?php echo CHtml::link($this->record['city_name'],$lnk); ?></td>
	<td width=10% class='widget-leftalign'><?php echo CHtml::link($this->record['status'],$lnk); ?></td>
	<td width=35% class='widget-leftalign'>&nbsp;</td>
</tr>
