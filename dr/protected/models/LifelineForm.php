<?php

class LifelineForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $life_date;
	public $life_num=80000;
	public $city;
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
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,life_date, city,life_num','safe'),
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
        $rows = Yii::app()->db->createCommand()->select("city,life_num")->from("swo_lifeline")
            ->where("city in ({$city_allow}) and life_date<=:date",array(":date"=>$date))
            ->order("life_date desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(!key_exists($row["city"],$list)){
                    $list[$row["city"]] = $row["life_num"];
                }
            }
        }
        return $list;
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
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
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

	public static function getCityList($code){
	    $list = array(""=>"");
        $notCityStr = ComparisonSetList::notCitySqlStr();
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select code,name from security{$suffix}.sec_city 
				where code not in (SELECT b.region FROM security{$suffix}.sec_city b WHERE b.region is not NULL and b.region!='' GROUP BY b.region)
				 AND code NOT in ('{$notCityStr}') AND name != '停用'
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
}
