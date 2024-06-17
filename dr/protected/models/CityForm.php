<?php

class CityForm extends CFormModel
{
	/* User Fields */
	public $code;
	public $name;
	public $region;
	public $ka_bool;//城市类型 0：城市 1：ka城市 2：区域
	public $incharge;
	public $currency;
	public $SARANK;//销售系统排行榜
	public $DRRANK;//日报表系统排行榜
	public $JD_city;//金蝶组织编号
    //public $BS_city;//北森组织编号

    protected $dynamic_fields = array(
        //货币
        'currency'=>array("type"=>"list","func"=>array("Currency","getDropDownList"),"param"=>array()),
        //销售系统排行榜
        'SARANK'=>array("type"=>"list","func"=>array("CityForm","getRankList"),"param"=>array()),
        //日报表系统排行榜
        'DRRANK'=>array("type"=>"list","func"=>array("CityForm","getRankList"),"param"=>array()),
        //金蝶系统编号
        'JD_city'=>array("type"=>"text"),
        //北森组织编号
        //'BS_city'=>array("type"=>"text"),
    );
	
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'code'=>Yii::t('code','Code'),
			'name'=>Yii::t('code','Name'),
			'region'=>Yii::t('code','Region'),
			'incharge'=>Yii::t('code','In Charge'),
			'currency'=>Yii::t('code','Currency'),
			'ka_bool'=>Yii::t('code','city type'),
			'SARANK'=>Yii::t('code','rank for sales'),
			'DRRANK'=>Yii::t('code','rank for dr'),
			'JD_city'=>Yii::t('code','JD City'),
			'BS_city'=>Yii::t('code','BS City'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
        $list = array_keys($this->dynamic_fields);
	    $list = implode(",",$list);
        $list.= ",region,ka_bool,incharge";
		return array(
			array('code','unique','allowEmpty'=>false,
					'attributeName'=>'code',
					'caseSensitive'=>false,
					'className'=>'City',
					'on'=>'new',
				),
			array('name,code,ka_bool','required'),
			array($list,'safe'),
		);
	}

	public function getDynamicFields(){
	    return $this->dynamic_fields;
    }

    public static function getRankList(){
	    return array(
	        "0"=>Yii::t("misc","Off"),
	        "1"=>Yii::t("misc","On"),
        );
    }

    public static function getDropDownList() {
        return Currency::getDropDownList();
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select * from security$suffix.sec_city where code='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->code = $row['code'];
			$this->name = $row['name'];
			$this->region = $row['region'];
			$this->ka_bool = $row['ka_bool'];
			$this->incharge = $row['incharge'];
			
			$sql = "select * from security$suffix.sec_city_info where code='".$index."'";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			if (count($rows) > 0) {
				foreach ($rows as $row) {
					if (key_exists($row['field_id'],$this->dynamic_fields)) {
						$fieldid = $row['field_id'];
						$this->$fieldid = $row['field_value'];
					}
				}
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveCity($connection);
			$this->saveCityInfo($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveCity(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from security$suffix.sec_city where code = :code";
				break;
			case 'new':
				$sql = "insert into security$suffix.sec_city(
						code, name, region,ka_bool, lcu, luu) values (
						:code, :name, :region,:ka_bool, :lcu, :luu)";
				break;
			case 'edit':
				$sql = "update security$suffix.sec_city set 
					name = :name, 
					region = :region,
					ka_bool = :ka_bool,
					incharge = :incharge, 
					luu = :luu
					where code = :code";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':code')!==false)
			$command->bindParam(':code',$this->code,PDO::PARAM_STR);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':ka_bool')!==false)
			$command->bindParam(':ka_bool',$this->ka_bool,PDO::PARAM_INT);
		if (strpos($sql,':region')!==false)
			$command->bindParam(':region',$this->region,PDO::PARAM_STR);
		if (strpos($sql,':incharge')!==false)
			$command->bindParam(':incharge',$this->incharge,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		return true;
	}

	protected function saveCityInfo(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from security$suffix.sec_city_info where code = :code";
				break;
			default:
				$sql = "insert into security$suffix.sec_city_info(
						code, field_id, field_value, lcu, luu) values (
						:code, :field_id, :field_value, :lcu, :luu)
						on duplicate key
						update field_value=:field_value, luu=:luu
					";
				break;
		}

		$uid = Yii::app()->user->id;

		foreach ($this->dynamic_fields as $field_id=>$list) {
			$command=$connection->createCommand($sql);
			if (strpos($sql,':code')!==false)
				$command->bindParam(':code',$this->code,PDO::PARAM_STR);
			if (strpos($sql,':field_id')!==false)
				$command->bindParam(':field_id',$field_id,PDO::PARAM_STR);
			$value = $this->$field_id;
			if (strpos($sql,':field_value')!==false)
				$command->bindParam(':field_value',$value,PDO::PARAM_STR);
			if (strpos($sql,':lcu')!==false)
				$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
			if (strpos($sql,':luu')!==false)
				$command->bindParam(':luu',$uid,PDO::PARAM_STR);
			$command->execute();
		}

		return true;
	}

	public function isOccupied($index) {
		$rtn = false;
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.username from security$suffix.sec_user a where a.city='".$index."' limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}
		return $rtn;
	}
	
	public function getCityInChargeList() {
		$rtn = array();
		$suffix = Yii::app()->params['envSuffix'];
		$city = $this->code;
		$sql = "select a.username, a.disp_name 
				from security$suffix.sec_user a
				where a.city = '$city' 
				and a.status='A' 
				order by a.disp_name
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn[$row['username']] = $row['disp_name'];
		}
		return $rtn;
	}
}