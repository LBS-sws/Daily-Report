<?php
/* Reimbursement Form */

class ReportH02Form extends CReportForm
{
	public $staffs;
	public $staffs_desc;
	
	protected function labelsEx() {
		return array(
				'staffs'=>Yii::t('report','Staffs'),
			);
	}
	
	protected function rulesEx() {
        return array(
            array('staffs, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
			);
	}
	
	public function init() {
		$this->id = 'RptFive';
		$this->name = Yii::t('app','Five Steps');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'start_dt,end_dt,staffs,staffs_desc';
		$this->start_dt = date("Y/m/d");
        $this->end_dt = date("Y/m/d");
		$this->five = array();
        $this->staffs = '';
        $this->month="";
        $this->year="";
		$this->staffs_desc = Yii::t('misc','All');
	}

	public function retrieveData($model){
        //获取月份
        $start_date = str_replace("/","-",$model['_scenario']['start_dt']); // 自动为00:00:00 时分秒
        $end_date = str_replace("/","-",$model['_scenario']['end_dt']);
        $start_arr = explode("-", $start_date);
        $end_arr = explode("-", $end_date);
        $start_year = intval($start_arr[0]);
        $start_month = intval($start_arr[1]);
        $end_year = intval($end_arr[0]);
        $end_month = intval($end_arr[1]);
        $diff_year = $end_year-$start_year;
        $month_arr = [];
        $year_arr=[];
        if($diff_year == 0){
            for($month = $start_month;$month<=$end_month;$month++){
                $month_arr[] = $month;
                $year_arr[] = $start_year;
            }
        } else {
            for($year =$start_year;$year<=$end_year;$year++){
                if($year == $start_year){
                    for($month = $start_month;$month<=12;$month++){
                        $month_arr[] = $month;
                        $year_arr[] = $year;
                    }
                }elseif($year==$end_year){
                    for($month = 1;$month<=$end_month;$month++){
                        $month_arr[] = $month;
                        $year_arr[] = $year;
                    }
                }else{
                    for($month = 1;$month<=12;$month++){
                        $month_arr[] = $month;
                        $year_arr[] = $year;
                    }
                }
            }
        }
        $i=0;
        $city=$model['_scenario']['city'];//获得城市
        $sum=count($month_arr);//获得个数
        $arr=array();
        for($i=0;$i<count($month_arr);$i++){
            $sql= "select b.month_no, c.excel_row, a.data_value, c.field_type,c.name
				from
					swo_monthly_dtl a, swo_monthly_hdr b, swo_monthly_field c
				where
					a.hdr_id = b.id and
					a.data_field = c.code and
					b.city = '$city' and
					b.year_no = '".$year_arr[$i]."' and
					b.month_no = '".$month_arr[$i]."' and
					c.status = 'Y'
				order by b.month_no, c.excel_row
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $arr[]=$rows;
        }
        $this->five=$arr;
        $this->year=$year_arr;
        $this->month=$month_arr;
        $this->ccuser=$sum;
//        print_r('<pre>');
//        print_r($model);
    }

}
