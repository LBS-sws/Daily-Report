<?php
$this->pageTitle=Yii::app()->name . ' - RankMonth';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'rankMonth-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Months Ranking list'); ?></strong>
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
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html.=TbHtml::dropDownList("{$modelName}[year]",$model->year,$model->getYearList(),array("class"=>"submitBtn"));
    $search_add_html.=TbHtml::dropDownList("{$modelName}[month]",$model->month,$model->getMonthList(),array("class"=>"submitBtn"));
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Months Ranking list'),
        'model'=>$model,
        'viewhdr'=>'//rankMonth/_listhdr',
        'viewdtl'=>'//rankMonth/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'hasPageBar'=>false,
        'hasSearchBar'=>false,
        'hasNavBar'=>false,
        'search_add_html'=>$search_add_html,
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
$js = "
    $('.submitBtn').on('change',function(){
        $('form:first').submit();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
