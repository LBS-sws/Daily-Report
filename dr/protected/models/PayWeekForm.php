<?php

class PayWeekForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $code;
	public $description;
	public $z_display;
	public $u_id;

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
            'u_id'=>"派单系统id",
            'z_display'=>"是否显示",
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
					'className'=>'PayWeek',
					'on'=>'new',
				),
			array('description,code,u_id,z_display','required'),
			array('id','safe'),
		);
	}

	public function retrieveData($index)
	{
		$sql = "select * from swo_payweek where id=".$index;
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->code = $row['code'];
				$this->description = $row['description'];
				$this->u_id = $row['u_id'];
				$this->z_display = $row['z_display'];
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

	public static function getPayWeekForId($id="",$bool=false){
	    $list = array(""=>"");
        $sql = "select id,description,code from swo_payweek";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["description"];
            }
        }
        if ($bool){
            return key_exists($id,$list)?$list[$id]:$id;
        }else{
            return $list;
        }
    }

	protected function saveProduct(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_payweek where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_payweek(
						code, description, u_id, z_display, city, lcu, luu) values (
						:code, :description, :u_id, :z_display, :city, :lcu, :luu)";
				break;
			case 'edit':
				$sql = "update swo_payweek set 
					code = :code,
					description = :description, 
					u_id = :u_id, 
					z_display = :z_display, 
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
		if (strpos($sql,':u_id')!==false)
			$command->bindParam(':u_id',$this->u_id,PDO::PARAM_STR);
		if (strpos($sql,':z_display')!==false)
			$command->bindParam(':z_display',$this->z_display,PDO::PARAM_STR);
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
/*		$sql = "select a.id from swo_service a where a.product_id=".$index." limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}*/
		return $rtn;
	}
}