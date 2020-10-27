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
		<strong><?php echo Yii::t('code','Monthly Report Data Analysis'); ?></strong>
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
                'submit'=>Yii::app()->createUrl('mfx/index')));
            ?>

        </div>
        <div class="btn-group pull-right" role="group">
            <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','xiazai'), array(
                'submit'=>Yii::app()->createUrl('mfx/downs')));
            ?>
        </div>
    </div></div>
<?php if(!empty($model->five)){ ?>
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
    <input name="ReportH02Form[city]" value="<?php echo $model->scenario['city'];?>" style="display: none">
    <input name="ReportH02Form[start_dt]" value="<?php echo $model->scenario['start_dt'];?>" style="display: none">
    <input name="ReportH02Form[start_dt1]" value="<?php echo $model->scenario['start_dt1'];?>" style="display: none">
    <input name="ReportH02Form[end_dt]" value="<?php echo $model->scenario['end_dt'];?>" style="display: none">
    <input name="ReportH02Form[end_dt1]" value="<?php echo $model->scenario['end_dt1'];?>" style="display: none">
    <table class="tftable" border="1">
        <tr><th colspan="2"><?php echo Yii::t('monthly','Managing projects');?></th><?php $i=0; for($i=0;$i<$model->ccuser;$i++){?><th><?php echo $model->year[$i]."/".$model->month[$i]?></th><?php }?><th>定义</th></tr>
        <?php for ($a=0;$a<count($model->five[0]);$a++){?>
        <tr><td  style="width: 13%;"></td><td  style="width: 20%;"><?php echo $model->five[0][$a]['name'];?></td><?php $i=0; for($i=0;$i<$model->ccuser;$i++){?><td><?php echo $model->five[$i][$a]['data_value'];?></td><?php }?><td style="width: 15%;"></td></tr>
        <?php }?>

        <tr><td style="width: 13%;"><?php echo Yii::t('monthly','Total score (100 points)');?>：</td><td style="width: 20%;"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f74']."</td>";}?><td style="width: 15%;"></td></tr>
        <tr><td style="width: 10%"><?php echo Yii::t('monthly','Sales Dept');?></td><td></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f75']."</td>";}?><td></td></tr>
        <tr><td rowspan="8"><?php echo Yii::t('monthly','New Revenue Status');?></td><td><?php echo Yii::t('monthly','New（IA，IB）New Services Annual Revenue Growth ((Current Month-Last Month)/Last Month)');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c76']."(".$model->excel[$a]['e76'].")</td>";}?><td>'-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td><?php echo Yii::t('monthly','New（IA，IB) Services Annual Revenue Year on year Growth((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c77']."(".$model->excel[$a]['e77'].")</td>";}?><td>'-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td><?php echo Yii::t('monthly','New（IA，IB） Contract Amount Growth（(Current Month-Last Month)/Last Month）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c78']."(".$model->excel[$a]['e78'].")</td>";}?><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td></tr>
        <tr><td><?php echo Yii::t('monthly','New（IA，IB） Contract Amount Year on year Growth((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c79']."(".$model->excel[$a]['e79'].")</td>";}?><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td></tr>
        <tr><td><?php echo Yii::t('monthly','New Client Yearly Revenue Growth(Scent，VOC，Kitchen or other)（(Current Month-Last Month)/Last Month）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c80']."(".$model->excel[$a]['e80'].")</td>";}?><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td></tr>
        <tr><td><?php echo Yii::t('monthly','New Client Yearly Revenue Year on year Growth(Scent，VOC，Kitchen or other)((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c81']."(".$model->excel[$a]['e81'].")</td>";}?><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td></tr>
        <tr><td><?php echo Yii::t('monthly','Revenue Net Growth Ratio （Yearly Revenue）（(Current Month-Last Month)/Last Month）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c82']."(".$model->excel[$a]['e82'].")</td>";}?><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td><?php echo Yii::t('monthly','Revenue Net Year on year Growth Ratio（Yearly Revenue）((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c83']."(".$model->excel[$a]['e83'].")</td>";}?><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td rowspan="2"><?php echo Yii::t('monthly','Business Structure Ratio');?></td><td><?php echo Yii::t('monthly','Catering And Non-Catering Services Revenue Ratio');?><br/><?php echo Yii::t('monthly','20% - 40%         （Between 2：8 And 3：7）');?><br/><?php echo Yii::t('monthly','40% - 70%         （Between 3：7 And 4：6）');?><br/><?php echo Yii::t('monthly','70% - 100%       （Between 4：6 And  5：5）');?><br/><?php echo Yii::t('monthly','100% - 150%     （Between 5：5  And  6：4）');?><br/><?php echo Yii::t('monthly','150% - 230%     （Between 6 ： 4 And  7：3）');?><br/><?php echo Yii::t('monthly','>230%               （Above 7：3）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c84']."(".$model->excel[$a]['e84'].")</td>";}?><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td></tr>
        <tr><td><?php echo Yii::t('monthly','Current Month IA，IB Annual Services Revenue Ratio');?><br/><?php echo Yii::t('monthly','20% - 40%         （Between 2：8 And 3：7）');?><br/><?php echo Yii::t('monthly','40% - 70%         （Between 3：7 And 4：6）');?><br/><?php echo Yii::t('monthly','70% - 100%       （Between 4：6 And  5：5）');?><br/><?php echo Yii::t('monthly','100% - 150%     （Between 5：5  And  6：4）');?><br/><?php echo Yii::t('monthly','150% - 230%     （Between 6 ： 4 And  7：3）');?><br/><?php echo Yii::t('monthly','>230%               （Above 7：3）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c85']."(".$model->excel[$a]['e85'].")</td>";}?><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td></tr>
        <tr><td><?php echo Yii::t('monthly','Cancellation  Situation');?></td><td><?php echo Yii::t('monthly','Ratio of Cancellation Amount to Revenue%（Current Month Service Cancellation Amount / Total Revenue (Current Month)）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c86']."(".$model->excel[$a]['e86'].")</td>";}?><td>0% - 0.8% : 5<br/>0.8% - 1.6% : 4<br/>1.6% - 2.4% : 3<br/>2.4% - 3.2% : 2<br/>X > 3.2% : 1</td></tr>

        <tr><td style="width: 13%;"><?php echo Yii::t('monthly','Field Department');?></td><td style="width: 20%;"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f87']."</td>";}?><td style="width: 15%;"></td></tr>
        <tr><td rowspan="3"><?php echo Yii::t('monthly','Technician productivity');?></td><td><?php echo Yii::t('monthly','Last Month Service Technicain Highest Service Revenue Exceed the standard revenue ratio（Standard：30000/Month，Service Technicain Avg. Service Revenue - Standard Revenue / Standard Revenue），Technician below supervisor/director level');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c88']."(".$model->excel[$a]['e88'].")</td>";}?><td ">>20% : 5<br/>0% - 10% : 4<br/>-10% - 0% : 3<br/>-20% - -10% : 2<br/>'-30% - -20% : 1<br/>< -30% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Last Month Service Technicain Highest Service Revenue Compare with standard（Standard：30000/Month）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c89']."(".$model->excel[$a]['e89'].")</td>";}?><td>>70% : 5<br/>30% - 70% : 4<br/>10% - 30% ： 3</td></tr>
        <tr><td><?php echo Yii::t('monthly','Last Month Service Technicain Highest Service Revenue');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c90']."</td>";}?><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td></tr>
        <tr><td rowspan="2"><?php echo Yii::t('monthly','Technician cost');?></td><td><?php echo Yii::t('monthly','Material ratio byTechnicain IA(Service Technicain Charge Out Amount（IA） /  IA Total Revenue (Current Month))');?>）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c91']."(".$model->excel[$a]['e91'].")</td>";}?><td><10% : 5<br/>10% - 15% : 4<br/>15% - 20% : 3<br/>20% - 25% : 2<br/>25% - 30% : 1<br/>>30% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Material ratio byTechnicain IB (Service Technicain Charge Out Amount（IB）/ IB Total Revenue (Current Month))');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c92']."(".$model->excel[$a]['e92'].")</td>";}?><td><5% : 5<br/>5% - 10% : 4<br/>10% - 15% : 3<br/>15% - 20% : 2<br/>20% - 25% : 1<br/>>25% : 0</td></tr>
        <tr><td rowspan="2"><?php echo Yii::t('monthly','Awards');?></td><td><?php echo Yii::t('monthly','Ratio of Current month Appreciation Amount To Total Service Technicain Amount(Skill Banner Amount / Total Service Technicain Amount)');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c93']."(".$model->excel[$a]['e93'].")</td>";}?><td>>20% : 5<br/>10% - 20% : 3<br/>5% - 10% : 1<br/><=0% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Current month Badge Issue Details （P:N) P is the number of technicians awarded，N is Badge Issue Amount');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c94']."</td>";}?><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td></tr>

        <tr><td style="width: 13%"><?php echo Yii::t('monthly','Account department');?></td><td style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f95']."</td>";}?><td style="width: 15%"> </td></tr>
        <tr><td rowspan="2"><?php echo Yii::t('monthly','Financial situation');?></td><td><?php echo Yii::t('monthly','IA,IB Gross margin（Current Month New（IA，IB）Annual Services Revenue - Current Month AP Amount - Current Month Service Technicain Total Salaries）/ Current Month New（IA，IB）Annual Services Revenue');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c96']."(".$model->excel[$a]['e96'].")</td>";}?><td>55% : 5<br/>50% - 55% : 4<br/>45% - 50%% : 3<br/>40% - 45% : 2<br/>35% - 40% : 1<br/><35% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Salary V.S Sales Rate');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c97']."(".$model->excel[$a]['e97'].")</td>";}?><td >20% - 25% : 5<br/>25% - 28% : 4 28% - 30% : 3 30% - 35% : 2 >35% : 1</td></tr>

        <?php if(!empty($model->excel[0]['bc102'])){?>
        <tr><td rowspan="3"><?php echo Yii::t('monthly','Profit position');?></td><td><?php echo Yii::t('monthly','Net interest rate');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['bc102']."(".$model->excel[$a]['be102'].")</td>";}?><td style="width: 10%"><5% : 1<br/>5%-10% : 2<br/>11%-15% : 3<br/>16%-20% : 4<br/>>20% : 5</td style="width: 10%"></td></tr>
        <tr><td><?php echo Yii::t('monthly','Net Profit increment compared with last month');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['bc103']."(".$model->excel[$a]['be103'].")</td>";}?><td style="width: 10%">>=1% : 1<br/>>=1.5% : 2<br/>>=2% : 3<br/>>=2.5% : 4<br/>>=3% : 5</td style="width: 10%"></tr>
        <tr><td><?php echo Yii::t('monthly','Net profit increment compared with same month of last year');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['bc104']."(".$model->excel[$a]['be104'].")</td>";}?><td style="width: 10%">0-8% : 1<br/>8%-16% : 2<br/>17%-25% : 3<br/>26%-34% : 4<br/>>34% : 5</td style="width: 10%"></tr>
        <?php }?>
        <tr><td  rowspan="2"><?php echo Yii::t('monthly','Collection status');?></td><td><?php echo Yii::t('monthly','Collection rate（Current Month AR Amount / Total Revenue (Last Month)）');?> </td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c98']."(".$model->excel[$a]['e98'].")</td>";}?><td> 100% : 5<br/>95% - 100% : 4<br/>90% - 95% : 3<br/>85% - 90% : 2<br/>80% - 85% : 1</td ></tr>
        <tr><td><?php echo Yii::t('monthly','company month end closing balance ( Until the last day of each month)');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c99']."</td>";}?><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td></tr>
        <tr><td><?php echo Yii::t('monthly','Accounts receivable and uncollected');?></td><td><?php echo Yii::t('monthly','Bad Debt Customers Ratio（AR List over 90 Days）Total Amount （Bad Debt CustomersTotal Amount / Total Revenue (Current Month)）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c100']."(".$model->excel[$a]['e100'].")</td>";}?><td><= 30% : 5<br/>30% - 40% : 4<br/>40% - 50% :３<br/>50% - 60% : 2<br/>60% - 70% : 1</td ></tr>

        <tr><td style="width: 13%"><?php echo Yii::t('monthly','Operations Department');?></td><td style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f101']."</td>";}?><td style="width: 15%"></td></tr>
        <tr><td><?php echo Yii::t('monthly','Overall situation');?></td><td><?php echo Yii::t('monthly','Rate of First Service within 7 days after signing the new contract （First Service within 7 days after signing the contract amount / Current month（IA，IB）new contract amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c102']."(".$model->excel[$a]['e102'].")</td>";}?><td >95% - 100% ： 5<br/>90% - 95% ： 4<br/>85% - 90% ： 3<br/>80% - 85% ： 2<br/>75% - 80% ： 1<br/><75% : 0</td></tr>
        <tr><td rowspan="3"><?php echo Yii::t('monthly','Logistics situation');?></td><td><?php echo Yii::t('monthly','Accuracy of soap delivery（Soap delivery "Real" / Soap delivery "Project"）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c103']."(".$model->excel[$a]['e103'].")</td>";}?><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>  80% - 85% ： 2<br/>   75% - 80% ： 1<br/>  <75% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Accuracy of Selling goods delivery（Selling goods delivery "Real" / Selling goods delivery "Project"）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c104']."(".$model->excel[$a]['e104'].")</td>";}?><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>    75% - 80% ： 1<br/>    <75% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Average car spending （C:M)');?><br/><?php echo Yii::t('monthly','C : No. of Vehicle，M：Current Month Avg. Vehicle fuel amount');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c105']."</td>";}?><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td></tr>
        <tr><td rowspan="2"><?php echo Yii::t('monthly','Warehouse situation');?></td><td><?php echo Yii::t('monthly','Current Month Stock take accuracy');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c106']."(".$model->excel[$a]['e106'].")</td>";}?><td>>108% : 0<br/>  104% - 108% ： 1<br/>  100% -104% ： 3<br/>  96% - 100% ： 5<br/>  92% - 96% ： 3<br/>   88% - 92% ： 1</td></tr>
        <tr><td><?php echo Yii::t('monthly','Successfully installed machine contracts within 5 days Ratio（Successfully installed machine contracts within 5 days amount / Current month（IA Installation）new contract amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c107']."(".$model->excel[$a]['e107'].")</td>";}?><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>   75% - 80% ： 1<br/>    <= 75% : 0</td></tr>
        <tr><td rowspan="3"><?php echo Yii::t('monthly','Quality inspection');?></td><td><?php echo Yii::t('monthly','No. of Current Month QC customers Ratio（Compared with the standard monthly customer visits）');?><br/><?php echo Yii::t('monthly','（It is estimated that each customer RM1500 / month，Local service customer amount / Customer value valuation= Approximate number of customers，Approximate Number of customers divided by 6（Hope Visit customers every 12 months），Equal standard monthly customer visits  amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c108']."(".$model->excel[$a]['e108'].")</td>";}?><td>>90% : 5<br/>  70% - 90% :４<br/>   50% - 70% : 3<br/>  30% - 50% : 2<br/>   10% - 30% : 1<br/>  <= 10% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Quality issue customer Ratio');?><br/><?php echo Yii::t('monthly','（Quality issue customer ： No. of Below 70 QC mark customers。Quality issue customer / No. of Current Month QC customers =Quality issue customer Ratio）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c109']."(".$model->excel[$a]['e109'].")</td>";}?><td>>20% : 3<br/>    10% - 20% : 5<br/>    0% - 10% : 1</td></tr>
        <tr><td><?php echo Yii::t('monthly','Satisfied performance technician（QC Highest Avg. score Technicain）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c110']."</td>";}?><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td></tr>
        <tr><td rowspan="5"><?php echo Yii::t('monthly','Customer complaint handling');?></td><td><?php echo Yii::t('monthly','Compare Current Month complaint cases（Current Month complaint cases - Last Month complaint cases / Last Month complaint cases）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c111']."(".$model->excel[$a]['e111'].")</td>";}?><td><-30% : 5<br/>  -30% - -20% : 4<br/>  -20% - -10% : 3<br/>  -10% - 0% : 2<br/>   0% - 5% : 1<br/>   >5% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Customer complaint resolution efficiency（Efficient complaint cases = Complaint solve (Within 2 days) cases）');?><br/><?php echo Yii::t('monthly','（Customer complaint resolution efficiency = Efficient complaint cases / Current Month complaint cases）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c112']."(".$model->excel[$a]['e112'].")</td>";}?><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/>  85% - 90% ： 3<br/>  80% - 85% ： 2<br/> 75% - 80% ： 1<br/> <75% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Team leaders meeting with Technicains cases ratio');?><br/><?php echo Yii::t('monthly','（Team/Group leaders meeting with Technicains cases / Current Month complaint cases）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c113']."(".$model->excel[$a]['e113'].")</td>";}?><td>15% - 20% ： 5<br/> 10% - 15% ： 3<br/>  5% - 10% ： 1<br/> <5% : 0 </td></tr>
        <tr><td><?php echo Yii::t('monthly','Efficient Return visit ratio（Efficient Return visi = Follow up with in 7 days after customer complaints cases）');?><br/><?php echo Yii::t('monthly','（Efficient Return visit ratio = Efficient Return visi / Current Month complaint cases）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c114']."(".$model->excel[$a]['e114'].")</td>";}?><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/> 85% - 90% ： 3<br/> 80% - 85% ： 2<br/> 75% - 80% ： 1<br/>  <75% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Team / Group leader complaints follow up cases');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c115']."</td>";}?><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td></tr>

        <tr><td style="width: 13%"><?php echo Yii::t('monthly','Personnel Dept');?></td><td  style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f116']."</td>";}?><td style="width: 15%"></td></tr>
        <tr><td><?php echo Yii::t('monthly','Overall situation');?></td><td><?php echo Yii::t('monthly','All colleagues Labor contract progress（Uncontracted Staff amount "Exceed 1 Month"）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c117']."(".$model->excel[$a]['e117'].")</td>";}?><td>0 : 5<br/>1 - 3 : 4<br/>3 - 5 : 3<br/>>5 : 0</td></tr>
        <tr><td rowspan="2"><?php echo Yii::t('monthly','Sales staff situation');?></td><td><?php echo Yii::t('monthly','Sales turnover rate （Working for over 1 month）（Sales Person turn over amount / Current Month Sales staff amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c118']."(".$model->excel[$a]['e118'].")</td>";}?><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Sales area vacancy rate（Sales Region Amount (Public) / Sales Region Amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c119']."(".$model->excel[$a]['e119'].")</td>";}?><td>0% - 20%     :  5<br/>20% - 60%   :  3<br/>60%  - 100% :  1</td></tr>
        <tr><td rowspan="3"><?php echo Yii::t('monthly','Field staff situation');?></td><td><?php echo Yii::t('monthly','Technicain  turnover rate（Working for over 1 month）% （Current Month Service Technicain turn over amount / Total Service Technicain Amount）');?>/td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c120']."(".$model->excel[$a]['e120'].")</td>";}?><td>0% - 5% : 5<br/>5% - 10% : 3<br/>10% - 15% : 1<br/>>15% : 0</td></tr>
        <tr><td><?php echo Yii::t('monthly','Team leader amount of Standard（Up to every 5 technicians，There must be a Team leader )');?><br/><?php echo Yii::t('monthly','(Technicain amount / 6=Standard Team leader amoun， Scale = Current Team leader amount / Standard Team leader amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c121']."(".$model->excel[$a]['e121'].")</td>";}?><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td></tr>
        <tr><td><?php echo Yii::t('monthly','Group leader amount of Standard（Up to every 30 technicians，There must be a Group leader)');?><br/><?php echo Yii::t('monthly','(Technicain amount / 30 =Standard  Group leader amoun，Scale = Current Group leader amount /Standard amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c122']."(".$model->excel[$a]['e122'].")</td>";}?><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td></tr>
        <tr><td><?php echo Yii::t('monthly','Office staff situation');?></td><td><?php echo Yii::t('monthly','Office staff turn over amount（Working for over 1 month)%（Current Month Office staff turn over amount / Total Office staff amount）');?></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c124']."(".$model->excel[$a]['e124'].")</td>";}?><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td></tr>
    </table>
    </div>
    </div>
</section>
<?php } else{echo "<br/><h1> ".Yii::t('code','No data available')."</h1>";}?>
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

