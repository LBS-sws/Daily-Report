<?php

class SupplierForm extends CListPageModel
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
	public $bank;
	public $acct_no;
	public $tax_reg_no;
//  新加的
    public $req_dt;
    public $req_user;
    public $trans_type_code;
    public $payee_type = 'C';
    public $payee_id;
    public $payee_name;
    public $item_desc;
    public $item_name;
    public $amount;
    public $status;
    public $status_desc;
    public $wfstatus;
    public $wfstatusdesc;
    public $city;
    public $no_of_attm = array(
        'payreq'=>0,
        'tax'=>0
    );
    private $dyn_fields = array(
        'acct_id',
        'ref_no',
        'acct_code',
        'reason',
        'item_code',
        'int_fee',
    );
//
    public $acct_id;
    public $ref_no;
    public $acct_code;
    public $acct_name;
    public $acct_code_desc;
    public $reason;
    public $item_code;
    public $pitem_desc;
    public $int_fee;

	public $service = array();
	
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('supplier','Record ID'),
			'code'=>Yii::t('supplier','Code'),
			'name'=>Yii::t('supplier','Name'),
			'full_name'=>Yii::t('supplier','Full Name'),
			'cont_name'=>Yii::t('supplier','Contact Name'),
			'cont_phone'=>Yii::t('supplier','Contact Phone'),
			'address'=>Yii::t('supplier','Address'),
			'bank'=>Yii::t('supplier','Bank'),
			'acct_no'=>Yii::t('supplier','Account No'),
			'tax_reg_no'=>Yii::t('code','Taxpayer No.'),

            'req_dt'=>Yii::t('supplier','req_dt'),
            'ref_no'=>Yii::t('supplier','ref_no'),
            'trans_type_desc'=>Yii::t('supplier','trans_type_desc'),
            'bank'=>Yii::t('supplier','bank'),
            'amount'=>Yii::t('supplier','amount'),
            'item_desc'=>Yii::t('supplier','item_desc'),
		);
	}
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
//		return array(
//			array('id, full_name, tax_reg_no, cont_name, cont_phone, address, bank, acct_no','safe'),
//			array('name, code','required'),
///*
//			array('code','unique','allowEmpty'=>true,
//					'attributeName'=>'code',
//					'caseSensitive'=>false,
//					'className'=>'Supplier',
//				),
//*/
//		);
        $a=parent::rules();
        $b=array(
            array('id, full_name, tax_reg_no, cont_name, cont_phone, address, bank, acct_no','safe'),
            array('name, code','required'),
//			array('code','validateCode'),
        );
        return array_merge($a,$b);
	}

	public function validateCode($attribute, $params) {
		$code = $this->$attribute;
		$city = Yii::app()->user->city();
		if (!empty($code)) {
			switch ($this->scenario) {
				case 'new':
					if (Supplier::model()->exists('code=? and city=?',array($code,$city))) {
						$this->addError($attribute, Yii::t('supplier','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
				case 'edit':
					if (Supplier::model()->exists('code=? and city=? and id<>?',array($code,$city,$this->id))) {
						$this->addError($attribute, Yii::t('supplier','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
			}
		}
	}

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $user = Yii::app()->user->id;
		$city = Yii::app()->user->city_allow();
		$sql = "select * from swo_supplier where id=".$index." and city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->code = $row['code'];
				$this->name = $row['name'];
				$this->full_name = $row['full_name'];
				$this->cont_name = $row['cont_name'];
				$this->cont_phone = $row['cont_phone'];
				$this->address = $row['address'];
				$this->bank = $row['bank'];
				$this->acct_no = $row['acct_no'];
				$this->tax_reg_no = $row['tax_reg_no'];
                $this->tax_reg_no = $row['tax_reg_no'];
				break;
			}
		}
       //$sql2="select * from account.acc_request where payee_name like '%".$row['name']."%' and city in ($city) and payee_type='S'";
//		$sql2="select * from account.acc_request where payee_id=".$row['id']." and payee_type='S' order by req_dt desc";
//        $dows = Yii::app()->db->createCommand($sql2)->queryAll();
//		//print_r($dows);
//        $this->attrs = array();
//        if (count($dows) > 0)
//        {
//            foreach ($dows as $k=>$dow)
//            {
//                $this->attr[] = array(
//                    'city_name'=>'',
//                    'id'=>$dow['id'],
//                    'req_dt'=>$dow['req_dt'],
//                    'payee_name'=>$dow['payee_name'],
//                    'payee_type'=>$dow['payee_type'],
//                    'amount'=>$dow['amount'],
//                );
//
//            }
//        }
////

        $sql1 = "select a.id, a.req_dt, e.trans_type_desc, a.item_desc, a.payee_name,a.payee_type,f.field_value,
					b.name as city_name, a.amount, a.status, f.field_value as ref_no, a.req_user, 
					g.field_value as int_fee,t.acct_name,
					(select case workflow$suffix.RequestStatus('PAYMENT',a.id,a.req_dt)
							when '' then '0DF' 
							when 'PC' then '1PC' 
							when 'PA' then '2PA' 
							when 'PR' then '3PR' 
							when 'PS' then '4PS' 
							when 'ED' then '5ED' 
					end) as wfstatus,
					workflow$suffix.RequestStatusDesc('PAYMENT',a.id,a.req_dt) as wfstatusdesc
				from account$suffix.acc_request a inner join security$suffix.sec_city b on a.city=b.code
					inner join account$suffix.acc_trans_type e on a.trans_type_code=e.trans_type_code 
					left outer join account$suffix.acc_request_info f on a.id=f.req_id and f.field_id='ref_no'
					left outer join account$suffix.acc_request_info g on a.id=g.req_id and g.field_id='int_fee'
				    left outer join account$suffix.acc_request_info h on a.id=h.req_id and h.field_id='acct_id' 
				    left outer join account$suffix.acc_account t on t.id=h.field_value
				where a.city in ($city) 
				and e.trans_cat='OUT' and a.payee_id=".$row['id']." and a.payee_type='S'  
			";
        $sql2 = "select count(a.id)
				from account$suffix.acc_request a inner join security$suffix.sec_city b on a.city=b.code
					inner join account$suffix.acc_trans_type e on a.trans_type_code=e.trans_type_code 
					left outer join account$suffix.acc_request_info f on a.id=f.req_id and f.field_id='ref_no'
					left outer join account$suffix.acc_request_info g on a.id=g.req_id and g.field_id='int_fee'
				    left outer join account$suffix.acc_request_info h on a.id=h.req_id and h.field_id='acct_id' 
				    left outer join account$suffix.acc_account t on t.id=h.field_value
				where ((a.city in ($city) and workflow$suffix.RequestStatus('PAYMENT',a.id,a.req_dt)<>'') or a.req_user='$user')
				and e.trans_cat='OUT' and a.payee_id=".$row['id']." and a.payee_type='S'    
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'req_dt':
                    $clause .= General::getSqlConditionClause('a.req_dt',$svalue);
                    break;
                case 'ref_no':
                    $clause .= General::getSqlConditionClause('f.field_value',$svalue);
                    break;
                case 'trans_type_desc':
                    $clause .= General::getSqlConditionClause('e.trans_type_desc',$svalue);
                    break;
                case 'acct_name':
                    $clause .= General::getSqlConditionClause('t.acct_name',$svalue);
                    break;
                case 'amount':
                    $clause .= General::getSqlConditionClause('a.amount',$svalue);
                    break;
                case 'item_desc':
                    $clause .= General::getSqlConditionClause('a.item_desc',$svalue);
                    break;
//                case 'int_fee':
//                    $field = "(select case g.field_value when 'Y' then '".Yii::t('misc','Yes')."'
//							else '".Yii::t('misc','No')."'
//						end) ";
//                    $clause .= General::getSqlConditionClause($field,$svalue);
//                    break;
//                case 'wfstatusdesc':
//                    $clause .= General::getSqlConditionClause("workflow$suffix.RequestStatusDesc('PAYMENT',a.id,a.req_dt)",$svalue);
//                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            switch ($this->orderField) {
//                case 'city_name': $orderf = 'b.name'; break;
                case 'req_dt': $orderf = 'a.req_dt'; break;
                case 'trans_type_desc': $orderf = 'e.trans_type_desc'; break;
                case 'bank': $orderf = 't.acct_name'; break;
                case 'item_desc': $orderf = 'a.item_desc'; break;
                case 'ref_no': $orderf = 'f.field_value'; break;
                case 'int_fee': $orderf = 'g.field_value'; break;
                default: $orderf = $this->orderField; break;
            }
            $order .= " order by ".$orderf." ";
            if ($this->orderType=='D') $order .= "desc ";
        }
        if ($order=="") $order = "order by wfstatus, req_dt desc";

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        $this->attr = array();
        //print_r($sql);

        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
               // $sql2="select * from account.acc_account WHERE id = (SELECT field_value from account.acc_request_info WHERE field_id = 'acct_id' AND req_id = ".$record['id'].")";
                //$recordes = Yii::app()->db->createCommand($sql2)->queryAll();

                $wfstatus = (empty($record['wfstatus'])?'0DF':$record['wfstatus']);
//                if (($wfstatus=='0DF' && $record['req_user']==$user) || $wfstatus!='0DF') {
                    $this->attr[] = array(
                        'id'=>$record['id'],
                        'bank'=>$record['acct_name'],
                        'req_dt'=>General::toDate($record['req_dt']),
                        'trans_type_desc'=>$record['trans_type_desc'],
                        'payee_name'=>$record['payee_name'],
                        'amount'=>$record['amount'],
                        'item_desc'=>str_replace("\n","<br>",$record['item_desc']),
                        'city_name'=>$record['city_name'],
//                        'status'=>($record['status']=='A'?'':General::getTransStatusDesc($record['status'])),
                        'wfstatusdesc'=>(empty($record['wfstatusdesc'])?Yii::t('misc','Draft'):$record['wfstatusdesc']) ,
                        'ref_no'=>$record['ref_no'],
                        'wfstatus'=> $wfstatus,
                        'req_user'=>$record['req_user'],
                        'int_fee'=>($record['int_fee']=='Y' ? Yii::t('misc','Yes') : Yii::t('misc','No')),
                    );
//                }
            }
        }

        $session = Yii::app()->session;
        $session['criteria_xa14'] = $this->getCriteria();
        //print_r( $records);
		return true;
	}

    public function retrieveDatas($index)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $user = Yii::app()->user->id;
        $city = Yii::app()->user->city_allow();
        $sql = "select *,  
				workflow$suffix.RequestStatus('PAYMENT',id,req_dt) as wfstatus,
				workflow$suffix.RequestStatusDesc('PAYMENT',id,req_dt) as wfstatusdesc,
				docman$suffix.countdoc('payreq',id) as payreqcountdoc,
				docman$suffix.countdoc('tax',id) as taxcountdoc
				from account$suffix.acc_request where id=$index 
				and ((city in ($city) and req_user<>'$user') or req_user='$user') 
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $sql1="select * from account$suffix.acc_account WHERE id = (SELECT field_value from account$suffix.acc_request_info WHERE field_id = 'acct_id' AND req_id = ".$rows[0]['id'].")";
        $acct_code_desc = Yii::app()->db->createCommand($sql1)->queryAll();
        $this->acct_code_desc = $acct_code_desc[0]['acct_name'].$acct_code_desc[0]['acct_no']."(".$acct_code_desc[0]['bank_name'].")";
        $sql2="select trans_type_desc from account$suffix.acc_trans_type where trans_type_code = '".$rows[0]['trans_type_code']."' and trans_cat = 'OUT'";
        $trans_type_code = Yii::app()->db->createCommand($sql2)->queryAll();
        $this->trans_type_code =  $trans_type_code[0]['trans_type_desc'];

        $sql3="select name from account$suffix.acc_account_item where code = (select field_value from account$suffix.acc_request_info where req_id = '".$rows[0]['id']."' and field_id = 'item_code')";
        $item_code = Yii::app()->db->createCommand($sql3)->queryAll();
        $this->item_name = $item_code[0]['name'];

        $sql4="select name from account$suffix.acc_account_code where code = (select field_value from account$suffix.acc_request_info where req_id = '".$rows[0]['id']."' and field_id = 'acct_code')";
        $acct_code = Yii::app()->db->createCommand($sql4)->queryAll();
        $this->acct_name = $acct_code[0]['name'];

        $sql5="select field_value from account$suffix.acc_request_info where req_id = '".$rows[0]['id']."' and field_id = 'int_fee'";
        $int_fee = Yii::app()->db->createCommand($sql5)->queryAll();
        if($int_fee==""){
            $this->int_fee="";
        }
        if(!empty($int_fee)&&$int_fee[0]['field_value']=="N"){
            $this->int_fee = "否";
        }
        if(!empty($int_fee)&&$int_fee[0]['field_value']=="Y"){
            $this->int_fee = "是";
        }
        //print_r( $this->int_fee);
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->req_dt = General::toDate($row['req_dt']);
                $this->req_user = $row['req_user'];

                $this->payee_type = $row['payee_type'];
                $this->payee_id = $row['payee_id'];
                $this->payee_name= $row['payee_name'];
                $this->item_desc = $row['item_desc'];
                $this->amount = $row['amount'];
                $this->status = $row['status'];
                //$this->status_desc = General::getTransStatusDesc($row['status']);
                $this->wfstatus = $row['wfstatus'];
                $this->wfstatusdesc = $row['wfstatusdesc'];
                $this->no_of_attm['payreq'] = $row['payreqcountdoc'];
                $this->no_of_attm['tax'] = $row['taxcountdoc'];
                $this->city = $row['city'];
                break;
            }
            //print_r($rows);
            $sql = "select * from account$suffix.acc_request_info where req_id=$index";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $dynfldid = $row['field_id'];
                    if (in_array($dynfldid,$this->dyn_fields)) {
                        $this->$dynfldid = $row['field_value'];
                    }
                }
            }

//            $acctcodelist = General::getAcctCodeList();
//            $acctitemlist = General::getAcctItemList();
//            if (isset($acctcodelist[$this->acct_code])) $this->acct_code_desc = $acctcodelist[$this->acct_code];
//            if (isset($acctitemlist[$this->item_code])) $this->pitem_desc = $acctitemlist[$this->item_code];
        }
        return (count($rows) > 0);
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->savesupplier($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function savesupplier(&$connection)
	{

		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_supplier where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into swo_supplier(
							code, name, full_name, tax_reg_no, cont_name, cont_phone, address, bank, acct_no, 
							city, luu, lcu
						) values (
							:code, :name, :full_name, :tax_reg_no, :cont_name, :cont_phone, :address, :bank, :acct_no,
							:city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_supplier set
							code = :code, 
							name = :name, 
							full_name = :full_name, 
							tax_reg_no = :tax_reg_no, 
							cont_name = :cont_name, 
							cont_phone = :cont_phone, 
							address = :address, 
							bank = :bank,
							acct_no = :acct_no,
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
		if (strpos($sql,':address')!==false)
			$command->bindParam(':address',$this->address,PDO::PARAM_STR);
		if (strpos($sql,':bank')!==false)
			$command->bindParam(':bank',$this->bank,PDO::PARAM_STR);
		if (strpos($sql,':acct_no')!==false)
			$command->bindParam(':acct_no',$this->acct_no,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		return true;
	}
}
