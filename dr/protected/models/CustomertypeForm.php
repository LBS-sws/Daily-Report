<?php

class CustomertypeForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $description;
	public $rpt_cat;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'description'=>Yii::t('code','Description'),
			'rpt_cat'=>Yii::t('code','Report Category'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('description','required'),
			array('id,rpt_cat','safe'), 
		);
	}

	public function retrieveData($index)
	{
		$sql = "select * from swo_customer_type where id=".$index."";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->description = $row['description'];
				$this->rpt_cat = $row['rpt_cat'];
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
			$this->saveUser($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveUser(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_customer_type where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_customer_type(
						description, rpt_cat, luu, lcu) values (
						:description, :rpt_cat, :luu, :lcu)";
				break;
			case 'edit':
				$sql = "update swo_customer_type set 
					description = :description, 
					rpt_cat = :rpt_cat,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':description')!==false)
			$command->bindParam(':description',$this->description,PDO::PARAM_STR);
		if (strpos($sql,':rpt_cat')!==false)
			$command->bindParam(':rpt_cat',$this->rpt_cat,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		return true;
	}

	public function isOccupied($index) {
		$rtn = false;
		$sql = "select a.id from swo_service a where a.cust_type=".$index." limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}
		return $rtn;
	}
}
