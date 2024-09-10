<?php
/* Reimbursement Form */

class ReportKaSignedForm extends CReportForm
{
	public $staff_list=array();//
	
	protected function labelsEx() {
        return array(
            //'company_status'=>Yii::t('customer','Status'),
            'staff_list'=>Yii::t('summary','Staff Name'),
        );
	}
	
	protected function rulesEx() {
        return array(
            array('staff_list','required'),
        );
	}

	protected function queueItemEx() {
		return array(
				'STAFFLIST'=>json_encode($this->staff_list),
			);
	}
	
	public function init() {
		$this->id = 'RptKaSigned';
		$this->name = Yii::t('app','KA signed report');
		$this->format = 'EXCEL';
		$this->city = "";
		$this->staff_list = array();
		$this->start_dt = date_format(date_create(),"Y/m/01");
		$this->end_dt = date_format(date_create(),"Y/m/d");
		$this->fields = 'start_dt,end_dt,staff_list';
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
            'CITY'=>(is_array($this->city) ? json_encode($this->city) : $this->city),
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

    public static function getGroupStaffList(){
        $suffix = Yii::app()->params['envSuffix'];
        $uid = Yii::app()->user->id;
        $list = array();
        $row = Yii::app()->db->createCommand()->select("b.id,b.code,b.name")->from("hr{$suffix}.hr_binding a")
            ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
            ->where("a.user_id=:username",array(":username"=>$uid))->queryRow();
        if($row){
            $list[$row["id"]] = $row["name"]." ({$row["code"]})";
            $bossRow = Yii::app()->db->createCommand()->select("a.id")
                ->from("hr{$suffix}.hr_group_staff a")
                ->leftJoin("hr{$suffix}.hr_group b","a.group_id=b.id")
                ->where("a.employee_id=:employee_id and b.group_code='KARPT'",array(":employee_id"=>$row["id"]))
                ->queryRow();
            if($bossRow){//该员工有分组
                $infoRows = Yii::app()->db->createCommand()->select("b.id,b.code,b.name")
                    ->from("hr{$suffix}.hr_group_branch a")
                    ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
                    ->where("a.group_staff_id=:group_staff_id",array(":group_staff_id"=>$bossRow["id"]))
                    ->queryAll();
                if($infoRows){//该员工有管辖员工
                    foreach ($infoRows as $infoRow){
                        $list[$infoRow["id"]] = $infoRow["name"]." ({$infoRow["code"]})";
                    }
                }
            }
        }else{
            $list["-1"] = "未绑定员工";
        }
        return $list;
    }
}
