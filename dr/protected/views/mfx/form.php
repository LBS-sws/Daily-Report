<?php
$this->pageTitle=Yii::app()->name . ' - Month Report';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','月报表数据分析'); ?></strong>
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
<!--	--><?php //$this->widget('ext.layout.ListPageWidget', array(
//			'title'=>Yii::t('monthly','Monthly Report Data List'),
//			'model'=>$model,
//				'viewhdr'=>'//month/_listhdr',
//				'viewdtl'=>'//month/_listdtl',
//				'gridsize'=>'24',
//				'height'=>'600',
//				'search'=>array(
//							'year_no',
//							'month_no',
//						),
//		));
//	?>
    <style type="text/css">
        .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
        .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
        .tftable tr {background-color:#d4e3e5;}
        .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
        .tftable tr:hover {background-color:#ffffff;}
    </style>

    <table class="tftable" border="1">
        <tr><th>/</th><th>地区</th><th>1月</th><th>2月</th><th>3月</th><th>4月</th><th>5月</th><th>6月</th><th>7月</th><th>8月</th><th>9月</th><th>10月</th><th>11月</th><th>12月</th></tr>
        <tr><td>销售部</td><td>Row:1 Cell:2</td><td>Row:1 Cell:2</td><td>Row:1 Cell:3</td><td>Row:1 Cell:4</td><td>Row:1 Cell:5</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td></tr>
        <tr><td>财务部</td><td>Row:1 Cell:2</td><td>Row:2 Cell:2</td><td>Row:2 Cell:3</td><td>Row:2 Cell:4</td><td>Row:2 Cell:5</td><td>Row:2 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td></tr>
        <tr><td>外勤部</td><td>Row:1 Cell:2</td><td>Row:3 Cell:2</td><td>Row:3 Cell:3</td><td>Row:3 Cell:4</td><td>Row:3 Cell:5</td><td>Row:3 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td></tr>
        <tr><td>营业部</td><td>Row:1 Cell:2</td><td>Row:4 Cell:2</td><td>Row:4 Cell:3</td><td>Row:4 Cell:4</td><td>Row:4 Cell:5</td><td>Row:4 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td></tr>
        <tr><td>人事部</td><td>Row:1 Cell:2</td><td>Row:5 Cell:2</td><td>Row:5 Cell:3</td><td>Row:5 Cell:4</td><td>Row:5 Cell:5</td><td>Row:5 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td></tr>
        <tr><td>总分</td><td>Row:1 Cell:2</td><td>Row:5 Cell:2</td><td>Row:5 Cell:3</td><td>Row:5 Cell:4</td><td>Row:5 Cell:5</td><td>Row:5 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td><td>Row:1 Cell:6</td></tr>

    </table>


</section>
<?php
//	echo $form->hiddenField($model,'pageNum');
//	echo $form->hiddenField($model,'totalRow');
//	echo $form->hiddenField($model,'orderField');
//	echo $form->hiddenField($model,'orderType');
//?>
<?php $this->endWidget(); ?>

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

