<?php

class StationForm extends CFormModel
{
	/* User Fields */
	public $station_id;
	public $station_name;
	public $city;
	public $status;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'station_id'=>Yii::t('station','Station ID'),
			'station_name'=>Yii::t('station','Name'),
			'city'=>Yii::t('station','City'),
			'status'=>Yii::t('station','Status'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('status','safe'),
		);
	}

	public function retrieveData($index)
	{
		$sql = "select * from swo_station where station_id='".$index."'";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->station_id = $row['station_id'];
				$this->station_name = $row['station_name'];
				$this->city = $row['city'];
				$this->status = $row['status'];
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
			$this->saveStation($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStation(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'edit':
				$sql = "update swo_station set 
					status = :status, 
					luu = :luu
					where station_id = :station_id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':station_id')!==false)
			$command->bindParam(':station_id',$this->station_id,PDO::PARAM_STR);
		if (strpos($sql,':status')!==false)
			$command->bindParam(':status',$this->status,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		return true;
	}
}
