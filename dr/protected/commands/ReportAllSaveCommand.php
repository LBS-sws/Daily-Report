<?php
class ReportAllSaveCommand extends CConsoleCommand
{

    public function run()
    {
        $rpt_id="RptAll";
        $paper_sz= "A3";
        $rptname = array(
            'RptCustnew'=>'Customer Report - New',
            'RptCustrenew'=>'Customer Report - Renewal',
            'RptCustamend'=>'Customer Report - Amendment',
            'RptCustsuspend'=>'Customer Report - Suspended',
            'RptCustresume'=>'Customer Report - Resume',
            'RptCustterminate'=>'Customer Report - Terminate',
            'RptComplaint'=>'Complaint Cases Report',
            'RptEnquiry'=>'Customer Report - Enquiry',
            'RptLogistic'=>'Product Delivery Report',
            'RptQc'=>'Quality Control Report',
            'RptStaff'=>'Staff Report',
        );
        $suffix = Yii::app()->params['envSuffix'];
        $uid = 'admin';
        $now = date("Y-m-d H:i:s");
        $sql="select * from swo_fixed_queue_value ";
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($records as $record){
            $sql2="select name from security$suffix.sec_city where code='".$record['city']."'";
            $cityname = Yii::app()->db->createCommand($sql2)->queryScalar();
            $data = array(
                'RPT_ID' => $rpt_id,
                'RPT_NAME' => 'All Daily Reports',
                'CITY' => $record['city'],
                'PAPER_SZ' => $paper_sz,
                'FIELD_LST' => 'start_dt,end_dt,format,city',
                'START_DT' =>date("Y-m-01"),
                'END_DT' => date("Y-m-d"),
                'TARGET_DT' => date("Y-m-d"),
                'EMAIL' => '',
                'EMAILCC' => '',
                'TOUSER' => $record['touser'],
                'CCUSER' => $record['ccuser'],
                'RPT_ARRAY' => json_encode($rptname),
                'LANGUAGE' => Yii::app()->language,
                'CITY_NAME' =>$cityname,
                'YEAR' => '',
                'MONTH' => '',
            );
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();
            try {
                $sql = "insert into swo_queue (rpt_desc, req_dt, username, status, rpt_type)
						values(:rpt_desc, :req_dt, :username, 'P', :rpt_type)
					";
                $command = $connection->createCommand($sql);
                if (strpos($sql, ':rpt_desc') !== false)
                    $command->bindParam(':rpt_desc', $data['RPT_NAME'], PDO::PARAM_STR);
                if (strpos($sql, ':req_dt') !== false)
                    $command->bindParam(':req_dt', $now, PDO::PARAM_STR);
                if (strpos($sql, ':username') !== false)
                    $command->bindParam(':username', $uid, PDO::PARAM_STR);
                if (strpos($sql, ':rpt_type') !== false)
                    $feed='FEED';
                    $command->bindParam(':rpt_type', $feed, PDO::PARAM_STR);
                $command->execute();
                $qid = Yii::app()->db->getLastInsertID();

                $sql = "insert into swo_queue_param (queue_id, param_field, param_value)
						values(:queue_id, :param_field, :param_value)
					";
                foreach ($data as $key => $value) {
                    $command = $connection->createCommand($sql);
                    if (strpos($sql, ':queue_id') !== false)
                        $command->bindParam(':queue_id', $qid, PDO::PARAM_INT);
                    if (strpos($sql, ':param_field') !== false)
                        $command->bindParam(':param_field', $key, PDO::PARAM_STR);
                    if (strpos($sql, ':param_value') !== false)
                        $command->bindParam(':param_value', $value, PDO::PARAM_STR);
                    $command->execute();
                }


                $sql = "insert into swo_queue_user (queue_id, username)
						values(:queue_id, :username)
					";

                $command = $connection->createCommand($sql);
                if (strpos($sql, ':queue_id') !== false)
                    $command->bindParam(':queue_id', $qid, PDO::PARAM_INT);
                if (strpos($sql, ':username') !== false)
                    $command->bindParam(':username',  $record['touser'], PDO::PARAM_STR);
                $command->execute();
                $ccuser=json_decode($record['ccuser'],true);
                if (!empty($ccuser) && is_array($ccuser)) {
                    foreach ($ccuser as $user) {
                        $command = $connection->createCommand($sql);
                        if (strpos($sql, ':queue_id') !== false)
                            $command->bindParam(':queue_id', $qid, PDO::PARAM_INT);
                        if (strpos($sql, ':username') !== false)
                            $command->bindParam(':username', $user, PDO::PARAM_STR);
                        $command->execute();
                    }
                }

                $transaction->commit();
            }
            catch(Exception $e) {
                $transaction->rollback();
            }
        }
    }

}

?>
