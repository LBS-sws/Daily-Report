 <?php
class CompreHenSiveCommand extends CConsoleCommand
{
    public function run()
    {
        $suffix = Yii::app()->params['envSuffix'];
        $year=date("Y");
        $month=date("m");
        $month=$month-1;
        if($month-1==0){
            $month=12;
            $year=$year-1;
        }
        $sql="select code
				from security$suffix.sec_city  ";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $city = $row['code'];
                $nocity = array('CS', 'H-N', 'HK','JMS', 'KS', 'MO', 'MY', 'RN', 'TC', 'TN', 'TP', 'TY', 'XM', 'ZS1', 'ZY', 'RW', 'WL;');
                if(in_array($city, $nocity, true)){
                }else{
                    $city_allow = City::model()->getDescendantList($city);
                    //城市
                    if(empty($city_allow)){
                        $arr['scenario']=array ( 'city' => $city, 'start_dt' => $year, 'start_dt1' => $month, 'end_dt' => $year, 'end_dt1' =>$month );
                        $model=new ComperHenSiveForm;
                        $model=$model->retrieveData($arr);
                        $email=$this->email($city,'CN12');
                        $to_addr = json_encode($email);
                        $from_addr = "it@lbsgroup.com.hk";
                        $subject=$model['city'][$model['scenario']['city']]."-综合数据对比分析".$year."/".$month;
                        $description='';
                        $lastcity1=$this->lastcity($city);
                        $lastemail1=$this->email($lastcity1,'CN13');
                        $lastcity2=$this->lastcity($lastcity1);
                        if($lastcity2!='CN'){
                            $lastemail2=$this->email($lastcity2,'CN13');
                        }else{
                            $lastemail2=array();
                        }
                        $lastemail3=$this->email('CN','CN14');
                        $cc_addr=json_encode(array_merge($lastemail1,$lastemail2,$lastemail3));
                        $message = <<<EOF
                        <div>
                        <table border="1" cellpadding="0" cellspacing="0" height="238" style="border-collapse:collapse;width:978.77pt;text-align: center;" width="1305">
                            <colgroup>
                                <col style="width:100.50pt;" width="134" />
                                <col style="width:112.50pt;" width="150" />
                                <col style="width:112.50pt;" width="150" />
                                <col span="2" style="width:90.00pt;" width="120" />
                                <col style="width:98.45pt;" width="131" />
                                <col span="5" style="width:75.00pt;" width="100" />
                            </colgroup>
                            <tbody>
                               <tr height="102" style="height:77.00pt;">
                                    <td class="et4" height="102" style="height:77.00pt;width:100.50pt;" width="134" >{$model['city'][$model['scenario']['city']]}</td>
                                    <td class="et4" height="102" style="height:77.00pt;width:100.50pt;" width="134" ></td>
                                    <td class="et5" style="width:112.50pt;" width="150" >生意额增长<br />
                                    （比上月/去年当月的数据为正是增长，为负是降低）</td>
                                    <td class="et6" style="width:112.50pt;" width="150" >纯利润增长<br />
                                    （比上月/去年当月的数据为正是增长，为负是降低）</td>
                                    <td class="et7" style="width:90.00pt;" width="120" >停单比例</td>
                                    <td class="et7" style="width:90.00pt;" width="120" >收款率</td>
                                    <td class="et7" style="width:115.45pt;" width="131" >技术员平均生产力</td>
                                    <td class="et7" style="width:75.00pt;" width="100" >月报表分数</td>
                                    <td class="et7" style="width:90.00pt;" width="100" >老总回馈次数</td>
                                    <td class="et9" style="width:75.00pt;" width="100" >质检拜访量</td>
                                    <td class="et9" style="width:75.00pt;" width="100" >销售拜访量</td>
                                    <td class="et9" style="width:75.00pt;" width="100" >签单成交率</td>
                                </tr>
                                <tr height="45" style="height:34.00pt;">
                                    <td class="et8" height="136" rowspan="3" style="height:102.00pt;width:100.50pt;" width="134" >{$year}/{$month}</td>
                                    <td class="et9" >当月</td>
                                    <td class="et9">{$model['excel'][0]['business']}</td>
                                    <td class="et9">{$model['excel'][0]['profit']}</td>
                                    <td class="et9">{$model['excel'][0]['stoporder']}</td>
                                    <td class="et9">{$model['excel'][0]['receipt']}</td>
                                    <td class="et9">{$model['excel'][0]['productivity']}</td>
                                    <td class="et9">{$model['excel'][0]['report']}</td>
                                    <td class="et9">{$model['excel'][0]['feedback']}</td>
                                    <td class="et9">{$model['excel'][0]['quality']}</td>
                                    <td class="et9">{$model['excel'][0]['visit']}</td>
                                    <td class="et9">{$model['excel'][0]['signing']}</td>
                                </tr>
                                <tr height="45" style="height:34.00pt;">
                                    <td class="et9" >比上月</td>
                                    <td class="et9">{$model['excel'][0]['businessMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['profitMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['stoporderMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['receiptMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['productivityMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['reportMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['feedbackMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['qualityMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['visitMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['signingMonth']}</td>
                                </tr>
                                <tr height="45" style="height:34.00pt;">
                                    <td class="et9" >比去年当月</td>
                                   <td class="et9">{$model['excel'][0]['businessYear']}</td>
                                    <td class="et9">{$model['excel'][0]['profitYear']}</td>
                                    <td class="et9">{$model['excel'][0]['stoporderYear']}</td>
                                    <td class="et9">{$model['excel'][0]['receiptYear']}</td>
                                    <td class="et9">{$model['excel'][0]['productivityYear']}</td>
                                    <td class="et9">{$model['excel'][0]['reportYear']}</td>
                                    <td class="et9">{$model['excel'][0]['feedbackYear']}</td>
                                    <td class="et9">{$model['excel'][0]['qualityYear']}</td>
                                    <td class="et9">{$model['excel'][0]['visitYear']}</td>
                                    <td class="et9">{$model['excel'][0]['signingYear']}</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>				
EOF;

                    }else{
                        $arr['scenario']=array ( 'city' => $city, 'start_dt' => $year, 'start_dt1' => $month, 'end_dt' => $year, 'end_dt1' =>$month );
                        $model=new ComperHenSiveForm;
                        $model=$model->retrieveData($arr);
                        $from_addr = "it@lbsgroup.com.hk";
                        $subject=$model['city'][$model['scenario']['city']]."-综合数据对比分析".$year."/".$month;
                        $description='';
                        if($city=='CN'){
                            $s=array();
                            $email=$this->email($city,'CN14');
                            $to_addr = json_encode($email);
//                            $s[]='flam@lbsgroup.com.hk';
                            $s[]='bella.cd@lbsgroup.com.cn';
                            $cc_addr=json_encode($s);
                        }else{
                            $email=$this->email($city,'CN13');
                            $to_addr = json_encode($email);
                            $lastcity2=$this->lastcity($city);
                            if($lastcity2!='CN'){
                                $lastemail2=$this->email($lastcity2,'CN13');
                            }else{
                                $lastemail2=array();
                            }
                            $lastemail3=$this->email('CN','CN14');
                            $cc_addr=json_encode(array_merge($lastemail2,$lastemail3));
                        }

                        $message = <<<EOF
                       <table border="1" cellpadding="0" cellspacing="0" height="308" style="border-collapse:collapse;width:1107.78pt;text-align: center" width="1477">
                            <colgroup>
                                <col style="width:100.50pt;" width="134" />
                                <col style="width:95.65pt;" width="127" />
                                <col style="width:112.45pt;" width="149" />
                                <col style="width:115.25pt;" width="153" />
                                <col style="width:91.80pt;" width="122" />
                                <col style="width:75.00pt;" width="100" />
                                <col style="width:115.30pt;" width="153" />
                                <col style="width:75.00pt;" width="100" />
                                <col style="width:90.00pt;" width="120" />
                                <col style="width:77.80pt;" width="103" />
                                <col span="2" style="width:79.65pt;" width="106" />
                            </colgroup>
                            <tbody>
                                <tr height="100" style="height:75.00pt;">
                                    <td class="et5" height="100" style="height:75.00pt;width:100.50pt;" width="134" >{$model['city'][$model['scenario']['city']]}</td>
                                    <td class="et5" style="width:95.65pt;" width="127">&nbsp;</td>
                                    <td class="et6" style="width:112.45pt;" width="149" >生意额增长<br />
                                    （比上月/去年当月的数据为正是增长，为负是降低）</td>
                                    <td class="et7" style="width:115.25pt;" width="153" >纯利润增长<br />
                                    （比上月/去年当月的数据为正是增长，为负是降低）</td>
                                    <td class="et7" style="width:90.00pt;" width="120" >停单比例</td>
                                    <td class="et7" style="width:90.00pt;" width="120" >收款率</td>
                                    <td class="et7" style="width:115.45pt;" width="131" >技术员平均生产力</td>
                                    <td class="et7" style="width:75.00pt;" width="100" >月报表分数</td>
                                    <td class="et7" style="width:90.00pt;" width="100" >老总回馈次数</td>
                                    <td class="et9" style="width:75.00pt;" width="100" >质检拜访量</td>
                                    <td class="et9" style="width:75.00pt;" width="100" >销售拜访量</td>
                                    <td class="et9" style="width:75.00pt;" width="100" >签单成交率</td>
                                </tr>
                                <tr height="52" style="height:39.00pt;">
                                    <td class="et9" height="208" rowspan="4" style="height:156.00pt;width:100.50pt;" width="134" >{$year}/{$month}</td>
                                    <td class="et10" >当月最高/最低</td>
                                    <td class="et11" >/</td>
                                    <td class="et10" >/</td>
                                    <td class="et10">{$model['excel'][0]['stopordermax']['max']}/{$model['excel'][0]['stopordermax']['end']}</td>
                                    <td class="et10">{$model['excel'][0]['receiptmax']['max']}/{$model['excel'][0]['receiptmax']['end']}</td>
                                    <td class="et10">{$model['excel'][0]['productivitymax']['max']}/{$model['excel'][0]['productivitymax']['end']}</td>
                                    <td class="et10">{$model['excel'][0]['reportmax']['max']}/{$model['excel'][0]['reportmax']['end']}</td>
                                    <td class="et10">{$model['excel'][0]['feedbackmax']['max']}/{$model['excel'][0]['feedbackmax']['end']}</td>
                                    <td class="et10">{$model['excel'][0]['qualitymax']['max']}/{$model['excel'][0]['qualitymax']['end']}</td>
                                    <td class="et10">{$model['excel'][0]['visitmax']['max']}/{$model['excel'][0]['visitmax']['end']}</td>
                                    <td class="et12">{$model['excel'][0]['signingmax']['max']}/{$model['excel'][0]['signingmax']['end']}</td>
                                </tr>
                                <tr height="52" style="height:39.00pt;">
                                    <td class="et10" >当月</td>
                                    <td class="et9">{$model['excel'][0]['business']}</td>
                                    <td class="et9">{$model['excel'][0]['profit']}</td>
                                    <td class="et9">{$model['excel'][0]['stoporder']}</td>
                                    <td class="et9">{$model['excel'][0]['receipt']}</td>
                                    <td class="et9">{$model['excel'][0]['productivity']}</td>
                                    <td class="et9">{$model['excel'][0]['report']}</td>
                                    <td class="et9">{$model['excel'][0]['feedback']}</td>
                                    <td class="et9">{$model['excel'][0]['quality']}</td>
                                    <td class="et9">{$model['excel'][0]['visit']}</td>
                                    <td class="et9">{$model['excel'][0]['signing']}</td>
                                </tr>
                                <tr height="52" style="height:39.00pt;">
                                    <td class="et10" >比上月</td>
                                     <td class="et9">{$model['excel'][0]['businessMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['profitMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['stoporderMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['receiptMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['productivityMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['reportMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['feedbackMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['qualityMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['visitMonth']}</td>
                                    <td class="et9">{$model['excel'][0]['signingMonth']}</td>
                                </tr>
                                <tr height="52" style="height:39.00pt;">
                                    <td class="et10" >比去年当月</td>
                                    <td class="et9">{$model['excel'][0]['businessYear']}</td>
                                    <td class="et9">{$model['excel'][0]['profitYear']}</td>
                                    <td class="et9">{$model['excel'][0]['stoporderYear']}</td>
                                    <td class="et9">{$model['excel'][0]['receiptYear']}</td>
                                    <td class="et9">{$model['excel'][0]['productivityYear']}</td>
                                    <td class="et9">{$model['excel'][0]['reportYear']}</td>
                                    <td class="et9">{$model['excel'][0]['feedbackYear']}</td>
                                    <td class="et9">{$model['excel'][0]['qualityYear']}</td>
                                    <td class="et9">{$model['excel'][0]['visitYear']}</td>
                                    <td class="et9">{$model['excel'][0]['signingYear']}</td>
                                </tr>
                            </tbody>
                        </table>
		
EOF;
                    }
                    $lcu = "admin";
                    $aaa = Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
                        'request_dt' => date('Y-m-d H:i:s'),
                        'from_addr' => $from_addr,
                        'to_addr' => $to_addr,
                        'cc_addr' => $cc_addr,
                        'subject' => $subject,//郵件主題
                        'description' => $description,//郵件副題
                        'message' => $message,//郵件內容（html）
                        'status' => "P",
                        'lcu' => $lcu,
                        'lcd' => date('Y-m-d H:i:s'),
                    ));
                }
            }
        }
    }

    public function email($city,$cn){
        $suffix = Yii::app()->params['envSuffix'];
        $sql="select email from security$suffix.sec_user  a
              left outer join security$suffix.sec_user_access  b on a.username=b.username
              where a.city='$city' and b.system_id='drs' and b.a_control like '%$cn%'
";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $email_addr=array();
        foreach ($rows as $a ){
            $email_addr[]=$a['email'];
        }
//        $to_addr = json_encode($email_addr);
        return $email_addr;
    }

    public function lastcity($city){
        $suffix = Yii::app()->params['envSuffix'];
        $sql="select region from security$suffix.sec_city  where code='$city'";
        $rows = Yii::app()->db->createCommand($sql)->queryScalar();
        return $rows;
    }


}
?>