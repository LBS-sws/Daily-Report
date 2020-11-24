<?php

class TaskForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $description;
	public $task_type;
    public $sales_products;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'description'=>Yii::t('code','Description'),
			'task_type'=>Yii::t('code','Type'),
            'sales_products'=>Yii::t('code','Sales Products'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('description, task_type,sales_products','required'),
			array('id','safe'), 
		);
	}

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city_allow();
		$sql = "select * from swo_task where id=$index and city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->description = $row['description'];
				$this->task_type = $row['task_type'];
                $this->sales_products = $row['sales_products'];
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
			$this->saveTask($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveTask(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_task where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into swo_task(
						sales_products,description, task_type, city, luu, lcu) values (
						:sales_products,:description, :task_type, :city, :luu, :lcu)";
				break;
			case 'edit':
				$sql = "update swo_task set 
                    sales_products=:sales_products,
					description = :description, 
					task_type = :task_type,
					luu = :luu
					where id = :id and city = :city";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':description')!==false)
			$command->bindParam(':description',$this->description,PDO::PARAM_STR);
		if (strpos($sql,':task_type')!==false)
			$command->bindParam(':task_type',$this->task_type,PDO::PARAM_STR);
        if (strpos($sql,':sales_products')!==false)
            $command->bindParam(':sales_products',$this->sales_products,PDO::PARAM_STR);
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
		$sql = "select a.id from swo_logistic_dtl a where a.task=".$index." limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}
		return $rtn;
	}
}
