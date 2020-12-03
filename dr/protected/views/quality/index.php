<?php
$this->pageTitle=Yii::app()->name . ' - Quality';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'quality-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('qc','Average score of quality inspection'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">

    <div class="btn-group" role="group">
        <?php
        echo TbHtml::button('dummyButton',array('style'=>'display:none','disabled'=>true,'submit'=>'#',));
        ?>
    </div>

	<?php 
		$search = array(
						'dt',
						'city',
						'job_staff'
					);
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('qc','Average score of quality inspection List'),
			'model'=>$model,
				'viewhdr'=>'//quality/_listhdr',
				'viewdtl'=>'//quality/_listdtl',
				'search'=>$search,
				'hasDateButton'=>true,
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

