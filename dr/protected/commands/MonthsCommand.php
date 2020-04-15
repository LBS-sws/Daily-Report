<?php
class MonthsCommand extends CConsoleCommand
{

    public function run()
    {
        $suffix = Yii::app()->params['envSuffix'];
        $firstDay = date("Y/m/d");
        $sql = "select a.*, b.name as region_name, c.disp_name as incharges, c.email as email
				from security$suffix.sec_city a left outer join security$suffix.sec_city b on a.region=b.code 
					left outer join security$suffix.sec_user c on a.incharge= c.username ";
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($records as  $record) {
            $from_addr = "it@lbsgroup.com.hk";
            $to_addr = "[" . $record['email'] . "]";
            $subject = "月报表总结输入提醒" ;
            $description = "月报表总结输入提醒";
            $message = "请各位地区负责人及时提交上月月报表总结";
            $lcu = "admin";
            $aaa = Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
                'request_dt' => date('Y-m-d H:i:s'),
                'from_addr' => $from_addr,
                'to_addr' => $to_addr,
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

?>
