<?php

class RptFeedbackstat extends ReportData2 {
	// For manually adjust the label content
	protected $rpt_labels = array(
			'city'=>array('label'=>'City','width'=>20,'align'=>'C'),
			'week_0'=>array('label'=>'Week 0','width'=>25,'align'=>'C'),
			'week_1'=>array('label'=>'Week 1','width'=>25,'align'=>'C'),
			'week_2'=>array('label'=>'Week 2','width'=>25,'align'=>'C'),
			'week_3'=>array('label'=>'Week 3','width'=>25,'align'=>'C'),
			'week_4'=>array('label'=>'Week 4','width'=>25,'align'=>'C'),
		);
		public function fields() {		return $this->rpt_labels;
	}
	public function getSelectString() {
		return Yii::t('report','Date').': '.$this->criteria->year.'/'.$this->criteria->month;
	}
	
	public function retrieveData() {
		$year = empty($this->criteria->year) ? date('Y') : $this->criteria->year;
		$month = empty($this->criteria->month) ? date('m') : $this->criteria->month;
		$first_date = $year.'-'.$month.'-01';
		$last_date = date('Y-m-d',strtotime(date('Y-m-d',strtotime($first_date.' +1 month')).' -1 day')); 
		$first_date2 = date('Y-m-d',strtotime($first_date.' -1 day'));
		
		$sql = "select week('".$first_date."',0) as first_week, week('".$last_date."',0) as last_week,
				week('".$first_date2."',0) as first_week2
			";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$yearX = $row['first_week']==0 ? date('Y',strtotime($first_date2)): $year;
			$weekX = $row['first_week']==0 ? $row['first_week2'] : $row['first_week'];
			$sweek = $row['first_week'];
			$eweek = $row['last_week'];
			$sql = "select str_to_date('".$yearX.$weekX." Sunday','%X%V %W') as start_date, 
					str_to_date('".$year.$eweek." Saturday','%X%V %W') as end_date
			";
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			if ($row!==false) {
				$this->criteria->start_dt = $row['start_date'];
				$this->criteria->end_dt = $row['end_date'];

				$this->rpt_labels['city']['label'] = Yii::t('feedback',$this->rpt_labels['city']['label']);
				for ($i=0; $i<=4; $i++) {
					$w = $sweek + $i;
					if ($w <= $eweek) {
						$sdate = ($i==0) ? $this->criteria->start_dt : date('Y-m-d',strtotime($this->criteria->start_dt.' +'.($i*7).' day'));
						$edate = date('Y-m-d',strtotime($sdate.' +6 day'));
						$this->rpt_labels['week_'.$i]['label'] = $sdate.' ~ '.$edate;
					} else
						$this->rpt_labels['week_'.$i]['label'] = '';
				}
			}
		}
		
		// Initiate Data Array
		$suffix = Yii::app()->params['envSuffix'];
		$rptdata = array();
		$sql = "select a.code, a.name from security$suffix.sec_city a left outer join security$suffix.sec_city b 
				on a.code=b.region
				where b.code is null and a.region is not null 
				order by a.code
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$rptdata[$row['code']] = array(
						'city'=>$row['name'],
						'week_0'=>0,
						'week_1'=>0,
						'week_2'=>0,
						'week_3'=>0,
						'week_4'=>0,
					);
			}
		}
		
		// Fill in Statistic into Data Array
		$sql = "select a.city, week(a.request_dt,0) as weeknum, 
					sum(case when a.status='Y' and datediff(a.feedback_dt,a.request_dt) < 2 then 1 else 0 end) as counter 
				from swo_mgr_feedback a 
				where a.id>0   
		";
		if (isset($this->criteria)) {
			$where = '';
			if (isset($this->criteria->start_dt))
				$where .= " and "."a.request_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			if (isset($this->criteria->end_dt))
				$where .= " and "."a.request_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
			if ($where!='') $sql .= $where;	
		}
		$sql .= " group by a.city, week(a.request_dt,0)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();		if (count($rows) > 0) {			foreach ($rows as $row) {
				$weekno = $row['weeknum'];
				$idx = $weekno - $sweek;
				if (isset($rptdata[$row['city']]))
					$rptdata[$row['city']]['week_'.$idx] = $row['counter'];			}		}
		$this->data = $rptdata;		return true;	}
}
?>