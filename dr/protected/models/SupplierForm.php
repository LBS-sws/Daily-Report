<?php

class SupplierForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $type;
	public $code;
	public $name;
	public $cont_name;
	public $cont_phone;
	public $nature;
	public $address;
	public $bank;
	public $acct_no;

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
			'cont_name'=>Yii::t('supplier','Contact Name'),
			'cont_phone'=>Yii::t('supplier','Contact Phone'),
			'address'=>Yii::t('supplier','Address'),
			'bank'=>Yii::t('supplier','Bank'),
			'acct_no'=>Yii::t('supplier','Account No'),
		);
	}
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, cont_name, cont_phone, address, bank, acct_no','safe'),
			array('name, code','required'),
/*
			array('code','unique','allowEmpty'=>true,
					'attributeName'=>'code',
					'caseSensitive'=>false,
					'className'=>'Supplier',
				),
*/
			array('code','validateCode'),
		);
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
				$this->cont_name = $row['cont_name'];
				$this->cont_phone = $row['cont_phone'];
				$this->address = $row['address'];
				$this->bank = $row['bank'];
				$this->acct_no = $row['acct_no'];
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
							code, name, cont_name, cont_phone, address, bank, acct_no, 
							city, luu, lcu
						) values (
							:code, :name, :cont_name, :cont_phone, :address, :bank, :acct_no,
							:city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_supplier set
							code = :code, 
							name = :name, 
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
