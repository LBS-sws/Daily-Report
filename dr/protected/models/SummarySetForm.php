<?php

class SummarySetForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $summary_year;
	public $city;
	public $one_gross;
	public $one_net;
	public $two_gross;
	public $two_net;
	public $three_gross;
	public $three_net;

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
            array('id,city,summary_year,one_gross,one_net,two_gross,two_net,three_gross,three_net','safe'),
			array('city,summary_year','required'),
            array('one_gross,one_net,two_gross,two_net,three_gross,three_net','numerical','allowEmpty'=>false,'integerOnly'=>false,'min'=>0),
            array('city','validateCity'),
		);
	}

    public function validateCity($attribute, $params) {
        $row = Yii::app()->db->createCommand()->select("id")->from("swo_summary_set")
            ->where("summary_year=:year and city=:city",
                array(":year"=>$this->summary_year,":city"=>$this->city)
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
		$sql = "select * from swo_summary_set where id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->city = $row['city'];
			$this->summary_year = $row['summary_year'];
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
        //id,city,summary_year,one_gross,one_net,two_gross,two_net,three_gross,three_net
        $arr = array();
        $arr["one_gross"] = empty($this->one_gross)?null:$this->one_gross;
        $arr["one_net"] = empty($this->one_net)?null:$this->one_net;
        $arr["two_gross"] = empty($this->two_gross)?null:$this->two_gross;
        $arr["two_net"] = empty($this->two_net)?null:$this->two_net;
        $arr["three_gross"] = empty($this->three_gross)?null:$this->three_gross;
        $arr["three_net"] = empty($this->three_net)?null:$this->three_net;
        if(!empty($this->id)){
            $arr["lcu"] = Yii::app()->user->id;
            Yii::app()->db->createCommand()->update("swo_summary_set",$arr,"id=:id",
                array(":id"=>$this->id)
            );
        }else{
            $arr["city"] = $this->city;
            $arr["summary_year"] = $this->summary_year;
            $arr["luu"] = Yii::app()->user->id;
            Yii::app()->db->createCommand()->insert("swo_summary_set",$arr);
        }
	}
}