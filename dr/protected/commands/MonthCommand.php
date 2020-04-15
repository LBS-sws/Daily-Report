<?php
class MonthCommand extends CConsoleCommand
{

    public function run()
    {
        $suffix = Yii::app()->params['envSuffix'];
        $firstDay = date("Y/m/d");
        $sql = "SELECT a.*
FROM security$suffix.sec_user a 
left outer join security$suffix.sec_user_access b on b.username = a.username 
WHERE b.system_id='drs' and b.a_read_write like '%A09%'";
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($records as  $record) {
            $from_addr = "it@lbsgroup.com.hk";
            $to_addr = "[" . $record['email'] . "]";
            $subject = "月报表数据输入提醒" ;
            $description = "月报表数据输入提醒";
            $message = "请及时检查并输入上月月报表数据，延误输入将错过更新时间";
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
