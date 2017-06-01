<?php
class RptMonthly extends ReportData2 {

	public function retrieveData() {
		if (isset($this->criteria)) {
			$city = $this->criteria->city;
			$year = $this->criteria->year;
			$month = $this->criteria->month;
		}
		$sql = "select b.month_no, c.excel_row, a.data_value, c.field_type 
				from 
					swo_monthly_dtl a, swo_monthly_hdr b, swo_monthly_field c  
				where 
					a.hdr_id = b.id and 
					a.data_field = c.code and 
					b.city = '$city' and 
					b.year_no = $year and 
					b.month_no <= $month and
					c.status = 'Y'
				order by b.month_no, c.excel_row 
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['month_no'] = $row['month_no'];
				$temp['row_no'] = $row['excel_row'];
				$temp['value'] = $row['data_value'];
				$temp['type'] = $row['field_type'];
				$this->data[] = $temp;
			}
		}
		return true;
	}
}
?>