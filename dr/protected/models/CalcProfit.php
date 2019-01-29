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


}

?>
