<?php

class ManageStaffSetForm extends CFormModel
{
    /* User Fields */
    public $id;
    public $start_date;
    public $employee_id;
    public $employee_name;
    public $city;
    public $city_allow=array();
    public $job_key;
    public $team_rate;
    public $person_type;
    public $person_money;
    public $condition_type;
    public $condition_money;
    public $max_bonus=4000;
    public $z_index=1;
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'start_date'=>Yii::t('summary','Effective date'),
            'employee_id'=>Yii::t('summary','Employee Name'),
            'employee_code'=>Yii::t('summary','employee code'),
            'employee_name'=>Yii::t('summary','Employee Name'),
            'city'=>Yii::t('summary','bonus city'),
            'city_allow'=>Yii::t('summary','bonus city'),
            'job_key'=>Yii::t('summary','bonus position'),
            'condition_type'=>Yii::t('summary','bonus condition type'),
            'condition_money'=>Yii::t('summary','min new money'),
            'person_type'=>Yii::t('summary','person take type'),
            'person_money'=>Yii::t('summary','person take money'),
            'team_rate'=>Yii::t('summary','team take rate'),
            'max_bonus'=>Yii::t('summary','max bonus'),
            'z_index'=>Yii::t('summary','z index'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('start_date,employee_id,city,city_allow,job_key','required'),
            array('id,start_date,employee_id,employee_name,city,city_allow,job_key,team_rate,person_type,person_money,
            condition_type,condition_money,max_bonus,z_index','safe'),
        );
    }

    public function retrieveData($index)
    {
        $sql = "select * from swo_manage_staff where id=".$index."";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row){
            $this->id = $row['id'];
            $this->start_date = General::toDate($row['start_date']);
            $this->employee_id = $row['employee_id'];
            $this->employee_name = GetNameToId::getEmployeeNameForId($row['employee_id']);
            $this->city = $row['city'];
            $this->city_allow = empty($row['city_allow'])?array():explode(",",$row['city_allow']);
            $this->job_key = $row['job_key'];
            $this->team_rate = floatval($row['team_rate']);
            $this->person_type = $row['person_type'];
            $this->person_money = floatval($row['person_money']);
            $this->condition_type = $row['condition_type'];
            $this->condition_money = floatval($row['condition_money']);
            $this->max_bonus = floatval($row['max_bonus']);
            $this->z_index = $row['z_index'];
            return true;
        }
        return false;
    }

    public static function getStaffAndCityListForCityAllow($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $whereSql = " a.city in ({$city_allow})";
        if(empty($city_allow)){
            $whereSql = "1=1";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.*,b.code as employee_code,b.name as employee_name")
            ->from("swo_manage_staff a")
            ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
            ->where($whereSql)->order("a.z_index desc,a.id")->queryAll();
        $list = array("staffRow"=>array(),"cityAllow"=>array());
        if($rows){
            foreach ($rows as $row){
                $row["dept_name"] = self::getJobStrForKey($row["job_key"]);
                $list["staffRow"][]=$row;
                if(!key_exists($row["city"],$list["cityAllow"])){
                    $list["cityAllow"][]=$row["city"];
                }
            }
        }
        return $list;
    }

    public static function getJobStrForKey($job_key){
        $job_key="".$job_key;
        $list = self::getJobList()["list"];
        if(key_exists($job_key,$list)){
            return $list[$job_key];
        }else{
            return $job_key;
        }
    }

    public static function getJobList(){
        $arr=array("list"=>array(
            ''=>'',
            '1'=>Yii::t("summary","deputy director"),//副总监
            '2'=>Yii::t("summary","Senior General Manager"),//高级总经理
            '3'=>Yii::t("summary","General Manager of First tier Cities"),//一线城市总经理
            '4'=>Yii::t("summary","General Manager of Non First tier Cities"),//非一线城市总经理
            '5'=>Yii::t("summary","Regional Director"),//地区主管
            '6'=>Yii::t("summary","Deputy Director (Frontline)"),//副总监（一线）
        ),"options"=>array(
            '1'=>array("data-team_rate"=>0.6,"data-person_type"=>1,"data-person_money"=>0,"data-condition_type"=>1,"data-condition_money"=>0,"data-max_bonus"=>4000,),
            '2'=>array("data-team_rate"=>0.6,"data-person_type"=>1,"data-person_money"=>0,"data-condition_type"=>1,"data-condition_money"=>0,"data-max_bonus"=>4000,),
            '3'=>array("data-team_rate"=>2,"data-person_type"=>1,"data-person_money"=>0,"data-condition_type"=>1,"data-condition_money"=>180000,"data-max_bonus"=>4000,),
            '4'=>array("data-team_rate"=>3,"data-person_type"=>1,"data-person_money"=>0,"data-condition_type"=>1,"data-condition_money"=>60000,"data-max_bonus"=>4000,),
            '5'=>array("data-team_rate"=>3,"data-person_type"=>2,"data-person_money"=>0,"data-condition_type"=>1,"data-condition_money"=>60000,"data-max_bonus"=>4000,),
            '6'=>array("data-team_rate"=>1.2,"data-person_type"=>1,"data-person_money"=>0,"data-condition_type"=>1,"data-condition_money"=>180000,"data-max_bonus"=>4000,),
        ));
        return $arr;
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
            var_dump($e);
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update.');
        }
    }

    protected function saveUser(&$connection)
    {
        $sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from swo_manage_staff where id = :id";
                break;
            case 'new':
                $sql = "insert into swo_manage_staff(
						start_date, employee_id, city, job_key, team_rate, person_type, person_money,
						 condition_type,condition_money,max_bonus,z_index,city_allow,city_allow_name,luu, lcu) values (
						:start_date,:employee_id,:city,:job_key,:team_rate,:person_type,:person_money,
						:condition_type,:condition_money,:max_bonus,:z_index,:allow_city,:name_allow_city, :luu, :lcu)";
                break;
            case 'edit':
                $sql = "update swo_manage_staff set 
					start_date = :start_date, 
					employee_id = :employee_id,
					city_allow = :allow_city,
					city_allow_name = :name_allow_city,
					city = :city,
					job_key = :job_key,
					team_rate = :team_rate,
					person_type = :person_type,
					person_money = :person_money,
					condition_type = :condition_type,
					condition_money = :condition_money,
					max_bonus = :max_bonus,
					z_index = :z_index,
					luu = :luu
					where id = :id";
                break;
        }

        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':start_date')!==false)
            $command->bindParam(':start_date',$this->start_date,PDO::PARAM_STR);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_INT);
        if (strpos($sql,':job_key')!==false)
            $command->bindParam(':job_key',$this->job_key,PDO::PARAM_INT);
        if (strpos($sql,':team_rate')!==false){
            $this->team_rate = empty($this->team_rate)?0:$this->team_rate;
            $command->bindParam(':team_rate',$this->team_rate,PDO::PARAM_INT);
        }
        if (strpos($sql,':allow_city')!==false){
            $city_allow = empty($this->city_allow)?null:implode(",",$this->city_allow);
            $command->bindParam(':allow_city',$city_allow,PDO::PARAM_INT);
        }
        if (strpos($sql,':name_allow_city')!==false){
            $city_allow_name = empty($this->city_allow)?null:$this->getCityAllowNameForCityArr($this->city_allow);
            $command->bindParam(':name_allow_city',$city_allow_name,PDO::PARAM_INT);
        }
        if (strpos($sql,':person_type')!==false){
            $this->person_type = empty($this->person_type)?0:$this->person_type;
            $command->bindParam(':person_type',$this->person_type,PDO::PARAM_INT);
        }
        if (strpos($sql,':person_money')!==false){
            $this->person_money = empty($this->person_money)?0:$this->person_money;
            $command->bindParam(':person_money',$this->person_money,PDO::PARAM_INT);
        }
        if (strpos($sql,':condition_type')!==false){
            $this->condition_type = empty($this->condition_type)?0:$this->condition_type;
            $command->bindParam(':condition_type',$this->condition_type,PDO::PARAM_INT);
        }
        if (strpos($sql,':condition_money')!==false){
            $this->condition_money = empty($this->condition_money)?0:$this->condition_money;
            $command->bindParam(':condition_money',$this->condition_money,PDO::PARAM_INT);
        }
        if (strpos($sql,':max_bonus')!==false){
            $this->max_bonus = empty($this->max_bonus)?0:$this->max_bonus;
            $command->bindParam(':max_bonus',$this->max_bonus,PDO::PARAM_INT);
        }
        if (strpos($sql,':z_index')!==false){
            $this->z_index = empty($this->z_index)?0:$this->z_index;
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
        }
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();
        return true;
    }

    private function getCityAllowNameForCityArr($city_arr){
        $cityListStr = "'".implode("','",$city_arr)."'";
        $suffix = Yii::app()->params['envSuffix'];
        $whereSql = "code in ({$cityListStr})";
        $rows = Yii::app()->db->createCommand()
            ->select("name")
            ->from("security{$suffix}.sec_city")
            ->where($whereSql)->queryAll();
        $cityName=array();
        if($rows){
            foreach ($rows as $row){
                $cityName[]=$row["name"];
            }
        }
        return implode(",",$cityName);
    }

    public function isOccupied($index) {
        return true;
    }

    public function isReadOnly() {
        return ($this->scenario=='view');
    }
}
