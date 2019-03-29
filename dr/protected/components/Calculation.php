<?php
class Calculation {
    public static function getLastMonthFigure($year, $month, $index) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);

		$rtn = array();
		$sql = "select a.city, b.data_value from swo_monthly_hdr a, swo_monthly_dtl b 
				where a.id=b.hdr_id and b.data_field='$index' and a.year_no=$ly and a.month_no=$lm 
				order by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['data_value'];
		}
		return $rtn;
    }

    public static function getLastYearFigure($year, $month, $index) {
		$d = strtotime('-1 year', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);

		$rtn = array();
		$sql = "select a.city, b.data_value from swo_monthly_hdr a, swo_monthly_dtl b 
				where a.id=b.hdr_id and b.data_field='$index' and a.year_no=$ly and a.month_no=$lm 
				order by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['data_value'];
		}
		return $rtn;
    }

    public static function getOperationFigure($year, $month, $index) {
		$ly = $year;
		$lm = $month;
		$suffix = Yii::app()->params['envSuffix'];

		$rtn = array();
		$sql = "select a.city, b.data_value from operation$suffix.opr_monthly_hdr a, operation$suffix.opr_monthly_dtl b 
				where a.id=b.hdr_id and b.data_field='$index' and a.year_no=$ly and a.month_no=$lm 
				order by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['data_value'];
		}
		return $rtn;
    }
	
	public static function getCityArray() {
		$rtn = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.code
				from security$suffix.sec_city a left outer join security$suffix.sec_city b on a.code=b.region 
				where b.code is null 
				order by a.code
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['code']] = 0;
		}
		return $rtn;
	}
}
?>