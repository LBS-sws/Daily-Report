<?php

class ComparisonSetForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $comparison_year;
	public $city;
	public $one_gross;
	public $one_net;
	public $two_gross;
	public $two_net;
	public $three_gross;
	public $three_net;
	public $month_type;
	public $cover_bool=0;//是否覆蓋本年之後的所有數據 0：否 1：是

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'city'=>Yii::t('code','city'),
            'one_gross'=>Yii::t('summary','Gross'),
            'one_net'=>Yii::t('summary','Net'),
            'two_gross'=>Yii::t('summary','Gross'),
            'two_net'=>Yii::t('summary','Net'),
            'three_gross'=>Yii::t('summary','Gross'),
            'three_net'=>Yii::t('summary','Net'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,city,cover_bool,month_type,comparison_year,one_gross,one_net,two_gross,two_net,three_gross,three_net','safe'),
			array('city,comparison_year,month_type','required'),
            array('one_gross,one_net,two_gross,two_net,three_gross,three_net','numerical','allowEmpty'=>false,'integerOnly'=>false,'min'=>0),
            array('city','validateCity'),
		);
	}

    public function validateCity($attribute, $params) {
        $row = Yii::app()->db->createCommand()->select("id")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                array(":year"=>$this->comparison_year,":month_type"=>$this->month_type,":city"=>$this->city)
            )->queryRow();
        if($row){
            $this->id = $row["id"];
        }else{
            $this->id = null;
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select * from swo_comparison_set where id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->city = $row['city'];
			$this->comparison_year = $row['comparison_year'];
			$this->month_type = $row['month_type'];
            $this->one_gross = empty($row["one_gross"])?"":floatval($row["one_gross"]);
            $this->one_net = empty($row["one_net"])?"":floatval($row["one_net"]);
            $this->two_gross = empty($row["two_gross"])?"":floatval($row["two_gross"]);
            $this->two_net = empty($row["two_net"])?"":floatval($row["two_net"]);
            $this->three_gross = empty($row["three_gross"])?"":floatval($row["three_gross"]);
            $this->three_net = empty($row["one_net"])?"":floatval($row["three_net"]);
            return true;
		}else{
		    return false;
        }
	}
	
	public function saveData(){
        //id,city,comparison_year,one_gross,one_net,two_gross,two_net,three_gross,three_net
        $arr = array();
        $arr["one_gross"] = empty($this->one_gross)?null:$this->one_gross;
        $arr["one_net"] = empty($this->one_net)?null:$this->one_net;
        $arr["two_gross"] = empty($this->two_gross)?null:$this->two_gross;
        $arr["two_net"] = empty($this->two_net)?null:$this->two_net;
        $arr["three_gross"] = empty($this->three_gross)?null:$this->three_gross;
        $arr["three_net"] = empty($this->three_net)?null:$this->three_net;
        $coverArr = $arr;
        if(!empty($this->id)){
            $arr["luu"] = Yii::app()->user->id;
            Yii::app()->db->createCommand()->update("swo_comparison_set",$arr,"id=:id",
                array(":id"=>$this->id)
            );
        }else{
            $arr["city"] = $this->city;
            $arr["comparison_year"] = $this->comparison_year;
            $arr["month_type"] = $this->month_type;
            $arr["lcu"] = Yii::app()->user->id;
            Yii::app()->db->createCommand()->insert("swo_comparison_set",$arr);
        }
        $this->saveCoverBool($coverArr);
	}

	//覆蓋保存本年以後的數據
	protected function saveCoverBool($arr){
        $arr["city"] = $this->city;
        $arr["comparison_year"] = $this->comparison_year;
        if(!empty($this->cover_bool)){
            $monthList = SummarySetList::getSummaryMonthList();
            foreach ($monthList as $month=>$str){
                if ($month>$this->month_type){
                    $arr["month_type"] = $month;
                    $this->saveCoverForData($arr);
                }
            }
        }
    }

    protected function saveCoverForData($arr){
        $row = Yii::app()->db->createCommand()->select("id")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                array(":year"=>$arr['comparison_year'],":month_type"=>$arr['month_type'],":city"=>$arr['city'])
            )->queryRow();
        if($row){
            $arr["luu"] = Yii::app()->user->id;
            Yii::app()->db->createCommand()->update("swo_comparison_set",$arr,"id=:id",
                array(":id"=>$row['id'])
            );
        }else{
            $arr["lcu"] = Yii::app()->user->id;
            Yii::app()->db->createCommand()->insert("swo_comparison_set",$arr);
        }
    }
}