<?php
/* Reimbursement Form */

class ReportG02Form extends CReportForm
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
		$this->city = $this->city();
		$this->fields = 'start_dt,end_dt,staffs,staffs_desc';
		$this->start_dt = date("Y/m/d");
        $this->end_dt = date("Y/m/d");
		$this->five = array();
		$this->date="";
        $this->staffs = '';
        $this->month="";
        $this->year="";
        $this->excel=array();
		$this->staffs_desc = Yii::t('misc','All');
	}

    public function retrieveDatas($model){
        $start_date = '2017-01-01'; // 自动为00:00:00 时分秒
        $end_date = date("Y-m-d");
        $start_arr = explode("-", $start_date);
        $end_arr = explode("-", $end_date);
        $start_year = intval($start_arr[0]);
        $start_month = intval($start_arr[1]);
        $end_year = intval($end_arr[0]);
        $end_month = intval($end_arr[1]);
        $diff_year = $end_year-$start_year;
        $year_arr=[];
        for($year=$end_year;$year>=$start_year;$year--){
            $year_arr[] = $year;
        }
        $this->date=$year_arr;
    }

    public function city(){
        $suffix = Yii::app()->params['envSuffix'];
        $model = new City();
        $city=Yii::app()->user->city();
        $records=$model->getDescendant($city);
        array_unshift($records,$city);
        $cityname=array();
        foreach ($records as $k) {
            $sql = "select name from security$suffix.sec_city where code='" . $k . "'";
            $name = Yii::app()->db->createCommand($sql)->queryAll();
            $cityname[]=$name[0]['name'];
        }
        $city=array_combine($records,$cityname);
        return $city;
    }

    public function retrieveData($model){
        //获取月份
        $start_date = $model['_scenario']['start_dt']."-".$model['_scenario']['start_dt1']."-1"; // 自动为00:00:00 时分秒
        $end_date = $model['_scenario']['end_dt']."-".$model['_scenario']['end_dt1']."-1";
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
        $city_allow = City::model()->getDescendantList($model['scenario']['city']);

        if(empty($city_allow)){
            $city_allow="'".$model['scenario']['city']."'";
        }
        //生意额增长 本月/上月/去年当月
        $business=$this->business($year_arr,$month_arr,$city_allow);
        $businessMonth=$this->businessMonth($year_arr,$month_arr,$city_allow);
        $businessYear=$this->businessYear($year_arr,$month_arr,$city_allow);
        //纯利润增长 本月/上月/去年当月
        $profit=$this->profit($year_arr,$month_arr,$city_allow);
        $profitMonth=$this->profitMonth($year_arr,$month_arr,$city_allow);
        $profitYear=$this->profitYear($year_arr,$month_arr,$city_allow);
        //停单比例  本月/上月/去年当月
        $stoporder=$this->stoporder($year_arr,$month_arr,$city_allow);
        $stoporderMonth=$this->stoporderMonth($year_arr,$month_arr,$city_allow);
        $stoporderYear=$this->stoporderYear($year_arr,$month_arr,$city_allow);
        $stopordermax=$this->stopordermax($year_arr,$month_arr,$city_allow);
        //收款率  本月/上月/去年当月
        $receipt=$this->receipt($year_arr,$month_arr,$city_allow);
        $receiptMonth=$this->receiptMonth($year_arr,$month_arr,$city_allow);
        $receiptYear=$this->receiptYear($year_arr,$month_arr,$city_allow);
        $receiptmax=$this->receiptmax($year_arr,$month_arr,$city_allow);
        //技术员平均生产力  (月报的技术当月平均生意额）
        $productivity=$this->productivity($year_arr,$month_arr,$city_allow);
        $productivityMonth=$this->productivityMonth($year_arr,$month_arr,$city_allow);
        $productivityYear=$this->productivityYear($year_arr,$month_arr,$city_allow);
        $productivitymax=$this->productivitymax($year_arr,$month_arr,$city_allow);
        //月报表分数
        $report=$this->report($year_arr,$month_arr,$city_allow);
        $reportMonth=$this->reportMonth($year_arr,$month_arr,$city_allow);
        $reportYear=$this->reportYear($year_arr,$month_arr,$city_allow);
        $reportmax=$this->reportmax($year_arr,$month_arr,$city_allow);
        //回馈
        $feedback=$this->feedback($year_arr,$month_arr,$city_allow);
        $feedbackMonth=$this->feedbackMonth($year_arr,$month_arr,$city_allow);
        $feedbackYear=$this->feedbackYear($year_arr,$month_arr,$city_allow);
        $feedbackmax=$this->feedbackmax($year_arr,$month_arr,$city_allow);
        //质检拜访量（月报里的质检客户数量） quality
        $quality=$this->quality($year_arr,$month_arr,$city_allow);
        $qualityMonth=$this->qualityMonth($year_arr,$month_arr,$city_allow);
        $qualityYear=$this->qualityYear($year_arr,$month_arr,$city_allow);
        $qualitymax=$this->qualitymax($year_arr,$month_arr,$city_allow);
        //销售拜访量
        $visit=$this->visit($year_arr,$month_arr,$city_allow);
        $visitMonth=$this->visitMonth($year_arr,$month_arr,$city_allow);
        $visitYear=$this->visitYear($year_arr,$month_arr,$city_allow);
        $visitmax=$this->visitmax($year_arr,$month_arr,$city_allow);
        //签单成交率 （当月签单量/当月拜访量）
        $signing=$this->signing($year_arr,$month_arr,$city_allow);
        $signingMonth=$this->signingMonth($year_arr,$month_arr,$city_allow);
        $signingYear=$this->signingYear($year_arr,$month_arr,$city_allow);
        $signingmax=$this->signingmax($year_arr,$month_arr,$city_allow);
        $arr=array();
//                 print_r('<pre>');
//        print_r($reportmax);exit();
        for ($i=0;$i<count($month_arr);$i++){
            $arr[$i]['time']=  $start=$year_arr[$i]."年".$month_arr[$i]."月" ;;
           $arr[$i]['business']=$business[$i];
           $arr[$i]['businessMonth']=$businessMonth[$i];
           $arr[$i]['businessYear']=$businessYear[$i];
           $arr[$i]['profit']=$profit[$i];
           $arr[$i]['profitMonth']=$profitMonth[$i];
           $arr[$i]['profitYear']=$profitYear[$i];
           $arr[$i]['stoporder']=$stoporder[$i];
           $arr[$i]['stoporderMonth']=$stoporderMonth[$i];
           $arr[$i]['stoporderYear']=$stoporderYear[$i];
           $arr[$i]['stopordermax']=$stopordermax[$i];
           $arr[$i]['receipt']=$receipt[$i];
           $arr[$i]['receiptMonth']=$receiptMonth[$i];
           $arr[$i]['receiptYear']=$receiptYear[$i];
           $arr[$i]['receiptmax']=$receiptmax[$i];
           $arr[$i]['productivity']=$productivity[$i];
           $arr[$i]['productivityMonth']=$productivityMonth[$i];
           $arr[$i]['productivityYear']=$productivityYear[$i];
           $arr[$i]['productivitymax']=$productivitymax[$i];
           $arr[$i]['report']=$report[$i];
           $arr[$i]['reportMonth']=$reportMonth[$i];
           $arr[$i]['reportYear']=$reportYear[$i];
           $arr[$i]['reportmax']=$reportmax[$i];
           $arr[$i]['feedback']=$feedback[$i];
           $arr[$i]['feedbackMonth']=$feedbackMonth[$i];
           $arr[$i]['feedbackYear']=$feedbackYear[$i];
           $arr[$i]['feedbackmax']=$feedbackmax[$i];
           $arr[$i]['quality']=$quality[$i];
           $arr[$i]['qualityMonth']=$qualityMonth[$i];
           $arr[$i]['qualityYear']=$qualityYear[$i];
           $arr[$i]['qualitymax']=$qualitymax[$i];
           $arr[$i]['visit']=$visit[$i];
           $arr[$i]['visitMonth']=$visitMonth[$i];
           $arr[$i]['visitYear']=$visitYear[$i];
           $arr[$i]['visitmax']=$visitmax[$i];
           $arr[$i]['signing']=$signing[$i];
           $arr[$i]['signingMonth']=$signingMonth[$i];
           $arr[$i]['signingYear']=$signingYear[$i];
           $arr[$i]['signingmax']=$signingmax[$i];
        }
            $model['excel']=$arr;

    }
    //提取月报表数据
    public function value($city,$year,$month,$data_field){
        $sql = "select  a.data_value,b.year_no,b.month_no,a.id,a.hdr_id
				from
					swo_monthly_dtl a  	
					inner join swo_monthly_hdr b on a.hdr_id = b.id 
					inner join swo_monthly_field c	 on  a.data_field = c.code 		  
					where c.status = 'Y' and  b.city in  ($city) and  b.year_no = '$year' and  b.month_no = '$month ' and a.data_field='$data_field'
				
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }
    //提取月报表分数
    public function fenshu($city,$year,$month){
            $sql="select b.month_no, c.excel_row, a.data_value, c.field_type ,c.name
				from 
					swo_monthly_dtl a, swo_monthly_hdr b, swo_monthly_field c  
				where 
					a.hdr_id = b.id and 
					a.data_field = c.code and 
					b.city=$city and 
					b.year_no = '$year' and 
					b.month_no = '$month' and
					c.status = 'Y' 
				order by b.month_no, c.excel_row ";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            if(!empty($rows)){
                if(empty($rows[64])){
                    $b3=intval($rows[0]['data_value']);
                    $b4=intval($rows[1]['data_value']);
                    $b5=intval($rows[2]['data_value']);
                    $b6=intval($rows[3]['data_value']);
                    $b7=intval($rows[4]['data_value']);
                    $b8=intval($rows[5]['data_value']);
                    $b9=intval($rows[6]['data_value']);
                    $b10=intval($rows[7]['data_value']);
                    $b11=intval($rows[8]['data_value']);
                    $b12=intval($rows[9]['data_value']);
                    $b13=intval($rows[10]['data_value']);
                    $b14=intval($rows[11]['data_value']);
                    $b15=intval($rows[12]['data_value']);
                    $b16=intval($rows[13]['data_value']);
                    $b17=intval($rows[14]['data_value']);
                    $b18=intval($rows[15]['data_value']);
                    $b19=intval($rows[16]['data_value']);
                    $b20=intval($rows[17]['data_value']);
                    $b21=intval($rows[18]['data_value']);
                    $b22=intval($rows[19]['data_value']);
                    $b23=intval($rows[20]['data_value']);
                    $b24=intval($rows[21]['data_value']);
                    $b25=intval($rows[22]['data_value']);
                    $b26=intval($rows[23]['data_value']);
                    $b27=intval($rows[24]['data_value']);
                    $b28=intval($rows[25]['data_value']);
                    $b29=intval($rows[26]['data_value']);
                    $b31=intval($rows[27]['data_value']);
                    $b32=intval($rows[28]['data_value']);
                    $b33=intval($rows[29]['data_value']);
                    $b34=intval($rows[30]['data_value']);
                    $b36=intval($rows[31]['data_value']);
                    $b37=intval($rows[32]['data_value']);
                    $b38=intval($rows[33]['data_value']);
                    $b40=intval($rows[34]['data_value']);
                    $b41=intval($rows[35]['data_value']);
                    $b42=intval($rows[36]['data_value']);
                    $b43=intval($rows[37]['data_value']);
                    $b44=intval($rows[38]['data_value']);
                    $b45=intval($rows[39]['data_value']);
                    $b46=intval($rows[40]['data_value']);
                    $b47=intval($rows[41]['data_value']);
                    $b48=intval($rows[42]['data_value']);
                    $b49=intval($rows[43]['data_value']);
                    $b50=intval($rows[44]['data_value']);
                    $b51=intval($rows[45]['data_value']);
                    $b53=intval($rows[46]['data_value']);
                    $b54=intval($rows[47]['data_value']);
                    $b55=intval($rows[48]['data_value']);
                    $b56=intval($rows[49]['data_value']);
                    $b57=intval($rows[50]['data_value']);
                    $b58=intval($rows[51]['data_value']);
                    $b59=intval($rows[52]['data_value']);
                    $b61=intval($rows[53]['data_value']);
                    $b62=intval($rows[54]['data_value']);
                    $b63=intval($rows[55]['data_value']);
                    $b64=intval($rows[56]['data_value']);
                    $b65=intval($rows[57]['data_value']);
                    $b66=intval($rows[58]['data_value']);
                    $b67=intval($rows[59]['data_value']);
                    $b68=intval($rows[60]['data_value']);
                    $b69=intval($rows[61]['data_value']);
                    $b70=intval($rows[62]['data_value']);
                    $b71=intval($rows[63]['data_value']);

                    $c76=($b8-$b7)/abs($b7==0?1:$b7);
                    $c77=($b8-$b9)/abs($b9==0?1:$b9);
                    $c78=($b32-$b31)/abs($b31==0?1:$b31);
                    $c79=($b32-$b34)/abs($b34==0?1:$b34);
                    $c80=($b11-$b10)/abs($b10==0?1:$b10);
                    $c81=($b11-$b12)/abs($b12==0?1:$b12);
                    $c82=($b16-$b15)/abs($b15==0?1:$b15);
                    $c83=($b16-$b17)/abs($b17==0?1:$b17);
                    $c84=$b13/($b14==0?1:$b14);
                    $c85=$b5/($b6==0?1:$b6);
                    $c86=$b19/($b4==0?1:$b4);
                    $c88=($b20-30000)/30000;
                    $c89=($b21-30000)/30000;
                    $c90=$b21;
                    $c91=$b25/($b5==0?1:$b5);
                    $c92=$b26/($b6==0?1:$b6);
                    $c93=$b36/($b65==0?1:$b65);
                    $c94=$b37.":".$b38;
                    $c96=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:$b5+$b6);
                    $c97=$b28/($b4==0?1:$b4);
                    $c98=$b23/($b3==0?1:$b3);
                    $c99=$b29;
                    $c100=$b22/($b4==0?1:$b4);
                    $c102=$b51/($b32==0?1:$b32);
                    $c103=$b56/($b55==0?1:$b55);
                    $c104=$b58/($b57==0?1:$b57);
                    $c106=$b59/100;
                    $c105=$b53.":".$b54;
                    $c107=$b50/($b33==0?1:$b33);
                    $c108=$b47/(($b18==0?1:$b18)/(1500*12));
                    $c109=$b48/($b47==0?1:$b47);
                    $c110=$b49;
                    $c111=($b41-$b40)/abs($b40==0?1:$b40);
                    $c112=$b43/($b41==0?1:$b41);
                    $c113=$b45/($b41==0?1:$b41);
                    $c114=$b44/($b41==0?1:$b41);
                    $c115=$b46;
                    $c117=$b61;
                    $c118=$b62/($b68==0?1:$b68);
                    $c119=$b71/($b70==0?1:$b70);
                    $c120=$b63/($b65==0?1:$b65);
                    $c121=$b66/(($b65==0?1:$b65)/6);
                    $c122=$b67/(($b65==0?1:$b65)/30);
                    $c124=$b64/($b69==0?1:$b69);

                    $e76=($c76>0.2?5:($c76>0.1?4:($c76>0?3:($c76>-0.1?2:($c76>-0.2?1:0)))));
                    $e77=($c77>0.2?5:($c77>0.1?4:($c77>0?3:($c77>-0.1?2:($c77>-0.2?1:0)))));
                    $e78=($c78>0.4?5:($c78>0.2?4:($c78>0?3:($c78>-0.2?2:($c78>-0.4?1:0)))));
                    $e79=($c79>0.4?5:($c79>0.2?4:($c79>0?3:($c79>-0.2?2:($c79>-0.4?1:0)))));
                    $e80=($c80>3?5:($c80>1?4:($c80>0?3:($c80>-1?2:($c80>-2?1:0)))));
                    $e81=($c81>3?5:($c81>1?4:($c81>0?3:($c81>-1?2:($c81>-2?1:0)))));
                    $e82=($c82>0.2?5:($c82>0.1?4:($c82>0?3:($c82>-0.1?2:($c82>-0.2?1:0)))));
                    $e83=($c83>0.2?5:($c83>0.1?4:($c83>0?3:($c83>-0.1?2:($c83>-0.2?1:0)))));
                    $e84=($c84>2.3?1:($c84>1.5?3:($c84>=1?5:($c84>0.7?4:($c84>0.4?2:($c84>0.2?1:0))))));
                    $e85=($c85>2.3?1:($c85>1.5?3:($c85>=1?5:($c85>0.7?4:($c85>0.4?2:($c85>0.2?1:0))))));
                    $e86=($c86>0.032?1:($c86>0.024?2:($c86>0.016?3:($c86>0.008?4:($c86>0?5:5)))));
                    $e88=($c88>0.2?5:($c88>0?4:($c88>-0.1?3:($c88>-0.2?2:($c88>-0.3?1:0)))));
                    $e89=($c89>0.7?5:($c89>0.3?4:($c89>0.1?3:0)));
                    $e90='NIL';
                    $e91=($c91>0.3?0:($c91>0.25?1:($c91>0.2?2:($c91>0.15?3:($c91>0.1?4:5)))));
                    $e92=($c92>0.25?0:($c92>0.2?1:($c92>0.15?2:($c92>0.1?3:($c92>0.05?4:5)))));
                    $e93=($c93>0.2?5:($c93>0.1?3:($c93>0.05?1:0)));
                    $e94='NIL';
                    $e96=($c96>0.555?5:($c96>0.5?4:($c96>0.45?3:($c96>0.4?2:($c96>0.35?1:0)))));
                    $e97=($c97>0.35?1:($c97>0.3?2:($c97>0.28?3:($c97>0.25?3:($c97>0.2?5:0)))));
                    $e98=($c98>1?5:($c98>0.95?4:($c98>0.9?3:($c98>0.85?2:($c98>0.8?1:0)))));
                    $e99='NIL';
                    $e100=($c100>0.7?0:($c100>0.6?1:($c100>0.5?2:($c100>0.4?3:($c100>0.3?4:5)))));
                    $e102=($c102>0.95?5:($c102>0.9?4:($c102>0.85?3:($c102>0.8?2:($c102>=0.75?1:0)))));
                    $e103=($c103>0.95?5:($c103>0.9?4:($c103>0.85?3:($c103>0.8?2:($c103>=0.75?1:0)))));
                    $e104=($c104>0.95?5:($c104>0.9?4:($c104>0.85?3:($c104>0.8?2:($c104>=0.75?1:0)))));
                    $e105='NIL';
                    $e106=($c106>1.08?0:($c106>1.04?1:($c106>1?3:($c106>0.96?5:($c106>0.92?3:($c106>0.88?1:0))))));
                    $e107=($c107>0.95?5:($c107>0.9?4:($c107>0.85?3:($c107>0.8?2:($c107>=0.75?1:0)))));
                    $e108=($c108>0.9?5:($c108>0.7?4:($c108>0.5?3:($c108>0.3?2:($c108>0.1?1:0)))));
                    $e109=($c109>0.2?3:($c109>0.1?5:($c109>=0?1:0)));
                    $e110='NIL';
                    $e111=($c111>0.05?0:($c111>0?1:($c111>-0.1?2:($c111>-0.2?3:($c111>-0.3?4:5)))));
                    $e112=($c112>0.95?5:($c112>0.9?4:($c112>0.85?3:($c112>0.8?2:($c112>=0.75?1:0)))));
                    $e113=($c113>0.15?5:($c113>0.1?3:($c113>0.05?1:0)));
                    $e114=($c114>0.95?5:($c114>0.9?4:($c114>0.85?3:($c114>0.8?2:($c114>=0.75?1:0)))));
                    $e115='NIL';
                    $e117=($c117>5?0:($c117>3?3:($c117>1?4:5)));
                    $e118=($c118>0.3?0:($c118>0.2?1:($c118>0.1?3:5)));
                    $e119=($c119>0.6?1:($c119>0.2?3:5));
                    $e120=($c120>0.15?0:($c120>0.1?1:($c120>0.05?3:5)));
                    $e121=($c121>1?5:($c121>0.8?3:1));
                    $e122=($c122>1?5:($c122>0.8?3:1));
                    $e124=($c124>0.3?0:($c124>0.2?1:($c124>0.1?3:5)));
                    $f75=round($e76+$e77+$e78+$e79+$e80+$e81+$e82+$e83+$e84+$e85+$e86,2);
                    $f87=round($e88+$e89+$e90+$e91+$e92+$e93+$e94,2);
                    $f95=round($e100+$e96+$e97+$e98+$e99,2);
                    $f101=round($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114,2);
                    $f116=round($e122+$e117+$e118+$e119+$e120+$e121+$e124,2);
                    $f74=round(($f75+$f87+$f95+$f101+$f116)/190*100,2);
                }elseif (!empty($rows[67])){
                    $b3=intval($rows[0]['data_value']);
                    $b4=intval($rows[1]['data_value']);
                    $b5=intval($rows[2]['data_value']);
                    $b6=intval($rows[3]['data_value']);
                    $b7=intval($rows[4]['data_value']);
                    $b8=intval($rows[5]['data_value']);
                    $b9=intval($rows[6]['data_value']);
                    $b10=intval($rows[7]['data_value']);
                    $b11=intval($rows[8]['data_value']);
                    $b12=intval($rows[9]['data_value']);
                    $b13=intval($rows[10]['data_value']);
                    $b14=intval($rows[11]['data_value']);
                    $b15=intval($rows[12]['data_value']);
                    $b16=intval($rows[13]['data_value']);
                    $b17=intval($rows[14]['data_value']);
                    $b18=intval($rows[15]['data_value']);
                    $b19=intval($rows[16]['data_value']);
                    $b20=intval($rows[17]['data_value']);
                    $b21=intval($rows[18]['data_value']);
                    $b22=intval($rows[19]['data_value']);
                    $b23=intval($rows[20]['data_value']);
                    $b24=intval($rows[21]['data_value']);
                    $b25=intval($rows[22]['data_value']);
                    $b26=intval($rows[23]['data_value']);
                    $b27=intval($rows[24]['data_value']);
                    $b28=intval($rows[25]['data_value']);
                    $b29=intval($rows[26]['data_value']);
                    $b31=intval($rows[27]['data_value']);
                    $b32=intval($rows[28]['data_value']);
                    $b33=intval($rows[29]['data_value']);
                    $b34=intval($rows[30]['data_value']);
                    $b35=intval($rows[31]['data_value']);
                    $b37=intval($rows[32]['data_value']);
                    $b38=intval($rows[33]['data_value']);
                    $b39=intval($rows[34]['data_value']);
                    $b41=intval($rows[35]['data_value']);
                    $b42=intval($rows[36]['data_value']);
                    $b43=intval($rows[37]['data_value']);
                    $b44=intval($rows[38]['data_value']);
                    $b45=intval($rows[39]['data_value']);
                    $b46=intval($rows[40]['data_value']);
                    $b47=intval($rows[41]['data_value']);
                    $b48=intval($rows[42]['data_value']);
                    $b49=intval($rows[43]['data_value']);
                    $b50=$rows[44]['data_value'];
                    $b51=intval($rows[45]['data_value']);
                    $b52=intval($rows[46]['data_value']);
                    $b54=intval($rows[47]['data_value']);
                    $b55=intval($rows[48]['data_value']);
                    $b56=intval($rows[49]['data_value']);
                    $b57=intval($rows[50]['data_value']);
                    $b58=intval($rows[51]['data_value']);
                    $b59=intval($rows[52]['data_value']);
                    $b60=intval($rows[53]['data_value']);
                    $b62=intval($rows[54]['data_value']);
                    $b63=intval($rows[55]['data_value']);
                    $b64=intval($rows[56]['data_value']);
                    $b65=intval($rows[57]['data_value']);
                    $b66=intval($rows[58]['data_value']);
                    $b67=intval($rows[59]['data_value']);
                    $b68=intval($rows[60]['data_value']);
                    $b69=intval($rows[61]['data_value']);
                    $b70=intval($rows[62]['data_value']);
                    $b71=intval($rows[63]['data_value']);
                    $b72=intval($rows[64]['data_value']);
                    $b73=intval($rows[65]['data_value']);
                    $b74=intval($rows[66]['data_value']);
                    $b75=intval($rows[67]['data_value']);
                    $c76=($b8-$b7)/abs($b7==0?1:$b7);
                    $c77=($b8-$b9)/abs($b9==0?1:$b9);
                    $c78=($b32-$b31)/abs($b31==0?1:$b31);
                    $c79=($b32-$b35)/abs($b35==0?1:$b35);
                    $c80=($b11-$b10)/abs($b10==0?1:$b10);
                    $c81=($b11-$b12)/abs($b12==0?1:$b12);
                    $c82=($b16-$b15)/abs($b15==0?1:$b15);
                    $c83=($b16-$b17)/abs($b17==0?1:$b17);
                    $c84=$b13/($b14==0?1:$b14);
                    $c85=$b5/($b6==0?1:$b6);
                    $c86=$b19/($b4==0?1:$b4);
                    $c88=($b20-30000)/30000;
                    $c89=($b21-30000)/30000;
                    $c90=$b21;
                    $c91=$b25/($b5==0?1:$b5);
                    $c92=$b26/($b6==0?1:$b6);
                    $c93=$b37/($b66==0?1:$b66);
                    $c94=$b38.":".$b39;
                    $c96=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:($b5+$b6));
                    $c97=$b28/($b4==0?1:$b4);
                    $c98=$b23/($b3==0?1:$b3);
                    $c99=$b29;
                    $c100=$b22/($b4==0?1:$b4);
                    $c102=$b52/($b32==0?1:$b32);
                    $c103=$b57/($b56==0?1:$b56);
                    $c104=$b59/($b58==0?1:$b58);
                    $c105=0;
                    $c106=$b60/100;
                    $c107=$b51/($b34==0?1:$b34);
                    $c108=$b48/(($b18==0?1:$b18)/(1500*12));
                    $c109=$b49/($b48==0?1:$b48);
                    $c110=$b50;
                    $c111=($b42-$b41)/abs($b41==0?1:$b41);
                    $c112=$b44/($b42==0?1:$b42);
                    $c113=$b46/($b42==0?1:$b42);
                    $c114=$b45/($b42==0?1:$b42);
                    $c115=$b47;
                    $c117=$b62;
                    $c118=$b63/($b69==0?1:$b69);
                    $c119=$b72/($b71==0?1:$b71);
                    $c120=$b64/($b66==0?1:$b66);
                    $c121=$b67/(($b66==0?1:$b66)/6);
                    $c122=$b68/(($b66==0?1:$b66)/30);
                    $c123=0;
                    $c124=$b65/($b70==0?1:$b70);
                    //利润的
                    $bc102=$b74/($b4==0?1:$b4);
                    $bc103=($b74-$b73)/abs(($b73==0?1:$b73));
                    $bc104=($b74-$b75)/abs(($b75==0?1:$b75));
                    $be102=($bc102>0.2?5:($bc102>0.15?4:($bc102>0.1?3:($bc102>0.05?2:1))));
                    $be103=($bc103>=0.03?5:($bc103>=0.025?4:($bc103>=0.02?3:($bc103>=0.015?2:($bc103>=0.01?1:0)))));
                    $be104=($bc104>0.34?5:($bc104>0.25?4:($bc104>0.16?3:($bc104>0.08?2:($bc104>0?1:0)))));

                    $e76=($c76>0.2?5:($c76>0.1?4:($c76>0?3:($c76>-0.1?2:($c76>-0.2?1:0)))));
                    $e77=($c77>0.2?5:($c77>0.1?4:($c77>0?3:($c77>-0.1?2:($c77>-0.2?1:0)))));
                    $e78=($c78>0.4?5:($c78>0.2?4:($c78>0?3:($c78>-0.2?2:($c78>-0.4?1:0)))));
                    $e79=($c79>0.4?5:($c79>0.2?4:($c79>0?3:($c79>-0.2?2:($c79>-0.4?1:0)))));
                    $e80=($c80>3?5:($c80>1?4:($c80>0?3:($c80>-1?2:($c80>-2?1:0)))));
                    $e81=($c81>3?5:($c81>1?4:($c81>0?3:($c81>-1?2:($c81>-2?1:0)))));
                    $e82=($c82>0.2?5:($c82>0.1?4:($c82>0?3:($c82>-0.1?2:($c82>-0.2?1:0)))));
                    $e83=($c83>0.2?5:($c83>0.1?4:($c83>0?3:($c83>-0.1?2:($c83>-0.2?1:0)))));
                    $e84=($c84>2.3?1:($c84>1.5?3:($c84>=1?5:($c84>0.7?4:($c84>0.4?2:($c84>0.2?1:0))))));
                    $e85=($c85>2.3?1:($c85>1.5?3:($c85>=1?5:($c85>0.7?4:($c85>0.4?2:($c85>0.2?1:0))))));
                    $e86=($c86>0.032?1:($c86>0.024?2:($c86>0.016?3:($c86>0.008?4:($c86>0?5:5)))));
                    $e88=($c88>0.2?5:($c88>0?4:($c88>-0.1?3:($c88>-0.2?2:($c88>-0.3?1:0)))));
                    $e89=($c89>0.7?5:($c89>0.3?4:($c89>0.1?3:0)));
                    $e90='NIL';
                    $e91=($c91>0.3?0:($c91>0.25?1:($c91>0.2?2:($c91>0.15?3:($c91>0.1?4:5)))));
                    $e92=($c92>0.25?0:($c92>0.2?1:($c92>0.15?2:($c92>0.1?3:($c92>0.05?4:5)))));
                    $e93=($c93>0.2?5:($c93>0.1?3:($c93>0.05?1:0)));
                    $e94='NIL';
                    $e96=($c96>0.555?5:($c96>0.5?4:($c96>0.45?3:($c96>0.4?2:($c96>0.35?1:0)))));
                    $e97=($c97>0.35?1:($c97>0.3?2:($c97>0.28?3:($c97>0.25?3:($c97>0.2?5:0)))));
                    $e98=($c98>1?5:($c98>0.95?4:($c98>0.9?3:($c98>0.85?2:($c98>0.8?1:0)))));
                    $e99='NIL';
                    $e100=($c100>0.7?0:($c100>0.6?1:($c100>0.5?2:($c100>0.4?3:($c100>0.3?4:5)))));
                    $e102=($c102>0.95?5:($c102>0.9?4:($c102>0.85?3:($c102>0.8?2:($c102>=0.75?1:0)))));
                    $e103=($c103>0.95?5:($c103>0.9?4:($c103>0.85?3:($c103>0.8?2:($c103>=0.75?1:0)))));
                    $e104=($c104>0.95?5:($c104>0.9?4:($c104>0.85?3:($c104>0.8?2:($c104>=0.75?1:0)))));
                    $e105='NIL';
                    $e106=($c106>1.08?0:($c106>1.04?1:($c106>1?3:($c106>0.96?5:($c106>0.92?3:($c106>0.88?1:0))))));
                    $e107=($c107>0.95?5:($c107>0.9?4:($c107>0.85?3:($c107>0.8?2:($c107>=0.75?1:0)))));
                    $e108=($c108>0.9?5:($c108>0.7?4:($c108>0.5?3:($c108>0.3?2:($c108>0.1?1:0)))));
                    $e109=($c109>0.2?3:($c109>0.1?5:($c109>=0?1:0)));
                    $e110='NIL';
                    $e111=($c111>0.05?0:($c111>0?1:($c111>-0.1?2:($c111>-0.2?3:($c111>-0.3?4:5)))));
                    $e112=($c112>0.95?5:($c112>0.9?4:($c112>0.85?3:($c112>0.8?2:($c112>=0.75?1:0)))));
                    $e113=($c113>0.15?5:($c113>0.1?3:($c113>0.05?1:0)));
                    $e114=($c114>0.95?5:($c114>0.9?4:($c114>0.85?3:($c114>0.8?2:($c114>=0.75?1:0)))));
                    $e115='NIL';
                    $e117=($c117>5?0:($c117>3?3:($c117>1?4:5)));
                    $e118=($c118>0.3?0:($c118>0.2?1:($c118>0.1?3:5)));
                    $e119=($c119>0.6?1:($c119>0.2?3:5));
                    $e120=($c120>0.15?0:($c120>0.1?1:($c120>0.05?3:5)));
                    $e121=($c121>1?5:($c121>0.8?3:1));
                    $e122=($c122>1?5:($c122>0.8?3:1));
                    $e124=($c124>0.3?0:($c124>0.2?1:($c124>0.1?3:5)));
                    $f75=round(($e76+$e77+$e78+$e79+$e80+$e81+$e82+$e83+$e84+$e85+$e86)/55*30,2);
                    $f87=round(($e94+$e88+$e89+$e90+$e91+$e92+$e93)/25*20,2);
                    $f95=round(($e100+$e96+$e97+$e98+$e99+$be102+$be103+$be104)/35*25,2);
                    $f101=round(($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
                    $f116=round(($e124+$e117+$e118+$e119+$e120+$e121+$e122)/35*10,2);
                    $f74=$f75+$f87+$f95+$f101+$f116;
                }
                else{
                    $b3=intval($rows[0]['data_value']);
                    $b4=intval($rows[1]['data_value']);
                    $b5=intval($rows[2]['data_value']);
                    $b6=intval($rows[3]['data_value']);
                    $b7=intval($rows[4]['data_value']);
                    $b8=intval($rows[5]['data_value']);
                    $b9=intval($rows[6]['data_value']);
                    $b10=intval($rows[7]['data_value']);
                    $b11=intval($rows[8]['data_value']);
                    $b12=intval($rows[9]['data_value']);
                    $b13=intval($rows[10]['data_value']);
                    $b14=intval($rows[11]['data_value']);
                    $b15=intval($rows[12]['data_value']);
                    $b16=intval($rows[13]['data_value']);
                    $b17=intval($rows[14]['data_value']);
                    $b18=intval($rows[15]['data_value']);
                    $b19=intval($rows[16]['data_value']);
                    $b20=intval($rows[17]['data_value']);
                    $b21=intval($rows[18]['data_value']);
                    $b22=intval($rows[19]['data_value']);
                    $b23=intval($rows[20]['data_value']);
                    $b24=intval($rows[21]['data_value']);
                    $b25=intval($rows[22]['data_value']);
                    $b26=intval($rows[23]['data_value']);
                    $b27=intval($rows[24]['data_value']);
                    $b28=intval($rows[25]['data_value']);
                    $b29=intval($rows[26]['data_value']);
                    $b31=intval($rows[27]['data_value']);
                    $b32=intval($rows[28]['data_value']);
                    $b33=intval($rows[29]['data_value']);
                    $b35=intval($rows[31]['data_value']);
                    $b34=intval($rows[30]['data_value']);
                    $b35=intval($rows[31]['data_value']);
                    $b37=intval($rows[32]['data_value']);
                    $b38=intval($rows[33]['data_value']);
                    $b39=intval($rows[34]['data_value']);
                    $b41=intval($rows[35]['data_value']);
                    $b42=intval($rows[36]['data_value']);
                    $b43=intval($rows[37]['data_value']);
                    $b44=intval($rows[38]['data_value']);
                    $b45=intval($rows[39]['data_value']);
                    $b46=intval($rows[40]['data_value']);
                    $b47=intval($rows[41]['data_value']);
                    $b48=intval($rows[42]['data_value']);
                    $b49=intval($rows[43]['data_value']);
                    $b50=intval($rows[44]['data_value']);
                    $b51=intval($rows[45]['data_value']);
                    $b52=intval($rows[46]['data_value']);
                    $b54=intval($rows[47]['data_value']);
                    $b55=intval($rows[48]['data_value']);
                    $b56=intval($rows[49]['data_value']);
                    $b57=intval($rows[50]['data_value']);
                    $b58=intval($rows[51]['data_value']);
                    $b59=intval($rows[52]['data_value']);
                    $b60=intval($rows[53]['data_value']);
                    $b62=intval($rows[54]['data_value']);
                    $b63=intval($rows[55]['data_value']);
                    $b64=intval($rows[56]['data_value']);
                    $b65=intval($rows[57]['data_value']);
                    $b66=intval($rows[58]['data_value']);
                    $b67=intval($rows[59]['data_value']);
                    $b68=intval($rows[60]['data_value']);
                    $b69=intval($rows[61]['data_value']);
                    $b70=intval($rows[62]['data_value']);
                    $b71=intval($rows[63]['data_value']);
                    $b72=intval($rows[64]['data_value']);

                    $c76=($b8-$b7)/abs($b7==0?1:$b7);
                    $c77=($b8-$b9)/abs($b9==0?1:$b9);
                    $c78=($b32-$b31)/abs($b31==0?1:$b31);
                    $c79=($b32-$b35)/abs($b35==0?1:$b35);
                    $c80=($b11-$b10)/abs($b10==0?1:$b10);
                    $c81=($b11-$b12)/abs($b12==0?1:$b12);
                    $c82=($b16-$b15)/abs($b15==0?1:$b15);
                    $c83=($b16-$b17)/abs($b17==0?1:$b17);
                    $c84=$b13/($b14==0?1:$b14);
                    $c85=$b5/($b6==0?1:$b6);
                    $c86=$b19/($b4==0?1:$b4);
                    $c88=($b20-30000)/30000;
                    $c89=($b21-30000)/30000;
                    $c90=$b21;
                    $c91=$b25/($b5==0?1:$b5);
                    $c92=$b26/($b6==0?1:$b6);
                    $c93=$b37/($b66==0?1:$b66);
                    $c94=0;
                    $c96=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:($b5+$b6));
                    $c97=$b28/($b4==0?1:$b4);
                    $c98=$b23/($b3==0?1:$b3);
                    $c99=$b29;
                    $c100=$b22/($b4==0?1:$b4);
                    $c102=$b52/($b32==0?1:$b32);
                    $c103=$b57/($b56==0?1:$b56);
                    $c104=$b59/($b58==0?1:$b58);
                    $c105=0;
                    $c106=$b60/100;
                    $c107=$b51/($b34==0?1:$b34);
                    $c108=$b48/(($b18==0?1:$b18)/(1500*12));
                    $c109=$b49/($b48==0?1:$b48);
                    $c110=$b50;
                    $c111=($b42-$b41)/abs($b41==0?1:$b41);
                    $c112=$b44/($b42==0?1:$b42);
                    $c113=$b46/($b42==0?1:$b42);
                    $c114=$b45/($b42==0?1:$b42);
                    $c115=$b47;
                    $c117=$b62;
                    $c118=$b63/($b69==0?1:$b69);
                    $c119=$b72/($b71==0?1:$b71);
                    $c120=$b64/($b66==0?1:$b66);
                    $c121=$b67/(($b66==0?1:$b66)/6);
                    $c122=$b68/(($b66==0?1:$b66)/30);
                    $c123=0;
                    $c124=$b65/($b70==0?1:$b70);

                    $e76=($c76>0.2?5:($c76>0.1?4:($c76>0?3:($c76>-0.1?2:($c76>-0.2?1:0)))));
                    $e77=($c77>0.2?5:($c77>0.1?4:($c77>0?3:($c77>-0.1?2:($c77>-0.2?1:0)))));
                    $e78=($c78>0.4?5:($c78>0.2?4:($c78>0?3:($c78>-0.2?2:($c78>-0.4?1:0)))));
                    $e79=($c79>0.4?5:($c79>0.2?4:($c79>0?3:($c79>-0.2?2:($c79>-0.4?1:0)))));
                    $e80=($c80>3?5:($c80>1?4:($c80>0?3:($c80>-1?2:($c80>-2?1:0)))));
                    $e81=($c81>3?5:($c81>1?4:($c81>0?3:($c81>-1?2:($c81>-2?1:0)))));
                    $e82=($c82>0.2?5:($c82>0.1?4:($c82>0?3:($c82>-0.1?2:($c82>-0.2?1:0)))));
                    $e83=($c83>0.2?5:($c83>0.1?4:($c83>0?3:($c83>-0.1?2:($c83>-0.2?1:0)))));
                    $e84=($c84>2.3?1:($c84>1.5?3:($c84>=1?5:($c84>0.7?4:($c84>0.4?2:($c84>0.2?1:0))))));
                    $e85=($c85>2.3?1:($c85>1.5?3:($c85>=1?5:($c85>0.7?4:($c85>0.4?2:($c85>0.2?1:0))))));
                    $e86=($c86>0.032?1:($c86>0.024?2:($c86>0.016?3:($c86>0.008?4:($c86>0?5:5)))));
                    $e88=($c88>0.2?5:($c88>0?4:($c88>-0.1?3:($c88>-0.2?2:($c88>-0.3?1:0)))));
                    $e89=($c89>0.7?5:($c89>0.3?4:($c89>0.1?3:0)));
                    $e90='NIL';
                    $e91=($c91>0.3?0:($c91>0.25?1:($c91>0.2?2:($c91>0.15?3:($c91>0.1?4:5)))));
                    $e92=($c92>0.25?0:($c92>0.2?1:($c92>0.15?2:($c92>0.1?3:($c92>0.05?4:5)))));
                    $e93=($c93>0.2?5:($c93>0.1?3:($c93>0.05?1:0)));
                    $e94='NIL';
                    $e96=($c96>0.555?5:($c96>0.5?4:($c96>0.45?3:($c96>0.4?2:($c96>0.35?1:0)))));
                    $e97=($c97>0.35?1:($c97>0.3?2:($c97>0.28?3:($c97>0.25?3:($c97>0.2?5:0)))));
                    $e98=($c98>1?5:($c98>0.95?4:($c98>0.9?3:($c98>0.85?2:($c98>0.8?1:0)))));
                    $e99='NIL';
                    $e100=($c100>0.7?0:($c100>0.6?1:($c100>0.5?2:($c100>0.4?3:($c100>0.3?4:5)))));
                    $e102=($c102>0.95?5:($c102>0.9?4:($c102>0.85?3:($c102>0.8?2:($c102>=0.75?1:0)))));
                    $e103=($c103>0.95?5:($c103>0.9?4:($c103>0.85?3:($c103>0.8?2:($c103>=0.75?1:0)))));
                    $e104=($c104>0.95?5:($c104>0.9?4:($c104>0.85?3:($c104>0.8?2:($c104>=0.75?1:0)))));
                    $e105='NIL';
                    $e106=($c106>1.08?0:($c106>1.04?1:($c106>1?3:($c106>0.96?5:($c106>0.92?3:($c106>0.88?1:0))))));
                    $e107=($c107>0.95?5:($c107>0.9?4:($c107>0.85?3:($c107>0.8?2:($c107>=0.75?1:0)))));
                    $e108=($c108>0.9?5:($c108>0.7?4:($c108>0.5?3:($c108>0.3?2:($c108>0.1?1:0)))));
                    $e109=($c109>0.2?3:($c109>0.1?5:($c109>=0?1:0)));
                    $e110='NIL';
                    $e111=($c111>0.05?0:($c111>0?1:($c111>-0.1?2:($c111>-0.2?3:($c111>-0.3?4:5)))));
                    $e112=($c112>0.95?5:($c112>0.9?4:($c112>0.85?3:($c112>0.8?2:($c112>=0.75?1:0)))));
                    $e113=($c113>0.15?5:($c113>0.1?3:($c113>0.05?1:0)));
                    $e114=($c114>0.95?5:($c114>0.9?4:($c114>0.85?3:($c114>0.8?2:($c114>=0.75?1:0)))));
                    $e115='NIL';
                    $e117=($c117>5?0:($c117>3?3:($c117>1?4:5)));
                    $e118=($c118>0.3?0:($c118>0.2?1:($c118>0.1?3:5)));
                    $e119=($c119>0.6?1:($c119>0.2?3:5));
                    $e120=($c120>0.15?0:($c120>0.1?1:($c120>0.05?3:5)));
                    $e121=($c121>1?5:($c121>0.8?3:1));
                    $e122=($c122>1?5:($c122>0.8?3:1));
                    $e124=($c124>0.3?0:($c124>0.2?1:($c124>0.1?3:5)));
                    $f75=round(($e76+$e77+$e78+$e79+$e80+$e81+$e82+$e83+$e84+$e85+$e86)/55*30,2);
                    $f87=round(($e88+$e89+$e90+$e91+$e92+$e93+$e94)/25*20,2);
                    $f95=round(($e100+$e96+$e97+$e98+$e99)/20*25,2);
                    $f101=round(($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
                    $f116=round(($e122+$e117+$e118+$e119+$e120+$e121+$e124)/35*10,2);
                    $f74=$f75+$f87+$f95+$f101+$f116;
                }
            }else{
                $f74=0;
            }

            return $f74;
    }

    //生意额增长 本月/上月/去年当月
    public function business($year,$month,$city){
	    $arr=array();
        for($i=0;$i<count($month);$i++) {
            $rows=$this->value($city,$year[$i],$month[$i],'00002');
            $business= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($business,0);
        }
        return $arr;
    }
    public function businessMonth($year,$month,$city){
        $arr=array();
        $business=$this->business($year,$month,$city);
        for($i=0;$i<count($month);$i++) {
            $rows=$this->value($city,$year[$i],$month[$i],'00001');
            $businessMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$businessMonth;
            $arr[$i]=($business[$i]-$arr[$i])/abs($arr[$i]==0?1:$arr[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";

        }
        return $arr;
    }
    public function businessYear($year,$month,$city){
        $arr=array();
        $business=$this->business($year,$month,$city);
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            $rows=$this->value($city,$year[$i],$month[$i],'00002');
            $businessYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$businessYear;
            $arr[$i]=($business[$i]-$arr[$i])/abs($arr[$i]==0?1:$arr[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";

        }
        return $arr;
    }
//纯利润增长 本月/上月/去年当月
    public function profit($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $rows=$this->value($city,$year[$i],$month[$i],'00067');
            $profit= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($profit,0);
        }
        return $arr;
    }
    public function profitMonth($year,$month,$city){
        $arr=array();
        $profit=$this->profit($year,$month,$city);
        for($i=0;$i<count($month);$i++) {
            $rows=$this->value($city,$year[$i],$month[$i],'00066');
            if(!empty($rows)){
                $profitMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
                $arr[]=$profitMonth;
                $arr[$i]=($profit[$i]-$arr[$i])/abs($arr[$i]==0?1:$arr[$i]);
                $arr[$i]=(round( $arr[$i],4)*100)."%";
            }else{
                $arr[$i]="暂无数据";
            }
        }
        return $arr;
    }
    public function profitYear($year,$month,$city){
        $arr=array();
        $profit=$this->profit($year,$month,$city);
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            $rows=$this->value($city,$year[$i],$month[$i],'00067');
            if(!empty($rows)){
            $profitYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$profitYear;
            $arr[$i]=($profit[$i]-$arr[$i])/abs($arr[$i]==0?1:$arr[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
            }else{
                $arr[$i]="暂无数据";
            }

        }
        return $arr;
    }

    //停单比例  本月/上月/去年当月  当月停单总月金额/当月生意额
    public function stoporder($year,$month,$city){
        $business=$this->business($year,$month,$city);
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $rows=$this->value($city,$year[$i],$month[$i],'00017');
            $stoporder= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$stoporder;
            $arr[$i]=$arr[$i]/abs($business[$i]==0?1:$business[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;
    }
    public function stoporderMonth($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            //停单生意额上月的
            $rows=$this->value($city,$year[$i],$month[$i],'00017');
            $stoporderMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$stoporderMonth;
            //上月生意额
            $row=$this->value($city,$year[$i],$month[$i],'00002');
            $businessMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $row));
            $business[]=$businessMonth;
            $arr[$i]=$arr[$i]/abs($business[$i]==0?1:$business[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;


    }
    public function stoporderYear($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            //停单生意额去年当月的
            $rows=$this->value($city,$year[$i],$month[$i],'00017');
            $stoporderMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$stoporderMonth;
            //去年当月生意额
            $row=$this->value($city,$year[$i],$month[$i],'00002');
            $businessYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $row));
            $business[]=$businessYear;
            $arr[$i]=$arr[$i]/abs($business[$i]==0?1:$business[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;

    }
    public function stopordermax($year,$month,$city){
        for($i=0;$i<count($month);$i++) {
            $suffix = Yii::app()->params['envSuffix'];
            $rows17=$this->value($city,$year[$i],$month[$i],'00017');
            $arr17[]=$rows17;
            $rows02=$this->value($city,$year[$i],$month[$i],'00002');
            $arr02[]=$rows02;
            $o=0;
            for($p=0;$p<count($arr17[$i]);$p++) {
                $end=$arr17[$i][$o]['hdr_id'];
                $sql="select a.name,a.code from security$suffix.sec_city a ,swo_monthly_hdr b  where a.code=b.city and b.id='".$end."'";
                $cityname = Yii::app()->db->createCommand($sql)->queryRow();
                if($cityname['code']=="TY"||$cityname['code']=="KS"||$cityname['code']=="TN"||$cityname['code']=="TC"||$cityname['code']=="HK"||$cityname['code']=="TP"||$cityname['code']=="ZS1"||$cityname['code']=="HN"||$cityname['code']=="MY"||$cityname['code']=="ZY"||$cityname['code']=="HXHB"||$cityname['code']=="MO"||$cityname['code']=="HD"||$cityname['code']=="JMS"||$cityname['code']=="XM"||$cityname['code']=="CS"||$cityname['code']=="HX"||$cityname['code']=="H-N"||$cityname['code']=="HD1"||$cityname['code']=="RN"||$cityname['code']=="HN1"||$cityname['code']=="HN2"||$cityname['code']=="CN"||$cityname['code']=="HB"){
                }else{
                    $arrs[$i][$o]['value'] = $arr17[$i][$o]['data_value'] / abs($arr02[$i][$o]['data_value'] == 0 ? 1 : $arr02[$i][$o]['data_value']);
                    $arrs[$i][$o]['value']=(round(  $arrs[$i][$o]['value'],4)*100)."%";
                    $arrs[$i][$o]['city']= $cityname['name'];
                    $o=$o+1;
                }

            }
            $last_names = array_column($arrs[$i],'value');
            array_multisort($last_names,SORT_DESC,$arrs[$i]);
          //  $arr[$i]=$arr[$i]/abs($business[$i]==0?1:$business[$i]);
          //  $arr[$i]=(round( $arr[$i],4)*100)."%";
            $model[$i]['max']=$arrs[$i][0]['value']." (".$arrs[$i][0]['city'].")";
            $model[$i]['end']=$arrs[$i][$o-1]['value']." (".$arrs[$i][$o-1]['city'].")";
        }
        return $model;
    }
    //"收款率
    //当月收款额/上月生意额"

    public function receipt($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            //当月收款额
            $rows=$this->value($city,$year[$i],$month[$i],'00021');
            $receipt= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$receipt;
            //上月生意额
            $row=$this->value($city,$year[$i],$month[$i],'00001');
            $businessMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $row));
            $business[]=$businessMonth;
            $arr[$i]=$arr[$i]/abs($business[$i]==0?1:$business[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;
    }
    public function receiptMonth($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            //上月收款额
            $rows=$this->value($city,$year[$i],$month[$i],'00021');
            $receiptMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$receiptMonth;
            //上月生意额
            $row=$this->value($city,$year[$i],$month[$i],'00001');
            $businessMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $row));
            $business[]=$businessMonth;
            $arr[$i]=$arr[$i]/abs($business[$i]==0?1:$business[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;
    }
    public function receiptYear($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            //当月收款额
            $rows=$this->value($city,$year[$i],$month[$i],'00021');
            $receiptYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=$receiptYear;
            //上月生意额
            $row=$this->value($city,$year[$i],$month[$i],'00001');
            $businessYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $row));
            $business[]=$businessYear;
            $arr[$i]=$arr[$i]/abs($business[$i]==0?1:$business[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;
    }
    public function receiptmax($year,$month,$city){
        for($i=0;$i<count($month);$i++) {
            $suffix = Yii::app()->params['envSuffix'];
            $rows21=$this->value($city,$year[$i],$month[$i],'00021');
            $arr21[]=$rows21;
            $rows01=$this->value($city,$year[$i],$month[$i],'00001');
            $arr01[]=$rows01;
            $o=0;
            for($p=0;$p<count($arr21[$i]);$p++) {
                $end=$arr21[$i][$o]['hdr_id'];
                $sql="select a.name,a.code from security$suffix.sec_city a ,swo_monthly_hdr b  where a.code=b.city and b.id='".$end."'";
                $cityname = Yii::app()->db->createCommand($sql)->queryRow();
                if($cityname['code']=="TY"||$cityname['code']=="KS"||$cityname['code']=="TN"||$cityname['code']=="TC"||$cityname['code']=="HK"||$cityname['code']=="TP"||$cityname['code']=="ZS1"||$cityname['code']=="HN"||$cityname['code']=="MY"||$cityname['code']=="ZY"||$cityname['code']=="HXHB"||$cityname['code']=="MO"||$cityname['code']=="HD"||$cityname['code']=="JMS"||$cityname['code']=="XM"||$cityname['code']=="CS"||$cityname['code']=="HX"||$cityname['code']=="H-N"||$cityname['code']=="HD1"||$cityname['code']=="RN"||$cityname['code']=="HN1"||$cityname['code']=="HN2"||$cityname['code']=="CN"||$cityname['code']=="HB"){
                }else{
                    $arrs[$i][$o]['value'] = $arr21[$i][$o]['data_value'] / abs($arr01[$i][$o]['data_value'] == 0 ? 1 : $arr01[$i][$o]['data_value']);
                    $arrs[$i][$o]['value'] = (round($arrs[$i][$o]['value'], 4) * 100) . "%";
                    $arrs[$i][$o]['city'] = $cityname['name'];
                    $o=$o+1;
                }
            }
            $last_names = array_column($arrs[$i],'value');
            array_multisort($last_names,SORT_DESC,$arrs[$i]);
            $model[$i]['max']=$arrs[$i][0]['value']." (".$arrs[$i][0]['city'].")";
            $model[$i]['end']=$arrs[$i][$o-1]['value']." (".$arrs[$i][$o-1]['city'].")";
        }
        return $model;
    }

    //技术员平均生产力  (月报的技术当月平均生意额）
    public function productivity($year,$month,$city){
        $arr=array();
        $citys = explode(",", $city);
        if(in_array("'MO'", $citys)){
            $a=19;

        }elseif(in_array("'HD1'", $citys)){
            $a=4;
        }
        elseif(in_array("'HN1'", $citys)){
            $a=9;
        }
        elseif(in_array("'HX'", $citys)){
            $a=6;
        }
        else{
            $a=count($citys);
        }
        for($i=0;$i<count($month);$i++) {
            //当月平均生意额
            $rows=$this->value($city,$year[$i],$month[$i],'00018');
            $productivity= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($productivity/$a,0);
        }
        return $arr;
    }
    public function productivityMonth($year,$month,$city){
        $arr=array();
        $citys = explode(",", $city);
        if(in_array("'MO'", $citys)){
            $a=19;

        }elseif(in_array("'HD1'", $citys)){
            $a=4;
        }
        elseif(in_array("'HN1'", $citys)){
            $a=9;
        }
        elseif(in_array("'HX'", $citys)){
            $a=6;
        }
        else{
            $a=count($citys);
        }
        for($i=0;$i<count($month);$i++) {
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            //上月平均生意额
            $rows=$this->value($city,$year[$i],$month[$i],'00018');
            $productivityMonth= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($productivityMonth/$a,0);
        }
        return $arr;
    }
    public function productivityYear($year,$month,$city){
        $arr=array();
        $citys = explode(",", $city);
        if(in_array("'MO'", $citys)){
            $a=19;

        }elseif(in_array("'HD1'", $citys)){
            $a=4;
        }
        elseif(in_array("'HN1'", $citys)){
            $a=9;
        }
        elseif(in_array("'HX'", $citys)){
            $a=6;
        }
        else{
            $a=count($citys);
        }
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            //去年平均生意额
            $rows=$this->value($city,$year[$i],$month[$i],'00018');
            $productivityYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($productivityYear/$a,0);
        }
        return $arr;
    }
    public function productivitymax($year,$month,$city){
        for($i=0;$i<count($month);$i++) {
            $suffix = Yii::app()->params['envSuffix'];
            //当月平均生意额
            $rows=$this->value($city,$year[$i],$month[$i],'00018');
            $arr[]=$rows;
            $o=0;
            for($p=0;$p<count($arr[$i]);$p++) {
                $end=$arr[$i][$o]['hdr_id'];
                $sql="select a.name,a.code from security$suffix.sec_city a ,swo_monthly_hdr b  where a.code=b.city and b.id='".$end."'";
                $cityname = Yii::app()->db->createCommand($sql)->queryRow();
                if($cityname['code']=="TY"||$cityname['code']=="KS"||$cityname['code']=="TN"||$cityname['code']=="TC"||$cityname['code']=="HK"||$cityname['code']=="TP"||$cityname['code']=="ZS1"||$cityname['code']=="HN"||$cityname['code']=="MY"||$cityname['code']=="ZY"||$cityname['code']=="HXHB"||$cityname['code']=="MO"||$cityname['code']=="HD"||$cityname['code']=="JMS"||$cityname['code']=="XM"||$cityname['code']=="CS"||$cityname['code']=="HX"||$cityname['code']=="H-N"||$cityname['code']=="HD1"||$cityname['code']=="RN"||$cityname['code']=="HN1"||$cityname['code']=="HN2"||$cityname['code']=="CN"||$cityname['code']=="HB"){
                }else{
                    $arrs[$i][$o]['city'] = $cityname['name'];
                    $arrs[$i][$o]['value'] = $arr[$i][$o]['data_value'];
                    $o=$o+1;
                }
            }
            $last_names = array_column($arrs[$i],'value');
            array_multisort($last_names,SORT_DESC,$arrs[$i]);
            $model[$i]['max']=$arrs[$i][0]['value']." (".$arrs[$i][0]['city'].")";
            $model[$i]['end']=$arrs[$i][$o-1]['value']." (".$arrs[$i][$o-1]['city'].")";
        }
        return $model;
    }


    //月报表分数
    public function report($year,$month,$city){
        $city = explode(",", $city);
        for($i=0;$i<count($month);$i++) {
            $o=0;
            $arr=array();
            foreach ($city as $c){
                if($c=="'TY'"||$c=="'KS'"||$c=="'TN'"||$c=="'TC'"||$c=="'HK'"||$c=="'TP'"||$c=="'ZS1'"||$c=="'HN'"||$c=="'MY'"||$c=="'ZY'"||$c=="'HXHB'"||$c=="'MO'"||$c=="'HD'"||$c=="'JMS'"||$c=="'XM'"||$c=="'CS'"||$c=="'HX'"||$c=="'H-N'"||$c=="'HD1'"||$c=="'RN'"||$c=="'HN1'"||$c=="'HN2'"||$c=="'CN'"||$c=="'HB'"){
                    $rows=0;
                }else{
                    $rows=$this-> fenshu($c,$year[$i],$month[$i]);
                }
                $arr[]=$rows;
                if($rows==0){
                    $o=$o+1;
                }
            }
            $count=(count($arr)-$o)==0?1:(count($arr)-$o);
            if(in_array("'JM'", $city)){
                $count=9;
                if(in_array("'HN'", $city)){
                    $count=19;
                }
            }
            $arrs[]=round((array_sum($arr))/$count,2);
        }
        return $arrs;
    }
    public function reportMonth($year,$month,$city){
        $city = explode(",", $city);
        for($i=0;$i<count($month);$i++) {
            $o=0;
            $arr=array();
            //每个月
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            foreach ($city as $c){
                //每个月的所有城市
                if($c=="'TY'"||$c=="'KS'"||$c=="'TN'"||$c=="'TC'"||$c=="'HK'"||$c=="'TP'"||$c=="'ZS1'"||$c=="'HN'"||$c=="'MY'"||$c=="'ZY'"||$c=="'HXHB'"||$c=="'MO'"||$c=="'HD'"||$c=="'JMS'"||$c=="'XM'"||$c=="'CS'"||$c=="'HX'"||$c=="'H-N'"||$c=="'HD1'"||$c=="'RN'"||$c=="'HN1'"||$c=="'HN2'"||$c=="'CN'"||$c=="'HB'"){
                    $rows=0;
                }else{
                    $rows=$this-> fenshu($c,$year[$i],$month[$i]);
                }
                $arr[]=$rows;
                if($rows==0){
                    $o=$o+1;
                }
            }
            $count=(count($arr)-$o)==0?1:(count($arr)-$o);
            if(in_array("'JM'", $city)){
                $count=9;
                if(in_array("'HN'", $city)){
                    $count=19;
                }
            }
            $arrs[]=round((array_sum($arr))/$count,2);
        }
        return $arrs;
    }
    public function reportYear($year,$month,$city){
        $city = explode(",", $city);
        for($i=0;$i<count($month);$i++) {
            $o=0;
            $arr=array();
            $year[$i]=$year[$i]-1;
            foreach ($city as $c){
                if($c=="'TY'"||$c=="'KS'"||$c=="'TN'"||$c=="'TC'"||$c=="'HK'"||$c=="'TP'"||$c=="'ZS1'"||$c=="'HN'"||$c=="'MY'"||$c=="'ZY'"||$c=="'HXHB'"||$c=="'MO'"||$c=="'HD'"||$c=="'JMS'"||$c=="'XM'"||$c=="'CS'"||$c=="'HX'"||$c=="'H-N'"||$c=="'HD1'"||$c=="'RN'"||$c=="'HN1'"||$c=="'HN2'"||$c=="'CN'"||$c=="'HB'"){
                    $rows=0;
                }else{
                    $rows=$this-> fenshu($c,$year[$i],$month[$i]);
                }
                $arr[]=$rows;
                if($rows==0){
                    $o=$o+1;
                }
            }
            $count=(count($arr)-$o)==0?1:(count($arr)-$o);
            if(in_array("'JM'", $city)){
                $count=9;
                if(in_array("'HN'", $city)){
                    $count=19;
                }
            }
            $arrs[]=round((array_sum($arr))/$count,2);
        }
        return $arrs;
    }
    public function reportmax($year,$month,$city){
        $city = explode(",", $city);
        for($i=0;$i<count($month);$i++) {
            $o=0;
            $suffix = Yii::app()->params['envSuffix'];
            foreach ($city as $c){
                if($c=="'TY'"||$c=="'KS'"||$c=="'TN'"||$c=="'TC'"||$c=="'HK'"||$c=="'TP'"||$c=="'ZS1'"||$c=="'HN'"||$c=="'MY'"||$c=="'ZY'"||$c=="'HXHB'"||$c=="'MO'"||$c=="'HD'"||$c=="'JMS'"||$c=="'XM'"||$c=="'CS'"||$c=="'HX'"||$c=="'H-N'"||$c=="'HD1'"||$c=="'RN'"||$c=="'HN1'"||$c=="'HN2'"||$c=="'CN'"||$c=="'HB'"){
                }else{
                        $rows=$this-> fenshu($c,$year[$i],$month[$i]);
                        $arr[$i][$o]['value']=$rows;
                        $sql="select name from security$suffix.sec_city where code=$c";
                        $cityname = Yii::app()->db->createCommand($sql)->queryScalar();
                        $arr[$i][$o]['city']=$cityname;
                        $o=$o+1;
                }
            }
            $last_names = array_column($arr[$i],'value');
            array_multisort($last_names,SORT_DESC,$arr[$i]);
            $model[$i]['max']=$arr[$i][0]['value']." (".$arr[$i][0]['city'].")";
            if($year[$i]==2019&&$month[$i]<=5&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }elseif ($year[$i]==2018&&$month[$i]<=12&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }
            else{
                $model[$i]['end']=$arr[$i][$o-1]['value']." (".$arr[$i][$o-1]['city'].")";
            }

        }
        return $model;
    }

    //回馈
    public function feedback($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql="select 
			    sum(case when a.status='Y' and datediff(a.feedback_dt,a.request_dt) < 2 then 1 else 0 end) as counter 
				from swo_mgr_feedback a 
				where a.id>0  and request_dt>='$start' and request_dt<='$end' and city in ($city) ";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $feedback= array_sum(array_map(create_function('$val', 'return $val["counter"];'), $rows));
            $arr[]=$feedback;

        }
        return $arr;
    }
    public function feedbackMonth($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql="select 
			    sum(case when a.status='Y' and datediff(a.feedback_dt,a.request_dt) < 2 then 1 else 0 end) as counter 
				from swo_mgr_feedback a 
				where a.id>0  and request_dt>='$start' and request_dt<='$end' and city in ($city) ";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $feedbackMonth= array_sum(array_map(create_function('$val', 'return $val["counter"];'), $rows));
            $arr[]=$feedbackMonth;

        }
        return $arr;
    }
    public function feedbackYear($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql="select 
			    sum(case when a.status='Y' and datediff(a.feedback_dt,a.request_dt) < 2 then 1 else 0 end) as counter 
				from swo_mgr_feedback a 
				where a.id>0  and request_dt>='$start' and request_dt<='$end' and city in ($city) ";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $feedbackYear= array_sum(array_map(create_function('$val', 'return $val["counter"];'), $rows));
            $arr[]=$feedbackYear;

        }
        return $arr;
    }
    public function feedbackmax($year,$month,$city){
        $city = explode(",", $city);
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $o=0;
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            foreach ($city as $c){
                if($c=="'TY'"||$c=="'KS'"||$c=="'TN'"||$c=="'TC'"||$c=="'HK'"||$c=="'TP'"||$c=="'ZS1'"||$c=="'HN'"||$c=="'MY'"||$c=="'ZY'"||$c=="'HXHB'"||$c=="'MO'"||$c=="'HD'"||$c=="'JMS'"||$c=="'XM'"||$c=="'CS'"||$c=="'HX'"||$c=="'H-N'"||$c=="'HD1'"||$c=="'RN'"||$c=="'HN1'"||$c=="'HN2'"||$c=="'CN'"||$c=="'HB'"){
                }else {
                        $sql = "select  
			    sum(case when a.status='Y' and datediff(a.feedback_dt,a.request_dt) < 2 then 1 else 0 end) as counter 
				from swo_mgr_feedback a 
				where a.id>0  and request_dt>='$start' and request_dt<='$end' and city =$c ";
                        $rows = Yii::app()->db->createCommand($sql)->queryScalar();
                        $sql1 = "select name from security$suffix.sec_city where code=$c";
                        $cityname = Yii::app()->db->createCommand($sql1)->queryScalar();
                        $arr[$i][$o]['city'] = $cityname;
                        $arr[$i][$o]['value'] = $rows;
                        $o = $o + 1;

                }
            }
            $last_names = array_column($arr[$i],'value');
            array_multisort($last_names,SORT_DESC,$arr[$i]);
            $model[$i]['max']=$arr[$i][0]['value']." (".$arr[$i][0]['city'].")";
            if($year[$i]==2019&&$month[$i]<=5&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }elseif ($year[$i]==2018&&$month[$i]<=12&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }
            else{
                $model[$i]['end']=$arr[$i][$o-1]['value']." (".$arr[$i][$o-1]['city'].")";
            }

        }
        return $model;
    }

    //质检拜访
    public function quality($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $rows=$this->value($city,$year[$i],$month[$i],'00042');
            $productivityYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($productivityYear,0);
        }
        return $arr;
    }
    public function qualityMonth($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            $rows=$this->value($city,$year[$i],$month[$i],'00042');
            $productivityYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($productivityYear,0);
        }
        return $arr;
    }
    public function qualityYear($year,$month,$city){
        $arr=array();
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            $rows=$this->value($city,$year[$i],$month[$i],'00042');
            $productivityYear= array_sum(array_map(create_function('$val', 'return $val["data_value"];'), $rows));
            $arr[]=round($productivityYear,0);
        }
        return $arr;
    }
    public function qualitymax($year,$month,$city){
        for($i=0;$i<count($month);$i++) {
            $suffix = Yii::app()->params['envSuffix'];
            //当月平均生意额
            $rows=$this->value($city,$year[$i],$month[$i],'00042');
            $arr[]=$rows;
            $o=0;
            for($p=0;$p<count($arr[$i]);$p++) {
                $end=$arr[$i][$o]['hdr_id'];
                $sql="select a.name,a.code from security$suffix.sec_city a ,swo_monthly_hdr b  where a.code=b.city and b.id='".$end."'";
                $cityname = Yii::app()->db->createCommand($sql)->queryRow();
                if($cityname['code']=="TY"||$cityname['code']=="KS"||$cityname['code']=="TN"||$cityname['code']=="TC"||$cityname['code']=="HK"||$cityname['code']=="TP"||$cityname['code']=="ZS1"||$cityname['code']=="HN"||$cityname['code']=="MY"||$cityname['code']=="ZY"||$cityname['code']=="HXHB"||$cityname['code']=="MO"||$cityname['code']=="HD"||$cityname['code']=="JMS"||$cityname['code']=="XM"||$cityname['code']=="CS"||$cityname['code']=="HX"||$cityname['code']=="H-N"||$cityname['code']=="HD1"||$cityname['code']=="RN"||$cityname['code']=="HN1"||$cityname['code']=="HN2"||$cityname['code']=="CN"||$cityname['code']=="HB"){
                }else{
                    $arrs[$i][$o]['city'] = $cityname['name'];
                    $arrs[$i][$o]['value'] = $arr[$i][$o]['data_value'];
                    $o=$o+1;
                }
            }
            $last_names = array_column($arrs[$i],'value');
            array_multisort($last_names,SORT_DESC,$arrs[$i]);
            $model[$i]['max']=$arrs[$i][0]['value']." (".$arrs[$i][0]['city'].")";
            $model[$i]['end']=$arrs[$i][$o-1]['value']." (".$arrs[$i][$o-1]['city'].")";
        }
        return $model;
    }

    //销售拜访量
    public function visit($year,$month,$city){
        $arr=array();
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'  
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $visit= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $arr[]=round($visit,0);
        }
        return $arr;
    }
    public function visitMonth($year,$month,$city){
        $arr=array();
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'  
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $visitMonth= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $arr[]=round($visitMonth,0);
        }
        return $arr;
    }
    public function visitYear($year,$month,$city){
        $arr=array();
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'  
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $visitYear= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $arr[]=round($visitYear,0);
        }
        return $arr;
    }
    public function visitmax($year,$month,$city){
        $city = explode(",", $city);
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $o=0;
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            foreach ($city as $c) {
                if($c=="'TY'"||$c=="'KS'"||$c=="'TN'"||$c=="'TC'"||$c=="'HK'"||$c=="'TP'"||$c=="'ZS1'"||$c=="'HN'"||$c=="'MY'"||$c=="'ZY'"||$c=="'HXHB'"||$c=="'MO'"||$c=="'HD'"||$c=="'JMS'"||$c=="'XM'"||$c=="'CS'"||$c=="'HX'"||$c=="'H-N'"||$c=="'HD1'"||$c=="'RN'"||$c=="'HN1'"||$c=="'HN2'"||$c=="'CN'"||$c=="'HB'"){
                }else {
                        $sql = "select count(id) as number from sales$suffix.sal_visit where visit_dt>='$start' and visit_dt<='$end' and city =$c ";
                        $rows = Yii::app()->db->createCommand($sql)->queryScalar();
                        $sql1 = "select name from security$suffix.sec_city where code=$c";
                        $cityname = Yii::app()->db->createCommand($sql1)->queryScalar();
                        $arr[$i][$o]['city'] = $cityname;
                        $arr[$i][$o]['value'] = $rows;
                        $o = $o + 1;

                }
            }
            $last_names = array_column($arr[$i],'value');
            array_multisort($last_names,SORT_DESC,$arr[$i]);
            $model[$i]['max']=$arr[$i][0]['value']." (".$arr[$i][0]['city'].")";
            if($year[$i]==2019&&$month[$i]<=5&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }elseif ($year[$i]==2018&&$month[$i]<=12&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }
            else{
                $model[$i]['end']=$arr[$i][$o-1]['value']." (".$arr[$i][$o-1]['city'].")";
            }

        }
        return $model;
    }

    //签单成交率
    public function signing($year,$month,$city){
        $arr=array();
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'   and  visit_obj like '%\"1\"%'
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $visit= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $visits[]=round($visit,0);
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'   and  visit_obj like '%10%'
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $signing= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $arr[]=$signing;
            $arr[$i]=$arr[$i]/abs($visits[$i]==0?1:$visits[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;
    }
    public function signingMonth($year,$month,$city){
        $arr=array();
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $month[$i]=$month[$i]-1;
            if( $month[$i]==0){
                $month[$i]=12;
                $year[$i]=$year[$i]-1;
            }
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'   and  visit_obj like '%\"1\"%'
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $visitMonth= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $visit[]=round($visitMonth,0);
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'   and  visit_obj like '%10%'
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $signingMonth= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $arr[]=$signingMonth;
            $arr[$i]=$arr[$i]/abs($visit[$i]==0?1:$visit[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;
    }
    public function signingYear($year,$month,$city){
        $arr=array();
        $suffix = Yii::app()->params['envSuffix'];
        for($i=0;$i<count($month);$i++) {
            $year[$i]=$year[$i]-1;
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'   and  visit_obj like '%\"1\"%'
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $visitYear= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $visit[]=round($visitYear,0);
            $sql = "select a.*, b.name as city_name, concat(f.code,' - ',f.name) as staff,  
				d.name as visit_type_name, g.name as cust_type_name,count(id) as number,
				h.name as district_name, VisitObjDesc(a.visit_obj) as visit_obj_name, i.cust_vip
				from sal_visit a 
				inner join hr$suffix.hr_binding c on a.username = c.user_id 
				inner join hr$suffix.hr_employee f on c.employee_id = f.id
				inner join sal_visit_type d on a.visit_type = d.id
				inner join sal_cust_type g on a.cust_type = g.id
				inner join sal_cust_district h on a.district = h.id
				left outer join security$suffix.sec_city b on a.city=b.code
				left outer join sal_custstar i on a.username=i.username and a.cust_name=i.cust_name
				where a.city in ($city) and visit_dt<='".$end."' and visit_dt>='".$start."'   and  visit_obj like '%10%'
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $signingYear= array_sum(array_map(create_function('$val', 'return $val["number"];'), $rows));
            $arr[]=$signingYear;
            $arr[$i]=$arr[$i]/abs($visit[$i]==0?1:$visit[$i]);
            $arr[$i]=(round( $arr[$i],4)*100)."%";
        }
        return $arr;
    }
    public function signingmax($year,$month,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $city = explode(",", $city);
        for($i=0;$i<count($month);$i++) {
            $o=0;
            $start=$year[$i]."-".$month[$i]."-1" ;
            $end=$year[$i]."-".$month[$i]."-31" ;
            foreach ($city as $c) {
                if($c=="'TY'"||$c=="'KS'"||$c=="'TN'"||$c=="'TC'"||$c=="'HK'"||$c=="'TP'"||$c=="'ZS1'"||$c=="'HN'"||$c=="'MY'"||$c=="'ZY'"||$c=="'HXHB'"||$c=="'MO'"||$c=="'HD'"||$c=="'JMS'"||$c=="'XM'"||$c=="'CS'"||$c=="'HX'"||$c=="'H-N'"||$c=="'HD1'"||$c=="'RN'"||$c=="'HN1'"||$c=="'HN2'"||$c=="'CN'"||$c=="'HB'"){
                }else {
                        $sql = "select count(id) as number from sales$suffix.sal_visit where visit_dt>='$start' and visit_dt<='$end' and city =$c and  visit_obj like '%10%'";
                        $row = Yii::app()->db->createCommand($sql)->queryScalar();
                        $sql2 = "select count(id) as number from sales$suffix.sal_visit where visit_dt>='$start' and visit_dt<='$end' and city =$c and  visit_obj like '%\"1\"%'";
                        $rows = Yii::app()->db->createCommand($sql2)->queryScalar();
                        $sql1 = "select name from security$suffix.sec_city where code=$c";
                        $cityname = Yii::app()->db->createCommand($sql1)->queryScalar();
                        $a=$row/($rows==0?1:$rows);
                        $arr[$i][$o]['city'] = $cityname;
                        $arr[$i][$o]['value'] =(round($a,4)*100)."%";
                        $o = $o + 1;

                }
            }
            $last_names = array_column($arr[$i],'value');
            array_multisort($last_names,SORT_DESC,$arr[$i]);
            $model[$i]['max']=$arr[$i][0]['value']." (".$arr[$i][0]['city'].")";
            if($year[$i]==2019&&$month[$i]<=5&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }elseif ($year[$i]==2018&&$month[$i]<=12&&$arr[$i][$o-1]['city']=='江门'){
                $model[$i]['end']=$arr[$i][$o-2]['value']." (".$arr[$i][$o-2]['city'].")";
            }
            else{
                $model[$i]['end']=$arr[$i][$o-1]['value']." (".$arr[$i][$o-1]['city'].")";
            }

        }
        return $model;
    }

    //下载报表
    public function retrieveXiaZai($model){
        Yii::$enableIncludePath = false;
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
        spl_autoload_unregister(array('YiiBase','autoload'));
        include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $objPHPExcel = new PHPExcel;
        $objReader  = PHPExcel_IOFactory::createReader('Excel2007');
        $city_allow = City::model()->getDescendantList($model['scenario']['city']);
        if(empty($city_allow)){
            $city_allow="'".$model['scenario']['city']."'";
        }
        $city = explode(",", $city_allow);
        if(count($city)==1){
            $path = Yii::app()->basePath.'/commands/template/month_comprehensive.xlsx';
            $objPHPExcel = $objReader->load($path);
            $excel_m=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            for($i=0;$i<count($model['excel']);$i++){
                $objPHPExcel->getActiveSheet()->setCellValue('A1', $model['city'][$model['scenario']['city']]) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'1', $model['excel'][$i]['time']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'2', $model['excel'][$i]['business']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'3', $model['excel'][$i]['businessMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'4', $model['excel'][$i]['businessYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'5', $model['excel'][$i]['profit']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'6', $model['excel'][$i]['profitMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'7', $model['excel'][$i]['profitYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'8', $model['excel'][$i]['stoporder']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'9', $model['excel'][$i]['stoporderMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'10', $model['excel'][$i]['stoporderYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'11', $model['excel'][$i]['receipt']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'12', $model['excel'][$i]['receiptMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'13', $model['excel'][$i]['receiptYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'14', $model['excel'][$i]['productivity']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'15', $model['excel'][$i]['productivityMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'16', $model['excel'][$i]['productivityYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'17', $model['excel'][$i]['report']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'18', $model['excel'][$i]['reportMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'19', $model['excel'][$i]['reportYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'20', $model['excel'][$i]['feedback']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'21', $model['excel'][$i]['feedbackMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'22', $model['excel'][$i]['feedbackYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'23', $model['excel'][$i]['quality']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'24', $model['excel'][$i]['qualityMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'25', $model['excel'][$i]['qualityYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'26', $model['excel'][$i]['visit']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'27', $model['excel'][$i]['visitMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'28', $model['excel'][$i]['visitYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'29', $model['excel'][$i]['signing']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'30', $model['excel'][$i]['signingMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'31', $model['excel'][$i]['signingYear']) ;
                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN, //细边框
//’color’ => array(‘argb’ => ‘FFFF0000’),
                        ),
                    ),
                );
                $objPHPExcel->getActiveSheet()->getStyle('A1:'.$excel_m[$i].'31')->applyFromArray($styleArray);
                $styleArray2 = array(
                    'borders' => array(
                        'outline' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
                        ),
                    ),
                );
                $objPHPExcel->getActiveSheet()->getStyle('A1:'.$excel_m[$i].'31')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A5:'.$excel_m[$i].'7')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A8:'.$excel_m[$i].'10')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A14:'.$excel_m[$i].'16')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A20:'.$excel_m[$i].'22')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A23:'.$excel_m[$i].'25')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A26:'.$excel_m[$i].'28')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A29:'.$excel_m[$i].'31')->applyFromArray($styleArray2);
            }
        }else{
            $path = Yii::app()->basePath.'/commands/template/month_comprehensive_quyu.xlsx';
            $objPHPExcel = $objReader->load($path);
            $excel_m=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            for($i=0;$i<count($model['excel']);$i++){
                $objPHPExcel->getActiveSheet()->setCellValue('A1', $model['city'][$model['scenario']['city']]) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'1', $model['excel'][$i]['time']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'2', $model['excel'][$i]['business']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'3', $model['excel'][$i]['businessMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'4', $model['excel'][$i]['businessYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'5', $model['excel'][$i]['profit']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'6', $model['excel'][$i]['profitMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'7', $model['excel'][$i]['profitYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'8', $model['excel'][$i]['stopordermax']['max']." / ".$model['excel'][$i]['stopordermax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'9', $model['excel'][$i]['stoporder']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'10', $model['excel'][$i]['stoporderMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'11', $model['excel'][$i]['stoporderYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'12', $model['excel'][$i]['receiptmax']['max']." / ".$model['excel'][$i]['receiptmax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'13', $model['excel'][$i]['receipt']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'14', $model['excel'][$i]['receiptMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'15', $model['excel'][$i]['receiptYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'16', $model['excel'][$i]['productivitymax']['max']." / ".$model['excel'][$i]['productivitymax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'17', $model['excel'][$i]['productivity']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'18', $model['excel'][$i]['productivityMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'19', $model['excel'][$i]['productivityYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'20', $model['excel'][$i]['reportmax']['max']." / ".$model['excel'][$i]['reportmax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'21', $model['excel'][$i]['report']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'22', $model['excel'][$i]['reportMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'23', $model['excel'][$i]['reportYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'24', $model['excel'][$i]['feedbackmax']['max']." / ".$model['excel'][$i]['feedbackmax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'25', $model['excel'][$i]['feedback']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'26', $model['excel'][$i]['feedbackMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'27', $model['excel'][$i]['feedbackYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'28', $model['excel'][$i]['qualitymax']['max']." / ".$model['excel'][$i]['qualitymax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'29', $model['excel'][$i]['quality']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'30', $model['excel'][$i]['qualityMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'31', $model['excel'][$i]['qualityYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'32', $model['excel'][$i]['visitmax']['max']." / ".$model['excel'][$i]['visitmax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'33', $model['excel'][$i]['visit']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'34', $model['excel'][$i]['visitMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'35', $model['excel'][$i]['visitYear']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'36', $model['excel'][$i]['signingmax']['max']." / ".$model['excel'][$i]['signingmax']['end']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'37', $model['excel'][$i]['signing']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'38', $model['excel'][$i]['signingMonth']) ;
                $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'39', $model['excel'][$i]['signingYear']) ;
                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN, //细边框
//’color’ => array(‘argb’ => ‘FFFF0000’),
                        ),
                    ),
                );
                $objPHPExcel->getActiveSheet()->getStyle('A1:'.$excel_m[$i].'39')->applyFromArray($styleArray);
                $styleArray2 = array(
                    'borders' => array(
                        'outline' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
                        ),
                    ),
                );
                $objPHPExcel->getActiveSheet()->getStyle('A1:'.$excel_m[$i].'39')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A5:'.$excel_m[$i].'7')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A8:'.$excel_m[$i].'11')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A16:'.$excel_m[$i].'19')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A24:'.$excel_m[$i].'27')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A32:'.$excel_m[$i].'35')->applyFromArray($styleArray2);

            }
        }

//        print_r('<pre/>');
//        print_r($model['all']);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $output = ob_get_clean();
        spl_autoload_register(array('YiiBase','autoload'));
        $time=time();
        $str="templates/performance_".$time.".xlsx";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$str.'"');
        header("Content-Transfer-Encoding:binary");
        echo $output;
    }
}
