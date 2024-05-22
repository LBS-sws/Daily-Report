<?php

class CrossApplyForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $table_type=0;
	public $service_id;
	public $u_system_id;
	public $contract_no;
	public $apply_date;
	public $month_amt;
	public $rate_num;
	public $old_city;
	public $cross_city;
	public $status_type;
	public $reject_note;
	public $remark;
	public $audit_date;
	public $audit_user;
	public $luu;
	public $lcu;

    public $cross_type;
    public $old_month_amt;
    public $u_update_user;
    public $u_update_date;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		$list = parent::attributeLabels();
        $list["service_id"] = Yii::t('service','Contract No');
        $list["contract_no"] = Yii::t('service','Contract No');
        $list["apply_date"] = Yii::t('service','Apply date');
        $list["month_amt"] = Yii::t('service','Monthly');
        $list["rate_num"] = Yii::t('service','Rate number');
        $list["cross_city"] = Yii::t('service','Cross city');
        $list["reject_note"] = Yii::t('service','reject note');
        $list["remark"] = Yii::t('service','Remarks');
        $list["status_type"] = Yii::t('service','status type');
        $list["luu"] = Yii::t('service','audit user');
        $list["audit_date"] = Yii::t('service','audit date');
        $list["audit_user"] = Yii::t('service','audit user');
        $list["cross_type"] = Yii::t('service','Cross type');
        $list["u_update_date"] = Yii::t('service','U audit date');
        $list["u_update_user"] = Yii::t('service','U audit user');

		return $list;
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,table_type,service_id,contract_no,apply_date,month_amt,rate_num,old_city,cross_type,
            cross_city,status_type,reject_note,remark,audit_date,audit_user,luu','safe'),
			array('service_id,apply_date,cross_type,cross_city','required'),
            array('month_amt','numerical','allowEmpty'=>false),
            array('rate_num','numerical','allowEmpty'=>false,'min'=>0,'max'=>100),
            array('service_id','validateServiceID'),
            array('cross_city','validateCrossCity'),
		);
	}

    public function validateCrossCity($attribute, $params) {
        if(!empty($this->cross_city)){
            if($this->cross_city==$this->old_city){
                $this->addError($attribute, "承接城市不能与合约城市一致");
                return false;
            }
        }
    }

    public function validateServiceID($attribute, $params) {
        $id = $this->$attribute;
        if($this->table_type==0){
            $tableNameOne="swo_service";
            $tableNameTwo="swo_service_contract_no";
        }else{
            $tableNameOne="swo_service";
            $tableNameTwo="swo_service_ka_no";
        }
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.city,b.contract_no")->from("{$tableNameOne} a")
            ->leftJoin("{$tableNameTwo} b","a.id=b.service_id")
            ->where("a.id=:id and a.city in ({$city_allow})",array(":id"=>$id))->queryRow();
        if($row){
            $this->old_city = $row["city"];
            $this->contract_no = $row["contract_no"];
            $this->resetContractNo(true);
        }else{
            $this->addError($attribute, "合约编号不存在，无法交叉派单");
            return false;
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $uid = Yii::app()->user->id;
		$sql = "select * from swo_cross where id='".$index."' and lcu='{$uid}'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->table_type = $row['table_type'];
			$this->service_id = $row['service_id'];
			$this->contract_no = $row['contract_no'];
			$this->apply_date = General::toDate($row['apply_date']);
			$this->month_amt = $row['month_amt'];
			$this->rate_num = floatval($row['rate_num']);
            $this->old_city = $row['old_city'];
            $this->cross_city = $row['cross_city'];
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
            $this->resetContractNo();
            return true;
		}else{
		    return false;
        }
	}

	protected function resetContractNo($bool=false){
        if($this->table_type==0){
            $tableNameOne="swo_service";
            $tableNameTwo="swo_service_contract_no";
        }else{
            $tableNameOne="swo_service_ka";
            $tableNameTwo="swo_service_ka_no";
        }
        $row = Yii::app()->db->createCommand()->select("a.city,a.u_system_id,b.contract_no,a.amt_paid")->from("{$tableNameOne} a")
            ->leftJoin("{$tableNameTwo} b","a.id=b.service_id")
            ->where("a.id=:id",array(":id"=>$this->service_id))->queryRow();
        if($row){
            $this->old_city = $row["city"];
            $this->contract_no = $row["contract_no"];
            $this->u_system_id = $row["u_system_id"];
            if($bool){
                $this->old_month_amt = $row["amt_paid"];
            }
        }
    }

    public static function getCityList(){
        $suffix = Yii::app()->params['envSuffix'];
        $list=array();
        $rows = Yii::app()->db->createCommand()->select("code,name")->from("security{$suffix}.sec_city")
            ->where("ka_bool in (0,1)")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["code"]] = $row["name"];
            }
        }
        return $list;
    }

    public static function getCrossTypeList(){
        return array(
            2=>Yii::t("service","more contract"),//资质借用
            3=>Yii::t("service","short contract"),//短约
            4=>Yii::t("service","long contract"),//长约
        );
    }

    public static function getCrossTypeStrToKey($key){
        $list = self::getCrossTypeList();
        $key="".$key;
        if (key_exists($key,$list)){
            return $list[$key];
        }else{
            return $key;
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
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_cross where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_cross(
						service_id, contract_no, apply_date, month_amt, rate_num, old_city, cross_city, cross_type, old_month_amt, remark, lcu, lcd) values (
						:service_id, :contract_no, :apply_date, :month_amt, :rate_num, :old_city, :cross_city, :cross_type, :old_month_amt, :remark, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update swo_cross set 
					apply_date = :apply_date, 
					month_amt = :month_amt,
					rate_num = :rate_num,
					cross_city = :cross_city,
					cross_type = :cross_type,
					old_month_amt = :old_month_amt,
					status_type = 1,
					reject_note = NULL ,
					remark = :remark ,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':service_id')!==false)
			$command->bindParam(':service_id',$this->service_id,PDO::PARAM_INT);
        if (strpos($sql,':contract_no')!==false)
            $command->bindParam(':contract_no',$this->contract_no,PDO::PARAM_STR);
        if (strpos($sql,':apply_date')!==false)
            $command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
        if (strpos($sql,':month_amt')!==false)
            $command->bindParam(':month_amt',$this->month_amt,PDO::PARAM_STR);
        if (strpos($sql,':rate_num')!==false)
            $command->bindParam(':rate_num',$this->rate_num,PDO::PARAM_STR);
        if (strpos($sql,':old_city')!==false)
            $command->bindParam(':old_city',$this->old_city,PDO::PARAM_STR);
        if (strpos($sql,':cross_city')!==false)
            $command->bindParam(':cross_city',$this->cross_city,PDO::PARAM_STR);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':cross_type')!==false)
            $command->bindParam(':cross_type',$this->cross_type,PDO::PARAM_STR);
        if (strpos($sql,':old_month_amt')!==false)
            $command->bindParam(':old_month_amt',$this->old_month_amt,PDO::PARAM_STR);

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false){
            $date = date("Y-m-d H:i:s");
            $command->bindParam(':lcd',$date,PDO::PARAM_STR);
        }
		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }

        if(in_array($this->getScenario(),array("new","edit"))){
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
        $title = "交叉派单 - 待审核";
        $rate_amt = ($this->month_amt*$this->rate_num)/100;
        $rate_amt = number_format($rate_amt,2,'.','');
        $message="<p>合约编号：".$serviceModel->contract_no."</p>";
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
        $emailModel->addEmailToPrefixAndCity("CD02",$this->cross_city);
        $emailModel->sent();
    }

	public function readonly(){
        return $this->getScenario()=="view"||$this->status_type!=2;
    }
}