<?php
class BonusCommand extends CConsoleCommand {
    public function actionReCompany(){
        echo "start:\n";
        $rows = Yii::app()->db->createCommand()
            ->select("customer_id,group_type,group_code,group_name")
            ->from("customer_group_mapping_temp")
            ->where("group_code is not null AND group_code!=''")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                echo "loading:{$row["customer_id"]}\n";
                $groupCode = $row["group_code"];
                if(mb_strlen($groupCode,'UTF-8')>20){
                    $groupCode=mb_substr($groupCode,0,20,'UTF-8');
                }
                Yii::app()->db->createCommand()->update("swo_company",array(
                    "group_type"=>$row["group_type"],
                    "group_id"=>$groupCode,
                    "group_name"=>$row["group_name"],
                ),"u_customer_id=:u_customer_id",array(":u_customer_id"=>$row["customer_id"]));
            }
        }
        echo "\n";
        echo "end!";
    }
    public function actionIndex(){
        $date = date_format(date_create(),"Y-m-d H:i");
        $day = date_format(date_create(),"j");
        if($day==1){
            //管理层月度奖金计算发送到北森
            echo "Bonus Start:".$date."\n";
            $model = new ManageMonthBonusForm();
            $model->search_year = date("Y",strtotime("- 1 months"));
            $model->search_month = date("n",strtotime("- 1 months"));
            if($model->validate()){
                $arr = $model->saveCache();
                if($arr["bool"]===false){
                    echo " - error!\n";
                }else{
                    echo " - success!\n";
                }
            }
            echo "Bonus End!\n";

            //技术员生产力分析数据发送到北森
            echo "ter Start:".$date."\n";
            $this->actionSendTerForBs($model->search_year,$model->search_month);
            echo "ter End!\n";

            //营运系统直升机数据发送到北森
            echo "acc Start:".$date."\n";
            $this->actionSendAccForBs($model->search_year,$model->search_month);
            echo "acc End!\n";
        }
    }

    //营运系统直升机数据发送到北森
    public function actionSendAccForBs($year,$month){
        $startDay = date("Y/m/01", strtotime("{$year}/{$month}/01"));
        $endDay = date("Y/m/t", strtotime("{$year}/{$month}/01"));
        $bsSendData = array(
            "presetSalarySubsetCode" => "PresetSalarySubset1",
            "models" => array()
        );
        $suffix = Yii::app()->params['envSuffix'];
        $planRows = Yii::app()->db->createCommand()->select("a.plane_sum,a.money_value,b.bs_staff_id")
            ->from("account{$suffix}.acc_plane a")
            ->leftJoin("hr{$suffix}.hr_employee b","b.id=a.employee_id")
            ->where("a.plane_year={$year} and a.plane_month={$month} and b.bs_staff_id is not null")
            ->queryAll();
        if($planRows){
            foreach ($planRows as $planRow){
                $bsSendData["models"][]=array(
                    "staffId"=>$planRow["bs_staff_id"],
                    "itemName"=>4,//做单金额
                    "startDate"=>$startDay,
                    "stopDate"=>$endDay,
                    "numericVal"=>$planRow["money_value"],
                );
                $bsSendData["models"][]=array(
                    "staffId"=>$planRow["bs_staff_id"],
                    "itemName"=>3,//直升机金额
                    "startDate"=>$startDay,
                    "stopDate"=>$endDay,
                    "numericVal"=>$planRow["plane_sum"],
                );
            }
            $this->sendBsByData($bsSendData);
        }
    }

    //发送所有技术员金额到北森系统
    public function actionSendTerForBs($year,$month){
        $startDay = date("Y/m/01",strtotime("{$year}/{$month}/01"));
        $endDay = date("Y/m/t",strtotime("{$year}/{$month}/01"));
        $bsSendData = array(
            "presetSalarySubsetCode"=>"PresetSalarySubset1",
            "models"=>array()
        );
        $rows = CountSearch::getTechnicianMoney($startDay,$endDay);
        if($rows){
            foreach ($rows as $row){
                $uStaffCode = isset($row["staff"])?$row["staff"]:"none";
                $staffList = $this->getStaffOne($uStaffCode);
                if($staffList){
                    $uAmt = isset($row["amt"])?$row["amt"]:0;
                    $bsSendData["models"][]=array(
                        "staffId"=>$staffList["bs_staff_id"],
                        "LBSStaffName"=>$staffList["name"]." (".$staffList["code"].")",
                        "itemName"=>4,//做单金额
                        "startDate"=>$startDay,
                        "stopDate"=>$endDay,
                        "numericVal"=>$uAmt,
                    );
                }
            }
            $this->sendBsByData($bsSendData);
        }else{
            echo " - u error!\n";
        }
    }

    private function sendBsByData($bsSendData){
        if(!empty($bsSendData["models"])){
            $bool = $this->sendDataForMaxLen($bsSendData);//一次发送250条数据
            echo "\n modelsLength：".count($bsSendData["models"]);
            if($bool){//curl异常，不继续执行
                echo " - success!\n";
            }else{
                echo " - bs error!\n";
            }
        }else{
            echo " - staff empty error!\n";
        }
    }

    private function sendDataForMaxLen($bsSendData,$maxLen=250){
        $sendData = array();
        $thisData = $bsSendData["models"];
        if(!empty($thisData)){
            foreach($thisData as $key=>$row){
                if(count($sendData)>$maxLen){
                    break;
                }else{
                    $sendData[]=$row;
                    unset($thisData[$key]);
                }
            }
            $bsSendData["models"]=$sendData;
            $bsCurlModel = new BsCurlModel();
            $bsCurlModel->sendData = $bsSendData;
            $curlData = $bsCurlModel->sendBsCurl();
            if($curlData["code"]!=200){//curl异常
                $bsCurlModel->logError($curlData);
            }else{
                $bsCurlModel->logError($curlData);//成功也需要写入日志
            }
            if(!empty($thisData)){
                $bsSendData["models"]=$thisData;
                return $this->sendDataForMaxLen($bsSendData);
            }else{
                return $curlData["code"]==200;
            }
        }else{
            return false;
        }
    }

    private function getStaffOne($staffCode){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()
            ->select("id,code,name,bs_staff_id,staff_status")
            ->from("hr{$suffix}.hr_employee")
            ->where("code=:code and bs_staff_id is not null",array(":code"=>$staffCode))
            ->order("staff_status desc,table_type asc,id desc")
            ->queryRow();
        return $row;
    }

    //检查发送数据
    public function actionSendOnlyTerForBs($year,$month){
        $startDay = date("Y-m-01",strtotime("{$year}/{$month}/01"));
        $endDay = date("Y-m-t",strtotime("{$year}/{$month}/01"));
        $successData = array();
        $errorData = array();
        $rows = CountSearch::getTechnicianMoney($startDay,$endDay);
        if($rows){
            foreach ($rows as $row){
                $uStaffCode = isset($row["staff"])?$row["staff"]:"none";
                $staffList = $this->getStaffOne($uStaffCode);
                if($staffList){
                    $successData[]=array("lbs_id"=>$staffList["id"],"code"=>$staffList["code"],"name"=>$staffList["name"],"bs_staff_id"=>$staffList["bs_staff_id"]);
                }else{
                    $errorData[]=array("code"=>$uStaffCode);
                }
            }
            $str = json_encode($successData,JSON_UNESCAPED_UNICODE);
            Yii::log("successData:".$str,CLogger::LEVEL_WARNING);
            echo $str;
            echo "\n\n\n\n";
            $str = json_encode($errorData,JSON_UNESCAPED_UNICODE);
            Yii::log("errorData:".$str,CLogger::LEVEL_WARNING);
            echo $str;
        }else{
            echo " - u error!\n";
        }
    }
}
?>