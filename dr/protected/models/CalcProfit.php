<?php

class CalcProfit extends Calculation {

	public static function getLastMonth($year, $month) {
        $city = Yii::app()->user->city();
        $months=$month-1;
        $sql="select data_value from swo_monthly_dtl where 
				data_field='00067' and hdr_id=(select id from swo_monthly_hdr where city='".$city."' and year_no='".$year."'  and month_no='".$months."')";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
	}

    public static function getLastYear($year, $month) {
        $city = Yii::app()->user->city();
        $years=$year-1;
		$sql="select data_value from swo_monthly_dtl where 
				data_field='00068' and hdr_id=(select id from swo_monthly_hdr where city='".$city."' and year_no='".$years."'  and month_no='".$month."')";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }

	public static function sumServiceAmount($year, $month) {
		$rtn = array();
		
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.city, sum(ifnull(b.data_value,0)) as total
				from operation$suffix.opr_monthly_hdr a 
				inner join operation$suffix.opr_monthly_dtl b on a.id=b.hdr_id
				where a.year_no=$year and a.month_no=$month  
				and b.data_field in ('10001','10002','10003','10004','10005','20001','20002','20004')
				and b.data_value REGEXP '^[0-9]+\\.?[0-9]*$'
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['total'],2,'.','');
		}
		return $rtn;
	}

	public static function sumRecAmount($year, $month) {
		$rtn = array();
		
		$suffix = Yii::app()->params['envSuffix'];
		$month_start_dt = date("Y-m-d",strtotime("$year-$month-1"));
		$end_dt = date("Y-m-t",strtotime("$year-$month-1"));
		
		$sql = "select a.city, sum(account$suffix.TransAmountByLCDWoIntTrf('IN',a.id,b.city,'$month_start_dt','$end_dt')) as total
				from swo_monthly_hdr a, account$suffix.acc_account b
				where a.year_no=$year and a.month_no=$month and (a.city=b.city or a.city='99999')
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['total'],2,'.','');
		}
		return $rtn;
	}
}

?>
