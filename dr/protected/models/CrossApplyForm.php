<?php

class CrossApplyForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $table_type=0;
	public $cross_num=0;
	public $service_id;
	public $u_system_id;
	public $office_id;
	public $contract_no;
	public $apply_date;
	public $month_amt;
	public $rate_num;
	public $old_city;
	public $cross_city;
	public $cross_amt;
	public $qualification_ratio;
	public $qualification_city;
	public $qualification_amt;
	public $status_type;
	public $reject_note;
	public $remark;
	public $audit_date;
	public $audit_user;
	public $luu;
	public $lcu;
	public $apply_category;
	public $effective_date;
	public $send_city;//额外收到邮件的城市

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
        $list["rate_num"] = Yii::t('service','accept rate');
        $list["cross_city"] = Yii::t('service','Cross city');
        $list["cross_amt"] = Yii::t('service','accept amt');
        $list["reject_note"] = Yii::t('service','reject note');
        $list["remark"] = Yii::t('service','Remarks');
        $list["status_type"] = Yii::t('service','status type');
        $list["luu"] = Yii::t('service','audit user');
        $list["audit_date"] = Yii::t('service','audit date');
        $list["audit_user"] = Yii::t('service','cross audit user');
        $list["cross_type"] = Yii::t('service','Cross type');
        $list["u_update_date"] = Yii::t('service','U audit date');
        $list["u_update_user"] = Yii::t('service','U audit user');
        $list["qualification_ratio"] = Yii::t('service','Qualification ratio');
        $list["qualification_city"] = Yii::t('service','Qualification city');
        $list["qualification_amt"] = Yii::t('service','Qualification Amt');
        $list["table_type"] = Yii::t('summary','menu name');
        $list["apply_category"] = Yii::t('service','apply category');
        $list["effective_date"] = Yii::t('service','effective date');
        $list["send_city"] = Yii::t('service','send cross city');

		return $list;
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,apply_category,table_type,cross_num,service_id,contract_no,apply_date,month_amt,rate_num,old_city,cross_type,
            cross_city,status_type,reject_note,remark,audit_date,audit_user,luu,qualification_ratio,
            qualification_city,qualification_amt,cross_amt,send_city,effective_date','safe'),
			array('service_id,apply_date,cross_type','required'),
            array('month_amt','numerical','allowEmpty'=>false),
            array('table_type','validateTableType'),
            array('service_id','validateServiceID'),
            array('cross_type','validateCrossType'),
            array('cross_city','validateCrossCity'),
            array('qualification_ratio,rate_num','numerical','min'=>0,'max'=>100),
            array('effective_date','validateEffective'),
		);
	}

    public function validateEffective($attribute, $params) {
        if(empty($this->effective_date)){
            $this->addError($attribute, "交叉生效日期不能为空");
        }else{
            $this->effective_date = date_format(date_create($this->effective_date),"Y/m/01");
        }
        return true;
    }

    public function validateTableType($attribute, $params) {
	    $this->effective_date = empty($this->effective_date)?null:date_format(date_create($this->effective_date),"Y/m/01");
	    $this->table_type = is_numeric($this->table_type)?intval($this->table_type):0;
        $list = self::getCrossTableTypeList();
	    if(!key_exists("{$this->table_type}",$list)){
            $this->addError($attribute, "菜单名称异常，请刷新重试");
            return false;
        }
        return true;
    }

    public function validateCrossType($attribute, $params) {
        $endCrossList = CrossApplyForm::getEndCrossListForTypeAndId($this->table_type,$this->service_id,$this->id);
	    $list = empty($endCrossList)?self::getCrossTypeList():self::getCrossTypeThreeList();
	    $this->cross_type="".$this->cross_type;
	    if(!key_exists($this->cross_type,$list)){
            $this->addError($attribute, "业务场景不存在，请刷新重试");
            return false;
        }else{
            if($endCrossList){
                if(empty($this->apply_category)){
                    $this->addError($attribute, "申请类型不能为空");
                    return false;
                }elseif ($this->apply_category==1){//调整金额
                    $this->cross_city = $endCrossList["cross_city"];
                    if($this->cross_type!=$endCrossList["cross_type"]){
                        $this->addError($attribute, "业务场景与上一次不一致");
                        return false;
                    }else{
                        $this->qualification_city = $endCrossList["qualification_city"];
                        $this->qualification_ratio = $endCrossList["qualification_ratio"];
                        $this->rate_num = $endCrossList["rate_num"];
                    }
                }elseif ($this->apply_category==3){//调整内容
                    if($this->cross_type!=$endCrossList["cross_type"]){
                        $this->addError($attribute, "业务场景与上一次不一致");
                        return false;
                    }
                }elseif ($this->apply_category==2){//合约类型调整
                    if($this->cross_type==11||$this->cross_type==12){//交叉普通、交叉KA
                        //$this->send_city=$endCrossList["cross_city"];
                        $this->cross_city=$endCrossList["old_city"];
                        $this->cross_amt=0;
                        $this->rate_num=0;
                        $this->qualification_city = $endCrossList["qualification_city"];
                        $this->qualification_ratio = $endCrossList["qualification_ratio"];
                        $this->qualification_amt = $endCrossList["qualification_amt"];
                    }elseif($this->cross_type==0||$this->cross_type==1){//普通合约、KA合约
                        $this->cross_city=null;
                        $this->cross_amt=0;
                        $this->rate_num=0;
                        $this->qualification_city = null;
                        $this->qualification_ratio = null;
                        $this->qualification_amt = null;
                    }
                }
            }else{
                $this->apply_category=2;//首次交叉，强制转换成类型调整
            }
	        if(in_array($this->cross_type,array('5','6','7','8'))){
                if($this->qualification_city===""){
                    $this->addError($attribute, "资质方不能为空");
                }
	            if(empty($this->qualification_ratio)){
                    $this->addError($attribute, "资质方比例不能为空");
                }
	            $this->qualification_amt=$this->month_amt*($this->qualification_ratio/100);
                $this->qualification_amt = round($this->qualification_amt,2);
                $this->cross_amt=$this->month_amt*((100-$this->qualification_ratio)/100)*($this->rate_num/100);
                $this->cross_amt = round($this->cross_amt,2);
            }else{
                if(!in_array($this->cross_type,array(11,12))){//普通合约、KA合约
                    $this->qualification_ratio=null;
                    $this->qualification_city=null;
                    $this->qualification_amt=null;
                }
                $this->cross_amt=$this->month_amt*($this->rate_num/100);
                $this->cross_amt = round($this->cross_amt,2);
            }
            if($this->cross_type==5){//资质借用、普通合约、KA合约
                $this->cross_city=null;
                $this->cross_amt=null;
                $this->rate_num=null;
            }
        }
        return true;
    }

    public function validateCrossCity($attribute, $params) {
        if($this->cross_type==5){//资质借用
            if($this->qualification_city==$this->old_city){
                $this->addError($attribute, "资质方不能与合约城市一致");
            }
        }elseif(!in_array($this->cross_type,array(0,1))){
            if(empty($this->rate_num)){
                $this->addError($attribute, "承接比例不能为空");
            }
            if(empty($this->cross_city)){
                $this->addError($attribute, "承接城市不能为空");
            }elseif(!in_array($this->cross_type,array(0,1,11,12))&&$this->cross_city==$this->old_city){
                $this->addError($attribute, "承接城市不能与合约城市一致");
            }
        }
        return true;
    }

    public function validateServiceID($attribute, $params) {
	    $crossId = empty($this->id)?0:$this->id;
        $id = $this->$attribute;
        if($this->table_type==0){
            $tableNameOne="swo_service";
            $tableNameTwo="swo_service_contract_no";
        }else{
            $tableNameOne="swo_service_ka";
            $tableNameTwo="swo_service_ka_no";
        }
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.city,a.is_intersect,a.u_system_id,b.contract_no")
            ->from("{$tableNameOne} a")
            ->leftJoin("{$tableNameTwo} b","a.id=b.service_id")
            ->where("a.id=:id and a.city in ({$city_allow})",array(":id"=>$id))->queryRow();
        if($row){
            if(empty($row["u_system_id"])){
                $this->addError($attribute, "派单系统id不能为空");
                return false;
            }
            $crossRow = Yii::app()->db->createCommand()->select("id")->from("swo_cross")
                ->where("status_type not in (2,5,6) and id!=:id and service_id=:service_id and table_type=:table_type",array(
                    ":id"=>$crossId,
                    ":service_id"=>$this->service_id,
                    ":table_type"=>$this->table_type
                ))->queryRow();
            if($crossRow){
                $this->addError($attribute, "已有进行中的交叉派单，请先完成交叉派单：".$crossRow["id"]);
                return false;
            }else{
                $this->old_city = $row["city"];
                $this->contract_no = $row["contract_no"];
                $this->resetContractNo(true);
                if($this->old_month_amt===""||$this->old_month_amt===null){
                    $this->addError($attribute, "合约的月金额不能为空");
                    return false;
                }
            }
        }else{
            $this->addError($attribute, "合约编号不存在，无法交叉派单");
            return false;
        }
        return true;
    }

    public static function getCrossTableTypeList(){
	    return array(
	        "0"=>Yii::t("app","Customer Service"),
	        "1"=>Yii::t("app","Customer Service KA"),
        );
    }

    public static function getCrossTableTypeNameForKey($key){
        $list = self::getCrossTableTypeList();
        if(key_exists("{$key}",$list)){
            return $list[$key];
        }else{
            return $key;
        }
    }

    public static function getEndCrossListForTypeAndId($table_type,$service_id,$notID=0){
        $notID = empty($notID)?0:$notID;
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("swo_cross")
            ->where("id!=:notID and table_type=:table_type and service_id=:service_id and status_type not in (2,6)",array(
                ":notID"=>$notID,":service_id"=>$service_id,":table_type"=>$table_type
            ))->order("id desc")->queryRow();
        return $row;
    }

    public static function getAllCrossListForTypeAndId($table_type,$service_id){
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("swo_cross")
            ->where("table_type=:table_type and service_id=:service_id and status_type not in (2,6)",array(
                ":service_id"=>$service_id,":table_type"=>$table_type
            ))->order("lcd asc")->queryAll();
        return $rows;
    }

    public static function getFlowCross($table_type,$service_id){
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("swo_cross")
            ->where("table_type=:table_type and service_id=:service_id and status_type not in (2,5,6)",array(
                ":service_id"=>$service_id,":table_type"=>$table_type
            ))->order("id desc")->queryRow();
        return $row;
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
			$this->cross_num = $row['cross_num'];
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
            $this->send_city = $row['send_city'];
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
        $row = Yii::app()->db->createCommand()->select("a.city,a.u_system_id,b.contract_no,a.amt_paid,a.office_id")->from("{$tableNameOne} a")
            ->leftJoin("{$tableNameTwo} b","a.id=b.service_id")
            ->where("a.id=:id",array(":id"=>$this->service_id))->queryRow();
        if($row){
            $this->old_city = $row["city"];
            $this->contract_no = $row["contract_no"];
            $this->u_system_id = $row["u_system_id"];
            $this->office_id = $row["office_id"];
            if($bool){
                $this->old_month_amt = $row["amt_paid"];
            }
            return true;
        }
        return false;
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

    public static function getApplyCategoryList(){
        return array(
            "1"=>Yii::t("service","amount adjustment"),//合约金额调整
            "2"=>Yii::t("service","type adjustment"),//合约类型调整
            "3"=>Yii::t("service","body adjustment"),//合约内容调整
        );
    }

    public static function getCrossTypeList(){
        return array(
            "3"=>Yii::t("service","short contract"),//短约
            "2"=>Yii::t("service","long contract"),//长约
            "5"=>Yii::t("service","more contract"),//资质借用
            "7"=>Yii::t("service","more contract - short"),//资质借用短约
            "6"=>Yii::t("service","more contract - long"),//资质借用长约
            "4"=>Yii::t("service","holdco contract"),//Holdco与收购
            "8"=>Yii::t("service","more contract - holdco"),//资质借用-Holdco与收购
            "10"=>Yii::t("service","Internal dispatch"),//内部派单
        );
    }

    public static function getCrossTypeThreeList(){
        return array(
            "0"=>Yii::t("service","ordinary"),//普通合约
            "1"=>Yii::t("service","KA"),//KA合约
            //"11"=>Yii::t("service","cross ordinary"),//交叉普通
            //"12"=>Yii::t("service","cross KA"),//交叉KA
            "3"=>Yii::t("service","short contract"),//短约
            "2"=>Yii::t("service","long contract"),//长约
            "5"=>Yii::t("service","more contract"),//资质借用
            "7"=>Yii::t("service","more contract - short"),//资质借用短约
            "6"=>Yii::t("service","more contract - long"),//资质借用长约
            "4"=>Yii::t("service","holdco contract"),//Holdco与收购
            "8"=>Yii::t("service","more contract - holdco"),//资质借用-Holdco与收购
            "10"=>Yii::t("service","Internal dispatch"),//内部派单
        );
    }

    public static function getCrossTypeStrToKey($key){
        $list = self::getCrossTypeThreeList();
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
						apply_category,send_city,effective_date,service_id,table_type, contract_no, apply_date, month_amt, rate_num, old_city, cross_city, cross_type, old_month_amt, remark, cross_amt, qualification_ratio, qualification_city, qualification_amt, lcu, lcd) values (
						:apply_category,:send_city,:effective_date,:service_id,:table_type, :contract_no, :apply_date, :month_amt, :rate_num, :old_city, :cross_city, :cross_type, :old_month_amt, :remark, :cross_amt, :qualification_ratio, :qualification_city, :qualification_amt, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update swo_cross set 
					effective_date = :effective_date, 
					send_city = :send_city, 
					apply_category = :apply_category, 
					apply_date = :apply_date, 
					month_amt = :month_amt,
					rate_num = :rate_num,
					cross_city = :cross_city,
					cross_type = :cross_type,
					old_month_amt = :old_month_amt,
					cross_amt = :cross_amt,
					qualification_ratio = :qualification_ratio,
					qualification_city = :qualification_city,
					qualification_amt = :qualification_amt,
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
		if (strpos($sql,':table_type')!==false)
			$command->bindParam(':table_type',$this->table_type,PDO::PARAM_INT);
		if (strpos($sql,':service_id')!==false)
			$command->bindParam(':service_id',$this->service_id,PDO::PARAM_INT);
        if (strpos($sql,':contract_no')!==false)
            $command->bindParam(':contract_no',$this->contract_no,PDO::PARAM_STR);
        if (strpos($sql,':apply_date')!==false)
            $command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
        if (strpos($sql,':month_amt')!==false){
            $month_amt = $this->month_amt===""||!is_numeric($this->month_amt)?null:floatval($this->month_amt);
            $command->bindParam(':month_amt',$month_amt,PDO::PARAM_STR);
        }
        if (strpos($sql,':rate_num')!==false){
            $rate_num = $this->rate_num===""||!is_numeric($this->rate_num)?null:floatval($this->rate_num);
            $command->bindParam(':rate_num',$rate_num,PDO::PARAM_STR);
        }
        if (strpos($sql,':old_city')!==false)
            $command->bindParam(':old_city',$this->old_city,PDO::PARAM_STR);
        if (strpos($sql,':cross_city')!==false)
            $command->bindParam(':cross_city',$this->cross_city,PDO::PARAM_STR);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':cross_type')!==false)
            $command->bindParam(':cross_type',$this->cross_type,PDO::PARAM_STR);
        if (strpos($sql,':old_month_amt')!==false){
            $old_month_amt = $this->old_month_amt===""||!is_numeric($this->old_month_amt)?null:floatval($this->old_month_amt);
            $command->bindParam(':old_month_amt',$old_month_amt,PDO::PARAM_STR);
        }
        if (strpos($sql,':cross_amt')!==false){
            $cross_amt = $this->cross_amt===""||!is_numeric($this->cross_amt)?null:floatval($this->cross_amt);
            $command->bindParam(':cross_amt',$cross_amt,PDO::PARAM_STR);
        }
        if (strpos($sql,':qualification_ratio')!==false){
            $qualification_ratio = $this->qualification_ratio===""||!is_numeric($this->qualification_ratio)?null:floatval($this->qualification_ratio);
            $command->bindParam(':qualification_ratio',$qualification_ratio,PDO::PARAM_STR);
        }
        if (strpos($sql,':qualification_city')!==false)
            $command->bindParam(':qualification_city',$this->qualification_city,PDO::PARAM_STR);
        if (strpos($sql,':qualification_amt')!==false){
            $qualification_amt = $this->qualification_amt===""||!is_numeric($this->qualification_amt)?null:floatval($this->qualification_amt);
            $command->bindParam(':qualification_amt',$qualification_amt,PDO::PARAM_STR);
        }
        if (strpos($sql,':send_city')!==false){
            $this->send_city = $this->send_city===""?null:$this->send_city;
            $command->bindParam(':send_city',$this->send_city,PDO::PARAM_STR);
        }
        if (strpos($sql,':apply_category')!==false){
            $this->apply_category = $this->apply_category===""?null:$this->apply_category;
            $command->bindParam(':apply_category',$this->apply_category,PDO::PARAM_STR);
        }
        if (strpos($sql,':effective_date')!==false){
            $this->effective_date = $this->effective_date===""?null:$this->effective_date;
            $command->bindParam(':effective_date',$this->effective_date,PDO::PARAM_STR);
        }

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
        //$rate_amt = number_format($rate_amt,2,'.','');
        $message="<p>合约编号：".$serviceModel->contract_no."</p>";
        $message.="<p>客户编号及名称：".$serviceModel->company_name."</p>";
        $message.="<p>客户类别：".$serviceModel->cust_type."</p>";
        $message.="<p>服务內容：".$serviceModel->service."</p>";
        $message.="<p>合约城市：".General::getCityName($serviceModel->city)."</p>";
        $message.="<p>承接城市：".General::getCityName($this->cross_city)."</p>";
        $message.="<p>月金额：".$this->month_amt."</p>";
        $message.="<p>比例：".$this->rate_num."%"."</p>";
        $message.="<p>比例后金额：".$this->cross_amt."</p>";
        $message.="<p>备注：".$this->remark."</p>";
        $message.="<p>申请时间：".$this->apply_date."</p>";
        $emailModel = new Email($title,$message,$title);
        $city = empty($this->cross_city)?$this->qualification_city:$this->cross_city;
        $emailModel->addEmailToPrefixAndCity("CD02",$city);
        $emailModel->sent();
        if(in_array($this->cross_type,array(11,12,0,1,5))&&!empty($this->send_city)){//普通合约、KA合约
            $title = "交叉派单 - ".CrossApplyForm::getCrossTypeStrToKey($this->cross_type);
            $emailModel->setSubject($title);
            $emailModel->setDescription($title);
            $emailModel->resetToAddr();
            $emailModel->addEmailToPrefixAndCity("CD01",$this->send_city);
            $emailModel->addEmailToPrefixAndCity("CD02",$this->send_city);
            $emailModel->addEmailToCity($this->send_city);
            $emailModel->sent();
        }
    }

	public function readonly(){
        return $this->getScenario()=="view"||$this->status_type!=2;
    }


    public static function getServiceList($table_type,$service_id){
        if($table_type==0){
            $tableNameOne="swo_service";
            $tableNameTwo="swo_service_contract_no";
        }else{
            $tableNameOne="swo_service_ka";
            $tableNameTwo="swo_service_ka_no";
        }
        $row = Yii::app()->db->createCommand()
            ->select("a.*,b.contract_no,f.code as company_code,f.name as company_name")
            ->from("{$tableNameOne} a")
            ->leftJoin("{$tableNameTwo} b","a.id=b.service_id")
            ->leftJoin("swo_company f","a.company_id=f.id")
            ->where("a.id=:id",array(":id"=>$service_id))->queryRow();
        return $row?$row:array();
    }

    public function validateFull(){
        $attrStr = key_exists("attrStr",$_POST['CrossApply'])?$_POST['CrossApply']["attrStr"]:"";
        $attrList = explode(",",$attrStr);
        $list=array();
        if(!empty($attrList)){
            foreach ($attrList as $id){
                $this->service_id = $id;
                $row = $this->getServiceList($this->table_type,$id);
                if(empty($row)){
                    continue;
                }
                $endCross = $this->getEndCrossListForTypeAndId($this->table_type,$id);
                $row["error"] = "";
                if(empty($row["contract_no"])){
                    $row["error"] = "合约编号不能为空";
                }
                if(empty($row["u_system_id"])){
                    $row["error"] = "U系统ID不能为空";
                }
                if($row["amt_paid"]===""||$row["amt_paid"]===null){
                    $row["error"] = "合约的月金额不能为空";
                }
                if($endCross){
                    if($this->cross_type!=$endCross["cross_type"]){
                        $row["error"] = "业务场景不一致";
                    }elseif (!empty($endCross["cross_city"])&&$this->cross_city!=$endCross["cross_city"]){
                        $row["error"] = "承接城市不一致";
                    }elseif (!empty($endCross["rate_num"])&&floatval($this->rate_num)!=floatval($endCross["rate_num"])){
                        $row["error"] = "承接比例不一致";
                    }elseif (!empty($endCross["qualification_city"])&&$this->qualification_city!=$endCross["qualification_city"]){
                        $row["error"] = "资质方不一致";
                    }elseif (!empty($endCross["qualification_ratio"])&&floatval($this->qualification_ratio)!=floatval($endCross["qualification_ratio"])){
                        $row["error"] = "资质方比例不一致";
                    }
                }
                $flowCross = $this->getFlowCross($this->table_type,$id);
                if($flowCross){
                    $row["error"] = "已有进行中的交叉派单";
                }
                $list[]=$row;
            }
        }
	    return $list;
    }

    public function saveCrossFull($list){
        $return = array("success"=>0,"error"=>0);
        if(!empty($list)) {
            $this->setScenario("new");
            foreach ($list as $row) {
                if(empty($row["error"])){
                    $this->service_id = $row["id"];
                    $this->contract_no = $row["contract_no"];
                    $this->old_city = $row["city"];
                    $this->old_month_amt = $row["amt_paid"];
                    $this->month_amt = $row["amt_paid"];
                    $this->validateCrossType("id","");
                    $this->validateEffective("id","");
                    $this->saveData();
                    $return["success"]++;
                }else{
                    $return["error"]++;
                }
            }
        }
        return $return;
    }

    public function getCrossFullHtml($list){
        $html="";
        if(!empty($list)){
            $cross_type_name = CrossApplyForm::getCrossTypeStrToKey($this->cross_type);
            $qualification_city_name = General::getCityName($this->qualification_city);
            $qualification_rate = empty($this->qualification_ratio)?"":round($this->qualification_ratio,2)."%";
            $cross_city_name = General::getCityName($this->cross_city);
            $cross_rate = empty($this->rate_num)?"":round($this->rate_num,2)."%";
            foreach ($list as $row){
                if(empty($row["error"])){
                    $error="正常";
                    $html.="<tr class='text-success'>";
                }else{
                    $error=$row["error"];
                    $html.="<tr class='text-danger'>";
                }
                $html.="<td>".General::getCityName($row["city"])."</td>";
                $html.="<td>".$row["company_name"].$row["company_code"]."</td>";
                $html.="<td>".$this->apply_date."</td>";
                $html.="<td>".$row["amt_paid"]."</td>";
                $html.="<td>".$cross_type_name."</td>";
                $html.="<td>".$qualification_city_name."</td>";
                $html.="<td>".$qualification_rate."</td>";
                $html.="<td>".$cross_city_name."</td>";
                $html.="<td>".$cross_rate."</td>";
                $html.="<td>".$error."</td>";
                $html.="</tr>";
            }
        }
        return $html;
    }
}