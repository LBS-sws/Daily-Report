<?php
	$flow = 'service/view';
	$lnk=Yii::app()->createUrl($flow,array('index'=>$this->record['id']));
?>

<tr>
	<td width=25% class='widget-leftalign'>
		<?php echo CHtml::link($this->record['service'],$lnk); ?>
	</td>
	<td width=15% class='widget-leftalign'>
		<?php echo CHtml::link($this->record['freq'],$lnk); ?>
	</td>
	<td width=10% class='widget-leftalign'>
		<?php echo CHtml::link($this->record['amt_month'],$lnk); ?>
	</td>
	<td width=10% class='widget-leftalign'>
		<?php echo CHtml::link($this->record['sign_dt'],$lnk); ?>
	</td>
	<td width=10% class='widget-leftalign'>
		<?php echo CHtml::link($this->record['ctrt_period'],$lnk); ?>
	</td>
	<td width=10% class='widget-leftalign'>
		<?php echo CHtml::link(General::getStatusDesc($this->record['status']),$lnk); ?>
	</td>
	<td width=20% class='widget-leftalign'>
		<?php 
			if (Yii::app()->user->validRWFunction('A01') && $this->model->scenario!='view') { 
				echo CHtml::Button(Yii::t('misc','Edit'), array(
					'submit'=>Yii::app()->createUrl('service/edit', array('index'=>$this->record['id'])))
				);
				if ($this->record['status']=='S') {
					echo CHtml::Button(Yii::t('misc','Resume'), array(
						'submit'=>Yii::app()->createUrl('service/resume', array('index'=>$this->record['id'])))
					);
				} else {
					echo CHtml::Button(Yii::t('misc','Amend'), array(
						'submit'=>Yii::app()->createUrl('service/amend', array('index'=>$this->record['id'])))
					);
					echo CHtml::Button(Yii::t('misc','Suspend'), array(
						'submit'=>Yii::app()->createUrl('service/suspend', array('index'=>$this->record['id'])))
					);
				}
			}
		?>
		&nbsp;
		<?php echo CHtml::hiddenField($this->getFieldName('id'),$this->record['id']); ?>
	</td>
</tr>
