<?php

class NatureForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $description;
	public $rpt_cat;
    public $detail = array(
        array(
            'id'=>0,
            'name'=>'',
            'rpt_u'=>'',
            'score_bool'=>0,//是否計算積分0：不計算 1：計算
            'uflag'=>'N',
        ),
    );

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
			'name'=>Yii::t('code','Description'),
			'rpt_u'=>Yii::t('code','rpt u'),
			'score_bool'=>Yii::t('code','score bool'),
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
		$city = Yii::app()->user->city();
		$sql = "select * from swo_nature where id=".$index." ";
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
        $sql = "select * from swo_nature_type where nature_id=$index ";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $this->detail = array();
            foreach ($rows as $row) {
                $temp = array();
                $temp['id'] = $row['id'];
                $temp['name'] = $row['name'];
                $temp['rpt_u'] = $row['rpt_u'];
                $temp['score_bool'] = $row['score_bool'];
                $temp['uflag'] = 'N';
                $this->detail[] = $temp;
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
            $this->saveNatureDtl($connection);
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
				$sql = "delete from swo_nature where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_nature(
						description, rpt_cat, lcu, luu) values (
						:description, :rpt_cat, :lcu, :luu)";
				break;
			case 'edit':
				$sql = "update swo_nature set 
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

    protected function saveNatureDtl(&$connection)
    {
        $uid = Yii::app()->user->id;
        //var_dump($_POST['NatureForm']['detail']);die();
        foreach ($_POST['NatureForm']['detail'] as $row) {
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from swo_nature_type where nature_id = :nature_id ";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into swo_nature_type(
									name, nature_id, rpt_u, score_bool,lcu
								) values (
									:name, :nature_id, :rpt_u, :score_bool,:lcu
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from swo_nature_type where id = :id ";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into swo_nature_type(
										name, nature_id, rpt_u, score_bool,lcu
									) values (
										:name, :nature_id, :rpt_u, :score_bool,:lcu
									)
									"
                                :
                                "update swo_nature_type set
										name = :name,
										rpt_u = :rpt_u, 
										score_bool = :score_bool,
										luu = :luu 
									where id = :id 
									";
                            break;
                    }
                    break;
            }

            if ($sql != '') {
                $command=$connection->createCommand($sql);
                if (strpos($sql,':id')!==false)
                    $command->bindParam(':id',$row['id'],PDO::PARAM_INT);
                if (strpos($sql,':name')!==false)
                    $command->bindParam(':name',$row['name'],PDO::PARAM_STR);
                if (strpos($sql,':nature_id')!==false)
                    $command->bindParam(':nature_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':rpt_u')!==false){
                    $row['rpt_u'] =$row['rpt_u']===""?null:$row['rpt_u'];
                    $command->bindParam(':rpt_u',$row['rpt_u'],PDO::PARAM_INT);
                }
                if (strpos($sql,':score_bool')!==false)
                    $command->bindParam(':score_bool',$row['score_bool'],PDO::PARAM_INT);
                if (strpos($sql,':luu')!==false)
                    $command->bindParam(':luu',$uid,PDO::PARAM_STR);
                if (strpos($sql,':lcu')!==false)
                    $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
                $command->execute();
            }
        }
        return true;
    }

	public function isOccupied($index) {
		$rtn = false;
		$sql = "select a.id from swo_service a where a.nature_type=".$index." limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}
		return $rtn;
	}

	public static function getNatureList(){
	    $list=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("id,description")
            ->from("swo_nature")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]]=$row["description"];
            }
        }
        return $list;
    }

	public static function getNatureTwoList(){
	    $list=array("select"=>array(""=>""),"options"=>array());
        $rows = Yii::app()->db->createCommand()->select("a.id,a.name,a.nature_id")
            ->from("swo_nature_type a")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list["select"][$row["id"]]=$row["name"];
                $list["options"][$row["id"]]=array("data-nature"=>$row["nature_id"]);
            }
        }
        return $list;
    }

    public function isReadOnly(){
	    return $this->getScenario()=="view";
    }
}
