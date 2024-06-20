<?php
$this->pageTitle=Yii::app()->name . ' - CrossSearch';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'crossSearch-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Cross Search'); ?></strong>
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
    <?php $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Cross Apply'),
        'model'=>$model,
        'viewhdr'=>'//crossSearch/_listhdr',
        'viewdtl'=>'//crossSearch/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>array(
            'contract_no',
            'company_name',
            'apply_date',
            'old_city',
            'cross_city'
        ),
    ));
    ?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');

echo TbHtml::button("aa",array("submit"=>"#","class"=>"hide"));
?>
<?php $this->endWidget(); ?>

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
