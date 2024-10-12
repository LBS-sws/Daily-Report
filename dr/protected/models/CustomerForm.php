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
            $curlModel->saveJDCustomerID($rtn);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	//发送所有客户资料到金蝶系统
    public function sendAllCustomerToJD($city="",$minID=0,$maxID=0){
        $whereSql="";
        if(!empty($city)){
            $cityList = explode(",",$city);
            $whereSql.= " and city in('".implode("','",$cityList)."')";
        }
        if(!empty($minID)){
            $whereSql.= " and id>$minID ";
        }
        if(!empty($maxID)){
            $whereSql.= " and id<$maxID ";
        }
        $pageMax = 100;//最大数量
        $sqlCount = "select count(id) from swo_company where id>0 {$whereSql}";
        $totalRow = Yii::app()->db->createCommand($sqlCount)->queryScalar();
        if($totalRow>0){
            echo "max number:{$totalRow}<br/>\r\n";
            $sql = "select * from swo_company where id>0 {$whereSql}";
            $this->sendCustomerToJDPage($sql,$totalRow,$pageMax);
        }
    }

	//发送所有客户资料到金蝶系统
    public function sendAllTrimCustomer(){
        $pageMax = 100;//最大数量
        $sqlCount = "select count(id) from swo_company where code LIKE '% %'";
        $totalRow = Yii::app()->db->createCommand($sqlCount)->queryScalar();
        if($totalRow>0){
            echo "max number:{$totalRow}<br/>\r\n";
            $sql = "select * from swo_company where code LIKE '% %'";
            $this->sendCustomerToJDPage($sql,$totalRow,$pageMax);
        }
        $updateSql = "UPDATE swo_company SET code = REPLACE(code, ' ', '')";
        Yii::app()->db->createCommand($updateSql)->execute();
    }

    protected function sendCustomerToJDPage($sql,$totalRow,$pageMax,$page=0){
        $startNum = $page*$pageMax;
        $whereSql = $sql." ORDER BY id ASC LIMIT {$startNum},$pageMax";
        $rows = Yii::app()->db->createCommand($whereSql)->queryAll();
        if($rows){
            $data = array();
            $curlModel = new CurlForCustomer();
            $curlModel->setInfoType("customerAll");
            foreach ($rows as $row){
                $this->id = $row['id'];
                $this->code = str_replace(' ','',$row['code']);
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
            $returnJD = $curlModel->sendJDCurlForCustomerData($data);
            $curlModel->saveTableForArr();
            $curlModel->saveJDCustomerID($returnJD);
            $page++;
            echo "send success。page:{$page}<br/>\r\n";
            if($totalRow>$startNum){
                $this->sendCustomerToJDPage($sql,$totalRow,$pageMax,$page);
            }
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
				$sql = "delete from swo_company where id = :id";
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
						where id = :id
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

    public function companyZZ(){
        $suffix = Yii::app()->params['envSuffix'];
        $codeStr = "'002DWXSBY','01BCQLRFZ','01CALYSHG','01DWXSHHGJ','01DWXSJFD','01DWXSJFLD','01DWXSMHD','01DWXSXED','01DWXSXT','01DWXSZB','01HYCQHGLSD','01JNHXSSGJ','01JOBNLKR','01JYDZCHXLSX','01LCDZBXCG','01LGKRWFJ','01LINLEESDNMC','01LKSPNRHGD','01LSQFCJ','01MBYBA','01MWYTHG','01RJSRW','01SCSL','01SPLHSY','01SXJDHYZXD','01SZRSLL','01WBYZCND','01WLDWFJ','01XXLCT','01XYFWFJ','01XYYJZCGHJT','01XYYJZCGLY','01YJYJHCZD','01YLSHG','01ZCXEDWLD','02BSDJZXCG','02CJDBJCCHGTRTHD','02CXECSMFHTD','02DBJCCHGZJCD','02DWXSHRWJ','02DWXSSMD','02DWXSWW','02DWXSYT','02DZHZRL','02FSCQ','02HMGFP','02HXRXHXL','02HYHGSMD','02LCDCYXHXLD','02SXJWD','02WLDWDD','02WYXY','02XLWNRHGCC','02XYEHTQD','02ZMGXCLLLYST','02ZZXXTY','03BCLW','03CXEMFSDD','03DWXSTXBM','03DWXSBDM','03JSKRWD','03LCDCYWDD','03LCXX','03PCSFD','03XYYJZCGTXD','03YJYJZJCHLSC','03ZHSDBGYXZRGS','03ZZSDWYGLYXZRGS','04CDGS','04CQLYSHGSND','04DHG','04DHMMGD','04DWXSDMZGC','04DWXSGYZX','04DWXSHJ','04DWXSLYD','04DWXSMDC','04DWXSQPYD','04DWXSSNC','04DWXSTHD','04DWXSZJD','04EYGTY','04FEKF','04FJXXCY','04GZC','04HNZZZBHY','04HYHGHCD','04JMSX','04LSCHGTYD','04LXESFQ','04LXJGGNRFG','04MDLSLC','04PCHXEZ','04PCJACY','04PCJYD','04PCLY','04QJKRGDD','04QXZY','04SDFPTYFQL','04SEJKSS','04SJYTCG','04SMLWSSHT','04SMLWSSHTD','04SNCF','04STJY','04SXJCJGCD','04SXJCJHPGFGKD','04SXJLSD','04SXJSGTSJFD','04SXJWGD','04SYSXCMDSDGC','04SZFPLJBJ','04TYQXHLCL','04TYQXLSXD','04WSHGW','04XCXCL','04XLKRGDD','04XRML','04XXGNPCD','04XYECTGD','04XYLCDCLTH','04XYRCDXYD','04YGYXYS','04YJYJFCMYD','04YJYJLTGJ','04YKLLHR','04YMZDPD','04YWKFYDGC','04YYDTBC','04YYWCYD','04ZMJL','04ZZHJJD','04ZZSYTWYYXZRGS','05DWXSLKD','06DWXSGYZXB6','06DWXSJHD','06DWXSXTCKB22','06MDLXTCZ','06MDLXTLC','06QSXLJCR','06THBHXT','06THCSXT','06XTSYHQJJYD','06YJYJXTDXLJD','06YJYJXTLCD','03XFNWDD','01PDHTKC','04SYSXCGD','06BTYKYXTYHJHXTD','01SGLLZZHXKR','01GFNKRHGZZ','03XLGHXKRZZLL','06CLBXQXTQJD','03QJX','04YJYJHNGYDXD','01SXTZZRL','01QJXLSD','02LCDCYCFGCD','02DHCFXHXLD','01DHCFSSYD','04ZZSTJDGCGS','03XYECTQSD','01YJYJSSYD','06BTYKYXTWDGCD','04YJYJXSMJD','02YJYJHQGCD','04MFJD','03HZFLRKR','06SXTRLXTCSHZ','04DWXSXNZD','04DWXSZSD','01DWXSJCD','04DWXSKQMJ','03DWXSWDD','01YKLLXRD','01LSQLZTFWZX','03WSWWJSGJ','04AA','06DWXSXTHLD','06NNJKCTHD','CSSPJMX','04NLSYJSST','03LCDHG','04HWHPCTD','04NHJTKFN','04SXTZZRLSNCD','04TQSK'";
        echo "start:<br/>";
        $CSRows = Yii::app()->db->createCommand()->select("id,code")->from("swo_company")
            ->where("city='CS' and code in ({$codeStr})")->queryAll();
        $lud = "2024-10-12 12:00:00";
        if($CSRows){
            foreach ($CSRows as $CSRow){
                echo "ID:{$CSRow["id"]}；CS Code:{$CSRow["code"]}；";
                $ZZRow = Yii::app()->db->createCommand()->select("id,code")->from("swo_company")
                    ->where("city='ZZ' and code=:code",array(":code"=>$CSRow["code"]))->queryRow();
                if($ZZRow){//如果株洲存在该编号
                    //删除株洲资料
                    Yii::app()->db->createCommand()->delete("swo_company","id=:id",array(":id"=>$ZZRow["id"]));
                    echo "<br/>Delete:{$ZZRow["id"]}！";
                    //修改客户服务(普通合约)
                    $aa=Yii::app()->db->createCommand()->update("swo_service",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service:{$aa}；";
                    //修改客户服务(KA合约)
                    $aa=Yii::app()->db->createCommand()->update("swo_service_ka",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service:{$aa}；";
                    //修改客户服务(ID合约)
                    $aa=Yii::app()->db->createCommand()->update("swo_serviceid",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service:{$aa}；";
                    //修改投诉个案
                    $aa=Yii::app()->db->createCommand()->update("swo_followup",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service:{$aa}；";
                    //修改物流配送
                    $aa=Yii::app()->db->createCommand()->update("swo_logistic",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service:{$aa}；";
                    //修改品鉴记录
                    $aa=Yii::app()->db->createCommand()->update("swo_qc",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service:{$aa}；";
                    //修改付款/收款记录
                    $aa=Yii::app()->db->createCommand()->update("account{$suffix}.acc_trans_info",array(
                        "field_value"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"field_id='payer_id' and field_value=".$ZZRow["id"]);
                    echo "service:{$aa}；";
                }
                echo "<br/>Update ZZ！";
                //修改长沙客户到株洲
                $aa=Yii::app()->db->createCommand()->update("swo_company",array(
                    "city"=>"ZZ",
                    "lud"=>$lud
                ),"id=:id",array(":id"=>$CSRow["id"]));
                echo "service:{$aa}；";
                //修改客户服务(普通合约)
                $aa=Yii::app()->db->createCommand()->update("swo_service",array(
                    "city"=>"ZZ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service:{$aa}；";
                //修改客户服务(KA合约)
                $aa=Yii::app()->db->createCommand()->update("swo_service_ka",array(
                    "city"=>"ZZ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service:{$aa}；";
                //修改客户服务(ID合约)
                $aa=Yii::app()->db->createCommand()->update("swo_serviceid",array(
                    "city"=>"ZZ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service:{$aa}；";
                //修改投诉个案
                $aa=Yii::app()->db->createCommand()->update("swo_followup",array(
                    "city"=>"ZZ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service:{$aa}；";
                //修改物流配送
                $aa=Yii::app()->db->createCommand()->update("swo_logistic",array(
                    "city"=>"ZZ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service:{$aa}；";
                //修改品鉴记录
                $aa=Yii::app()->db->createCommand()->update("swo_qc",array(
                    "city"=>"ZZ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service:{$aa}；<br/><br/>";
            }
        }
        echo "end:<br/>";
    }
}
