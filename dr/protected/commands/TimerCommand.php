<?php
class TimerCommand extends CConsoleCommand {
    protected $send_list = array();//信息列表
    protected $city_list = array();//所有有信息的城市（優化查詢使用）
    protected $city = "";//

    public function run() {
        echo "start：\r\n";
        $this->drServiceSendEmail();//日報表系統的服務提醒（新增、續約需要五天內添加附件）

        $this->sendEmail();//統一發送郵件
        echo "end\r\n";
    }

    private function sendEmail(){
        $systemId = Yii::app()->params['systemId'];
        $email = new Email("新增/续约追踪提醒","","新增/续约追踪提醒");
        $userlist = $email->getEmailUserList($this->city_list,"kittyzhou");
        $joeEmail = $email->getJoeEmail();
        $kittyEmail = $email->getKittyEmail();
        if($userlist){
            foreach ($userlist as $user){
                $message="";
                $city_list = empty($user["look_city"])?array($user["city"]):explode(",",$user["look_city"]); //判斷是否需要查詢下級城市

                foreach ($this->send_list as $send){
                    $maxBool = false;//最大權限
                    $html = "";
                    $bool = array_intersect($this->city_list,$send["city_list"]);
                    if(key_exists("joeEmail",$send)){//驗證是否額外給繞生發郵件
                        if($send["joeEmail"]){
                            if($user["email"]==$joeEmail){//用戶是繞生
                                $bool=1;//繞生不需要城市驗證
                                $maxBool = true;
                                $city_list = $send["city_list"];//繞生收到所有城市的郵件
                            }
                        }
                    }
                    if(key_exists("kittyEmail",$send)){//驗證是否額外給kitty發郵件
                        if($send["kittyEmail"]){
                            if($user["email"]==$kittyEmail){//用戶是kitty
                                $bool=1;//kitty不需要城市驗證
                                $maxBool = true;
                                $city_list = $send["city_list"];//kitty收到所有城市的郵件
                            }
                        }
                    }
                    if(key_exists("send_all_city",$send)&&$send["send_all_city"]){
                        $bool = 1;
                        $city_list = $send["city_list"];//所有城市的郵件
                    }
                    if(!$maxBool){
                        if(empty($bool)){
                            continue;//該城市沒有提示信息
                        }
                        $inchargeBool = !empty($send["incharge"])&&!empty($user["incharge"]);//boss身份
                        $authBool = !empty($send["auth_list"])&&$this->arrSearchStr($send["auth_list"],$user["a_read_write"]);
                        if($inchargeBool==false&&$authBool==false){
                            continue;
                        }
                    }
                    $html.=$send["title"];
                    $html.="<table border='1'>".$send["table_head"]."<tbody>";
                    $tBody="";
                    foreach ($city_list as $city){//城市循環
                        if(in_array($city,$send["city_list"])){
                            $tBody .= implode("",$send[$city]["table_body"]);
                        }
                    }
                    $html=$html.$tBody."</tbody></table><p>&nbsp;</p><br/>";
                    if(!empty($tBody)){
                        $message.=$html;
                    }
                }

                if(!empty($message)){ //如果有內容則發送郵件
                    echo "to do transaction:".$user['username']."\r\n";
                    $email->setMessage($message);
                    $email->addToAddrEmail($user["email"]);
                    $email->sent("系统生成",$systemId);
                    $email->resetToAddr();
                }
            }
        }
    }

    private function arrSearchStr($arr,$str){
        foreach ($arr as $item){
            if (strpos($str,$item)!==false)
                return true;
        }
        return false;
    }

    //日報表系統的服務提醒（新增、續約需要五天內添加附件）
    private function drServiceSendEmail(){
        $suffix = Yii::app()->params['envSuffix'];
        $startDate="2022/12/01";//2022/11/18
        $endday = date("Y/m/d",strtotime("- 5 day"));
        //$sql = "a.city != 'MO' and a.status in ('N','C') and a.status_dt>='{$startDate}' and a.status_dt<='{$endday}' and docman$suffix.countdoc('SERVICE',a.id)=0";
        $sql = "a.status in ('N','C') and a.status_dt>='{$startDate}' and a.status_dt<='{$endday}' and docman$suffix.countdoc('SERVICE',a.id)=0";
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.lud,a.status,a.status_dt,a.company_name,a.salesman,a.city, b.description as nature_desc, c.description as type_desc")
            ->from("swo_service a")
            ->leftJoin("swo_nature b","a.nature_type=b.id")
            ->leftJoin("swo_customer_type c","a.cust_type=c.id")
            ->where($sql." and c.rpt_cat!='INV'")->queryAll();
        if($rows){
            $description="<p>以下新增或续约客户已超过5天未上传合同到表单附件上，请尽快处理</p>";
            $arr = array();
            $arr["city_list"] = array();
            $arr["title"] = $description;
            $arr["table_head"] = "<thead><th>城市</th><th>记录类别</th><th>日期</th><th>客户编号及名称</th><th>客户类别</th><th>性质</th><th>业务员</th></thead>";
            foreach ($rows as $row){
                $row["status_desc"]=$row["status"]=="N"?"新增":"续约";
                $row["status_dt"]=General::toDate($row["status_dt"]);
                if(!in_array($row["city"],$this->city_list)){
                    $this->city_list[]=$row["city"];
                    /*
                    $cityAllow = Email::getAllCityToMinCity($row["city"]);
                    $this->city_list = array_unique(array_merge($cityAllow,$this->city_list));
                    */
                }
                if(!key_exists($row["city"],$arr)){
                    $arr["city_list"][]=$row["city"];
                    $arr[$row["city"]]=array();
                    $arr[$row["city"]]["city_name"]=General::getCityName($row["city"]);
                }
                $arr[$row["city"]]["table_body"][]="<tr><td>".$arr[$row["city"]]["city_name"]."</td>"."<td>".$row["status_desc"]."</td>"."<td>".$row["status_dt"]."</td>"."<td>".$row["company_name"]."</td>"."<td>".$row["nature_desc"]."</td>"."<td>".$row["type_desc"]."</td>"."<td>".$row["salesman"]."</td></tr>";
            }
            $arr["auth_list"] = array('A02');
            $arr["city_allow"] = false;
            $arr["incharge"] = 1;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }
}
?>