<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class EnquiryForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $contact_dt;
	public $customer;
	public $type;
	public $nature_type;
	public $contact;
	public $tel_no;
	public $address;
	public $source_code;
	public $source;
	public $follow_staff;
	public $follow_dt;
	public $follow_result;
	public $remarks;
	public $record_by;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('enquiry','Record ID'),
			'contact_dt'=>Yii::t('enquiry','Date').' '.Yii::t('misc','(Y/M/D)'),
			'customer'=>Yii::t('enquiry','Customer'),
			'nature_type'=>Yii::t('enquiry','Nature'),
			'type'=>Yii::t('enquiry','Type').' (IA/IB/INV/OTHR)',
			'source'=>Yii::t('enquiry','Source'),
			'contact'=>Yii::t('enquiry','Contact Person'),
			'tel_no'=>Yii::t('enquiry','Contact Phone'),
			'address'=>Yii::t('enquiry','Contact Address'),
			'follow_staff'=>Yii::t('enquiry','Resp. Staff'),
			'follow_dt'=>Yii::t('enquiry','Follow-up Date').' '.Yii::t('misc','(Y/M/D)'),
			'follow_result'=>Yii::t('enquiry','Result'),
			'remarks'=>Yii::t('enquiry','Remarks'),
			'record_by'=>Yii::t('enquiry','Record By'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, follow_staff, type, source, remarks, contact, tel_no, address, nature_type, follow_result, record_by, source_code','safe'),
			array('report, contact_dt','required'),
			array('contact_dt','date','allowEmpty'=>false,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('follow_dt','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
		);
	}

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city_allow();
		$sql = "select * from swo_enquiry where id=$index and city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->contact_dt = General::toDate($row['contact_dt']);
				$this->customer = $row['customer'];
				$this->nature_type = $row['nature_type'];
				$this->type = $row['type'];
				$this->source_code = $row['source_code'];
				$this->source = $row['source'];
				$this->contact = $row['contact'];
				$this->tel_no = $row['tel_no'];
				$this->address = $row['address'];
				$this->follow_staff = $row['follow_staff'];
				$this->follow_dt = General::toDate($row['follow_dt']);
				$this->follow_result = $row['follow_result'];
				$this->remarks = $row['remarks'];
				$this->record_by = $row['record_by'];
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
			$this->saveEnquiry($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveEnquiry(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_enquiry where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into swo_enquiry(
							contact_dt, report, type, source_code, source, follow_staff, follow_dt, follow_result,
							contact, tel_no, remarks, nature_type, address, record_by, city, luu, lcu
						) values (
							:contact_dt, :report, :type, :source_code, :source, :follow_staff, :follow_dt, :follow_result,
							:contact, :tel_no, :remarks, :nature_type, :address, :record_by, :city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_enquiry set
							contact_dt = :contact_dt,
							report = :report, 
							nature_type = :nature_type,
							type = :type,
							source_code = :source_code, 
							source = :source, 
							contact = :contact,
							tel_no = :tel_no,
							address = :address,
							follow_staff = :follow_staff,
							follow_dt = :follow_dt,
							follow_result = :follow_result,
							remarks = :remarks,
							record_by = :record_by,
							luu = :luu 
						where id = :id and city = :city
						";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		$ctntdt = General::toMyDate($this->contact_dt);
		$fllwdt = General::toMyDate($this->follow_dt);
		
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':contact_dt')!==false)
			$command->bindParam(':contact_dt',$ctntdt,PDO::PARAM_STR);
		if (strpos($sql,':report')!==false)
			$command->bindParam(':report',$this->customer,PDO::PARAM_STR);
		if (strpos($sql,':nature_type')!==false)
			$command->bindParam(':nature_type',$this->nature_type,PDO::PARAM_INT);
		if (strpos($sql,':type')!==false)
			$command->bindParam(':type',$this->type,PDO::PARAM_STR);
		if (strpos($sql,':source_code')!==false)
			$command->bindParam(':source_code',$this->source_code,PDO::PARAM_STR);
		if (strpos($sql,':source')!==false)
			$command->bindParam(':source',$this->source,PDO::PARAM_STR);
		if (strpos($sql,':contact')!==false)
			$command->bindParam(':contact',$this->contact,PDO::PARAM_STR);
		if (strpos($sql,':tel_no')!==false)
			$command->bindParam(':tel_no',$this->tel_no,PDO::PARAM_STR);
		if (strpos($sql,':address')!==false)
			$command->bindParam(':address',$this->address,PDO::PARAM_STR);
		if (strpos($sql,':follow_staff')!==false)
			$command->bindParam(':follow_staff',$this->follow_staff,PDO::PARAM_INT);
		if (strpos($sql,':follow_dt')!==false)
			$command->bindParam(':follow_dt',$fllwdt,PDO::PARAM_STR);
		if (strpos($sql,':follow_result')!==false)
			$command->bindParam(':follow_result',$this->follow_result,PDO::PARAM_STR);
		if (strpos($sql,':remarks')!==false)
			$command->bindParam(':remarks',$this->remarks,PDO::PARAM_STR);
		if (strpos($sql,':record_by')!==false)
			$command->bindParam(':record_by',$this->record_by,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		return true;
	}

	public function getStaffList()
	{
		return General::getStaffList();
	}
}
