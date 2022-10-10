<?php
$this->pageTitle=Yii::app()->name . ' - Feedback';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'feedback-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('feedback','Feedback'); ?></strong>
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
    $city_allow = Yii::app()->user->city_allow();
    $cityList=General::getCityListWithNoDescendant($city_allow);
    if(!empty($cityList)){
        $cityList = array_merge(array(""=>"-- 城市 --"),$cityList);
        $search_add_html .= TbHtml::dropDownList($modelName.'[city]',$model->city,$cityList,
            array("class"=>"form-control submitBtn"));
    }

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('feedback','Feedback List'),
        'model'=>$model,
        'viewhdr'=>'//feedback/_listhdr',
        'viewdtl'=>'//feedback/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'request_dt',
            'feedback_dt',
            'status',
            'feedback_cat',
            'feedbacker',
        ),
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
$('.submitBtn').change(function(){
    $('form:first').submit();
});";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
