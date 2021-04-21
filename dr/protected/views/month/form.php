<?php
$this->pageTitle=Yii::app()->name . ' - Month Report Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Monthly Report Data Form'); ?></strong>
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
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('month/index')));
		?>

			<?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','xiazai'), array(
				'submit'=>Yii::app()->createUrl('month/xiazai')));
			?>
        <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','ZongJie'), array(
            'submit'=>Yii::app()->createUrl('month/summarize',array('index'=> $model->id,'city'=>$_GET['city']))));
        ?>
	</div>
	</div></div>
    <div class="btn-group">
        <button id="hide" type="button" class="btn btn-default" style="width: 555px;font-size: 16px"><?php echo Yii::t('monthly','Monthly  Report');?></button>
        <button id="show" type="button" class="btn btn-default" style="width: 555px;font-size: 16px""><?php echo Yii::t('monthly','Department details');?></button>
    </div>

	<div class="box box-info" id="p">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'year_no',array('class'=>"col-sm-1 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'year_no', 
						array('size'=>10,'readonly'=>true)
					); ?>
				</div>
				<?php echo $form->labelEx($model,'month_no',array('class'=>"col-sm-1 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'month_no', 
						array('size'=>10,'readonly'=>true)
					); ?>
				</div>
			</div>
	
			<legend>&nbsp;</legend>

<?php
	$modelName = get_class($model);
	$cnt=0;
	foreach ($model->record as $key=>$data) {
		$cnt++;
		$id_prefix = $modelName.'_record_'.$key;
		$name_prefix = $modelName.'[record]['.$key.']';
		echo '<div class="form-group">';
		echo '<div class="col-sm-4">';
		echo  TbHtml::label($cnt.'. '.$data['name'].($data['updtype']!='M' ? ' *' : ''),$id_prefix.'_datavalue');
		echo '</div>';
		echo '<div class="col-sm-3">';
		echo TbHtml::textField($name_prefix.'[datavalue]',$data['datavalue'],
				array('size'=>40,'maxlength'=>100,'class'=>($data['updtype']!='M' ? 'bg-gray' : ''),'readonly'=>($model->scenario=='view'||$data['updtype']!='M'))
			);		
		echo TbHtml::hiddenField($name_prefix.'[id]',$data['id']);
		echo TbHtml::hiddenField($name_prefix.'[code]',$data['code']);
		echo TbHtml::hiddenField($name_prefix.'[name]',$data['name']);
		echo TbHtml::hiddenField($name_prefix.'[datavalueold]',$data['datavalueold']);
		echo TbHtml::hiddenField($name_prefix.'[updtype]',$data['updtype']);
		echo TbHtml::hiddenField($name_prefix.'[fieldtype]',$data['fieldtype']);
		echo TbHtml::hiddenField($name_prefix.'[manualinput]',$data['manualinput']);
        echo TbHtml::hiddenField($name_prefix.'[excel_row]',$data['excel_row']);
		echo '</div>';
		echo '</div>';
	}
?>
		</div>
	</div>
    <div id="s" class="box box-info" style="display: none">
        <style type="text/css">
            .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
            .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
            .tftable tr {background-color:#d4e3e5;}
            .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
        </style>

        <table class="tftable" border="1">
            <tr><th colspan="2" ><?php echo Yii::t('monthly','Managing projects');?></th><th style="width: 10%"><?php echo Yii::t('monthly','Result');?></th><th style="width: 10%"><?php echo Yii::t('monthly','Scoring criteria');?></th><th style="width: 10%"><?php echo Yii::t('monthly','score');?></th><th style="width: 12%"><?php echo Yii::t('monthly','Department score / total score');?></th></tr>
            <tr><td colspan="5"></td><td><?php echo Yii::t('monthly','Total score (100 points)');?>：<?php echo $model->excel['f74'];?></td></tr>
            <tr><td style="width: 10%"><?php echo Yii::t('monthly','Sales Dept');?></td><td colspan="4"></td><td><?php echo $model->excel['f75'];?></td></tr>
            <tr><td rowspan="8"><?php echo Yii::t('monthly','New Revenue Status');?></td><td><?php echo Yii::t('monthly','New（IA，IB）New Services Annual Revenue Growth ((Current Month-Last Month)/Last Month)');?></td><td><?php echo $model->excel['c76'];?></td><td> -20% - -10%    :  1<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5 "<br/></td><td><?php echo $model->excel['e76'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','New（IA，IB) Services Annual Revenue Year on year Growth((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><td><?php echo $model->excel['c77'];?></td><td>'-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td><td><?php echo $model->excel['e77'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','New（IA，IB） Contract Amount Growth（(Current Month-Last Month)/Last Month）');?></td><td><?php echo $model->excel['c78'];?></td><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td><td><?php echo $model->excel['e78'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','New（IA，IB） Contract Amount Year on year Growth((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><td><?php echo $model->excel['c79'];?></td><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td><td><?php echo $model->excel['e79'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','New Client Yearly Revenue Growth(Scent，VOC，Kitchen or other)（(Current Month-Last Month)/Last Month）');?></td><td><?php echo $model->excel['c80'];?></td><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td><td><?php echo $model->excel['e80'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','New Client Yearly Revenue Year on year Growth(Scent，VOC，Kitchen or other)((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><td><?php echo $model->excel['c81'];?></td><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td><td><?php echo $model->excel['e81'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Revenue Net Growth Ratio （Yearly Revenue）（(Current Month-Last Month)/Last Month）');?></td><td><?php echo $model->excel['c82'];?></td><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td><td><?php echo $model->excel['e82'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Revenue Net Year on year Growth Ratio（Yearly Revenue）((Current Month-Last Year "Current Month" )/Last Year "Current Month" )');?></td><td><?php echo $model->excel['c83'];?></td><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td><td><?php echo $model->excel['e83'];?></td><td></td></tr>
            <tr><td rowspan="2"><?php echo Yii::t('monthly','Business Structure Ratio');?></td><td><?php echo Yii::t('monthly','Catering And Non-Catering Services Revenue Ratio');?><br/><?php echo Yii::t('monthly','20% - 40%         （Between 2：8 And 3：7）');?><br/><?php echo Yii::t('monthly','40% - 70%         （Between 3：7 And 4：6）');?><br/><?php echo Yii::t('monthly','70% - 100%       （Between 4：6 And  5：5）');?><br/><?php echo Yii::t('monthly','100% - 150%     （Between 5：5  And  6：4）');?><br/><?php echo Yii::t('monthly','150% - 230%     （Between 6 ： 4 And  7：3）');?><br/><?php echo Yii::t('monthly','>230%               （Above 7：3）');?></td><td><?php echo $model->excel['c84'];?></td><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td><td><?php echo $model->excel['e84'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Current Month IA，IB Annual Services Revenue Ratio');?><br/><?php echo Yii::t('monthly','20% - 40%         （Between 2：8 And 3：7）');?><br/><?php echo Yii::t('monthly','40% - 70%         （Between 3：7 And 4：6）');?><br/><?php echo Yii::t('monthly','70% - 100%       （Between 4：6 And  5：5）');?><br/><?php echo Yii::t('monthly','100% - 150%     （Between 5：5  And  6：4）');?><br/><?php echo Yii::t('monthly','150% - 230%     （Between 6 ： 4 And  7：3）');?><br/><?php echo Yii::t('monthly','>230%               （Above 7：3）');?></td><td><?php echo $model->excel['c85'];?></td><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td><td><?php echo $model->excel['e85'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Cancellation Situation');?></td><td><?php echo Yii::t('monthly','Ratio of Cancellation Amount to Revenue%（Current Month Service Cancellation Amount / Total Revenue (Current Month)）');?></td><td><?php echo $model->excel['c86'];?></td><td>0% - 0.8% : 5<br/>0.8% - 1.6% : 4<br/>1.6% - 2.4% : 3<br/>2.4% - 3.2% : 2<br/>X > 3.2% : 1</td><td><?php echo $model->excel['e86'];?></td><td></td></tr>
        </table>
        <style type="text/css">
            .tftable1 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #9dcc7a;border-collapse: collapse;}
            .tftable1 tr {background-color:#bedda7;}
            .tftable1 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #9dcc7a;}
        </style>
        <table class="tftable1" border="1">
            <tr><td style="width: 10%"><?php echo Yii::t('monthly','Field Department');?></td><td colspan="4"></td><td><?php echo $model->excel['f87'];?></td></tr>
            <tr><td rowspan="3"><?php echo Yii::t('monthly','Technician productivity');?></td><td><?php echo Yii::t('monthly','Last Month Service Technicain Highest Service Revenue Exceed the standard revenue ratio（Standard：30000/Month，Service Technicain Avg. Service Revenue - Standard Revenue / Standard Revenue），Technician below supervisor/director level');?></td><td style="width: 10%"><?php echo  $model->excel['c88'];?></td><td style="width: 10%">>20% : 5<br/>0% - 10% : 4<br/>-10% - 0% : 3<br/>-20% - -10% : 2<br/>'-30% - -20% : 1<br/>< -30% : 0</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e88'];?></td><td style="width: 12%"></td></tr>
            <tr><td><?php echo Yii::t('monthly','Last Month Service Technicain Highest Service Revenue Compare with standard（Standard：30000/Month）');?></td><td><?php echo  $model->excel['c89'];?></td><td>>70% : 5<br/>30% - 70% : 4<br/>10% - 30% ： 3</td><td><?php echo $model->excel['e89'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Last Month Service Technicain Highest Service Revenue');?></td><td><?php echo  $model->excel['c90'];?></td><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td><td><?php echo $model->excel['e90'];?></td><td></td></tr>
            <tr><td rowspan="2"><?php echo Yii::t('monthly','Technician cost');?></td><td><?php echo Yii::t('monthly','Material ratio byTechnicain IA(Service Technicain Charge Out Amount（IA） /  IA Total Revenue (Current Month))');?></td><td><?php echo  $model->excel['c91'];?></td><td><10% : 5<br/>10% - 15% : 4<br/>15% - 20% : 3<br/>20% - 25% : 2<br/>25% - 30% : 1<br/>>30% : 0</td><td><?php echo $model->excel['e91'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Proportion of materials used by technicians for pest control (amount of IB received by technicians / IB business volume of the month)');?></td><td><?php echo  $model->excel['c92'];?></td><td><5% : 5<br/>5% - 10% : 4<br/>10% - 15% : 3<br/>15% - 20% : 2<br/>20% - 25% : 1<br/>>25% : 0</td><td><?php echo $model->excel['e92'];?></td><td></td></tr>
            <tr><td rowspan="2"><?php echo Yii::t('monthly','Awards');?></td><td><?php echo Yii::t('monthly','Ratio of Current month Appreciation Amount To Total Service Technicain Amount(Skill Banner Amount / Total Service Technicain Amount)');?></td><td><?php echo  $model->excel['c93'];?></td><td>>20% : 5<br/>10% - 20% : 3<br/>5% - 10% : 1<br/><=0% : 0</td><td><?php echo $model->excel['e93'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Current month Badge Issue Details （P:N) P is the number of technicians awarded，N is Badge Issue Amount');?></td><td><?php echo  $model->excel['c94'];?></td><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td><td><?php echo $model->excel['e94'];?></td><td></td></tr>
        </table>
        <style type="text/css">
            .tftable2 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #a9a9a9;border-collapse: collapse;}
            .tftable2 tr {background-color:#b8b8b8;}
            .tftable2 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9a9a9;}
        </style>

        <table class="tftable2" border="1">
            <tr><td style="width: 10%"><?php echo Yii::t('monthly','Account department');?></td><td colspan="4"></td><td><?php echo $model->excel['f95'];?></td></tr>
            <tr><td rowspan="2"><?php echo Yii::t('monthly','Financial situationt');?></td><td><?php echo Yii::t('monthly','IA,IB Gross margin（Current Month New（IA，IB）Annual Services Revenue - Current Month AP Amount - Current Month Service Technicain Total Salaries）/ Current Month New（IA，IB）Annual Services Revenue');?></td><td style="width: 10%"><?php echo $model->excel['c96'];?></td><td style="width: 10%">>55% : 5<br/>50% - 55% : 4<br/>45% - 50%% : 3<br/>40% - 45% : 2<br/>35% - 40% : 1<br/><35% : 0</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e96'];?></td><td style="width: 12%"></td></tr>
            <tr><td><?php echo Yii::t('monthly','Salary V.S Sales Rate');?></td><td style="width: 10%"><?php echo $model->excel['c97'];?></td><td style="width: 10%">20% - 25% : 5<br/>25% - 28% : 4 28% - 30% : 3 30% - 35% : 2 >35% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e97'];?></td><td style="width: 12%"></td></tr>
           <?php if(!empty($model->excel['bc102'])){?>
            <tr><td rowspan="3"><?php echo Yii::t('monthly','Profit position');?></td><td><?php echo Yii::t('monthly','Net interest rate');?></td><td style="width: 10%"><?php echo $model->excel['bc102'];?></td><td style="width: 10%"><5% : 1<br/>5%-10% : 2<br/>11%-15% : 3<br/>16%-20% : 4<br/>>20% : 5</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['be102'];?></td><td style="width: 12%"></td></tr>
            <tr><td><?php echo Yii::t('monthly','Net Profit increment compared with last month');?></td><td style="width: 10%"><?php echo $model->excel['bc103'];?></td><td style="width: 10%">>=1% : 1<br/>>=1.5% : 2<br/>>=2% : 3<br/>>=2.5% : 4<br/>>=3% : 5</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['be103'];?></td><td style="width: 12%"></td></tr>
            <tr><td><?php echo Yii::t('monthly','Net profit increment compared with same month of last year');?></td><td style="width: 10%"><?php echo $model->excel['bc104'];?></td><td style="width: 10%">0-8% : 1<br/>8%-16% : 2<br/>17%-25% : 3<br/>26%-34% : 4<br/>>34% : 5</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['be104'];?></td><td style="width: 12%"></td></tr>
          <?php }?>
            <tr><td  rowspan="2"><?php echo Yii::t('monthly','Collection status');?></td><td><?php echo Yii::t('monthly','Collection rate（Current Month AR Amount / Total Revenue (Last Month)）');?> </td><td style="width: 10%"><?php echo $model->excel['c98'];?></td><td style="width: 10%">> 100% : 5<br/>95% - 100% : 4<br/>90% - 95% : 3<br/>85% - 90% : 2<br/>80% - 85% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e98'];?></td><td style="width: 12%"></td></tr>
            <tr><td><?php echo Yii::t('monthly','company month end closing balance ( Until the last day of each month)');?></td><td style="width: 10%"><?php echo $model->excel['c99'];?></td><td style="width: 10%"><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e99'];?></td><td style="width: 12%"></td></tr>
            <tr><td><?php echo Yii::t('monthly','Accounts receivable and uncollected');?></td><td><?php echo Yii::t('monthly','Bad Debt Customers Ratio（AR List over 90 Days）Total Amount （Bad Debt CustomersTotal Amount / Total Revenue (Current Month)）');?></td><td style="width: 10%"><?php echo $model->excel['c100'];?></td><td style="width: 10%"><= 30% : 5<br/>30% - 40% : 4<br/>40% - 50% :３<br/>50% - 60% : 2<br/>60% - 70% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e100'];?></td><td style="width: 12%"></td></tr>
        </table>
        <style type="text/css">
            .tftable3 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #ebab3a;border-collapse: collapse;}
            .tftable3 tr {background-color:#f0c169;}
            .tftable3 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #ebab3a;}
        </style>

        <table class="tftable3" border="1">
            <tr><td style="width: 10%"><?php echo Yii::t('monthly','Operations Department');?></td><td colspan="4"></td><td><?php echo $model->excel['f101'];?></td></tr>
            <tr><td><?php echo Yii::t('monthly','Overall situation');?></td><td><?php echo Yii::t('monthly','Rate of First Service within 7 days after signing the new contract （First Service within 7 days after signing the contract amount / Current month（IA，IB）new contract amount）');?></td><td style="width: 10%"><?php echo $model->excel['c102'];?></td><td style="width: 10%">95% - 100% ： 5<br/>90% - 95% ： 4<br/>85% - 90% ： 3<br/>80% - 85% ： 2<br/>75% - 80% ： 1<br/><75% : 0</td><td style="width: 10%"><?php echo $model->excel['e102'];?></td><td style="width: 12%"></td></tr>
            <tr><td rowspan="3"><?php echo Yii::t('monthly','Logistics situation');?></td><td><?php echo Yii::t('monthly','Accuracy of soap delivery（Soap delivery "Real" / Soap delivery "Project"）');?></td><td><?php echo $model->excel['c103'];?></td><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>  80% - 85% ： 2<br/>   75% - 80% ： 1<br/>  <75% : 0</td><td><?php echo $model->excel['e103'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Accuracy of Selling goods delivery（Selling goods delivery "Real" / Selling goods delivery "Project"）');?></td><td><?php echo $model->excel['c104'];?></td><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>    75% - 80% ： 1<br/>    <75% : 0</td><td><?php echo $model->excel['e104'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Average car spending （C:M)');?><br/><?php echo Yii::t('monthly','C : No. of Vehicle，M：Current Month Avg. Vehicle fuel amount');?></td><td><?php echo $model->excel['c105'];?></td><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td><td><?php echo $model->excel['e105'];?></td><td></td></tr>
            <tr><td rowspan="2"><?php echo Yii::t('monthly','Warehouse situation');?></td><td><?php echo Yii::t('monthly','Current Month Stock take accuracy');?></td><td><?php echo $model->excel['c106'];?></td><td>>108% : 0<br/>  104% - 108% ： 1<br/>  100% -104% ： 3<br/>  96% - 100% ： 5<br/>  92% - 96% ： 3<br/>   88% - 92% ： 1</td><td><?php echo $model->excel['e106'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Successfully installed machine contracts within 5 days Ratio（Successfully installed machine contracts within 5 days amount / Current month（IA Installation）new contract amount）');?></td><td><?php echo $model->excel['c107'];?></td><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>   75% - 80% ： 1<br/>    <= 75% : 0</td><td><?php echo $model->excel['e107'];?></td><td></td></tr>
            <tr><td rowspan="3"><?php echo Yii::t('monthly','Quality inspection');?></td><td><?php echo Yii::t('monthly','No. of Current Month QC customers Ratio（Compared with the standard monthly customer visits）');?><br/><?php echo Yii::t('monthly','（It is estimated that each customer RM1500 / month，Local service customer amount / Customer value valuation= Approximate number of customers，Approximate Number of customers divided by 6（Hope Visit customers every 12 months），Equal standard monthly customer visits  amount）');?></td><td><?php echo $model->excel['c108'];?></td><td>>90% : 5<br/>  70% - 90% :４<br/>   50% - 70% : 3<br/>  30% - 50% : 2<br/>   10% - 30% : 1<br/>  <= 10% : 0</td><td><?php echo $model->excel['e108'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Quality issue customer Ratio');?><br/><?php echo Yii::t('monthly','（Quality issue customer ： No. of Below 70 QC mark customers。Quality issue customer / No. of Current Month QC customers =Quality issue customer Ratio）');?></td><td><?php echo $model->excel['c109'];?></td><td>>20% : 3<br/>    10% - 20% : 5<br/>    0% - 10% : 1<br/>或<br/>>20% : 1<br/>    10% - 20% : 3<br/>    0% - 10% : 5</td><td><?php echo $model->excel['e109'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Satisfied performance technician（QC Highest Avg. score Technicain）');?></td><td><?php echo $model->excel['c110'];?></td><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td><td><?php echo $model->excel['e110'];?></td><td></td></tr>
            <tr><td rowspan="5"><?php echo Yii::t('monthly','Customer complaint handling');?></td><td><?php echo Yii::t('monthly','Compare Current Month complaint cases（Current Month complaint cases - Last Month complaint cases / Last Month complaint cases）');?></td><td><?php echo $model->excel['c111'];?></td><td><-30% : 5<br/>  -30% - -20% : 4<br/>  -20% - -10% : 3<br/>  -10% - 0% : 2<br/>   0% - 5% : 1<br/>   >5% : 0</td><td><?php echo $model->excel['e111'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Customer complaint resolution efficiency（Efficient complaint cases = Complaint solve (Within 2 days) cases）');?><br/><?php echo Yii::t('monthly','（Customer complaint resolution efficiency = Efficient complaint cases / Current Month complaint cases）');?></td><td><?php echo $model->excel['c112'];?></td><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/>  85% - 90% ： 3<br/>  80% - 85% ： 2<br/> 75% - 80% ： 1<br/> <75% : 0</td><td><?php echo $model->excel['e112'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Team leaders meeting with Technicains cases ratio');?><br/><?php echo Yii::t('monthly','（Team/Group leaders meeting with Technicains cases / Current Month complaint cases）');?></td><td><?php echo $model->excel['c113'];?></td><td>15% - 20% ： 5<br/> 10% - 15% ： 3<br/>  5% - 10% ： 1<br/> <5% : 0 </td><td><?php echo $model->excel['e113'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Efficient Return visit ratio（Efficient Return visi = Follow up with in 7 days after customer complaints cases）');?><br/><?php echo Yii::t('monthly','（Efficient Return visit ratio = Efficient Return visi / Current Month complaint cases）');?></td><td><?php echo $model->excel['c114'];?></td><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/> 85% - 90% ： 3<br/> 80% - 85% ： 2<br/> 75% - 80% ： 1<br/>  <75% : 0</td><td><?php echo $model->excel['e114'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Team / Group leader complaints follow up cases');?></td><td><?php echo $model->excel['c115'];?></td><td><?php echo Yii::t('monthly','For reference only, no score will be calculated');?></td><td><?php echo $model->excel['e115'];?></td><td></td></tr>
        </table>

        <style type="text/css">
            .tftable4 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #bcaf91;border-collapse: collapse;}
            .tftable4 tr {background-color:#e9dbbb;}
            .tftable4 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #bcaf91;}
        </style>
        <table class="tftable4" border="1">
            <tr><td style="width: 10%"><?php echo Yii::t('monthly','Personnel Dept');?></td><td colspan="4"></td><td><?php echo $model->excel['f116'];?></td></tr>
            <tr><td><?php echo Yii::t('monthly','Overall situation');?></td><td><?php echo Yii::t('monthly','All colleagues Labor contract progress（Uncontracted Staff amount "Exceed 1 Month"）');?></td><td style="width: 10%"><?php echo $model->excel['c117'];?></td><td style="width: 10%">0 : 5<br/>1 - 3 : 4<br/>3 - 5 : 3<br/>>5 : 0</td><td style="width: 10%"><?php echo $model->excel['e117'];?></td><td style="width: 12%"></td></tr>
            <tr><td rowspan="2"><?php echo Yii::t('monthly','Sales staff situation');?></td><td><?php echo Yii::t('monthly','Sales turnover rate （Working for over 1 month）（Sales Person turn over amount / Current Month Sales staff amount）');?></td><td><?php echo $model->excel['c118'];?></td><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td><td><?php echo $model->excel['e118'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Sales area vacancy rate（Sales Region Amount (Public) / Sales Region Amount）');?></td><td><?php echo $model->excel['c119'];?></td><td>0% - 20%     :  5<br/>20% - 60%   :  3<br/>60%  - 100% :  1</td><td><?php echo $model->excel['e119'];?></td><td></td></tr>
            <tr><td rowspan="3"><?php echo Yii::t('monthly','Field staff situation');?></td><td><?php echo Yii::t('monthly','Technicain  turnover rate（Working for over 1 month）% （Current Month Service Technicain turn over amount / Total Service Technicain Amount）');?>/td><td><?php echo $model->excel['c120'];?></td><td>0% - 5% : 5<br/>5% - 10% : 3<br/>10% - 15% : 1<br/>>15% : 0</td><td><?php echo $model->excel['e120'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Team leader amount of Standard（Up to every 5 technicians，There must be a Team leader )');?><br/><?php echo Yii::t('monthly','(Technicain amount / 6=Standard Team leader amoun， Scale = Current Team leader amount / Standard Team leader amount）');?></td><td><?php echo $model->excel['c121'];?></td><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td><td><?php echo $model->excel['e121'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Group leader amount of Standard（Up to every 30 technicians，There must be a Group leader)');?><br/><?php echo Yii::t('monthly','(Technicain amount / 30 =Standard  Group leader amoun，Scale = Current Group leader amount /Standard amount）');?></td><td><?php echo $model->excel['c122'];?></td><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td><td><?php echo $model->excel['e122'];?></td><td></td></tr>
            <tr><td><?php echo Yii::t('monthly','Office staff situation');?></td><td><?php echo Yii::t('monthly','Office staff turn over amount（Working for over 1 month)%（Current Month Office staff turn over amount / Total Office staff amount）');?></td><td><?php echo $model->excel['c124'];?></td><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td><td><?php echo $model->excel['e124'];?></td><td></td></tr>
        </table>
    </div>

</section>

<?php
$js = <<<EOF
$(document).ready(function(){
  $("#hide").click(function(){
 document.getElementById('p').style.display = 'block';
 document.getElementById('s').style.display = 'none';
  });
  $("#show").click(function(){
 document.getElementById('p').style.display = 'none';
 document.getElementById('s').style.display = 'block';
  });
});

EOF;
?>
<?php
Yii::app()->clientScript->registerScript('calculate',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


