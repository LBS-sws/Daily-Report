<?php

class ServiceEndreasonFrom extends CFormModel
{
	/* User Fields */
	public $id;
	public $reason;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('endreason','Id'),
            'reason'=>Yii::t('endreason','Reason'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('reason','required'),
			array('id','safe'),
		);
	}

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city();
		$sql = "select * from swo_service_end_reasons where id=".$index." ";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->reason = $row['reason'];
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
				$sql = "delete from swo_service_end_reasons where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_service_end_reasons(
						reason) values (
						:reason)";
				break;
			case 'edit':
				$sql = "update swo_service_end_reasons set 
					reason = :reason  
					where id = :id";
				break;
		}
		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':reason')!==false)
			$command->bindParam(':reason',$this->reason,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();

		return true;
	}

	public function isOccupied($index) {
		$rtn = false;
		$sql = "select a.id from swo_followup a where a.type='".$index."' limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}
		return $rtn;
	}
}
