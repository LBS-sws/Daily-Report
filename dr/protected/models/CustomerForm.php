<?php

class CustomerForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $type;
	public $code;
	public $name;
	public $full_name;
	public $cont_name;
	public $cont_phone;
	public $nature;
	public $address;
	public $tax_reg_no;
	public $group_id;
	public $group_name;
	public $status;
	public $city;
	public $email;

	public $service = array();

    public $jd_set = array();
    public static $jd_set_list=array(
        array("field_id"=>"jd_customer_id","field_type"=>"text","field_name"=>"jd customer id"),
    );
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('customer','Record ID'),
			'code'=>Yii::t('customer','Code'),
			'name'=>Yii::t('customer','Customer Name'),
			'full_name'=>Yii::t('customer','Registered Company Name'),
			'cont_name'=>Yii::t('customer','Contact Name'),
			'cont_phone'=>Yii::t('customer','Contact Phone'),
			'address'=>Yii::t('customer','Address'),
			'tax_reg_no'=>Yii::t('code','SSM No.'),
			'group_id'=>Yii::t('customer','Group ID'),
			'group_name'=>Yii::t('customer','Group Name'),
            'email'=>Yii::t('customer','Email'),
			'status'=>Yii::t('customer','Status'),
		);
	}
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, jd_set, full_name, cont_name, cont_phone, address, tax_reg_no, group_id, group_name, status,email','safe'),
			array('name, code','required'),
/*
			array('code','unique','allowEmpty'=>true,
					'attributeName'=>'code',
					'caseSensitive'=>false,
					'className'=>'Customer',
				),
*/
			array('id','validateID'),
			array('code','validateCode'),
		);
	}

	public function validateID($attribute, $params) {
	    if($this->scenario!="new"){
            $index = $this->id;
            $city = Yii::app()->user->city_allow();
            $sql = "select city from swo_company where id='{$index}' and city in ($city)";
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            if($row){
                $this->city = $row["city"];
            }else{
                $this->addError($attribute, "数据异常，请刷新重试");
            }
        }else{
            $this->city = Yii::app()->user->city();
        }
    }

	public function validateCode($attribute, $params) {
		$code = $this->$attribute;
		$city = $this->city;
		if (!empty($code)) {
			switch ($this->scenario) {
				case 'new':
					if (Customer::model()->exists('code=? and city=?',array($code,$city))) {
						$this->addError($attribute, Yii::t('customer','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
				case 'edit':
					if (Customer::model()->exists('code=? and city=? and id<>?',array($code,$city,$this->id))) {
						$this->addError($attribute, Yii::t('customer','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
			}
		}
	}

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city_allow();
		$sql = "select * from swo_company where id=".$index." and city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();

		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->code = $row['code'];
				$this->city = $row['city'];
				$this->name = $row['name'];
				$this->full_name = $row['full_name'];
				$this->cont_name = $row['cont_name'];
				$this->cont_phone = $row['cont_phone'];
				$this->address = $row['address'];
				$this->tax_reg_no = $row['tax_reg_no'];
				$this->group_id = $row['group_id'];
				$this->group_name = $row['group_name'];
				$this->status = $row['status'];
                $this->email = $row['email'];

                $setRows = Yii::app()->db->createCommand()->select("field_id,field_value")
                    ->from("swo_send_set_jd")->where("table_id=:table_id and set_type='customer'",array(":table_id"=>$index))->queryAll();
                $setList = array();
                foreach ($setRows as $setRow){
                    $setList[$setRow["field_id"]] = $setRow["field_value"];
                }
                $this->jd_set=array();
                foreach (self::$jd_set_list as $item){
                    $fieldValue = key_exists($item["field_id"],$setList)?$setList[$item["field_id"]]:null;
                    $this->jd_set[$item["field_id"]] = $fieldValue;
                }
				break;
			}
		}
		
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveCustomer($connection);
            //保存金蝶要求的字段
            $this->saveJDSetInfo($connection);
            //客户资料保存后需要发消息给金蝶系统
            $curlModel = new CurlForCustomer();
            $rtn = $curlModel->sendJDCurlForCustomer($this);
            $curlModel->saveTableForArr();
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	//发送所有客户资料到金蝶系统
    public function sendAllCustomerToJD($city=""){
	    $data = array();
        $curlModel = new CurlForCustomer();
        $whereSql="";
        if(!empty($city)){
            $cityList = explode(",",$city);
            $whereSql= "and city in('".implode("','",$cityList)."')";
        }
        $sql = "select * from swo_company where id>0 {$whereSql}";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->id = $row['id'];
                $this->code = $row['code'];
                $this->city = $row['city'];
                $this->name = $row['name'];
                $this->full_name = $row['full_name'];
                $this->cont_name = $row['cont_name'];
                $this->cont_phone = $row['cont_phone'];
                $this->address = $row['address'];
                $this->tax_reg_no = $row['tax_reg_no'];
                $this->group_id = $row['group_id'];
                $this->group_name = $row['group_name'];
                $this->status = $row['status'];
                $this->email = $row['email'];
                $data[] = $curlModel->getDataForCustomerModel($this);
            }
            $curlModel->sendJDCurlForCustomerData($data);
            $curlModel->saveTableForArr();
            echo "send success";
        }else{
            echo "data is null";
        }
    }


    //保存金蝶要求的字段
    protected function saveJDSetInfo(&$connection) {
        foreach (self::$jd_set_list as $list){
            $field_value = key_exists($list["field_id"],$this->jd_set)?$this->jd_set[$list["field_id"]]:null;
            $rs = Yii::app()->db->createCommand()->select("id,field_id")->from("swo_send_set_jd")
                ->where("set_type ='customer' and table_id=:table_id and field_id=:field_id",array(
                    ':field_id'=>$list["field_id"],':table_id'=>$this->id,
                ))->queryRow();
            if($rs){
                $connection->createCommand()->update('swo_send_set_jd',array(
                    "field_value"=>$field_value,
                ),"id=:id",array(':id'=>$rs["id"]));
            }else{
                $connection->createCommand()->insert('swo_send_set_jd',array(
                    "table_id"=>$this->id,
                    "set_type"=>'customer',
                    "field_id"=>$list["field_id"],
                    "field_value"=>$field_value,
                ));
            }
        }
    }

	protected function saveCustomer(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_company where id = :id and city = :city";
				$this->delHistorySave();
				break;
			case 'new':
				$sql = "insert into swo_company(
							code, name, full_name, tax_reg_no, cont_name, cont_phone, address,
							group_id, group_name, status,
							city, luu, lcu
						) values (
							:code, :name, :full_name, :tax_reg_no, :cont_name, :cont_phone, :address,
							:group_id, :group_name, :status,
							:city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_company set
							code = :code, 
							name = :name, 
							full_name = :full_name, 
							tax_reg_no = :tax_reg_no, 
							cont_name = :cont_name, 
							cont_phone = :cont_phone, 
							email = :email,
							address = :address, 
							group_id = :group_id,
							group_name = :group_name,
							status = :status,
							luu = :luu 
						where id = :id and city = :city
						";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':full_name')!==false)
			$command->bindParam(':full_name',$this->full_name,PDO::PARAM_STR);
		if (strpos($sql,':tax_reg_no')!==false)
			$command->bindParam(':tax_reg_no',$this->tax_reg_no,PDO::PARAM_STR);
		if (strpos($sql,':code')!==false)
			$command->bindParam(':code',$this->code,PDO::PARAM_STR);
		if (strpos($sql,':cont_name')!==false)
			$command->bindParam(':cont_name',$this->cont_name,PDO::PARAM_STR);
		if (strpos($sql,':cont_phone')!==false)
			$command->bindParam(':cont_phone',$this->cont_phone,PDO::PARAM_STR);
        if (strpos($sql,':email')!==false)
            $command->bindParam(':email',$this->email,PDO::PARAM_STR);
		if (strpos($sql,':address')!==false)
			$command->bindParam(':address',$this->address,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$this->city,PDO::PARAM_STR);
		if (strpos($sql,':group_id')!==false)
			$command->bindParam(':group_id',$this->group_id,PDO::PARAM_STR);
		if (strpos($sql,':group_name')!==false)
			$command->bindParam(':group_name',$this->group_name,PDO::PARAM_STR);
		if (strpos($sql,':status')!==false)
			$command->bindParam(':status',$this->status,PDO::PARAM_INT);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		return true;
	}
	
	public function getStatusList() {
		return array(
			0=>Yii::t('customer','Unknown'),
			1=>Yii::t('customer','In Service'),
			2=>Yii::t('customer','Stop Service'),
			3=>Yii::t('customer','Others'),
		);
	}

    //哪些字段修改后需要记录
    protected function historyUpdateList(){
	    $list = array(
	        'code','name','full_name','tax_reg_no','cont_name','cont_phone','email','address',
	        'group_id','group_name','status'
        );
        return $list;
    }

    //哪些字段修改后需要记录
    protected function getNameForValue($key,$value){
        return $value;
    }

    protected function delHistorySave(){
        $model = new CustomerForm();
        $model->retrieveData($this->id);
        $keyArr = self::historyUpdateList();
        $delText=array();
        $delText[]="id：".$this->id;
        foreach ($keyArr as $key){
            $delText[]=$this->getAttributeLabel($key)."：".self::getNameForValue($key,$model->$key);
        }
        $delText= implode("<br/>",$delText);
        $systemLogModel = new SystemLogForm();
        $systemLogModel->log_date=date("Y/m/d H:i:s");
        $systemLogModel->log_user=Yii::app()->user->id;
        $systemLogModel->log_type=get_class($this);
        $systemLogModel->log_type_name="客户资料";
        $systemLogModel->option_str="删除";
        $systemLogModel->option_text=$delText;
        $systemLogModel->insertSystemLog("D");
    }
}
