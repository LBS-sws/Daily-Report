<?php

class ServiceCountForm extends CFormModel
{
	/* User Fields */
    public $cust_type;//查詢客戶類型
    public $status;//查詢狀態
    public $search_year;//查詢年份
    public $city_allow;//查詢城市
    public $data=array();

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'start_date'=>Yii::t('summary','start date'),
            'end_date'=>Yii::t('summary','end date'),
            'day_num'=>Yii::t('summary','day num'),
            'search_type'=>Yii::t('summary','search type'),
            'search_start_date'=>Yii::t('summary','start date'),
            'search_end_date'=>Yii::t('summary','end date'),
            'search_year'=>Yii::t('summary','search year'),
            'search_quarter'=>Yii::t('summary','search quarter'),
            'search_month'=>Yii::t('summary','search month'),
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('search_year,cust_type,status,city_allow','safe'),
            array('search_type,cust_type,status,city_allow','required'),
        );
    }

    public static function getServiceTypeList(){
    }

    public static function getYearList(){
        $year = date("Y");
        $arr = array();
        for ($i=$year-3;$i<=$year+1;$i++){
            $arr[$i] = $i."年";
        }
        return $arr;
    }

    public static function getStatusList(){
        return array(
            "N"=>"新增",
            "C"=>"续约",
            "A"=>"更改",
            "S"=>"暂停",
            "T"=>"终止",
        );
    }

    public function retrieveData() {
        $city_allow = City::model()->getDescendantList($this->city_allow);
        $cstr = $this->city_allow;
        $city_allow .= (empty($city_allow)) ? "'$cstr'" : ",'$cstr'";
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("b.name,sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount")
            ->from("swo_service a")
            ->leftJoin("security{$suffix}.sec_city b","a.city=b.code")
            ->where("a.city in ({$city_allow}) and a.status='{$this->status}' and a.cust_type='{$this->cust_type}' and date_format(a.status_dt,'%Y')={$this->search_year}")
            ->group("b.name")
            ->queryAll();
        if($rows){
            $this->data = $rows;
        }
        return true;
    }

    public function printHtml(){
        $html="";
        if(!empty($this->data)){
            $sum = 0;
            $html="<table class='table table-striped table-hover table-bordered'><thead><tr>";
            $html.="<th>城市</th>";
            $html.="<th>服務年金額</th>";
            $html.="</tr></thead><tbody>";
            foreach ($this->data as $row){
                $sum+=floatval($row["sum_amount"]);
                $html.="<tr>";
                $html.="<td>".$row["name"]."</td>";
                $html.="<td>".$row["sum_amount"]."</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td>汇总</td>";
            $html.="<td>".$sum."</td>";
            $html.="</tr>";
            $html.="</tfoot></table>";
        }
        echo $html;
    }
}