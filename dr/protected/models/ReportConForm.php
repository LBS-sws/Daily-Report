<?php
/* Reimbursement Form */

class ReportConForm extends CReportForm
{

    public function init() {
        $this->id = 'RptContractCom';
        $this->name = Yii::t('app','Contract comparison report');
        $this->format = 'EXCEL';
        $this->city = Yii::app()->user->city();
        $this->start_dt = date("Y/01/01");
        $this->end_dt = date("Y/m/t");
        $this->fields = 'city,start_dt,end_dt';
    }

    public function addQueueItem() {
        $uid = Yii::app()->user->id;
        $bosses = Yii::app()->params['feedbackCcBoss'];
        $now = date("Y-m-d H:i:s");
        if (empty($rpt_array)) $rpt_array = array($this->id=>$this->name);
        $this->ccuser = (!empty($this->ccuser) && is_array($this->ccuser)) ? array_merge($this->ccuser, $bosses) : $bosses;
        $data = array(
            'RPT_ID'=>$this->id,
            'RPT_NAME'=>$this->name,
            'CITY'=>$this->city,
            'PAPER_SZ'=>$this->paper_sz,
            'FIELD_LST'=>$this->fields,
            'START_DT'=>General::toMyDate($this->start_dt),
            'END_DT'=>General::toMyDate($this->end_dt),
            'TARGET_DT'=>General::toMyDate($this->target_dt),
            'EMAIL'=>$this->email,
            'EMAILCC'=>$this->emailcc,
            'TOUSER'=>$this->touser,
            'CCUSER'=>json_encode($this->ccuser),
            'RPT_ARRAY'=>json_encode($rpt_array),
            'LANGUAGE'=>Yii::app()->language,
            'CITY_NAME'=>Yii::app()->user->city_name(),
            'YEAR'=>$this->year,
            'MONTH'=>$this->month,
        );
        $dataex = $this->queueItemEx();
        if (!empty($dataex)) $data = array_merge($data, $dataex);

        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $sql = "insert into swo_queue (rpt_desc, req_dt, username, status, rpt_type)
						values(:rpt_desc, :req_dt, :username, 'P', :rpt_type)
					";
            $command=$connection->createCommand($sql);
            if (strpos($sql,':rpt_desc')!==false)
                $command->bindParam(':rpt_desc',$this->name,PDO::PARAM_STR);
            if (strpos($sql,':req_dt')!==false)
                $command->bindParam(':req_dt',$now,PDO::PARAM_STR);
            if (strpos($sql,':username')!==false)
                $command->bindParam(':username',$uid,PDO::PARAM_STR);
            if (strpos($sql,':rpt_type')!==false)
                $command->bindParam(':rpt_type',$this->format,PDO::PARAM_STR);
            $command->execute();
            $qid = Yii::app()->db->getLastInsertID();

            $sql = "insert into swo_queue_param (queue_id, param_field, param_value)
						values(:queue_id, :param_field, :param_value)
					";
            foreach ($data as $key=>$value) {
                $command=$connection->createCommand($sql);
                if (strpos($sql,':queue_id')!==false)
                    $command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
                if (strpos($sql,':param_field')!==false)
                    $command->bindParam(':param_field',$key,PDO::PARAM_STR);
                if (strpos($sql,':param_value')!==false)
                    $command->bindParam(':param_value',$value,PDO::PARAM_STR);
                $command->execute();
            }

            if ($this->multiuser) {
                $sql = "insert into swo_queue_user (queue_id, username)
						values(:queue_id, :username)
					";

                $command=$connection->createCommand($sql);
                if (strpos($sql,':queue_id')!==false)
                    $command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
                if (strpos($sql,':username')!==false)
                    $command->bindParam(':username',$this->touser,PDO::PARAM_STR);
                $command->execute();

                if (!empty($this->ccuser) && is_array($this->ccuser)) {
                    foreach ($this->ccuser as $user) {
                        $command=$connection->createCommand($sql);
                        if (strpos($sql,':queue_id')!==false)
                            $command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
                        if (strpos($sql,':username')!==false)
                            $command->bindParam(':username',$user,PDO::PARAM_STR);
                        $command->execute();
                    }
                }
            }
            $transaction->commit();
        }
        catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update.'.$e->getMessage());
        }
    }
}
