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
        <tr><th colspan="2">管理项目</th><?php $i=0; for($i=0;$i<$model->ccuser;$i++){?><th><?php echo $model->year[$i]."/".$model->month[$i]?></th><?php }?><th>定义</th></tr>
        <?php for ($a=0;$a<count($model->five[0]);$a++){?>
        <tr><td  style="width: 13%;"></td><td  style="width: 20%;"><?php echo $model->five[0][$a]['name'];?></td><?php $i=0; for($i=0;$i<$model->ccuser;$i++){?><td><?php echo $model->five[$i][$a]['data_value'];?></td><?php }?><td style="width: 15%;"></td></tr>
        <?php }?>

        <tr><td style="width: 13%;">总分(100分）：</td><td style="width: 20%;"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f74']."</td>";}?><td style="width: 15%;"></td></tr>
        <tr><td style="width: 10%">销售部</td><td></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f75']."</td>";}?><td></td></tr>
        <tr><td rowspan="8">新生意情况</td><td>新(IA,IB)新服务年生意额增长 （(当月-上月)/上月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c76']."(".$model->excel[$a]['e76'].")</td>";}?><td>'-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td>新(IA,IB)服务年生意额同比增长 （(当月-去年当月）/去年当月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c77']."(".$model->excel[$a]['e77'].")</td>";}?><td>'-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td>新增(IA,IB)生意合同数目增长（(当月-上月)/上月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c78']."(".$model->excel[$a]['e78'].")</td>";}?><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td></tr>
        <tr><td>新(IA,IB)生意合同数目同比增长（(当月-去年当月)/去年当月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c79']."(".$model->excel[$a]['e79'].")</td>";}?><td>-40% - -20%     :  1<br/>-20% - 0%   :  2<br/>0% - 20%   :  3<br/>20% - 40%   :  4<br/>> 40% :  5</td></tr>
        <tr><td>新业务(飘盈香，甲醛，厨房或其他)新年生意金额增长（(当月-上月)/上月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c80']."(".$model->excel[$a]['e80'].")</td>";}?><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td></tr>
        <tr><td>新兴业务(飘盈香，甲醛，厨房或其他)新年生意金额同比增长 （(当月-去年当月)/去年当月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c81']."(".$model->excel[$a]['e81'].")</td>";}?><td>-200% - -100%     :  1<br/>-100% - 0% : 2<br/>0% - 100% :3<br/>100% - 300%   :  4<br/>> 300% :  5</td></tr>
        <tr><td>公司年生意额净增长比例（（当月-上月）/上月）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c82']."(".$model->excel[$a]['e82'].")</td>";}?><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td>公司年生意额净增长同比比例（（当月-去年当月）/去年当月）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c83']."(".$model->excel[$a]['e83'].")</td>";}?><td>-20% - -10%     :  1<br/>-10% - 0%   :  2<br/>0% - 10%   :  3<br/>10% - 20%   :  4<br/>> 20% :  5</td></tr>
        <tr><td rowspan="2">生意结构比例</td><td>餐饮非餐饮新生意年生意额比例<br/>20% - 40%           （2：8和3：7之间）<br/>40% - 70%         （3：7和4：6之间）<br/>70% - 100%       （4：6和 5：5之间）<br/>100% - 150%    （5：5 和 6：4之间）<br/>150% - 230%    （6 ： 4 和 7：3之间）<br/>>230%                 （7：3以上）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c84']."(".$model->excel[$a]['e84'].")</td>";}?><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td></tr>
        <tr><td>当月IA, IB年生意额比例<br/>20% - 40%           （2：8和3：7之间）<br/>40% - 70%         （3：7和4：6之间）<br/>70% - 100%       （4：6和 5：5之间）<br/>100% - 150%    （5：5 和 6：4之间）<br/>150% - 230%    （6 ： 4 和 7：3之间）<br/>>230%                 （7：3以上）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c85']."(".$model->excel[$a]['e85'].")</td>";}?><td>20% - 40%     :  1<br/>40% - 70%   :  2<br/>70% - 100%   :  4<br/>100% - 150%   :  5<br/>150% - 230% : 3<br/>> 230% :  1</td></tr>
        <tr><td>停单情况</td><td>停单金额占生意比例% （当月停单总月金额/当月生意额）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c86']."(".$model->excel[$a]['e86'].")</td>";}?><td>0% - 0.8% : 5<br/>0.8% - 1.6% : 4<br/>1.6% - 2.4% : 3<br/>2.4% - 3.2% : 2<br/>X > 3.2% : 1</td></tr>

        <tr><td style="width: 13%;">外勤部</td><td style="width: 20%;"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f87']."</td>";}?><td style="width: 15%;"></td></tr>
        <tr><td rowspan="3">技术员生产力</td><td>上月技术员平均生意额超出标准门栏比例 （标准：30000/月， 当地平均技术员生意额 - 标准生意额 / 标准生意额 ），主管/主任级别以下技术员</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c88']."(".$model->excel[$a]['e88'].")</td>";}?><td ">>20% : 5<br/>0% - 10% : 4<br/>-10% - 0% : 3<br/>-20% - -10% : 2<br/>'-30% - -20% : 1<br/>< -30% : 0</td></tr>
        <tr><td>上月技术员最高生意额技术员金额跟标准比较  （标准：30000/月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c89']."(".$model->excel[$a]['e89'].")</td>";}?><td>>70% : 5<br/>30% - 70% : 4<br/>10% - 30% ： 3</td></tr>
        <tr><td>上月技术员最高生意额技术员金额</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c90']."</td>";}?><td>仅供参考，不计算分数</td></tr>
        <tr><td rowspan="2">技术员成本</td><td>技术员用料比例 清洁（技术员IA领货金额/当月IA生意额）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c91']."(".$model->excel[$a]['e91'].")</td>";}?><td><10% : 5<br/>10% - 15% : 4<br/>15% - 20% : 3<br/>20% - 25% : 2<br/>25% - 30% : 1<br/>>30% : 0</td></tr>
        <tr><td>技术员用料比例 灭虫（技术员IB领货金额/当月IB生意额）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c92']."(".$model->excel[$a]['e92'].")</td>";}?><td><5% : 5<br/>5% - 10% : 4<br/>10% - 15% : 3<br/>15% - 20% : 2<br/>20% - 25% : 1<br/>>25% : 0</td></tr>
        <tr><td rowspan="2">获奖情况</td><td>当月锦旗获奖数目占整体技术员比例 （锦旗数目/整体技术员数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c93']."(".$model->excel[$a]['e93'].")</td>";}?><td>>20% : 5<br/>10% - 20% : 3<br/>5% - 10% : 1<br/><=0% : 0</td></tr>
        <tr><td>当月襟章颁发明细 （P:N) P为受颁技术员数目，N为襟章发放数目</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c94']."</td>";}?><td>仅供参考，不计算分数</td></tr>

        <tr><td style="width: 13%">财务部</td><td style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f95']."</td>";}?><td style="width: 15%"> </td></tr>
        <tr><td rowspan="2">财政状况</td><td>IA,IB毛利率 （当月IA,IB生意额 - 材料订购 - 技术员工资）/当月IA,IB生意额</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c96']."(".$model->excel[$a]['e96'].")</td>";}?><td>55% : 5<br/>50% - 55% : 4<br/>45% - 50%% : 3<br/>40% - 45% : 2<br/>35% - 40% : 1<br/><35% : 0</td></tr>
        <tr><td>工资占生意额比例</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c97']."(".$model->excel[$a]['e97'].")</td>";}?><td >20% - 25% : 5<br/>25% - 28% : 4 28% - 30% : 3 30% - 35% : 2 >35% : 1</td></tr>
        <?php if(!empty($model->excel['bc102'])){?>
        <tr><td rowspan="3">利润状况</td><td>纯利率</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['bc102']."(".$model->excel[$a]['be102'].")</td>";}?><td style="width: 10%"><5% : 1<br/>5%-10% : 2<br/>11%-15% : 3<br/>16%-20% : 4<br/>>20% : 5</td style="width: 10%"></td></tr>
        <tr><td>纯利跟上月横比增长</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['bc103']."(".$model->excel[$a]['be103'].")</td>";}?><td style="width: 10%">>=1% : 1<br/>>=1.5% : 2<br/>>=2% : 3<br/>>=2.5% : 4<br/>>=3% : 5</td style="width: 10%"></tr>
        <tr><td>纯利跟去年同比增长</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['bc104']."(".$model->excel[$a]['be104'].")</td>";}?><td style="width: 10%">0-8% : 1<br/>8%-16% : 2<br/>17%-25% : 3<br/>26%-34% : 4<br/>>34% : 5</td style="width: 10%"></tr>
        <?php }?>
        <tr><td  rowspan="2">收款情况</td><td>收款效率（当月收款额/上月生意额） </td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c98']."(".$model->excel[$a]['e98'].")</td>";}?><td> 100% : 5<br/>95% - 100% : 4<br/>90% - 95% : 3<br/>85% - 90% : 2<br/>80% - 85% : 1</td ></tr>
        <tr><td>公司累积结余（到每月最后一天止）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c99']."</td>";}?><td>仅供参考，不计算分数</td></tr>
        <tr><td>应收未收帐情况</td><td>问题客人（超过90天没有结款）比例 (问题客户总月费金额/当月生意额）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c100']."(".$model->excel[$a]['e100'].")</td>";}?><td><= 30% : 5<br/>30% - 40% : 4<br/>40% - 50% :３<br/>50% - 60% : 2<br/>60% - 70% : 1</td ></tr>

        <tr><td style="width: 13%">营运部</td><td style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f101']."</td>";}?><td style="width: 15%"></td></tr>
        <tr><td>整体情况</td><td>新合同7天内安排首次比例 （成功7天首次客户数目/整体当月新IA,IB合同数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c102']."(".$model->excel[$a]['e102'].")</td>";}?><td >95% - 100% ： 5<br/>90% - 95% ： 4<br/>85% - 90% ： 3<br/>80% - 85% ： 2<br/>75% - 80% ： 1<br/><75% : 0</td></tr>
        <tr><td rowspan="3">物流情况</td><td>运送皂液准确度 （实际送皂液/应送皂液）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c103']."(".$model->excel[$a]['e103'].")</td>";}?><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>  80% - 85% ： 2<br/>   75% - 80% ： 1<br/>  <75% : 0</td></tr>
        <tr><td>运送销售货品准确度 （实际送销售货品/应送销售货品）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c104']."(".$model->excel[$a]['e104'].")</td>";}?><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>    75% - 80% ： 1<br/>    <75% : 0</td></tr>
        <tr><td>汽车支出平均 （C:M)<br/>C : 车辆数目，M：车的平均用油量</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c105']."</td>";}?><td>仅供参考，不计算分数</td></tr>
        <tr><td rowspan="2">仓库情况</td><td>每月盘点准确度</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c106']."(".$model->excel[$a]['e106'].")</td>";}?><td>>108% : 0<br/>  104% - 108% ： 1<br/>  100% -104% ： 3<br/>  96% - 100% ： 5<br/>  92% - 96% ： 3<br/>   88% - 92% ： 1</td></tr>
        <tr><td>新合同5天内安排安装比例 （成功5天安装客户数目/今月新IA服务合同数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c107']."(".$model->excel[$a]['e107'].")</td>";}?><td>95% - 100% ： 5<br/>   90% - 95% ： 4<br/>   85% - 90% ： 3<br/>   80% - 85% ： 2<br/>   75% - 80% ： 1<br/>    <= 75% : 0</td></tr>
        <tr><td rowspan="3">质检情况</td><td>当月质检客户数量效率 （跟标准每月客户拜访数目比较）<br/>（估计客户每个约￥1500/月，当地服务客户金额/客户金额估值=客户约数量，客户约数量除6（希望每12个月拜访客户一次），等于标准每月客户拜访数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c108']."(".$model->excel[$a]['e108'].")</td>";}?><td>>90% : 5<br/>  70% - 90% :４<br/>   50% - 70% : 3<br/>  30% - 50% : 2<br/>   10% - 30% : 1<br/>  <= 10% : 0</td></tr>
        <tr><td>质检问题客户数量比例<br/>（问题客户 ： 质检拜访客户分数低于70分。问题客户/当月质检拜访客户 = 质检问题客户数量比例）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c109']."(".$model->excel[$a]['e109'].")</td>";}?><td>>20% : 3<br/>    10% - 20% : 5<br/>    0% - 10% : 1</td></tr>
        <tr><td>表现满意技术员 (质检拜访表平均分数最高同事）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c110']."</td>";}?><td>仅供参考，不计算分数</td></tr>
        <tr><td rowspan="5">客诉处理</td><td>当月客诉数目比较（当月客诉数目 - 上月客诉数目 / 上月客诉数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c111']."(".$model->excel[$a]['e111'].")</td>";}?><td><-30% : 5<br/>  -30% - -20% : 4<br/>  -20% - -10% : 3<br/>  -10% - 0% : 2<br/>   0% - 5% : 1<br/>   >5% : 0</td></tr>
        <tr><td>客诉解决效率（高效客诉数目 = 2工作天内处理完成客诉数量）<br/>（客诉解决效率 = 高效客诉解决数目/当月客诉数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c112']."(".$model->excel[$a]['e112'].")</td>";}?><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/>  85% - 90% ： 3<br/>  80% - 85% ： 2<br/> 75% - 80% ： 1<br/> <75% : 0</td></tr>
        <tr><td>队长跟投诉技术员面谈比例<br/>（队长/组长面谈客诉技术员数目/客诉数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c113']."(".$model->excel[$a]['e113'].")</td>";}?><td>15% - 20% ： 5<br/> 10% - 15% ： 3<br/>  5% - 10% ： 1<br/> <5% : 0 </td></tr>
        <tr><td>高效回访率 （高效回访 = 客诉后7天内电话客户回访数目）<br/>（高效回访率=高效回访/今月客诉数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c114']."(".$model->excel[$a]['e114'].")</td>";}?><td>95% - 100% ： 5<br/> 90% - 95% ： 4<br/> 85% - 90% ： 3<br/> 80% - 85% ： 2<br/> 75% - 80% ： 1<br/>  <75% : 0</td></tr>
        <tr><td>问题客户需要队长/组长跟进数目</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c115']."</td>";}?><td>仅供参考，不计算分数</td></tr>

        <tr><td style="width: 13%">人事部</td><td  style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f116']."</td>";}?><td style="width: 15%"></td></tr>
        <tr><td>整体情况</td><td>所有同事劳动合同进展 (超过一个月没有签署劳动合同同事数目（张））</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c117']."(".$model->excel[$a]['e117'].")</td>";}?><td>0 : 5<br/>1 - 3 : 4<br/>3 - 5 : 3<br/>>5 : 0</td></tr>
        <tr><td rowspan="2">销售人员情况</td><td>销售人员流失率 （工作满一个月的）（离职销售人员/当月所有销售人员）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c118']."(".$model->excel[$a]['e118'].")</td>";}?><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td></tr>
        <tr><td>销售区域空置率（公共区域/销售划分区域）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c119']."(".$model->excel[$a]['e119'].")</td>";}?><td>0% - 20%     :  5<br/>20% - 60%   :  3<br/>60%  - 100% :  1</td></tr>
        <tr><td rowspan="3">外勤人员情况</td><td>离职技术员（工作满一个月的）人数% （当月离职技术人员/整体外勤技术人员）/td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c120']."(".$model->excel[$a]['e120'].")</td>";}?><td>0% - 5% : 5<br/>5% - 10% : 3<br/>10% - 15% : 1<br/>>15% : 0</td></tr>
        <tr><td>队长数目跟标准比例 （最多每5个技术员，就要有一个队长的设置)<br/>(技术员数目/6=标准队长数目， 比例 = 队长数目/标准队长数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c121']."(".$model->excel[$a]['e121'].")</td>";}?><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td></tr>
        <tr><td>组长数目跟标准比例 （最多每30个技术员，就要有一个组长的设置)<br/>(技术员数目/30=标准组长数目， 比例 = 组长数目/标准数目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c122']."(".$model->excel[$a]['e122'].")</td>";}?><td>>100% : 5<br/>80% - 100% : 3<br/><= 80% : 1</td></tr>
        <tr><td>办公室人员情况</td><td>离职办公室（工作满一个月的）人数% （当月离职办公室人员/整体办公室人员）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c124']."(".$model->excel[$a]['e124'].")</td>";}?><td>0% - 10% : 5<br/>10% - 20% : 3<br/>20% - 30% : 1<br/>>30% : 0</td></tr>
    </table>
    </div>
    </div>
</section>
<?php } else{echo "<br/><h1>暂无数据</h1>";}?>
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

