<?php

class CitySetForm extends CFormModel
{
	/* User Fields */
	public $code;
	public $city_name;
	public $show_type=1;
	public $add_type=0;
	public $region_code;
	public $z_index=0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'code'=>Yii::t('summary','City Code'),
            'city_name'=>Yii::t('summary','City Name'),
            'show_type'=>Yii::t('summary','show type'),
            'region_code'=>Yii::t('summary','end region'),
            'add_type'=>Yii::t('summary','add type'),
            'z_index'=>Yii::t('summary','z index'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('code,city_name, show_type,region_code,add_type,z_index','safe'),
			array('code, city_name,show_type','required'),
            array('show_type','validateDate'),
		);
	}

    public function validateDate($attribute, $params) {
	    if(empty($this->show_type)){
	        $this->region_code=null;
	        $this->add_type=0;
        }elseif(empty($this->region_code)){
            $this->addError($attribute, Yii::t('summary','end region')."不能为空");
        }
    }

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
		$row = Yii::app()->db->createCommand()->select("a.code,a.name as city_name,b.show_type,b.add_type,b.region_code,b.z_index")
            ->from("security$suffix.sec_city a")
            ->leftJoin("swo_city_set b","a.code=b.code")
            ->where("a.code=:code",array(":code"=>$index))
            ->queryRow();
		if ($row){
            $this->code = $row['code'];
            $this->city_name = $row['city_name'];
            $this->show_type = $row['show_type'];
            $this->region_code = $row['region_code'];
            $this->add_type = $row['add_type'];
            $this->z_index = $row['z_index'];
		}
		return true;
	}

	public static function getCitySetList($city_allow=""){
	    $list=array();
        $suffix = Yii::app()->params['envSuffix'];
        $cityWhere="";
        if($city_allow!=="all"){
            $city_allow = empty($city_allow)?Yii::app()->user->city_allow():$city_allow;
            $cityWhere=" and b.code in ({$city_allow})";
        }
		$rows = Yii::app()->db->createCommand()
            ->select("a.code,a.name as city_name,b.show_type,b.add_type,b.region_code,f.name as region_name")
            ->from("swo_city_set b")
            ->leftJoin("security$suffix.sec_city a","a.code=b.code")
            ->leftJoin("security$suffix.sec_city f","b.region_code=f.code")
            ->where("b.show_type=1 {$cityWhere}")
            ->order("b.z_index desc,a.name asc")
            ->queryAll();
		if ($rows){
		    foreach ($rows as $row){
		        $list[$row["code"]] = $row;
            }
		}
		return $list;
	}

	public static function getListForCityCode($city_code,$list){
	    if(key_exists($city_code,$list)){
	        return $list[$city_code];
        }else{
	        return array("code"=>$city_code,"city_name"=>"","show_type"=>0,"add_type"=>0,"region_code"=>"null","region_name"=>"null");
        }
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveCitySet($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveCitySet(&$connection)
	{
        $row = Yii::app()->db->createCommand()->select("code")->from("swo_city_set")
            ->where("code=:code",array(":code"=>$this->code))
            ->queryRow();
        if($row){
            $this->setScenario("edit");
        }else{
            $this->setScenario("new");
        }
		$sql = '';
		switch ($this->scenario) {
			case 'new':
				$sql = "insert into swo_city_set(
						code, show_type, add_type, region_code, z_index, luu, lcu) values (
						:code, :show_type, :add_type, :region_code, :z_index, :luu, :lcu)";
				break;
			case 'edit':
				$sql = "update swo_city_set set 
					show_type = :show_type, 
					add_type = :add_type, 
					region_code = :region_code,
					z_index = :z_index,
					luu = :luu
					where code = :code";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
        if (strpos($sql,':code')!==false)
            $command->bindParam(':code',$this->code,PDO::PARAM_STR);
		if (strpos($sql,':show_type')!==false)
			$command->bindParam(':show_type',$this->show_type,PDO::PARAM_INT);
		if (strpos($sql,':add_type')!==false)
			$command->bindParam(':add_type',$this->add_type,PDO::PARAM_INT);
		if (strpos($sql,':z_index')!==false){
            $this->z_index=empty($this->z_index)?0:$this->z_index;
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
        }
		if (strpos($sql,':region_code')!==false)
			$command->bindParam(':region_code',$this->region_code,PDO::PARAM_STR);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		return true;
	}

	public function isOccupied($index) {
		$rtn = false;
		return $rtn;
	}
}
