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
            'submit'=>Yii::app()->createUrl('month/summarize',array('index'=> $model->id))));
        ?>
	</div>
	</div></div>
    <div class="btn-group">
        <button id="hide" type="button" class="btn btn-default" style="width: 555px;font-size: 16px">月报数据</button>
        <button id="show" type="button" class="btn btn-default" style="width: 555px;font-size: 16px"">部门明细</button>
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
            <tr><th colspan="2" >管理项目</th><th style="width: 10%">成绩</th><th style="width: 10%">评分标准</th><th style="width: 10%">得分</th><th style="width: 12%">部门得分/总得分</th></tr>
            <tr><td colspan="5"></td><td>总分(100分）：<?php echo $model->excel['f74'];?></td></tr>
            <tr><td style="width: 10%">销售部</td><td colspan="4"></td><td><?php echo $model->excel['f75'];?></td></tr>
            <tr><td rowspan="8">新生意情况</td><td>新(IA,IB)新服务年生意额增长 （(当月-上月)/上月)</td><td><?php echo $model->excel['c76'];?></td><td> -20% - -10%    :  1<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5 "<br/></td><td><?php echo $model->excel['e76'];?></td><td></td></tr>
            <tr><td>新(IA,IB)服务年生意额同比增长 （(当月-去年当月）/去年当月)</td><td><?php echo $model->excel['c77'];?></td><td>'-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td><td><?php echo $model->excel['e77'];?></td><td></td></tr>
            <tr><td>新增(IA,IB)生意合同数目增长（(当月-上月)/上月)</td><td><?php echo $model->excel['c78'];?></td><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td><td><?php echo $model->excel['e78'];?></td><td></td></tr>
            <tr><td>新(IA,IB)生意合同数目同比增长（(当月-去年当月)/去年当月)</td><td><?php echo $model->excel['c79'];?></td><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td><td><?php echo $model->excel['e79'];?></td><td></td></tr>
            <tr><td>新业务(飘盈香，甲醛，厨房或其他)新年生意金额增长（(当月-上月)/上月)</td><td><?php echo $model->excel['c80'];?></td><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td><td><?php echo $model->excel['e80'];?></td><td></td></tr>
            <tr><td>新兴业务(飘盈香，甲醛，厨房或其他)新年生意金额同比增长 （(当月-去年当月)/去年当月)</td><td><?php echo $model->excel['c81'];?></td><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td><td><?php echo $model->excel['e81'];?></td><td></td></tr>
            <tr><td>公司年生意额净增长比例（（当月-上月）/上月）</td><td><?php echo $model->excel['c82'];?></td><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td><td><?php echo $model->excel['e82'];?></td><td></td></tr>
            <tr><td>公司年生意额净增长同比比例（（当月-去年当月）/去年当月）</td><td><?php echo $model->excel['c83'];?></td><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td><td><?php echo $model->excel['e83'];?></td><td></td></tr>
            <tr><td rowspan="2">生意结构比例</td><td>餐饮非餐饮新生意年生意额比例<br/>20% - 40%           （2：8和3：7之间）<br/>40% - 70%         （3：7和4：6之间）<br/>70% - 100%       （4：6和 5：5之间）<br/>100% - 150%    （5：5 和 6：4之间）<br/>150% - 230%    （6 ： 4 和 7：3之间）<br/>>230%                 （7：3以上）</td><td><?php echo $model->excel['c84'];?></td><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td><td><?php echo $model->excel['e84'];?></td><td></td></tr>
            <tr><td>当月IA, IB年生意额比例<br/>20% - 40%           （2：8和3：7之间）<br/>40% - 70%         （3：7和4：6之间）<br/>70% - 100%       （4：6和 5：5之间）<br/>100% - 150%    （5：5 和 6：4之间）<br/>150% - 230%    （6 ： 4 和 7：3之间）<br/>>230%                 （7：3以上）</td><td><?php echo $model->excel['c85'];?></td><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td><td><?php echo $model->excel['e85'];?></td><td></td></tr>
            <tr><td>停单情况</td><td>停单金额占生意比例% （当月停单总月金额/当月生意额）</td><td><?php echo $model->excel['c86'];?></td><td>0% - 0.8% : 5<br/>0.8% - 1.6% : 4<br/>1.6% - 2.4% : 3<br/>2.4% - 3.2% : 2<br/>X > 3.2% : 1</td><td><?php echo $model->excel['e86'];?></td><td></td></tr>
        </table>
        <style type="text/css">
            .tftable1 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #9dcc7a;border-collapse: collapse;}
            .tftable1 tr {background-color:#bedda7;}
            .tftable1 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #9dcc7a;}
        </style>
        <table class="tftable1" border="1">
            <tr><td style="width: 10%">外勤部</td><td colspan="4"></td><td><?php echo $model->excel['f87'];?></td></tr>
            <tr><td rowspan="3">技术员生产力</td><td>上月技术员平均生意额超出标准门栏比例 （标准：30000/月， 当地平均技术员生意额 - 标准生意额 / 标准生意额 ），主管/主任级别以下技术员</td><td style="width: 10%"><?php echo  $model->excel['c88'];?></td><td style="width: 10%">>20% : 5<br/>0% - 10% : 4<br/>-10% - 0% : 3<br/>-20% - -10% : 2<br/>'-30% - -20% : 1<br/>< -30% : 0</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e88'];?></td><td style="width: 12%"></td></tr>
            <tr><td>上月技术员最高生意额技术员金额跟标准比较  （标准：30000/月)</td><td><?php echo  $model->excel['c89'];?></td><td>>70% : 5<br/>30% - 70% : 4<br/>10% - 30% ： 3</td><td><?php echo $model->excel['e89'];?></td><td></td></tr>
            <tr><td>上月技术员最高生意额技术员金额</td><td><?php echo  $model->excel['c90'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e90'];?></td><td></td></tr>
            <tr><td rowspan="2">技术员成本</td><td>技术员用料比例 清洁（技术员IA领货金额/当月IA生意额）</td><td><?php echo  $model->excel['c91'];?></td><td><10% : 5<br/>10% - 15% : 4<br/>15% - 20% : 3<br/>20% - 25% : 2<br/>25% - 30% : 1<br/>>30% : 0</td><td><?php echo $model->excel['e91'];?></td><td></td></tr>
            <tr><td>技术员用料比例 灭虫（技术员IB领货金额/当月IB生意额）</td><td><?php echo  $model->excel['c92'];?></td><td><5% : 5<br/>5% - 10% : 4<br/>10% - 15% : 3<br/>15% - 20% : 2<br/>20% - 25% : 1<br/>>25% : 0</td><td><?php echo $model->excel['e92'];?></td><td></td></tr>
            <tr><td rowspan="2">获奖情况</td><td>当月锦旗获奖数目占整体技术员比例 （锦旗数目/整体技术员数目）</td><td><?php echo  $model->excel['c93'];?></td><td>>20% : 5<br/>10% - 20% : 3<br/>5% - 10% : 1<br/><=0% : 0</td><td><?php echo $model->excel['e93'];?></td><td></td></tr>
            <tr><td>当月襟章颁发明细 （P:N) P为受颁技术员数目，N为襟章发放数目</td><td><?php echo  $model->excel['c94'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e94'];?></td><td></td></tr>
        </table>
        <style type="text/css">
            .tftable2 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #a9a9a9;border-collapse: collapse;}
            .tftable2 tr {background-color:#b8b8b8;}
            .tftable2 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9a9a9;}
        </style>

        <table class="tftable2" border="1">
            <tr><td style="width: 10%">财务部</td><td colspan="4"></td><td><?php echo $model->excel['f95'];?></td></tr>
            <tr><td rowspan="2">财政状况</td><td>IA,IB毛利率 （当月IA,IB生意额 - 材料订购 - 技术员工资）/当月IA,IB生意额</td><td style="width: 10%"><?php echo $model->excel['c96'];?></td><td style="width: 10%">>55% : 5<br/>50% - 55% : 4<br/>45% - 50%% : 3<br/>40% - 45% : 2<br/>35% - 40% : 1<br/><35% : 0</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e96'];?></td><td style="width: 12%"></td></tr>
            <tr><td>工资占生意额比例</td><td style="width: 10%"><?php echo $model->excel['c97'];?></td><td style="width: 10%">20% - 25% : 5<br/>25% - 28% : 4 28% - 30% : 3 30% - 35% : 2 >35% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e97'];?></td><td style="width: 12%"></td></tr>
            <tr><td  rowspan="2">收款情况</td><td>收款效率（当月收款额/上月生意额） </td><td style="width: 10%"><?php echo $model->excel['c98'];?></td><td style="width: 10%">> 100% : 5<br/>95% - 100% : 4<br/>90% - 95% : 3<br/>85% - 90% : 2<br/>80% - 85% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e98'];?></td><td style="width: 12%"></td></tr>
            <tr><td>公司累积结余（到每月最后一天止）</td><td style="width: 10%"><?php echo $model->excel['c99'];?></td><td style="width: 10%">仅供参考，不计算分数</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e99'];?></td><td style="width: 12%"></td></tr>
            <tr><td>应收未收帐情况</td><td>问题客人（超过90天没有结款）比例 (问题客户总月费金额/当月生意额）</td><td style="width: 10%"><?php echo $model->excel['c100'];?></td><td style="width: 10%"><= 30% : 5<br/>30% - 40% : 4<br/>40% - 50% :３<br/>50% - 60% : 2<br/>60% - 70% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e100'];?></td><td style="width: 12%"></td></tr>
        </table>
        <style type="text/css">
            .tftable3 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #ebab3a;border-collapse: collapse;}
            .tftable3 tr {background-color:#f0c169;}
            .tftable3 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #ebab3a;}
        </style>

        <table class="tftable3" border="1">
            <tr><td style="width: 10%">营运部</td><td colspan="4"></td><td><?php echo $model->excel['f101'];?></td></tr>
            <tr><td>整体情况</td><td>新合同7天内安排首次比例 （成功7天首次客户数目/整体当月新IA,IB合同数目）</td><td style="width: 10%"><?php echo $model->excel['c102'];?></td><td style="width: 10%">95% - 100% ： 5<br/>90% - 95% ： 4<br/>85% - 90% ： 3<br/>80% - 85% ： 2<br/>75% - 80% ： 1<br/><75% : 0</td><td style="width: 10%"><?php echo $model->excel['e102'];?></td><td style="width: 12%"></td></tr>
            <tr><td rowspan="3">物流情况</td><td>运送皂液准确度 （实际送皂液/应送皂液）</td><td><?php echo $model->excel['c103'];?></td><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>  80% - 85% ： 2<br/>   75% - 80% ： 1<br/>  <75% : 0</td><td><?php echo $model->excel['e103'];?></td><td></td></tr>
            <tr><td>运送销售货品准确度 （实际送销售货品/应送销售货品）</td><td><?php echo $model->excel['c104'];?></td><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>    75% - 80% ： 1<br/>    <75% : 0</td><td><?php echo $model->excel['e104'];?></td><td></td></tr>
            <tr><td>汽车支出平均 （C:M)<br/>C : 车辆数目，M：车的平均用油量</td><td><?php echo $model->excel['c105'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e105'];?></td><td></td></tr>
            <tr><td rowspan="2">仓库情况</td><td>每月盘点准确度</td><td><?php echo $model->excel['c106'];?></td><td>>108% : 0<br/>  104% - 108% ： 1<br/>  100% -104% ： 3<br/>  96% - 100% ： 5<br/>  92% - 96% ： 3<br/>   88% - 92% ： 1</td><td><?php echo $model->excel['e106'];?></td><td></td></tr>
            <tr><td>新合同5天内安排安装比例 （成功5天安装客户数目/今月新IA服务合同数目）</td><td><?php echo $model->excel['c107'];?></td><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>   75% - 80% ： 1<br/>    <= 75% : 0</td><td><?php echo $model->excel['e107'];?></td><td></td></tr>
            <tr><td rowspan="3">质检情况</td><td>当月质检客户数量效率 （跟标准每月客户拜访数目比较）<br/>（估计客户每个约￥1500/月，当地服务客户金额/客户金额估值=客户约数量，客户约数量除6（希望每12个月拜访客户一次），等于标准每月客户拜访数目）</td><td><?php echo $model->excel['c108'];?></td><td>>90% : 5<br/>  70% - 90% :４<br/>   50% - 70% : 3<br/>  30% - 50% : 2<br/>   10% - 30% : 1<br/>  <= 10% : 0</td><td><?php echo $model->excel['e108'];?></td><td></td></tr>
            <tr><td>质检问题客户数量比例<br/>（问题客户 ： 质检拜访客户分数低于70分。问题客户/当月质检拜访客户 = 质检问题客户数量比例）</td><td><?php echo $model->excel['c109'];?></td><td>>20% : 3<br/>    10% - 20% : 5<br/>    0% - 10% : 1</td><td><?php echo $model->excel['e109'];?></td><td></td></tr>
            <tr><td>表现满意技术员 (质检拜访表平均分数最高同事）</td><td><?php echo $model->excel['c110'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e110'];?></td><td></td></tr>
            <tr><td rowspan="5">客诉处理</td><td>当月客诉数目比较（当月客诉数目 - 上月客诉数目 / 上月客诉数目）</td><td><?php echo $model->excel['c111'];?></td><td><-30% : 5<br/>  -30% - -20% : 4<br/>  -20% - -10% : 3<br/>  -10% - 0% : 2<br/>   0% - 5% : 1<br/>   >5% : 0</td><td><?php echo $model->excel['e111'];?></td><td></td></tr>
            <tr><td>客诉解决效率（高效客诉数目 = 2工作天内处理完成客诉数量）<br/>（客诉解决效率 = 高效客诉解决数目/当月客诉数目）</td><td><?php echo $model->excel['c112'];?></td><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/>  85% - 90% ： 3<br/>  80% - 85% ： 2<br/> 75% - 80% ： 1<br/> <75% : 0</td><td><?php echo $model->excel['e112'];?></td><td></td></tr>
            <tr><td>队长跟投诉技术员面谈比例<br/>（队长/组长面谈客诉技术员数目/客诉数目）</td><td><?php echo $model->excel['c113'];?></td><td>15% - 20% ： 5<br/> 10% - 15% ： 3<br/>  5% - 10% ： 1<br/> <5% : 0 </td><td><?php echo $model->excel['e113'];?></td><td></td></tr>
            <tr><td>高效回访率 （高效回访 = 客诉后7天内电话客户回访数目）<br/>（高效回访率=高效回访/今月客诉数目）</td><td><?php echo $model->excel['c114'];?></td><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/> 85% - 90% ： 3<br/> 80% - 85% ： 2<br/> 75% - 80% ： 1<br/>  <75% : 0</td><td><?php echo $model->excel['e114'];?></td><td></td></tr>
            <tr><td>问题客户需要队长/组长跟进数目</td><td><?php echo $model->excel['c115'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e115'];?></td><td></td></tr>
        </table>

        <style type="text/css">
            .tftable4 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #bcaf91;border-collapse: collapse;}
            .tftable4 tr {background-color:#e9dbbb;}
            .tftable4 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #bcaf91;}
        </style>
        <table class="tftable4" border="1">
            <tr><td style="width: 10%">人事部</td><td colspan="4"></td><td><?php echo $model->excel['f116'];?></td></tr>
            <tr><td>整体情况</td><td>所有同事劳动合同进展 (超过一个月没有签署劳动合同同事数目（张））</td><td style="width: 10%"><?php echo $model->excel['c117'];?></td><td style="width: 10%">0 : 5<br/>1 - 3 : 4<br/>3 - 5 : 3<br/>>5 : 0</td><td style="width: 10%"><?php echo $model->excel['e117'];?></td><td style="width: 12%"></td></tr>
            <tr><td rowspan="2">销售人员情况</td><td>销售人员流失率 （工作满一个月的）（离职销售人员/当月所有销售人员）</td><td><?php echo $model->excel['c118'];?></td><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td><td><?php echo $model->excel['e118'];?></td><td></td></tr>
            <tr><td>销售区域空置率（公共区域/销售划分区域）</td><td><?php echo $model->excel['c119'];?></td><td>0% - 20%     :  5<br/>20% - 60%   :  3<br/>60%  - 100% :  1</td><td><?php echo $model->excel['e119'];?></td><td></td></tr>
            <tr><td rowspan="3">外勤人员情况</td><td>离职技术员（工作满一个月的）人数% （当月离职技术人员/整体外勤技术人员）/td><td><?php echo $model->excel['c120'];?></td><td>0% - 5% : 5<br/>5% - 10% : 3<br/>10% - 15% : 1<br/>>15% : 0</td><td><?php echo $model->excel['e120'];?></td><td></td></tr>
            <tr><td>队长数目跟标准比例 （最多每5个技术员，就要有一个队长的设置)<br/>(技术员数目/6=标准队长数目， 比例 = 队长数目/标准队长数目）</td><td><?php echo $model->excel['c121'];?></td><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td><td><?php echo $model->excel['e121'];?></td><td></td></tr>
            <tr><td>组长数目跟标准比例 （最多每30个技术员，就要有一个组长的设置)<br/>(技术员数目/30=标准组长数目， 比例 = 组长数目/标准数目）</td><td><?php echo $model->excel['c122'];?></td><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td><td><?php echo $model->excel['e122'];?></td><td></td></tr>
            <tr><td>办公室人员情况</td><td>离职办公室（工作满一个月的）人数% （当月离职办公室人员/整体办公室人员）</td><td><?php echo $model->excel['c124'];?></td><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td><td><?php echo $model->excel['e124'];?></td><td></td></tr>
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


