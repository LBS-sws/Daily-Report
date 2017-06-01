<?php
$this->pageTitle=Yii::app()->name . ' - Station';
?>

<div class="form frame">
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'station-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
)); ?>

<div class="grid_17">
	<h2 class="page-heading"><?php echo Yii::t('station','Station'); ?></h2>
</div>
<div class="grid_5 prefix_2" style="text-align:right;"><h2 class="page-heading">
	&nbsp;
</div>
<div class="clear"></div>

<div class="grid_24 box" style="padding:0;">
	<div class="grid_24 omega alpha" style="background:#333;">
		<h2>
		<?php echo Yii::t('station','Station List'); ?>
		</h2>
	</div>
		<?php $this->widget('ext.layout.ListPageWidget', array(
				'model'=>$model,
				'viewhdr'=>'//station/_listhdr',
				'viewdtl'=>'//station/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>array(
							'station_id',
							'station_name',
							'city_name',
							'status',
						),
			));
		?>
</div>
<div class="clear"></div>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

</div><!-- form -->
