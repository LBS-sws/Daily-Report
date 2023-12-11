<?php

class PestTypeForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $pest_name;
	public $z_index=0;
	public $display_num=1;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'pest_name'=>Yii::t('followup','Pest Type Name'),
            'display_num'=>Yii::t('followup','display'),
            'z_index'=>Yii::t('followup','z_index'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,pest_name,z_index,display_num','safe'),
			array('pest_name','required'),
            array('z_index,display_num','numerical','allowEmpty'=>false,'integerOnly'=>true),
            array('id','validateID','on'=>array("delete")),
		);
	}

    public function validateID($attribute, $params) {
        $id = $this->$attribute;
        $row = Yii::app()->db->createCommand()->select("id")->from("swo_followup")
            ->where("pest_id=:id",array(":id"=>$id))->queryRow();
        if($row){
            $this->addError($attribute, "这条记录已被使用无法删除");
            return false;
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select * from swo_pest_type where id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->pest_name = $row['pest_name'];
			$this->display_num = $row['display_num'];
			$this->z_index = $row['z_index'];
            return true;
		}else{
		    return false;
        }
	}

    public static function getPestTypeList($id=0){
        $id = empty($id)?0:$id;
        $id = is_array($id)?implode(",",$id):$id;
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("*")->from("swo_pest_type")
            ->where("display_num=1 or id in ({$id})")
            ->order("z_index asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["pest_name"];
            }
        }
        return $list;
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_pest_type where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_pest_type(
						pest_name, z_index, display_num, lcu, lcd) values (
						:pest_name, :z_index, :display_num, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update swo_pest_type set 
					pest_name = :pest_name, 
					z_index = :z_index,
					display_num = :display_num,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':z_index')!==false)
			$command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
		if (strpos($sql,':display_num')!==false)
			$command->bindParam(':display_num',$this->display_num,PDO::PARAM_INT);
		if (strpos($sql,':pest_name')!==false)
			$command->bindParam(':pest_name',$this->pest_name,PDO::PARAM_STR);

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false){
            $date = date("Y-m-d H:i:s");
            $command->bindParam(':lcd',$date,PDO::PARAM_STR);
        }
		$command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();

		return true;
	}
}