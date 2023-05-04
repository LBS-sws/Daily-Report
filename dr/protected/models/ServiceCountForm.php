<?php

class ServiceCountForm extends CFormModel
{
	/* User Fields */
    public $cust_type;//查詢客戶類型
    public $status;//查詢狀態
    public $search_year;//查詢年份
    public $city_allow;//查詢城市
    public $company_name;//查詢客户名称
    public $data=array();

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'company_name'=>Yii::t('service','Customer'),
            'cust_type'=>Yii::t('service','Customer Type'),
            'status'=>Yii::t('service','Record Type'),
            'search_year'=>Yii::t('Summary','Year'),
            'city_allow'=>Yii::t('code','City'),
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('search_year,cust_type,status,city_allow,company_name','safe'),
            array('search_year,status,city_allow','required'),
        );
    }

    public static function getServiceTypeList(){
        $data = array(""=>"-- 所有 --");
        $rows = Yii::app()->db->createCommand()->select("id,description")
            ->from("swo_customer_type")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $data[$row["id"]] = $row["description"];
            }
        }
        return $data;
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

    public static function getCityList(){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $data = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("code,name")
            ->from("security{$suffix}.sec_city")
            ->where("code in ({$city_allow})")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $data[$row["code"]] = $row["name"];
            }
        }
        return $data;
    }

    public function retrieveData() {
        $city_allow = City::model()->getDescendantList($this->city_allow);
        $cstr = $this->city_allow;
        $city_allow .= (empty($city_allow)) ? "'$cstr'" : ",'$cstr'";
        $suffix = Yii::app()->params['envSuffix'];
        $where="";
        if(!empty($this->cust_type)){
            $svalue = is_numeric($this->cust_type)?intval($this->cust_type):1;
            $where.=" and a.cust_type='{$svalue}'";
        }
        if(!empty($this->company_name)){
            $svalue = str_replace("'","\'",$this->company_name);
            $where.=" and f.name like '%{$svalue}%'";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("b.name,sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount")
            ->from("swo_service a")
            ->leftJoin("swo_company f","a.company_id=f.id")
            ->leftJoin("security{$suffix}.sec_city b","a.city=b.code")
            ->where("a.city!='ZY' {$where} and a.city in ({$city_allow}) and a.status='{$this->status}' and date_format(a.status_dt,'%Y')={$this->search_year}")
            ->group("b.name")
            ->queryAll();
        if($rows){
            $this->data = $rows;
        }
        return true;
    }

    public function printHtml(){
        $html="<table class='table table-striped table-hover table-bordered'><thead><tr>";
        $html.="<th>城市</th>";
        $html.="<th>服務年金額</th>";
        $html.="</tr></thead>";
        if(!empty($this->data)){
            $sum = 0;
            $html.="<tbody>";
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
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='2'>无数据</td></tr></tbody>";
        }
        $html.="</table>";
        echo $html;
    }
}