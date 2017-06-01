<tr>
	<th width=20% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('station_id'),'#',$this->createOrderLink('station-list','station_id'))
			.$this->drawOrderArrow('station_id');
		?>
	</th>
	<th width=20% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('station_name'),'#',$this->createOrderLink('station-list','station_name'))
			.$this->drawOrderArrow('station_name');
		?>
	</th>
	<th width=15% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('city_name'),'#',$this->createOrderLink('station-list','city_name'))
			.$this->drawOrderArrow('city_name');
		?>
	</th>
	<th width=10% class='widget-leftalign'>
		<?php echo CHtml::link($this->getLabelName('status'),'#',$this->createOrderLink('station-list','status'))
			.$this->drawOrderArrow('status');
		?>
	</th>
	<th width=35% class='widget-leftalign'>
		&nbsp;
	</th>
</tr>
