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
	public $company_id;
	public $company_name;
	public $nature_type;
	public $cust_type;
	public $product_id;
	public $service;
	public $paid_type;
	public $amt_paid;
	public $amt_install;
	public $need_install;
	public $salesman;
    public $technician;
	public $sign_dt;
	public $ctrt_end_dt;
	public $ctrt_period=12;
	public $cont_info;
	public $first_dt;
	public $first_tech;
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
    public $all_number;
    public $surplus_edit0;
    public $all_number_edit0;
    public $surplus_edit1;
    public $all_number_edit1;
    public $surplus_edit2;
    public $all_number_edit2;
    public $surplus_edit3;
    public $all_number_edit3;
    public $surplus_edit4;
    public $all_number_edit4;
	public $b4_product_id;
	public $b4_service;
	public $b4_paid_type;
	public $b4_amt_paid;
	public $othersalesman;
	public $status_desc;
	public $backlink;
	
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
	
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('service','Record ID'),
			'company_name'=>Yii::t('service','Customer'),
			'service'=>Yii::t('service','Service'),
			'nature_type'=>Yii::t('service','Nature'),
			'cust_type'=>Yii::t('service','Customer Type'),
			'amt_paid'=>Yii::t('service','Paid Amt'),
			'amt_install'=>Yii::t('service','Install Amt'),
			'need_install'=>Yii::t('service','Installation'),
			'salesman'=>Yii::t('service','Salesman'),
            'othersalesman'=>Yii::t('service','othersalesman'),
            'technician'=>Yii::t('service','Technician'),
			'sign_dt'=>Yii::t('service','Sign Date'),
			'ctrt_end_dt'=>Yii::t('service','Contract End Date'),
			'ctrt_period'=>Yii::t('service','Contract Period'),
			'cont_info'=>Yii::t('service','Contact'),
			'first_dt'=>Yii::t('service','First Service Date'),
			'first_tech'=>Yii::t('service','First Technician'),
			'reason'=>Yii::t('service','Reason'),
			'status'=>Yii::t('service','Status'),
			'status_dt'=>Yii::t('service','Status Date'),
			'remarks'=>Yii::t('service','Remarks'),
			'remarks2'=>Yii::t('service','Remarks 2'),
			'b4_service'=>Yii::t('service','Service (Before)'),
			'b4_amt_paid'=>Yii::t('service','Paid Amt (Before)'),
			'af_service'=>Yii::t('service','Service (After)'),
			'af_amt_paid'=>Yii::t('service','Paid Amt (After)'),
			'equip_install_dt'=>Yii::t('service','Install Date'),
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
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
/*
			array('id, salesman, cont_info, first_tech, reason, remarks, remarks2, paid_type, nature_type, cust_type, 
				status, status_desc, company_id, product_id, backlink, fresh, paid_type, city, 
				b4_product_id, b4_service, b4_paid_type, docType, files, removeFileId, downloadFileId, need_install, no_of_attm','safe'),
*/
			array('id, technician, cont_info, first_tech, reason, remarks,othersalesman, remarks2, paid_type, nature_type, cust_type, 
				status, status_desc, company_id, product_id, backlink, fresh, paid_type, city, all_number,surplus,all_number_edit0,surplus_edit0,all_number_edit1,surplus_edit1,
				all_number_edit2,surplus_edit2,all_number_edit3,surplus_edit3,b4_product_id, b4_service, b4_paid_type, need_install','safe'),
			array('files, removeFileId, docMasterId, no_of_attm','safe'),
			array('company_name,salesman, service,all_number,surplus, status_dt','required'),
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
		);
	}

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql = "select *, docman$suffix.countdoc('SERVICE',id) as no_of_attm from swo_service where id=$index and city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$this->id = $row['id'];
				$this->company_id = $row['company_id'];
				$this->company_name = $row['company_name'];
				$this->nature_type = $row['nature_type'];
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
				$this->salesman = $row['salesman'];
                $this->othersalesman = $row['othersalesman'];
                $this->technician = $row['technician'];
				$this->sign_dt = General::toDate($row['sign_dt']);
				$this->ctrt_end_dt = General::toDate($row['ctrt_end_dt']);
				$this->ctrt_period = $row['ctrt_period'];
				$this->cont_info = $row['cont_info'];
				$this->first_dt = General::toDate($row['first_dt']);
				$this->first_tech = $row['first_tech'];
				$this->reason = $row['reason'];
				$this->status_dt = General::toDate($row['status_dt']);
				$this->status = $row['status'];
				$this->remarks = $row['remarks'];
				$this->remarks2 = $row['remarks2'];
				$this->equip_install_dt = General::toDate($row['equip_install_dt']);
				$this->org_equip_qty = $row['org_equip_qty'];
				$this->rtn_equip_qty = $row['rtn_equip_qty'];
				$this->need_install = $row['need_install'];
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
			$this->saveService($connection);
			$this->updateDocman($connection,'SERVICE');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveService(&$connection)
	{
		$sql = array();
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_service where id = :id and city = :city";
				$this->execSql($connection,$sql);
				break;
			case 'renew':
			case 'new':
			case 'amend':
			case 'suspend':
			case 'terminate':
			case 'resume':
				$sql = "insert into swo_service(
							company_id, company_name, product_id, service, nature_type, cust_type, 
							paid_type, amt_paid, amt_install, need_install, salesman,othersalesman,technician, sign_dt, b4_product_id,
							b4_service, b4_paid_type, b4_amt_paid, 
							ctrt_period, cont_info, first_dt, first_tech, reason,
							status, status_dt, remarks, remarks2, ctrt_end_dt,
							equip_install_dt, org_equip_qty, rtn_equip_qty, 
							city, luu, lcu,all_number,surplus,all_number_edit0,surplus_edit0,all_number_edit1,surplus_edit1,all_number_edit2,surplus_edit2,all_number_edit3,surplus_edit3
						) values (
							:company_id, :company_name, :product_id, :service, :nature_type, :cust_type, 
							:paid_type, :amt_paid, :amt_install, :need_install, :salesman,:othersalesman,:technician, :sign_dt, :b4_product_id,
							:b4_service, :b4_paid_type, :b4_amt_paid, 
							:ctrt_period, :cont_info, :first_dt, :first_tech, :reason,
							:status, :status_dt, :remarks, :remarks2, :ctrt_end_dt,
							:equip_install_dt, :org_equip_qty, :rtn_equip_qty, 
							:city, :luu, :lcu,:all_number,:surplus,:all_number_edit0,:surplus_edit0,:all_number_edit1,:surplus_edit1,:all_number_edit2,:surplus_edit2,:all_number_edit3,:surplus_edit3
						)";
				$this->execSql($connection,$sql);
				$this->id = Yii::app()->db->getLastInsertID();
				break;
			case 'edit':
				$sql = "update swo_service set                      
							company_id = :company_id, 
							company_name = :company_name, 
							cust_type = :cust_type,
							product_id = :product_id, 
							nature_type = :nature_type,
							service = :service, 
							paid_type = :paid_type, 
							amt_paid = :amt_paid, 
							b4_product_id = :b4_product_id, 
							b4_service = :b4_service, 
							b4_paid_type = :b4_paid_type, 
							b4_amt_paid = :b4_amt_paid, 
							amt_install = :amt_install, 
							need_install = :need_install,
							salesman = :salesman, 
							othersalesman=:othersalesman,
							technician = :technician,
							sign_dt = :sign_dt,
							ctrt_end_dt = :ctrt_end_dt,
							ctrt_period = :ctrt_period, 
							cont_info = :cont_info, 
							first_dt = :first_dt, 
							first_tech = :first_tech, 
							reason = :reason,
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
							luu = :luu 
						where id = :id and city = :city
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
		if (strpos($sql,':cust_type')!==false) {
			$ctid = General::toMyNumber($this->cust_type);
			$command->bindParam(':cust_type',$ctid,PDO::PARAM_INT);
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

		if (strpos($sql,':salesman')!==false)
			$command->bindParam(':salesman',$this->salesman,PDO::PARAM_STR);

        if (strpos($sql,':othersalesman')!==false)
            $command->bindParam(':othersalesman',$this->othersalesman,PDO::PARAM_STR);

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
		if (strpos($sql,':first_tech')!==false)
			$command->bindParam(':first_tech',$this->first_tech,PDO::PARAM_INT);
		if (strpos($sql,':status_dt')!==false) {
			$stsdate = General::toMyDate($this->status_dt);
			$command->bindParam(':status_dt',$stsdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':reason')!==false)
			$command->bindParam(':reason',$this->reason,PDO::PARAM_STR);
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
			$oeq = General::toMyNumber($this->org_equip_qty);
			$command->bindParam(':org_equip_qty',$oeq,PDO::PARAM_INT);
		}
		if (strpos($sql,':rtn_equip_qty')!==false) {
			$req = General::toMyNumber($this->rtn_equip_qty);
			$command->bindParam(':rtn_equip_qty',$req,PDO::PARAM_INT);
		}
        if (strpos($sql,':all_number')!==false) {
            $all_number = General::toMyNumber($this->all_number);
            $command->bindParam(':all_number',$all_number,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus')!==false) {
            $surplus = General::toMyNumber($this->surplus);
            $command->bindParam(':surplus',$surplus,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit0')!==false) {
            $all_number_edit0 = General::toMyNumber($this->all_number_edit0);
            $command->bindParam(':all_number_edit0',$all_number_edit0,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit0')!==false) {
            $surplus_edit0 = General::toMyNumber($this->surplus_edit0);
            $command->bindParam(':surplus_edit0',$surplus_edit0,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit1')!==false) {
            $all_number_edit1 = General::toMyNumber($this->all_number_edit1);
            $command->bindParam(':all_number_edit1',$all_number_edit1,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit1')!==false) {
            $surplus_edit1 = General::toMyNumber($this->surplus_edit1);
            $command->bindParam(':surplus_edit1',$surplus_edit1,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit2')!==false) {
            $all_number_edit2 = General::toMyNumber($this->all_number_edit2);
            $command->bindParam(':all_number_edit2',$all_number_edit2,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit2')!==false) {
            $surplus_edit2 = General::toMyNumber($this->surplus_edit2);
            $command->bindParam(':surplus_edit2',$surplus_edit2,PDO::PARAM_INT);
        }
        if (strpos($sql,':all_number_edit3')!==false) {
            $all_number_edit3 = General::toMyNumber($this->all_number_edit3);
            $command->bindParam(':all_number_edit3',$all_number_edit3,PDO::PARAM_INT);
        }
        if (strpos($sql,':surplus_edit3')!==false) {
            $surplus_edit3 = General::toMyNumber($this->surplus_edit3);
            $command->bindParam(':surplus_edit3',$surplus_edit3,PDO::PARAM_INT);
        }
//        print_r('<pre>');
//        print_r($this->all_number);exit();
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

	public function getStatusDesc() {
		return General::getStatusDesc($this->status);
	}

	public function getCompanyName() {
		if ($this->company_id!=0) {
			$tcompany = Customer::model()->find('id=?',array($this->company_id));
			if (!empty($tcompany)) $this->company_name=$tcompany->name; 
		}
	}
}
