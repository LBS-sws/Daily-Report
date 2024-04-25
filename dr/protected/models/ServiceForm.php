<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class ServiceForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $service_no;
	public $service_new_id=0;
	public $company_id;
	public $customer_code;
	public $customer_name;
	public $company_name;
	public $nature_type;
	public $nature_type_two;
	public $cust_type;
    public $cust_type_name;
    public $pieces=0;
	public $product_id;
	public $service;
	public $paid_type;
	public $amt_paid;
	public $amt_install;
	public $need_install;
	public $salesman;
	public $salesman_id;
    public $technician;
    public $technician_id;
	public $sign_dt;
	public $ctrt_end_dt;
	public $ctrt_period=12;
	public $cont_info;
	public $first_dt;
	public $first_tech;
	public $first_tech_id;
	public $reason;
	public $status;
	public $status_dt;
	public $remarks;
	public $remarks2;
	public $equip_install_dt;
	public $org_equip_qty = 0;
	public $rtn_equip_qty = 0;
	public $city;
	public $surplus=0;
    public $all_number=0;
    public $surplus_edit0=0;
    public $all_number_edit0=0;
    public $surplus_edit1=0;
    public $all_number_edit1=0;
    public $surplus_edit2=0;
    public $all_number_edit2=0;
    public $surplus_edit3=0;
    public $all_number_edit3=0;
    public $surplus_edit4=0;
    public $all_number_edit4=0;
	public $b4_product_id;
	public $b4_service;
	public $b4_paid_type;
	public $b4_amt_paid;
	public $othersalesman;
	public $othersalesman_id;
	public $status_desc;
	public $backlink;
	public $prepay_month=0;
	public $prepay_start=0;
    public $office_id;
    public $contract_no;
    public $commission;
    public $other_commission;
    public $tracking;

	public $files;

	public $docMasterId = array(
							'service'=>0,
						);
	public $removeFileId = array(
							'service'=>0,
						);
	public $no_of_attm = array(
							'service'=>0,
						);
	
	public function init() {
		$this->city = Yii::app()->user->city();
	}
	public $send;
	public $lcd;
	public $lud;
	public $lcu;
	public $luu;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('service','Record ID'),
            'service_no'=>Yii::t('service','service no'),
			'company_id'=>Yii::t('service','Customer')." ID",
			'company_name'=>Yii::t('service','Customer'),
			'service'=>Yii::t('service','Service'),
			'product_id'=>Yii::t('service','Service'),
			'nature_type'=>Yii::t('service','Nature'),
			'nature_type_two'=>Yii::t('service','Nature'),
			'cust_type'=>Yii::t('service','Customer Type'),
			'cust_type_name'=>Yii::t('service','Customer Type'),
			'amt_paid'=>Yii::t('service','Paid Amt'),
			'paid_type'=>Yii::t('service','Paid Amt Type'),
			'amt_install'=>Yii::t('service','Installation Fee'),
			'need_install'=>Yii::t('service','Installation'),
			'salesman_id'=>Yii::t('service','Resp. Sales')." ID",
			'salesman'=>Yii::t('service','Resp. Sales'),
            'othersalesman'=>Yii::t('service','OtherSalesman'),
            'othersalesman_id'=>Yii::t('service','OtherSalesman'),
            'technician'=>Yii::t('service','Resp. Tech.'),
            'technician_id'=>Yii::t('service','Resp. Tech.'),
			'sign_dt'=>Yii::t('service','Sign Date'),
			'ctrt_end_dt'=>Yii::t('service','Contract End Date'),
			'ctrt_period'=>Yii::t('service','Contract Period'),
			'cont_info'=>Yii::t('service','Contact'),
			'first_dt'=>Yii::t('service','First Service Date'),
			'first_tech'=>Yii::t('service','First Service Tech.'),
			'first_tech_id'=>Yii::t('service','First Service Tech.'),
			'reason'=>Yii::t('service','Reason'),
			'status'=>Yii::t('service','Record Type'),
			'status_dt'=>Yii::t('service','Record Date'),
			'remarks'=>Yii::t('service','Cross Area Remarks'),
			'remarks2'=>Yii::t('service','Remarks'),
			'b4_service'=>Yii::t('service','Service (Before)'),
			'b4_product_id'=>Yii::t('service','Service (Before)'),
			'b4_amt_paid'=>Yii::t('service','Payment  (Before)'),
			'b4_paid_type'=>Yii::t('service','Payment  (Before)'),
			'af_service'=>Yii::t('service','Service (After)'),
			'af_amt_paid'=>Yii::t('service','Paid Amt (After)'),
			'af_paid_type'=>Yii::t('service','Paid Amt (After)'),
			'equip_install_dt'=>Yii::t('service','Installation Date'),
			'org_equip_qty'=>Yii::t('service','Org. Equip. Qty'),
			'rtn_equip_qty'=>Yii::t('service','Return Equip. Qty'),
			'new_dt'=>Yii::t('service','New Date'),
			'renew_dt'=>Yii::t('service','Renew Date'),
			'amend_dt'=>Yii::t('service','Amend Date'),
			'resume_dt'=>Yii::t('service','Resume Date'),
			'suspend_dt'=>Yii::t('service','Suspend Date'),
			'terminate_dt'=>Yii::t('service','Terminate Date'),
            'all_number'=>Yii::t('service','Number'),
            'surplus'=>Yii::t('service','Surplus'),
            'all_number_edit0'=>Yii::t('service','Number edit0'),
            'surplus_edit0'=>Yii::t('service','Surplus edit0'),
            'all_number_edit1'=>Yii::t('service','Number edit1'),
            'surplus_edit1'=>Yii::t('service','Surplus edit1'),
            'all_number_edit2'=>Yii::t('service','Number edit2'),
            'surplus_edit2'=>Yii::t('service','Surplus edit2'),
            'all_number_edit3'=>Yii::t('service','Number edit3'),
            'surplus_edit3'=>Yii::t('service','Surplus edit3'),
            'pieces'=>Yii::t('service','Pieces'),
            'prepay_month'=>Yii::t('service','Prepay Month'),
            'prepay_start'=>Yii::t('service','Prepay Start'),
            'contract_no'=>Yii::t('service','Contract No'),
            'tracking'=>Yii::t('service','tracking'),
            'lcu'=>Yii::t('service','lcu'),
            'luu'=>Yii::t('service','luu'),
            'lcd'=>Yii::t('service','lcd'),
            'lud'=>Yii::t('service','lud'),
            'office_id'=>"归属",
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return $this->getScenario()=="delete"
		? array(
			array('id, office_id,tracking,technician_id, salesman_id, othersalesman_id, first_tech_id, technician, cont_info, first_tech, reason, remarks,othersalesman, remarks2, paid_type, nature_type, nature_type_two, cust_type, prepay_month,prepay_start,contract_no
				status, status_desc, company_id, product_id, backlink, fresh, paid_type, city, all_number,surplus,all_number_edit0,surplus_edit0,all_number_edit1,surplus_edit1,
				all_number_edit2,surplus_edit2,all_number_edit3,surplus_edit3,b4_product_id, b4_service, b4_paid_type,cust_type_name,pieces, need_install','safe'),
			array('files, removeFileId, docMasterId, no_of_attm','safe'),
			array('company_id,salesman_id,company_name,salesman, service,all_number,surplus, status_dt','safe'),
			array('ctrt_period','safe'),
			array('amt_paid, amt_install','safe'),
			array('org_equip_qty, rtn_equip_qty','safe'),
			array('b4_amt_paid','safe'),
			array('sign_dt, ctrt_end_dt, first_dt, equip_install_dt','safe'),
			array('status_dt','safe'),
            array('id','validateID'),
		)
		: array(
/*
			array('id, salesman, cont_info, first_tech, reason, remarks, remarks2, paid_type, nature_type, cust_type, 
				status, status_desc, company_id, product_id, backlink, fresh, paid_type, city, 
				b4_product_id, b4_service, b4_paid_type, docType, files, removeFileId, downloadFileId, need_install, no_of_attm','safe'),
*/
			array('id, office_id, tracking,technician_id, salesman_id, othersalesman_id, first_tech_id, technician, cont_info, first_tech, reason, remarks,othersalesman, remarks2, paid_type, nature_type, nature_type_two, cust_type, prepay_month,prepay_start,contract_no
				status, status_desc, company_id, product_id, backlink, fresh, paid_type, city, all_number,surplus,all_number_edit0,surplus_edit0,all_number_edit1,surplus_edit1,
				all_number_edit2,surplus_edit2,all_number_edit3,surplus_edit3,b4_product_id, b4_service, b4_paid_type,cust_type_name,pieces, need_install','safe'),
			array('files, removeFileId, docMasterId, no_of_attm, company_id','safe'),
			array('salesman_id,company_name,salesman,nature_type, service,all_number,surplus, status_dt','required'),
			array('ctrt_period','numerical','allowEmpty'=>true,'integerOnly'=>true),
			array('amt_paid, amt_install','numerical','allowEmpty'=>true),
			array('org_equip_qty, rtn_equip_qty','numerical','allowEmpty'=>true),
			array('b4_amt_paid','numerical','allowEmpty'=>true),
			array('sign_dt, ctrt_end_dt, first_dt, equip_install_dt','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('status_dt','date','allowEmpty'=>false,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
            array('id','validateID'),
            array('id','validateAutoFinish'),
            array('status_dt','validateVisitDt','on'=>array('new')),
		);
	}

    //驗證该服务是否已经参加销售提成计算
    public function validateID($attribute, $params) {
        $id=$this->getScenario()=="new"?0:$this->id;
        $notUpdate=array("status","status_dt","cust_type","cust_type_name",
            "paid_type","amt_install","all_number","salesman","salesman_id",
            "othersalesman","othersalesman_id","ctrt_period","first_dt",
            "b4_paid_type","b4_amt_paid","surplus","company_name","company_id");
        $row = Yii::app()->db->createCommand()
            ->select("id,".implode(",",$notUpdate))
            ->from("swo_service")
            ->where("(commission is not null or other_commission is not null) and id=:id",array(":id"=>$id))->queryRow();
        if($row){
            if($this->getScenario()=="delete"){
                $this->addError($attribute, "该服务已参加销售提成计算，无法删除");
            }else{
                foreach ($notUpdate as $item){
                    $this->$item = $row[$item];
                }
            }
        }
    }

    //驗證新增時是否有該服務
    public function validateAutoFinish($attribute, $params){
        $this->service_new_id = 0;
        if(in_array($this->getScenario(),array("renew","amend","suspend","terminate","resume"))||($this->getScenario()=="edit"&&$this->status!="N")){
            $this->cust_type_name = empty($this->cust_type_name)?0:$this->cust_type_name;
            $row = Yii::app()->db->createCommand()->select("id")->from("swo_service")
                ->where("status='N' and company_id=:company_id and cust_type=:cust_type and cust_type_name=:cust_type_name",array(
                    ":company_id"=>$this->company_id,
                    ":cust_type"=>$this->cust_type,
                    ":cust_type_name"=>$this->cust_type_name,
                ))->order("sign_dt desc")->queryRow();
            if($row){
                $this->service_new_id = $row["id"];
            }else{
                $this->service_new_id = -1;
            }
        }
        $this->salesman_id = empty($this->salesman_id)?"":$this->salesman_id;
        $this->othersalesman_id = empty($this->othersalesman_id)?0:$this->othersalesman_id;
        $this->technician_id = empty($this->technician_id)?0:$this->technician_id;
        $this->first_tech_id = empty($this->first_tech_id)?0:$this->first_tech_id;
    }

    public function validateVisitDt($attribute, $params) {
        $visit_dt = date("Y-m-d",strtotime($this->status_dt));
        $nowDate = date("Y-m-d");
        $firstDate = date("Y-m-01",strtotime($nowDate));
        $firstDate = date("Y-m-01",strtotime("$firstDate - 2 month"));
        if($visit_dt<$firstDate){
            $this->addError($attribute, "新增日期必须大于".$firstDate);
        }
    }

    public function dataCopy($index){
        $row = Yii::app()->db->createCommand()->select("a.*,b.contract_no")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no b","a.id=b.service_id")
            ->where("a.id=:id",array(":id"=>$index))
            ->queryRow();
        if($row){
            if(empty($row["contract_no"])){
                Dialog::message(Yii::t('dialog','Validation Message'), "合同编号为空，无法复制");
                return false;
            }else{
                $bool = Yii::app()->db->createCommand()->select("a.id,b.contract_no")
                    ->from("swo_service_ka a")
                    ->leftJoin("swo_service_ka_no b","a.id=b.service_id")
                    ->where("a.status_dt=:status_dt and a.status=:status and b.contract_no=:contract_no",array(
                        ":status"=>$row["status"],
                        ":status_dt"=>$row["status_dt"],
                        ":contract_no"=>$row["contract_no"],
                    ))->queryRow();
                if($bool){
                    Dialog::message(Yii::t('dialog','Validation Message'), "KA服务已存在，无法复制：".$bool["id"]);
                    return false;
                }
                $data = $row;
                unset($data["id"]);
                unset($data["contract_no"]);
                Yii::app()->db->createCommand()->insert("swo_service_ka",$data);
                $this->id = Yii::app()->db->getLastInsertID();
                Yii::app()->db->createCommand()->insert("swo_service_ka_no",array(
                    "contract_no"=>$row["contract_no"],
                    "status_dt"=>$row["status_dt"],
                    "status"=>$row["status"],
                    "service_id"=>$this->id,
                ));
                Yii::app()->db->createCommand()->insert("swo_service_history",array(
                    "update_html"=>"<span>复制</span><br><span>复制service_id：{$index}</span>",
                    "update_type"=>2,
                    "service_type"=>3,
                    "service_id"=>$this->id,
                    "lcu"=>Yii::app()->user->id
                ));
                return true;
            }
        }else{
            return false;
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql = "select a.*,f.code as com_code,f.name as com_name, docman$suffix.countdoc('SERVICE',a.id) as no_of_attm,b.contract_no from swo_service a
        left outer join swo_service_contract_no b on a.id=b.service_id 
        left outer join swo_company f on a.company_id=f.id 
        where a.id=$index and a.city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
//		print_r('<pre>');
//        print_r($rows);
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$this->id = $row['id'];
				$this->service_new_id = $row['service_new_id'];
				$this->tracking = $row['tracking'];
				$this->commission = $row['commission'];
				$this->other_commission = $row['other_commission'];
				$this->service_no = $row['service_no'];
				$this->customer_code = $row['com_code'];
				$this->customer_name = $row['com_name'];
				$this->company_id = empty($row['company_id'])?"":$row['company_id'];
				$this->company_name = empty($row['com_name'])?$row['company_name']:$row['com_code'].$row['com_name'];
				$this->nature_type = $row['nature_type'];
				$this->nature_type_two = $row['nature_type_two'];
				$this->cust_type = $row['cust_type'];
				$this->product_id = $row['product_id'];
				$this->service = $row['service'];
				$this->paid_type = $row['paid_type'];
				$this->amt_paid = $row['amt_paid'];
				$this->b4_product_id = $row['b4_product_id'];
				$this->b4_service = $row['b4_service'];
				$this->b4_paid_type = $row['b4_paid_type'];
				$this->b4_amt_paid = $row['b4_amt_paid'];
				$this->amt_install = $row['amt_install'];
				$salesman = General::getEmployeeCodeAndNameForID($row['salesman_id']);
				$othersalesman = General::getEmployeeCodeAndNameForID($row['othersalesman_id']);
				$this->salesman = empty($salesman)?$row["salesman"]:$salesman;
				$this->salesman_id = empty($row['salesman_id'])?"":$row['salesman_id'];
                $this->othersalesman = empty($othersalesman)?$row["othersalesman"]:$othersalesman;
                $this->othersalesman_id = $row['othersalesman_id'];
                $this->technician = $row['technician'];
                $this->technician_id = $row['technician_id'];
				$this->sign_dt = General::toDate($row['sign_dt']);
				$this->ctrt_end_dt = General::toDate($row['ctrt_end_dt']);
				$this->ctrt_period = $row['ctrt_period'];
				$this->cont_info = $row['cont_info'];
				$this->first_dt = General::toDate($row['first_dt']);
				$this->first_tech = $row['first_tech'];
				$this->first_tech_id = $row['first_tech_id'];
				$this->reason = $row['reason'];
				$this->status_dt = General::toDate($row['status_dt']);
				$this->status = $row['status'];
				$this->remarks = $row['remarks'];
				$this->remarks2 = $row['remarks2'];
				$this->equip_install_dt = General::toDate($row['equip_install_dt']);
				$this->org_equip_qty = $row['org_equip_qty'];
				$this->rtn_equip_qty = $row['rtn_equip_qty'];
				$this->need_install = $row['need_install']=="N"?"":$row['need_install'];
				$this->no_of_attm['service'] = $row['no_of_attm'];
                $this->city = $row['city'];
                $this->surplus = $row['surplus'];
                $this->all_number = $row['all_number'];
                $this->surplus_edit0 = $row['surplus_edit0'];
                $this->all_number_edit0 = $row['all_number_edit0'];
                $this->surplus_edit1 = $row['surplus_edit1'];
                $this->all_number_edit1 = $row['all_number_edit1'];
                $this->surplus_edit2 = $row['surplus_edit2'];
                $this->all_number_edit2 = $row['all_number_edit2'];
                $this->surplus_edit3 = $row['surplus_edit3'];
                $this->all_number_edit3 = $row['all_number_edit3'];
                //var_dump($row['cust_type_name']);
                $this->cust_type_name = $row['cust_type_name'];
                $this->pieces = $row['pieces'];
                $this->prepay_month = $row['prepay_month'];
                $this->prepay_start = $row['prepay_start'];
                $this->contract_no = $row['contract_no'];
                $this->send = $row['send'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->office_id = $row['office_id'];
//                print_r('<pre>');
//                print_r($this);exit();
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
			$this->historySave($connection);
			$this->saveService($connection);
			$this->updateServiceContract($connection);
			$this->updateDocman($connection,'SERVICE');
            $this->updateContractNoContract($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

    //获取服务操作记录
    public static function getServiceHistoryRows($bot_id){
        $rows = Yii::app()->db->createCommand()->select("update_html,lcu,lcd")
            ->from("swo_service_history")
            ->where("service_id=:service_id and service_type=1",array(":service_id"=>$bot_id))->order("lcd desc")->queryAll();
        return $rows;
    }
    //哪些字段修改后需要记录
    protected static function historyUpdateList($status){
        $list = array(
            'status_dt','contract_no','company_id','nature_type','nature_type_two',
            'cust_type','cust_type_name','product_id','paid_type','amt_paid','office_id'
        );
        switch ($status){
            case "N"://新增
                $expr = array('equip_install_dt','first_tech_id','first_dt','surplus','cont_info');
                $list=array_merge($list,$expr);
                break;
            case "C"://续约
                $expr = array('equip_install_dt','first_tech_id','first_dt','cont_info');
                $list=array_merge($list,$expr);
                break;
            case "A"://更改
                $expr = array('first_dt','b4_service','b4_product_id','b4_paid_type','b4_amt_paid','surplus');
                $list=array_merge($list,$expr);
                break;
            case "S"://暂停
                $expr = array('surplus','org_equip_qty','rtn_equip_qty');
                $list=array_merge($list,$expr);
                break;
            case "R"://恢复
                break;
            case "T"://终止
                $expr = array('surplus','org_equip_qty','rtn_equip_qty','surplus_edit0','all_number_edit0','surplus_edit1','all_number_edit1','surplus_edit2',
                    'all_number_edit2','surplus_edit3','all_number_edit3');
                $list=array_merge($list,$expr);
                break;
        }
        $expr = array(
            'salesman_id','othersalesman_id','technician_id',
            'sign_dt','ctrt_period','ctrt_end_dt','need_install','amt_install',
            'all_number','pieces','prepay_month','prepay_start'
        );
        $list=array_merge($list,$expr);
        return $list;
    }

    //哪些字段修改后需要记录
    protected static function getNameForValue($type,$value){
        switch ($type){
            case "office_id":
                $value = GetNameToId::getOfficeNameForID($value);
                break;
            case "first_tech_id":
                $value = GetNameToId::getEmployeeNameForStr($value);
                break;
            case "othersalesman_id":
            case "technician_id":
            case "salesman_id":
                $value = GetNameToId::getEmployeeNameForId($value);
                break;
            case "company_id":
                $value = GetNameToId::getCompanyNameForId($value);
                break;
            case "nature_type":
                $value = GetNameToId::getNatureOneNameForId($value);
                break;
            case "nature_type_two":
                $value = GetNameToId::getNatureTwoNameForId($value);
                break;
            case "cust_type":
                $value = GetNameToId::getCustOneNameForId($value);
                break;
            case "cust_type_name":
                $value = GetNameToId::getCustTwoNameForId($value);
                break;
            case "product_id":
            case "b4_product_id":
                $value = GetNameToId::getProductNameForId($value);
                break;
            case "paid_type":
            case "b4_paid_type":
                $value = GetNameToId::getPaidTypeForId($value);
                break;
            case "need_install":
                $value = GetNameToId::getNeedInstallForId($value);
                break;
        }
        return $value;
    }

    protected function delHistorySave(){
        $model = new ServiceForm();
        $model->retrieveData($this->id);
        $keyArr = self::historyUpdateList($model->status);
        $delText=array();
        $delText[]="id：".$this->id;
        $delText[]="服务状态：".General::getStatusDesc($model->status);
        foreach ($keyArr as $key){
            $delText[]=$this->getAttributeLabel($key)."：".self::getNameForValue($key,$model->$key);
        }
        $delText= implode("<br/>",$delText);
        $systemLogModel = new SystemLogForm();
        $systemLogModel->log_date=date("Y/m/d H:i:s");
        $systemLogModel->log_user=Yii::app()->user->id;
        $systemLogModel->log_type=get_class($this);
        $systemLogModel->log_type_name="客户服务";
        $systemLogModel->option_str="删除";
        $systemLogModel->option_text=$delText;
        $systemLogModel->insertSystemLog("D");
    }

    //保存历史记录
    protected function historySave(&$connection){
        $uid = Yii::app()->user->id;
        $list=array("service_id"=>$this->id,"lcu"=>$uid,"service_type"=>1,"update_type"=>1,"update_html"=>array());
        switch ($this->getScenario()){
            case "delete":
                //$connection->createCommand()->delete("swo_service_history", "service_id=:id", array(":id" => $this->id));
                $this->delHistorySave();
                break;
            case "edit":
                $model = new ServiceForm();
                $model->retrieveData($this->id);
                $keyArr = self::historyUpdateList($model->status);
                foreach ($keyArr as $key){
                    if($model->$key!=$this->$key){
                        $list["update_html"][]="<span>".$this->getAttributeLabel($key)."：".self::getNameForValue($key,$model->$key)." 修改为 ".self::getNameForValue($key,$this->$key)."</span>";
                    }
                }
                if(!empty($list["update_html"])){
                    $list["update_html"] = implode("<br/>",$list["update_html"]);
                    $connection->createCommand()->insert("swo_service_history", $list);
                }
                break;
        }
    }

	protected function updateServiceContract(&$connection) {
		if ($this->scenario=='delete') {
			$sql = "delete from swo_service_contract_no where service_id=".$this->id;
			$connection->createCommand($sql)->execute();
		}
	}

    protected function updateContractNoContract(&$connection) {
        if (empty($this->contract_no)&&$this->scenario=='edit') {
            $sql = "delete from swo_service_contract_no where service_id=".$this->id;
            $connection->createCommand($sql)->execute();
        }elseif(!empty($this->contract_no)&&$this->scenario!='delete'){
            $no_id = Yii::app()->db->createCommand()->select("id")
                ->from("swo_service_contract_no")
                ->where("service_id=:id",array(":id"=>$this->id))
                ->queryScalar();
            if($no_id){
                Yii::app()->db->createCommand()->update("swo_service_contract_no",array(
                    "contract_no"=>$this->contract_no,
                    "status_dt"=>$this->status_dt,
                    "status"=>$this->status,
                ),"id=:id",array(":id"=>$no_id));
            }else{
                Yii::app()->db->createCommand()->insert("swo_service_contract_no",array(
                    "service_id"=>$this->id,
                    "contract_no"=>$this->contract_no,
                    "status_dt"=>$this->status_dt,
                    "status"=>$this->status,
                ));
            }
        }
    }

	protected function saveService(&$connection)
	{
		$sql = array();
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_service where id = :id";
				$this->execSql($connection,$sql);
				break;
			case 'renew':
			case 'new':
			case 'amend':
			case 'suspend':
			case 'terminate':
			case 'resume':
				$sql = "insert into swo_service(
							service_new_id,company_id, company_name, product_id, service, nature_type, nature_type_two, cust_type, 
							paid_type, amt_paid, amt_install, need_install, salesman_id, salesman,othersalesman_id,othersalesman,technician_id,technician, sign_dt, b4_product_id,
							b4_service, b4_paid_type, b4_amt_paid, 
							ctrt_period, cont_info, first_dt, first_tech_id, first_tech, reason,tracking,
							status, status_dt, remarks, remarks2, ctrt_end_dt,
							equip_install_dt, org_equip_qty, rtn_equip_qty, cust_type_name,pieces,office_id,
							city, luu, lcu,all_number,surplus,all_number_edit0,surplus_edit0,all_number_edit1,surplus_edit1,all_number_edit2,surplus_edit2,all_number_edit3,surplus_edit3,prepay_month,prepay_start
						) values (
							:service_new_id,:company_id, :company_name, :product_id, :service, :nature_type, :two_nature_type, :cust_type, 
							:paid_type, :amt_paid, :amt_install, :need_install, :salesman_id, :salesman,:othersalesman_id,:othersalesman,:technician_id,:technician, :sign_dt, :b4_product_id,
							:b4_service, :b4_paid_type, :b4_amt_paid, 
							:ctrt_period, :cont_info, :first_dt, :first_tech_id, :first_tech, :reason,:tracking,
							:status, :status_dt, :remarks, :remarks2, :ctrt_end_dt,
							:equip_install_dt, :org_equip_qty, :rtn_equip_qty, :cust_type_name,:pieces,:office_id,
							:city, :luu, :lcu,:all_number,:surplus,:all_number_edit0,:surplus_edit0,:all_number_edit1,:surplus_edit1,:all_number_edit2,:surplus_edit2,:all_number_edit3,:surplus_edit3,:prepay_month,:prepay_start
						)";
				$this->execSql($connection,$sql);
				$this->id = Yii::app()->db->getLastInsertID();
                Yii::app()->db->createCommand()->update("swo_service",array(
                    "service_no"=>$this->status.(100000+$this->id)
                ),"id=".$this->id);
				break;
			case 'edit':
				$sql = "update swo_service set                      
							company_id = :company_id, 
							company_name = :company_name, 
							cust_type_name=:cust_type_name,
							cust_type = :cust_type,
							product_id = :product_id, 
							nature_type = :nature_type,
							nature_type_two = :two_nature_type,
							pieces=:pieces,
							service = :service, 
							paid_type = :paid_type, 
							amt_paid = :amt_paid, 
							b4_product_id = :b4_product_id, 
							b4_service = :b4_service, 
							b4_paid_type = :b4_paid_type, 
							b4_amt_paid = :b4_amt_paid, 
							amt_install = :amt_install, 
							need_install = :need_install,
							salesman_id = :salesman_id, 
							salesman = :salesman, 
							othersalesman_id=:othersalesman_id,
							othersalesman=:othersalesman,
							technician_id = :technician_id,
							technician = :technician,
							sign_dt = :sign_dt,
							ctrt_end_dt = :ctrt_end_dt,
							ctrt_period = :ctrt_period, 
							cont_info = :cont_info, 
							first_dt = :first_dt, 
							first_tech_id = :first_tech_id, 
							first_tech = :first_tech, 
							reason = :reason,
							tracking = :tracking,
							remarks = :remarks,
							remarks2 = :remarks2,
							status = :status, 
							status_dt = :status_dt,
							equip_install_dt = :equip_install_dt,
							org_equip_qty = :org_equip_qty,
							rtn_equip_qty = :rtn_equip_qty,
							all_number = :all_number, 
                            surplus = :surplus, 
                            all_number_edit0 = :all_number_edit0, 
                            surplus_edit0 = :surplus_edit0, 
                            all_number_edit1 = :all_number_edit1, 
                            surplus_edit1 = :surplus_edit1, 
                            all_number_edit2 = :all_number_edit2, 
                            surplus_edit2 = :surplus_edit2, 
                            all_number_edit3 = :all_number_edit3, 
                            surplus_edit3 = :surplus_edit3, 
                            prepay_month = :prepay_month, 
                            prepay_start = :prepay_start,                  
                            office_id = :office_id,                  
							luu = :luu 
						where id = :id 
						";
				$this->execSql($connection,$sql);
				break;
		}

		return true;
	}
	
	protected function execSql(&$connection, $sql) {
		$city = $this->city; 	//Yii::app()->user->city();
		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':service_new_id')!==false)
			$command->bindParam(':service_new_id',$this->service_new_id,PDO::PARAM_INT);
		if (strpos($sql,':company_id')!==false) {
			$cid = General::toMyNumber($this->company_id);
			$command->bindParam(':company_id',$cid,PDO::PARAM_INT);
		}
		if (strpos($sql,':company_name')!==false)
			$command->bindParam(':company_name',$this->company_name,PDO::PARAM_STR);
		if (strpos($sql,':product_id')!==false) {
			$pid = General::toMyNumber($this->product_id);
			$command->bindParam(':product_id',$pid,PDO::PARAM_INT);
		}
		if (strpos($sql,':service')!==false)
			$command->bindParam(':service',$this->service,PDO::PARAM_STR);
		if (strpos($sql,':nature_type')!==false)
			$command->bindParam(':nature_type',$this->nature_type,PDO::PARAM_INT);
		if (strpos($sql,':office_id')!==false){
            $this->office_id = empty($this->office_id)?null:$this->office_id;
            $command->bindParam(':office_id',$this->office_id,PDO::PARAM_INT);
        }
		if (strpos($sql,':two_nature_type')!==false){
            $this->nature_type_two = empty($this->nature_type_two)?null:$this->nature_type_two;
            $command->bindParam(':two_nature_type',$this->nature_type_two,PDO::PARAM_INT);
        }
        if (strpos($sql,':cust_type_name')!==false) {
            $cust_type_name= (empty($this->cust_type_name) ? 0 : $this->cust_type_name);
            $command->bindParam(':cust_type_name',$cust_type_name,PDO::PARAM_INT);
        }
		if (strpos($sql,':cust_type')!==false) {
			$ctid = General::toMyNumber($this->cust_type);
			$command->bindParam(':cust_type',$ctid,PDO::PARAM_INT);
		}
        if (strpos($sql,':pieces')!==false) {
			$pieces = !is_numeric($this->pieces) ? null : $this->pieces;
            $command->bindParam(':pieces',$pieces,PDO::PARAM_INT);
        }
		if (strpos($sql,':paid_type')!==false)
			$command->bindParam(':paid_type',$this->paid_type,PDO::PARAM_STR);
		if (strpos($sql,':amt_paid')!==false) {
			$apaid = General::toMyNumber($this->amt_paid);
			$command->bindParam(':amt_paid',$apaid,PDO::PARAM_STR);
		}
		if (strpos($sql,':amt_install')!==false) {
			$ainstall = General::toMyNumber($this->amt_install);
			$command->bindParam(':amt_install',$ainstall,PDO::PARAM_STR);
		}
		if (strpos($sql,':need_install')!==false)
			$command->bindParam(':need_install',$this->need_install,PDO::PARAM_STR);

		if (strpos($sql,':salesman_id')!==false)
			$command->bindParam(':salesman_id',$this->salesman_id,PDO::PARAM_INT);
		if (strpos($sql,':salesman')!==false)
			$command->bindParam(':salesman',$this->salesman,PDO::PARAM_STR);

        if (strpos($sql,':othersalesman_id')!==false)
            $command->bindParam(':othersalesman_id',$this->othersalesman_id,PDO::PARAM_INT);
        if (strpos($sql,':othersalesman')!==false)
            $command->bindParam(':othersalesman',$this->othersalesman,PDO::PARAM_STR);

        if (strpos($sql,':technician_id')!==false)
            $command->bindParam(':technician_id',$this->technician_id,PDO::PARAM_INT);
        if (strpos($sql,':technician')!==false)
            $command->bindParam(':technician',$this->technician,PDO::PARAM_STR);

		if (strpos($sql,':sign_dt')!==false) {
			$sdate = General::toMyDate($this->sign_dt);
			$command->bindParam(':sign_dt',$sdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':ctrt_end_dt')!==false) {
			$edate = General::toMyDate($this->ctrt_end_dt);
			$command->bindParam(':ctrt_end_dt',$edate,PDO::PARAM_STR);
		}
		if (strpos($sql,':ctrt_period')!==false) {
			$cp = General::toMyNumber($this->ctrt_period);
			$command->bindParam(':ctrt_period',$cp,PDO::PARAM_INT);
		}

		if (strpos($sql,':cont_info')!==false)
			$command->bindParam(':cont_info',$this->cont_info,PDO::PARAM_STR);
		if (strpos($sql,':first_dt')!==false) {
			$fdate = General::toMyDate($this->first_dt);
			$command->bindParam(':first_dt',$fdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':first_tech_id')!==false)
			$command->bindParam(':first_tech_id',$this->first_tech_id,PDO::PARAM_INT);
		if (strpos($sql,':first_tech')!==false)
			$command->bindParam(':first_tech',$this->first_tech,PDO::PARAM_STR);
		if (strpos($sql,':status_dt')!==false) {
			$stsdate = General::toMyDate($this->status_dt);
			$command->bindParam(':status_dt',$stsdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':reason')!==false)
			$command->bindParam(':reason',$this->reason,PDO::PARAM_STR);
		if (strpos($sql,':tracking')!==false)
			$command->bindParam(':tracking',$this->tracking,PDO::PARAM_STR);
		if (strpos($sql,':status')!==false)
			$command->bindParam(':status',$this->status,PDO::PARAM_STR);
		if (strpos($sql,':remarks')!==false)
			$command->bindParam(':remarks',$this->remarks,PDO::PARAM_STR);
		if (strpos($sql,':remarks2')!==false)
			$command->bindParam(':remarks2',$this->remarks2,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		if (strpos($sql,':b4_product_id')!==false) {
			$pid = General::toMyNumber($this->b4_product_id);
			$command->bindParam(':b4_product_id',$pid,PDO::PARAM_INT);
		}
		if (strpos($sql,':b4_service')!==false)
			$command->bindParam(':b4_service',$this->b4_service,PDO::PARAM_STR);
		if (strpos($sql,':b4_paid_type')!==false)
			$command->bindParam(':b4_paid_type',$this->b4_paid_type,PDO::PARAM_STR);
		if (strpos($sql,':b4_amt_paid')!==false) {
			$b4apaid = General::toMyNumber($this->b4_amt_paid);
			$command->bindParam(':b4_amt_paid',$b4apaid,PDO::PARAM_STR);
		}

		if (strpos($sql,':equip_install_dt')!==false) {
			$eidate = General::toMyDate($this->equip_install_dt);
			$command->bindParam(':equip_install_dt',$eidate,PDO::PARAM_STR);
		}
		if (strpos($sql,':org_equip_qty')!==false) {
			$oeq = General::toMyNumber($this->org_equip_qty, true);
			$command->bindParam(':org_equip_qty',$oeq,PDO::PARAM_INT);
		}
		if (strpos($sql,':rtn_equip_qty')!==false) {
			$req = General::toMyNumber($this->rtn_equip_qty, true);
			$command->bindParam(':rtn_equip_qty',$req,PDO::PARAM_INT);
		}
        if (strpos($sql,':all_number')!==false) {
            $command->bindParam(':all_number',$this->all_number,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus')!==false) {
            $command->bindParam(':surplus',$this->surplus,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit0')!==false) {
            $command->bindParam(':all_number_edit0',$this->all_number_edit0,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit0')!==false) {
            $command->bindParam(':surplus_edit0',$this->surplus_edit0,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit1')!==false) {
            $command->bindParam(':all_number_edit1',$this->all_number_edit1,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit1')!==false) {
            $command->bindParam(':surplus_edit1',$this->surplus_edit1,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit2')!==false) {
            $command->bindParam(':all_number_edit2',$this->all_number_edit2,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit2')!==false) {
            $command->bindParam(':surplus_edit2',$this->surplus_edit2,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit3')!==false) {
            $command->bindParam(':all_number_edit3',$this->all_number_edit3,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit3')!==false) {
            $command->bindParam(':surplus_edit3',$this->surplus_edit3,PDO::PARAM_INT);
        }
        if(empty($this->prepay_month)){
            $this->prepay_month=0;
        }
        if (strpos($sql,':prepay_month')!==false) {
            $command->bindParam(':prepay_month',$this->prepay_month,PDO::PARAM_INT);
        }
        if(empty($this->prepay_start)){
            $this->prepay_start=0;
        }
         if (strpos($sql,':prepay_start')!==false) {
             $command->bindParam(':prepay_start',$this->prepay_start,PDO::PARAM_INT);
         }
//        print_r('<pre>');
//        print_r($this->prepay_month);exit();
		$command->execute();
	}
	
//	public function saveFiles() {
//		$docman = new DocMan();
//		foreach ($this->files as $file) {
//			$docman->save($data);
//		}
//	}
	
	protected function updateDocman(&$connection, $doctype) {
		if ($this->scenario=='new'||$this->scenario=='renew'||$this->scenario=='amend'||$this->scenario=='suspend'||$this->scenario=='terminate'||$this->scenario=='resume') {
			$docidx = strtolower($doctype);
			if ($this->docMasterId[$docidx] > 0) {
				$docman = new DocMan($doctype,$this->id,get_class($this));
				$docman->masterId = $this->docMasterId[$docidx];
				$docman->updateDocId($connection, $this->docMasterId[$docidx]);
			}
		}
	}

    public static function getCustTypeList($a=1) {
        $city = Yii::app()->user->city();
        $rtn = array(0=>Yii::t('misc','-- None --'));
        $sql = "select id, cust_type_name from swo_customer_type_twoname where  cust_type_id=$a order by cust_type_name";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach($rows as $row) {
                $rtn[$row['id']] = $row['cust_type_name'];
            }
        }
        return $rtn;
    }

	public function getStatusDesc() {
		return General::getStatusDesc($this->status);
	}

	public function getCompanyName() {
		if ($this->company_id!=0) {
			$tcompany = Customer::model()->find('id=?',array($this->company_id));
			if (!empty($tcompany)) $this->company_name=$tcompany->name; 
		}
	}
	//发送邮件
    public function sendemail($reason_id,$year,$month,$company,$service_id){
        $suffix = Yii::app()->params['envSuffix'];
        //发送邮箱
        $sql1 = "SELECT email FROM swo_company WHERE  concat(`code`,`name`) = '".$company."' order by id desc limit 1";
        $rs = Yii::app()->db->createCommand($sql1)->queryRow();
        $email = $rs['email'];
        if (empty($email)){
            return "<script language=javascript>alert('客户邮箱不存在');history.back();</script>";
        }
        //原因内容
        $sql2 = "SELECT * FROM swo_service_end_reasons WHERE  id=".$reason_id;
        $reason = Yii::app()->db->createCommand($sql2)->queryRow();
        $content = $reason['content'];
        $reason = $reason['reason'];
//        $this->webroot = Yii::app()->params['webroot'];
        $subject = "史伟莎服务暂停或终止邮件通知".date('Y-m-d');
        $message = <<<EOF
        <p>尊敬的客户: </p>
<p style="text-indent:2em;">
贵店由于{$reason}，服务将从{$year}年{$month}月开始暂停，请知悉！</p>
<p style="text-indent:2em;">{$content}</p>
EOF;
//        	<tr height="36">
//			<td colspan="6" height="36" style="height:36px;width:663px;" x:num="44275"><span style="font-size:14px;">{$subject}</span></td>
//		</tr>
        $from_addr = "no-reply@lbsgroup.com.cn";
        $to_addr = "[\"" .$email."\"]";
        $description = "</<br>" .date('Y-m-d');
        $lcu = "admin";

        $aaa = Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
            'request_dt' => date('Y-m-d H:i:s'),
            'from_addr' => $from_addr,
            'to_addr' => $to_addr,
            'subject' => $subject,//郵件主題
            'description' => '',//郵件副題
            'message' => $message,//郵件內容（html）
            'status' => "P",
            'lcu' => $lcu,
            'lcd' => date('Y-m-d H:i:s'),
        ));

        //改变服务邮箱发送状态
        $sql_s="update swo_service set send ='Y'  where id='$service_id'";
        $record = Yii::app()->db->createCommand($sql_s)->execute();
        return "<script language=javascript>alert('发送成功');history.back();</script>";

    }

    public function getReadonly(){
        return $this->scenario=='view'||$this->commission!==null||$this->other_commission!==null;
    }
}
