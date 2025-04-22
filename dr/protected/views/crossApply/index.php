<?php
$this->pageTitle=Yii::app()->name . ' - CrossApply';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'crossApply-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Cross Apply'); ?></strong>
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
    <div class="box hide">
        <div class="box-body">
            <p class="text-danger">
                <?php $this->renderPartial('//crossApply/crossNote'); ?>
            </p>
        </div>
    </div>
	<?php
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('service','My Cross Apply'),
        'model'=>$model,
        'viewhdr'=>'//crossApply/_listhdr',
        'viewdtl'=>'//crossApply/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>array(
            'contract_no',
            'company_name',
            'apply_date',
            'old_city',
            'cross_city',
            'status_type',
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
