<?php

class StopRemarkForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $remark;
	public $city;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'remark'=>Yii::t('service','Stop Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('remark','required'),
			array('id,remark','safe'),
		);
	}

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city();
		$sql = "select * from swo_stop_remark where id=".$index." ";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->remark = $row['remark'];
				$this->city = $row['city'];
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
        $city =Yii::app()->user->city();
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_stop_remark where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_stop_remark(
						remark, city, lcu, luu) values (
						:remark, :city, :lcu, :luu)";
				break;
			case 'edit':
				$sql = "update swo_stop_remark set 
					remark = :remark, 
					city = :city,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
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

	public function isOccupied($index) {
		$rtn = false;
		$sql = "select a.id from swo_stop_remark a where a.id='".$index."' limit 1";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if(!$row){ //如果不存在則無法刪除
		    $rtn = true;
        }
		return $rtn;
	}
}
