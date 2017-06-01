<?php

class ProductForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $code;
	public $description;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'code'=>Yii::t('code','Code'),
			'description'=>Yii::t('code','Description'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('code','unique','allowEmpty'=>false,
					'attributeName'=>'code',
					'caseSensitive'=>false,
					'className'=>'Product',
					'on'=>'new',
				),
			array('description,code','required'),
			array('id','safe'),
		);
	}

	public function retrieveData($index)
	{
		$sql = "select * from swo_product where id=".$index;
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->code = $row['code'];
				$this->description = $row['description'];
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
			$this->saveProduct($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveProduct(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_product where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_product(
						code, description, city, lcu, luu) values (
						:code, :description, :city, :lcu, :luu)";
				break;
			case 'edit':
				$sql = "update swo_product set 
					code = :code,
					description = :description, 
					luu = :luu,
					city = :city
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;
		$city = '99999';	//Yii::app()->user->city();

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':code')!==false)
			$command->bindParam(':code',$this->code,PDO::PARAM_STR);
		if (strpos($sql,':description')!==false)
			$command->bindParam(':description',$this->description,PDO::PARAM_STR);
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

	public function isOccupied($index) {
		$rtn = false;
		$sql = "select a.id from swo_service a where a.product_id=".$index." limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}
		return $rtn;
	}
}