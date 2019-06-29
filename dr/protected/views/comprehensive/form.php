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
		<strong><?php echo Yii::t('report','Comprehensive data comparative analysis'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>
<div class="box"><div class="box-body">
        <div class="btn-group" role="group">

            <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                'submit'=>Yii::app()->createUrl('comprehensive/index')));
            ?>

        </div>
        <div class="btn-group pull-right" role="group">
            <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','xiazai'), array(
                'submit'=>Yii::app()->createUrl('comprehensive/downs')));
            ?>
        </div>
    </div></div>

<section class="content" >
    <div style="width: 100%;">
        <div class="acc" style=" overflow-x:auto; overflow-y:auto;">
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
    <input name="ReportG02Form[city]" value="<?php echo $model->scenario['city'];?>" style="display: none">
    <input name="ReportG02Form[start_dt]" value="<?php echo $model->scenario['start_dt'];?>" style="display: none">
    <input name="ReportG02Form[start_dt1]" value="<?php echo $model->scenario['start_dt1'];?>" style="display: none">
    <input name="ReportG02Form[end_dt]" value="<?php echo $model->scenario['end_dt'];?>" style="display: none">
    <input name="ReportG02Form[end_dt1]" value="<?php echo $model->scenario['end_dt1'];?>" style="display: none">
    <table class="tftable" border="1">
        <tr><td style="width: 150px;"></td><td style="width: 80px;"></td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['time']."</td>" ;}?></tr>
        <tr><td  rowspan="3">生意额增长<br/>(当月生意额-上月）/上月</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['business']."</td>" ;}?></tr>
        <tr><td>比上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['businessMonth']."</td>" ;}?></tr>
        <tr><td>比去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['businessYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">纯利润增长<br/>(当月利润额-上月）/上月</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['profit']."</td>" ;}?></tr>
        <tr><td>比上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['profitMonth']."</td>" ;}?></tr>
        <tr><td>比去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['profitYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">停单比例 <br/>当月停单总月金额/当月生意额</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['stoporder']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['stoporderMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['stoporderYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">收款率 <br/>当月收款额/上月生意额</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['receipt']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['receiptMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['receiptYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">技术员平均生产力<br/>(月报的技术当月平均生意额）</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['productivity']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['productivityMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['productivityYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">月报表分数</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['report']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['reportMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['reportYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">老总回馈次数</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['feedback']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['feedbackMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['feedbackYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">质检拜访量 <br/>(月报里的质检客户数量）</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['quality']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['qualityMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['qualityYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">销售拜访量</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['visit']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['visitMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['visitYear']."</td>" ;}?></tr>
        <tr><td rowspan="3">签单成交率 <br/>(当月签单量/当月拜访量）</td><td>当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['signing']."</td>" ;}?></tr>
        <tr><td>上月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['signingMonth']."</td>" ;}?></tr>
        <tr><td>去年当月</td><?php foreach ($model['excel'] as $arr){ echo "<td>".$arr['signingYear']."</td>" ;}?></tr>
    </table>
    </div>
    </div>
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

