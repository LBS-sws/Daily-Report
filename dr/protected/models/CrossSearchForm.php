<?php

class CrossSearchForm extends CrossApplyForm
{
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,table_type,service_id,apply_category,contract_no,apply_date,month_amt,rate_num,old_city,
            cross_city,cross_type,status_type,reject_note,remark,audit_date,audit_user,send_city','safe'),
			array('service_id,effective_date,apply_date,month_amt,rate_num,cross_city,cross_type','required'),
			array('reject_note','required',"on"=>array("reject")),
			array('id','validateID'),
		);
	}

    public function validateID($attribute, $params) {
        $index = is_numeric($this->id)?$this->id:0;
        $city_allow = Yii::app()->user->city_allow();
        $sql = "select * from swo_cross where id='".$index."' and status_type in (1,2,3,5,6) and (cross_city in ({$city_allow}) or old_city in ({$city_allow}) or (cross_type=5 and qualification_city in ({$city_allow})))";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if($row){
            $this->table_type = $row["table_type"];
            $this->cross_num = $row['cross_num'];
            $this->service_id = $row["service_id"];
            $this->old_city = $row["old_city"];
            $this->lcu = $row["lcu"];
        }else{
            $this->addError($attribute, "交叉派单不存在，请刷新重试");
            return false;
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql = "select * from swo_cross where id='".$index."' and (cross_city in ({$city_allow}) or old_city in ({$city_allow}) or (cross_type=5 and qualification_city in ({$city_allow})))";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->table_type = $row['table_type'];
            $this->cross_num = $row['cross_num'];
			$this->service_id = $row['service_id'];
			$this->contract_no = $row['contract_no'];
			$this->apply_date = General::toDate($row['apply_date']);
			$this->month_amt = $row['month_amt'];
			$this->rate_num = floatval($row['rate_num']);
            $this->old_city = $row['old_city'];
            $this->cross_city = $row['cross_city'];
            $this->send_city = $row['send_city'];
            $this->status_type = $row['status_type'];
            $this->reject_note = $row['reject_note'];
            $this->remark = $row['remark'];
            $this->luu = $row['luu'];
            $this->audit_user = $row['audit_user'];
            $this->audit_date = $row['audit_date'];
            $this->cross_type = $row['cross_type'];
            $this->old_month_amt = $row["old_month_amt"];
            $this->u_update_user = $row["u_update_user"];
            $this->u_update_date = $row["u_update_date"];
            $this->cross_amt = $row['cross_amt'];
            $this->qualification_city = $row['qualification_city'];
            $this->qualification_ratio = floatval($row['qualification_ratio']);
            $this->qualification_amt = $row['qualification_amt'];
            $this->apply_category = $row['apply_category'];
            $this->effective_date = General::toDate($row['effective_date']);
            return true;
		}else{
		    return false;
        }
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->getScenario()) {
			case "delete"://删除
                $sql = "delete from swo_cross where id = :id";
				break;
			case "reject"://拒绝
                $sql = "update swo_cross set 
					status_type = 2,
					reject_note = :reject_note ,
					luu = :luu
					where id = :id";
				break;
			case "audit"://通过
                $sql = "update swo_cross set 
					audit_user = :audit_user, 
					audit_date = :audit_date,
					status_type = 5,
					luu = :luu
					where id = :id";
			    break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':reject_note')!==false)
			$command->bindParam(':reject_note',$this->reject_note,PDO::PARAM_STR);
        if (strpos($sql,':audit_user')!==false)
            $command->bindParam(':audit_user',$this->audit_user,PDO::PARAM_STR);
        if (strpos($sql,':audit_date')!==false){
            $this->audit_date = date_format(date_create(),"Y/m/d H:i:s");
            $command->bindParam(':audit_date',$this->audit_date,PDO::PARAM_STR);
        }

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		if(in_array($this->getScenario(),array("audit","reject"))){
		    $this->sendEmail();
        }
		return true;
	}

    private function sendEmail(){
        if($this->table_type==0){
            $serviceModel = new ServiceForm("view");
            $serviceModel->retrieveData($this->service_id);
            $serviceModel->cust_type=GetNameToId::getCustOneNameForId($serviceModel->cust_type);
        }else{
            $serviceModel = new ServiceKAForm("view");
            $serviceModel->retrieveData($this->service_id);
        }
        $title = "交叉派单 - ";
        $message="";
        if($this->getScenario()=="audit"){
            $title.="审核通过";
            $this->sendCurl($serviceModel);
        }elseif ($this->getScenario()=="reject"){
            $title.="已拒绝";
            $message.="<p style='color:red;font-weight: bold'>拒绝原因：".$this->reject_note."</p>";
        }
        $rate_amt = ($this->month_amt*$this->rate_num)/100;
        $rate_amt = number_format($rate_amt,2,'.','');
        $message.="<p>合约编号：".$serviceModel->contract_no."</p>";
        $message.="<p>客户编号及名称：".$serviceModel->company_name."</p>";
        $message.="<p>客户类别：".$serviceModel->cust_type."</p>";
        $message.="<p>服务內容：".$serviceModel->service."</p>";
        $message.="<p>合约城市：".General::getCityName($serviceModel->city)."</p>";
        $message.="<p>承接城市：".General::getCityName($this->cross_city)."</p>";
        $message.="<p>月金额：".$this->month_amt."</p>";
        $message.="<p>比例：".$this->rate_num."%"."</p>";
        $message.="<p>比例后金额：".$rate_amt."</p>";
        $message.="<p>备注：".$this->remark."</p>";
        $message.="<p>申请时间：".$this->apply_date."</p>";
        $emailModel = new Email($title,$message,$title);
        $emailModel->addEmailToLcu($this->lcu);
        $emailModel->sent();
    }

    private function sendCurl($serviceModel){
        $rate_amt = ($this->month_amt*$this->rate_num)/100;
        $rate_amt = is_numeric($rate_amt)?round($rate_amt,2):0;
        $data=array(
            "lbs_id"=>$this->id,//唯一标识
            "customer_code"=>$serviceModel->customer_code."-{$this->old_city}",//客户编号
            "customer_name"=>$serviceModel->customer_name,//客户名称
            "contract_number"=>$this->contract_no,//合约编号
            "audit_ratio"=>$this->rate_num,//审核比例
            "send_money"=>$this->month_amt,//发包方金额
            "accept_money"=>$rate_amt,//承接方金额
            "send_contract_id"=>$this->old_city,//发包方（城市代号：ZY）
            "accept_contract_id"=>$this->cross_city,//承接方（城市代号：ZY）
            "audit_user_name"=>self::getEmployeeStrForUsername(Yii::app()->user->id),//审核人名称+编号如：400002_沈超
            "audit_date"=>$this->audit_date,//审核日期
        );
        SystemU::sendUForCross($data);
    }

    public static function getEmployeeStrForUsername($username){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select()->from("hr{$suffix}.hr_binding a")
            ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
            ->where("a.user_id=:user_id",array(":user_id"=>$username))->queryRow();
        if($row){
            return $row["code"]."_".$row["name"];
        }else{
            return $username;
        }
    }

	public function readonly(){
        return true;
    }
}