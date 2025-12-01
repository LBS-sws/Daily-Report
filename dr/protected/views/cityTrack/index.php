<?php
$this->pageTitle=Yii::app()->name . ' - CityTrack';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'code-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','City Track Set'); ?></strong>
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

	<?php
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('summary','City List'),
        'model'=>$model,
        'viewhdr'=>'//cityTrack/_listhdr',
        'viewdtl'=>'//cityTrack/_listdtl',
        'search'=>array(
            'code',
            'city_name',
            'end_name',
        ),
    ));
	?>
</section>
<?php
echo TbHtml::button("",array("class"=>"hide","submit"=>"#"));
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


