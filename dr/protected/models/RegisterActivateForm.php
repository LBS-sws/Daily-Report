<?php

class RegisterActivateForm extends CFormModel
{
	/* User Fields */
	public $req_key;
	public $email;
	public $city;
	public $hash1;
	public $hash2;
	public $timestamp;
	public $station_id;
	public $station_name;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'req_key'=>Yii::t('register','Request Key'),
			'email'=>Yii::t('register','Email'),
			'city'=>Yii::t('register','City'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('req_key, hash1, hash2, timestamp, station_id, station_name','safe'),
			array('email','checkValue','field'=>'hash1'),
			array('city','checkValue','field'=>'hash2'),
		);
	}

	public function retrieveData($index)
	{
		$sql = "select * from swo_station_request where req_key='".$index."'";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->req_key = $row['req_key'];
				$this->hash1 = md5($row['email'].$row['req_key']);
				$this->hash2 = md5($row['city'].$row['req_key']);
				$this->timestamp = $row['lud'];
				$this->station_name = $row['station_name'];
				break;
			}
			$timelimit = "-".Yii::app()->params['validRegDuration'];
			if (empty($row['station_id']) && strtotime($timelimit) < strtotime($row['lcd'])) return true;
		}
		return false;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->station_id = uniqid($this->city.'-');
			if (!$this->saveRegister($connection)) throw new CDbException('Zero record affected');
			$this->saveStation($connection);
			$this->saveKeyToCookie();
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveRegister(&$connection)
	{
		$sql = "update swo_station_request set 
					station_id = :station_id, 
					luu = 'admin'
					where req_key = :req_key and lud = :timestamp and station_id is null";

		$command=$connection->createCommand($sql);
		if (strpos($sql,':req_key')!==false)
			$command->bindParam(':req_key',$this->req_key,PDO::PARAM_STR);
		if (strpos($sql,':station_id')!==false)
			$command->bindParam(':station_id',$this->station_id,PDO::PARAM_STR);
		if (strpos($sql,':timestamp')!==false)
			$command->bindParam(':timestamp',$this->timestamp,PDO::PARAM_STR);
		$affected_row = $command->execute();

		return ($affected_row>0);
	}
	
	protected function saveStation(&$connection)
	{
		$sql = "replace into swo_station (station_id, station_name, city, lcu, luu)
					values (:station_id, :station_name, :city, 'admin', 'admin')";

		$command=$connection->createCommand($sql);
		if (strpos($sql,':station_id')!==false)
			$command->bindParam(':station_id',$this->station_id,PDO::PARAM_STR);
		if (strpos($sql,':station_name')!==false)
			$command->bindParam(':station_name',$this->station_name,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$this->city,PDO::PARAM_STR);
		$affected_row = $command->execute();

		return ($affected_row>0);
	}
	
	public function saveKeyToCookie() {
		$name = 'station_key';
		$value = $this->station_id;
		$time = -1;
		$disableClientCookies = false;
		Cookie::setCookie($name, $value, $time, $disableClientCookies);
	}
	
	public function checkValue($attribute, $params) {
		$field = $params['field'];
		$value = md5($this->$attribute.$this->req_key);
		if ($value!=$this->$field) {
			$labels = $this->attributeLabels();
			$this->addError($attribute, $labels[$attribute].' - '.Yii::t('register','Input information does not match'));
		}
	}
}
