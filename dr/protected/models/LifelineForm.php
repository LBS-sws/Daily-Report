<?php

class LifelineForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $life_date;
	public $life_num=80000;
	public $city;
    public $detail = array(
        array('id'=>0,
            'lifeline_id'=>0,
            'office_id'=>0,
            'life_num'=>0,
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
            'life_date'=>Yii::t('summary','Effective Year'),
            'city'=>Yii::t('misc','City'),
            'life_num'=>Yii::t('summary','life num'),
            'office_id'=>Yii::t('summary','office Name'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,life_date, city,life_num,detail','safe'),
			array('life_date, city,life_num','required'),
            array('life_date','validateDate'),
		);
	}

    public function validateDate($attribute, $params) {
        $row = Yii::app()->db->createCommand()->select("id")->from("swo_lifeline")
            ->where("id!=:id and DATE_FORMAT(life_date,'%Y')=:life_date and city=:city",
                array(":id"=>$this->id,":life_date"=>$this->life_date,":city"=>$this->city)
            )->queryRow();
        if($row){
            $this->addError($attribute, "该设置已存在，无法重复添加");
        }
    }

    public static function getLifeLineList($city_allow,$date) {
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("id,city,life_num")->from("swo_lifeline")
            ->where("city in ({$city_allow}) and life_date<=:date",array(":date"=>$date))
            ->order("life_date desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(!key_exists($row["city"],$list)){ //城市的生命線
                    $list[$row["city"]] = $row["life_num"];
                    $infoRows = Yii::app()->db->createCommand()->select("office_id,life_num")->from("swo_lifeline_info")
                        ->where("lifeline_id=:id",array(":id"=>$row["id"]))->queryAll();
                    if($infoRows){ //辦事處的生命線
                        foreach ($infoRows as $infoRow){
                            $infoKey = $row["city"]."_".$infoRow["office_id"];
                            if(!key_exists($infoKey,$list)){
                                $list[$infoKey] = $infoRow["life_num"];
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }

    public static function getLineValueForC_O($list,$city,$office_id){
        $officeKey = $city."_".$office_id;
        if(key_exists($officeKey,$list)){
            return $list[$officeKey];
        }elseif (key_exists($city,$list)){
            return $list[$city];
        }else{
            return 80000;
        }
    }

	public function retrieveData($index)
	{
		$sql = "select * from swo_lifeline where id=$index";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->city = $row['city'];
				$this->life_num = $row['life_num'];
                $this->life_date = intval($row['life_date']);
                $sql = "select * from swo_lifeline_info where lifeline_id=$index ";
                $rows = Yii::app()->db->createCommand($sql)->queryAll();
                if (count($rows) > 0) {
                    $this->detail = array();
                    foreach ($rows as $row) {
                        $temp = array();
                        $temp['id'] = $row['id'];
                        $temp['lifeline_id'] = $index;
                        $temp['office_id'] = $row['office_id'];
                        $temp['life_num'] = $row['life_num'];
                        $temp['uflag'] = 'N';
                        $this->detail[] = $temp;
                    }
                }
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
			$this->saveLifeline($connection);
            $this->saveLifelineInfo($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

    protected function saveLifelineInfo(&$connection){
	    if($this->getScenario()=="new"){
	        return false;
        }
        $uid = Yii::app()->user->id;
        foreach ($_POST['LifelineForm']['detail'] as $row) {
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from swo_lifeline_info where lifeline_id = :lifeline_id ";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into swo_lifeline_info(
									lifeline_id, office_id, life_num,
									 luu, lcu
								) values (
									:lifeline_id, :office_id, :life_num,
									 :luu, :lcu
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from swo_lifeline_info where id = :id ";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into swo_lifeline_info(
										lifeline_id, office_id, life_num,
										 luu, lcu
									) values (
										:lifeline_id, :office_id, :life_num,
										:luu, :lcu
									)
									"
                                :
                                "update swo_lifeline_info set
										lifeline_id = :lifeline_id,
										office_id = :office_id, 
										life_num = :life_num,
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
                if (strpos($sql,':lifeline_id')!==false)
                    $command->bindParam(':lifeline_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':office_id')!==false){
                    $row['office_id'] = empty($row['office_id'])?0:$row['office_id'];
                    $command->bindParam(':office_id',$row['office_id'],PDO::PARAM_INT);
                }
                if (strpos($sql,':life_num')!==false){
                    $row['life_num'] = empty($row['life_num'])?0:$row['life_num'];
                    $command->bindParam(':life_num',$row['life_num'],PDO::PARAM_INT);
                }
                if (strpos($sql,':luu')!==false)
                    $command->bindParam(':luu',$uid,PDO::PARAM_STR);
                if (strpos($sql,':lcu')!==false)
                    $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
                $command->execute();
            }
        }
        return true;
    }

	protected function saveLifeline(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_lifeline where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_lifeline(
						life_date, life_num, city, luu, lcu) values (
						:life_date, :life_num, :city, :luu, :lcu)";
				break;
			case 'edit':
				$sql = "update swo_lifeline set 
                    life_date=:life_date,
					life_num = :life_num, 
					city = :city,
					luu = :luu
					where id = :id";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':life_num')!==false)
			$command->bindParam(':life_num',$this->life_num,PDO::PARAM_INT);
		if (strpos($sql,':life_date')!==false){
            $lifeDate = $this->life_date."/01/01";
            $command->bindParam(':life_date',$lifeDate,PDO::PARAM_STR);
        }
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
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
		return $rtn;
	}

	public function isReadOnly(){
	    return $this->getScenario()=='view';
    }

	public static function getCityList($code){
	    $list = array(""=>"");
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select b.code,b.name from swo_city_set a
                LEFT JOIN security{$suffix}.sec_city b ON a.code=b.code
				where a.show_type=1 
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["code"]] = $row["name"];
            }
        }
        if(!empty($code)&&!key_exists($code,$list)){
            $list[$code] = $code;
        }
        return $list;
    }

	public static function getOfficeList($city){
	    $list = array(0=>Yii::t("summary","local office"));
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("id,name")->from("hr{$suffix}.hr_office")
            ->where("city=:city",array(":city"=>$city))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["name"];
            }
        }
        return $list;
    }
}
