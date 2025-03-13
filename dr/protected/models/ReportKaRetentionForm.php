<?php
/* Reimbursement Form */

class ReportKaRetentionForm extends CReportForm
{
	public $chain_num;//KA销售
	
	protected function labelsEx() {
        return array(
            'chain_num'=>"KA销售",
        );
	}
	
	protected function rulesEx() {
        return array(
            array('year,chain_num','safe'),
            array('year,chain_num','required'),
        );
	}

	protected function queueItemEx() {
		return array(
				'CHAINNUM'=>json_encode($this->chain_num)
			);
	}
	
	public function init() {
		$this->id = 'RptKaRetention';
		$this->name = Yii::t('app','KA Retention report');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'year,chain_num';
	}

    public function addQueueItem() {
        $uid = Yii::app()->user->id;
        $bosses = Yii::app()->params['feedbackCcBoss'];
        $now = date("Y-m-d H:i:s");
        $rpt_array = array($this->id=>$this->name,);
        $maxMonth = date("n",strtotime(" - 1 months"));
        for ($i=1;$i<=$maxMonth;$i++){
            $rpt_array["RptKaRetentionMonth{$i}"]="{$i}月终止金额";
        }
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
            'MONTH'=>$maxMonth,
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

    //获取KA所有员工
    public function getKaManBoxHtml($year){
	    $employeeID = RetentionKARateForm::getEmployeeIDForUsername();
	    $kaSalesList = RetentionKARateForm::getKaManForKaBot($year,$employeeID);
	    $html = "";
	    $className = get_class($this);
	    if(!empty($kaSalesList)){
	        foreach ($kaSalesList as $row){
                $html.=TbHtml::checkBox("{$className}[chain_num][]",true,array(
                    "label"=>$row["name"],
                    "value"=>$row["id"],
                    "class"=>"changeCheck",
                    "labelOptions"=>array("class"=>"checkbox-inline"),
                ));
            }
        }
        return $html;
    }

    public static function getYearList(){
        $minYear = 2025;
        $maxYear = date("Y");
        $list =array();
        for ($i=$minYear;$i<=$maxYear;$i++){
            $list[$i] = $i."年";
        }
        return $list;
    }
}
