<tr>
	<th width=25% class='widget-leftalign'>
		<?php echo CHtml::label($this->getLabelName('service'), false); ?>
	</th>
	<th width=15% class='widget-leftalign'>
		<?php echo CHtml::label($this->getLabelName('freq'), false); ?>
	</th>
	<th width=10% class='widget-leftalign'>
		<?php echo CHtml::label($this->getLabelName('amt_month'), false); ?>
	</th>
	<th width=10% class='widget-leftalign'>
		<?php echo CHtml::label($this->getLabelName('sign_dt'), false); ?>
	</th>
	<th width=10% class='widget-leftalign'>
		<?php echo CHtml::label($this->getLabelName('ctrt_period'), false); ?>
	</th>
	<th width=10% class='widget-leftalign'>
		<?php echo CHtml::label($this->getLabelName('status'), false); ?>
	</th>
	<th width=20% class='widget'>
		&nbsp;
		<?php	if (Yii::app()->user->validRWFunction('A01'))
					echo CHtml::Button(Yii::t('misc','Add'), array(
						'submit'=>Yii::app()->createUrl('service/new', array('index'=>$this->model->id)))
				);			
		?>
	</th>
</tr>
