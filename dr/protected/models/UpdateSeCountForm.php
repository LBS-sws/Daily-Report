<?php

class UpdateSeCountForm extends CFormModel
{
	/* User Fields */
    public $start_date;
    public $end_date;
    public $city;
    public $city_allow;

    public $data=array();
    public $dataTwo=array();
    public $searchType=0;//0:员工查询、1：合约编号查询

	public $th_sum=0;//所有th的个数

    public $downJsonText='';
    public $u_load_data=array();//查询时长数组
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'day_num'=>Yii::t('summary','day num'),
            'start_date'=>Yii::t('summary','start date'),
            'city'=>Yii::t('summary','City'),
            'end_date'=>Yii::t('summary','end date')
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('city,start_date,end_date,day_num','safe'),
            array('start_date,end_date','required'),
            array('end_date','validateDate'),
            array('city','validateCity'),
        );
    }

    public function validateCity($attribute, $params) {
        if(empty($this->city)){
            $this->addError($attribute, "城市不能为空");
        }else{
            if(is_array($this->city)){
                if (count($this->city)>1){
                    $startDate = date("Y/m",strtotime($this->start_date));
                    $endDate = date("Y/m",strtotime($this->end_date));
                    if($startDate!=$endDate){
                        $this->addError($attribute, "无法跨月查询多个城市");
                    }
                }
            }else{
                $this->addError($attribute, "城市查询异常");
            }
        }
    }

    public function validateDate($attribute, $params) {
        if(!empty($this->start_date)&&!empty($this->end_date)){
            if(date("Y/m/d",strtotime($this->start_date))>date("Y/m/d",strtotime($this->end_date))){
                $this->addError($attribute, "查询时间异常");
            }
        }
    }

    public function setCriteria($criteria){
        if (count($criteria) > 0) {
            foreach ($criteria as $k=>$v) {
                $this->$k = $v;
            }
        }
    }

    public function getCriteria() {
        return array(
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'searchType'=>$this->searchType,
            'city'=>$this->city,
        );
    }

    protected function resetCityAllow(){
        if (empty($this->city)){
            $this->city_allow = Yii::app()->user->city_allow();
        }else{
            if(is_array($this->city)){
                $cityAllow = implode("','",$this->city);
            }else{
                $cityAllow = $this->city;
            }
            $this->city_allow = "'{$cityAllow}'";
        }
    }

    public function retrieveData($searchType) {
        $this->u_load_data['load_start'] = time();

        $this->searchType = $searchType;
        $this->resetCityAllow();

        if(empty($this->searchType)){
            $this->data = $this->getDataForStaff();
        }else{
            $this->data = $this->getDataForContract();
        }
        $session = Yii::app()->session;
        $session['updateSeCount_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    private function getDataForStaff(){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = $this->city_allow;
        $startDate = date("Y/m/d",strtotime($this->start_date));
        $endDate = date("Y/m/d",strtotime($this->end_date));
        $tempData = $this->defMoreCity();
        $data = array();
        //swo_service_history
        $rows = Yii::app()->db->createCommand()->select("a.lcu,
            sum(IF(a.update_type=1,1,0)) as staff_update_num,
            sum(IF(a.update_type=1,IFNULL(a.change_amt,0),0)) as staff_update_amt,
            sum(IF(a.update_type=1,0,1)) as staff_add_num,
            sum(IF(a.update_type=1,0,IFNULL(a.change_amt,0))) as staff_add_amt
            ")
            ->from("swo_service_history a")
            ->leftJoin("security{$suffix}.sec_user b","a.lcu=b.username")
            ->where("a.service_type in (1,3) and DATE_FORMAT(a.lcd,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}' and b.city in ({$city_allow})")
            ->group("a.lcu")
            ->queryAll();
        $delRows =$this->getDelRows($startDate,$endDate);
        if($rows){
            foreach ($rows as $row){
                $tempList = $tempData;
                $userList = $this->getUserListByUsername($row["lcu"]);
                $staffList = $this->getStaffListByUsername($row["lcu"]);
                $cityCode = in_array($row["lcu"],array("updateAdmin","admin","Admin"))?"none":$userList["city"];
                $tempList["city"]=$userList["city"];
                $tempList["city_name"]=$userList["city_name"];
                $tempList["username"]=$userList["username"];
                $tempList["disp_name"]=$userList["disp_name"];
                $tempList["employee_str"]=$staffList["employee_str"];
                $tempList["employee_dept"]=$staffList["employee_dept"];
                $tempList["staff_upt_num"]=$row["staff_update_num"];
                $tempList["staff_add_num"]=$row["staff_add_num"];
                $tempList["staff_upt_amt"]=$row["staff_update_amt"];
                $tempList["staff_add_amt"]=$row["staff_add_amt"];
                if(key_exists($row["lcu"],$delRows)){
                    $tempList["staff_del_num"]=$delRows[$row["lcu"]]["staff_del_num"];
                    $tempList["staff_del_amt"]=$delRows[$row["lcu"]]["staff_del_amt"];
                    unset($delRows[$row["lcu"]]);
                }
                if(!key_exists($cityCode,$data)){
                    $data[$cityCode]=array("city"=>$cityCode,"city_name"=>$userList["city_name"],"list"=>array());
                }
                $data[$cityCode]["list"][]=$tempList;
            }
        }
        if(!empty($delRows)){
            foreach ($delRows as $delUser=>$delRow){
                $tempList = $tempData;
                $userList = $this->getUserListByUsername($delUser);
                $staffList = $this->getStaffListByUsername($delUser);
                $cityCode = $delUser=="updateAdmin"?"none":$userList["city"];
                $tempList["city"]=$userList["city"];
                $tempList["city_name"]=$userList["city_name"];
                $tempList["username"]=$userList["username"];
                $tempList["disp_name"]=$userList["disp_name"];
                $tempList["employee_str"]=$staffList["employee_str"];
                $tempList["employee_dept"]=$staffList["employee_dept"];
                $tempList["staff_del_num"]=$delRow["staff_del_num"];
                $tempList["staff_del_amt"]=$delRow["staff_del_amt"];
                if(!key_exists($cityCode,$data)){
                    $data[$cityCode]=array("city"=>$cityCode,"city_name"=>$userList["city_name"],"list"=>array());
                }
                $data[$cityCode]["list"][]=$tempList;
            }
        }

        if(isset($data["none"])){
            $noneData = $data["none"];
            unset($data["none"]);
            $data["end"]=$noneData;
        }

        return $data;
    }

    private function getDelRows($startDate,$endDate){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = $this->city_allow;
        $rows = Yii::app()->db->createCommand()->select("a.log_user,
            count(a.id) as staff_del_num,
            sum(IFNULL(a.change_amt,0)) as staff_del_amt
            ") ->from("swo_system_log a")
            ->leftJoin("security{$suffix}.sec_user b","a.log_user=b.username")
            ->where("DATE_FORMAT(a.log_date,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'
             and a.log_type in ('ServiceForm','ServiceKAForm')
             and b.city in ({$city_allow})
             ")
            ->group("a.log_user")
            ->queryAll();
        $list = array();
        if($rows){
            foreach ($rows as $row){
                $list[$row["log_user"]]=array(
                    "staff_del_num"=>$row["staff_del_num"],
                    "staff_del_amt"=>$row["staff_del_amt"],
                );
            }
        }
        return $list;
    }

    public static function getUserListByUsername($username){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("b.name as city_name,a.city,a.username,a.disp_name")
            ->from("security{$suffix}.sec_user a")
            ->leftJoin("security{$suffix}.sec_city b","a.city=b.code")
            ->where("a.username=:username",array(":username"=>$username))
            ->queryRow();
        if($row){
            return $row;
        }else{
            return array("city_name"=>"","city"=>"none","username"=>$username,"disp_name"=>"");
        }
    }

    private function getStaffListByUsername($username){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()
            ->select("CONCAT(b.name,' (',b.code,')') as employee_str,f.name as employee_dept")
            ->from("hr{$suffix}.hr_binding a")
            ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
            ->leftJoin("hr{$suffix}.hr_dept f","f.id=b.position")
            ->where("a.user_id=:username",array(":username"=>$username))
            ->queryRow();
        if($row){
            return $row;
        }else{
            return array("employee_str"=>"","employee_dept"=>"");
        }
    }

    private function getAdminStr(){
        return "'admin'";//,'updateAdmin'
    }
    private function getDataForContract(){
        $startDate = date("Y/m/d",strtotime($this->start_date));
        $endDate = date("Y/m/d",strtotime($this->end_date));
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = $this->city_allow;
        $tempData = $this->defMoreCity();
        //$adminUser = $this->getAdminStr();
        $whereSql = "and DATE_FORMAT(a.lcd,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}' and b.city in ({$city_allow})";
        $data = array();
        $oneRows = Yii::app()->db->createCommand()->select("a.service_type,a.service_id,
            sum(IF(a.update_type=1,1,0)) as con_update_num,
            sum(IF(a.update_type=1,IFNULL(a.change_amt,0),0)) as con_update_amt,
            sum(IF(a.update_type=1,0,1)) as con_add_num,
            sum(IF(a.update_type=1,0,IFNULL(a.change_amt,0))) as con_add_amt,
            CONCAT(0) as con_del_num,CONCAT(0) as con_del_amt
            ")
            ->from("swo_service_history a")
            ->leftJoin("swo_service b","a.service_id=b.id")
            ->where("a.service_type=1 and b.id is not null {$whereSql}")
            ->group("a.service_type,a.service_id")
            ->order("b.city,a.service_type asc,b.company_id desc,b.cust_type desc,b.status_dt desc")
            ->queryAll();
        $oneRows= $oneRows?$oneRows:array();
        $twoRows = Yii::app()->db->createCommand()->select("a.service_type,a.service_id,
            sum(IF(a.update_type=1,1,0)) as con_update_num,
            sum(IF(a.update_type=1,IFNULL(a.change_amt,0),0)) as con_update_amt,
            sum(IF(a.update_type=1,0,1)) as con_add_num,
            sum(IF(a.update_type=1,0,IFNULL(a.change_amt,0))) as con_add_amt,
            CONCAT(0) as con_del_num,CONCAT(0) as con_del_amt
            ")
            ->from("swo_service_history a")
            ->leftJoin("swo_service_ka b","a.service_id=b.id")
            ->where("a.service_type=3 and b.id is not null {$whereSql}")
            ->group("a.service_type,a.service_id")
            ->order("b.city,a.service_type asc,b.company_id desc,b.cust_type desc,b.status_dt desc")
            ->queryAll();
        $twoRows= $twoRows?$twoRows:array();
        $delRows = Yii::app()->db->createCommand()
            ->select("a.id,a.id as service_id,a.log_user as lcu,a.option_text as update_html,a.lcd,
            IF(a.log_type='ServiceForm',1,3) as service_type,a.change_amt as con_del_amt,
            CONCAT(-1) as service_id,CONCAT(0) as con_update_num,CONCAT(0) as con_update_amt,
            CONCAT(0) as con_add_num,CONCAT(0) as con_add_amt,CONCAT(1) as con_del_num
            ")
            ->from("swo_system_log a")
            ->where("DATE_FORMAT(a.log_date,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'
             and a.log_type in ('ServiceForm','ServiceKAForm')
             and a.city in ({$city_allow})
             ")
            ->order("a.log_type,a.lcd desc")
            ->queryAll();
        $delRows= $delRows?$delRows:array();
        $rows = array_merge($oneRows,$twoRows,$delRows);
        if($rows){
            foreach ($rows as $row){
                $tempList = $tempData;
                $serviceRow = $this->getServiceRow($row);
                $cityCode = $serviceRow["city"];
                $tempList["id"]=isset($row["id"])?$row["id"]:$row["service_id"];
                $tempList["city"]=$serviceRow["city"];
                $tempList["city_name"]=$serviceRow["city_name"];
                $tempList["service_id"]=$row["service_id"];
                $tempList["table_type"]=$row["service_type"];
                $tempList["system_form"]=$serviceRow["system_form"];
                $tempList["status"]=$serviceRow["status"];
                $tempList["status_dt"]=$serviceRow["status_dt"];
                $tempList["company_str"]=$serviceRow["company_str"];
                $tempList["cust_type"]=$serviceRow["cust_type_str"];
                $tempList["con_upt_num"]=$row["con_update_num"];
                $tempList["con_add_num"]=$row["con_add_num"];
                $tempList["con_del_num"]=$row["con_del_num"];
                $tempList["con_upt_amt"]=$row["con_update_amt"];
                $tempList["con_add_amt"]=$row["con_add_amt"];
                $tempList["con_del_amt"]=$row["con_del_amt"];
                if(isset($serviceRow["noneBool"])&&$serviceRow["noneBool"]==1){//删除的合约需要额外查找
                    $tempList["noneBool"]=1;
                    $row["service_id"]=self::getServiceIDForRow($row);
                    $UANumList = $this->getUANumByContract($row);
                    $tempList["service_id"]=$row["service_id"];
                    $tempList["con_upt_num"]+=$UANumList["con_update_num"];
                    $tempList["con_add_num"]+=$UANumList["con_add_num"];
                    $tempList["con_upt_amt"]+=$UANumList["con_update_amt"];
                    $tempList["con_add_amt"]+=$UANumList["con_add_amt"];
                }
                if(!key_exists($cityCode,$data)){
                    $data[$cityCode]=array("city"=>$cityCode,"city_name"=>$serviceRow["city_name"],"list"=>array());
                }
                $data[$cityCode]["list"][]=$tempList;
            }
        }

        return$data;
    }

    private function getUANumByContract($row){
        //$adminUser = $this->getAdminStr();
        //由于删除的客户服务无法跳转到详情，所以查询全部
        $list = Yii::app()->db->createCommand()
            ->select("
            sum(IF(update_type=1,1,0)) as con_update_num,
            sum(IF(update_type=1,IFNULL(change_amt,0),0)) as con_update_amt,
            sum(IF(update_type=1,0,1)) as con_add_num,
            sum(IF(update_type=1,0,IFNULL(change_amt,0))) as con_add_amt
            ")
            ->from("swo_service_history")
            ->where("service_type=:service_type and service_id=:service_id",array(
                ":service_type"=>$row["service_type"],
                ":service_id"=>$row["service_id"],
            ))->queryRow();
        return $list?$list:array("con_update_num"=>0,"con_update_amt"=>0,"con_add_num"=>0,"con_add_amt"=>0);
    }

    public static function getServiceRow($row){
        $suffix = Yii::app()->params['envSuffix'];
        if($row["service_type"]==1){
            $list = Yii::app()->db->createCommand()
                ->select("a.id,a.city,g.name as city_name,CONCAT('1') as table_type,CONCAT('客户服务') as system_form,
                a.status,a.status_dt,CONCAT(b.code,b.name) as company_str,
                f.description as cust_type_str")
                ->from("swo_service a")
                ->leftJoin("swo_company b","a.company_id=b.id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("security{$suffix}.sec_city g","a.city=g.code")
                ->where("a.id=:id",array(":id"=>$row["service_id"]))
                ->queryRow();
        }else{
            $list = Yii::app()->db->createCommand()
                ->select("a.id,a.city,g.name as city_name,CONCAT('3') as table_type,CONCAT('KA客户服务') as system_form,
                a.status,a.status_dt,CONCAT(b.code,b.name) as company_str,
                f.description as cust_type_str")
                ->from("swo_service_ka a")
                ->leftJoin("swo_company b","a.company_id=b.id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("security{$suffix}.sec_city g","a.city=g.code")
                ->where("a.id=:id",array(":id"=>$row["service_id"]))
                ->queryRow();
        }
        if($list){
            $list["status_dt"]=General::toDate($list["status_dt"]);
            $list["status"]=GetNameToId::getServiceStatusForKey($list["status"]);
            return $list;
        }else{
            return array(
                "id"=>$row["service_id"],
                "table_type"=>5,//1：客户服务 3：KA客户服务 5：已删除
                "system_form"=>$row["service_type"]==1?"客户服务":"KA客户服务",
                "city"=>"未知",
                "city_name"=>"未知",
                "status"=>"已删除",
                "status_dt"=>"已删除",
                "company_str"=>"已删除",
                "cust_type_str"=>"已删除",
                "noneBool"=>"1",
            );
        }
    }

    public static function getServiceIDForRow($row){
        if($row["service_id"]==-1&&isset($row["update_html"])){
            $list = explode("<br/>",$row["update_html"]);
            $list = current($list);
            $list = explode("：",$list);
            return end($list);
        }else{
            return $row["service_id"];
        }
    }

    private function defMoreCity(){
        if(empty($this->searchType)){
            return array(
                "id"=>"",//
                "city"=>"",//城市
                "city_name"=>"",//账号城市
                "username"=>"",//账号
                "disp_name"=>"",//昵称
                "employee_str"=>"",//绑定员工
                "employee_dept"=>"",//员工职位
                "staff_add_num"=>0,//新增次数
                "staff_upt_num"=>0,//修改次数
                "staff_del_num"=>0,//删除次数
                "staff_add_amt"=>0,//新增次数
                "staff_upt_amt"=>0,//修改次数
                "staff_del_amt"=>0,//删除次数
                "noneBool"=>0,//是否已删除 0：未删除
            );
        }else{
            return array(
                "id"=>"",//
                "city"=>"",//城市
                "city_name"=>"",//城市
                "system_form"=>"",//系统来源
                "service_id"=>"",//合约ID
                "table_type"=>1,//1:客户服务 3：KA客户服务
                "status"=>"",//合约状态
                "status_dt"=>"",//合约时间
                "company_str"=>"",//客户编号及名称
                "cust_type"=>"",//客户类别
                //"system_num"=>"",//系统操作次数
                //"employee_num"=>"",//员工操作次数
                "con_add_num"=>0,//新增次数
                "con_upt_num"=>0,//修改次数
                "con_del_num"=>0,//删除次数
                "con_add_amt"=>0,//新增次数
                "con_upt_amt"=>0,//修改次数
                "con_del_amt"=>0,//删除次数
                "noneBool"=>0,//是否已删除 0：未删除
            );
        }
    }

    protected function resetTdRow(&$list,$bool=false){
    }

    //顯示提成表的表格內容
    public function updateSeCountHtml(){
        $html= '<table id="updateSeCount" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        if(empty($this->searchType)){
            return array(
                array(
                    "name"=>Yii::t("summary","City"),//城市
                ),
                array(
                    "name"=>Yii::t("summary","username"),//账号
                ),
                array(
                    "name"=>Yii::t("summary","disp_name"),//昵称
                ),
                array(
                    "name"=>Yii::t("summary","binding employee"),//绑定员工
                ),
                array(
                    "name"=>Yii::t("summary","dept name"),//员工职位
                ),
                array(
                    "name"=>Yii::t("summary","add num"),//新增次数
                ),
                array(
                    "name"=>Yii::t("summary","update num"),//修改次数
                ),
                array(
                    "name"=>Yii::t("summary","delete num"),//删除次数
                ),
                array(
                    "name"=>Yii::t("summary","add amt"),//新增金额
                ),
                array(
                    "name"=>Yii::t("summary","update amt"),//修改金额
                ),
                array(
                    "name"=>Yii::t("summary","delete amt"),//删除金额
                ),
            );
        }else{
            return array(
                array(
                    "name"=>Yii::t("summary","City"),//城市
                ),
                array(
                    "name"=>Yii::t("summary","System Form"),//系统来源
                ),
                array(
                    "name"=>Yii::t("summary","Service ID"),//合约ID
                ),
                array(
                    "name"=>Yii::t("service","Customer"),//客户编号及名称
                ),
                array(
                    "name"=>Yii::t("service","Customer Type"),//客户类别
                ),
                array(
                    "name"=>Yii::t("summary","service date"),//合约时间
                ),
                array(
                    "name"=>Yii::t("summary","Service Status"),//合约状态
                ),
                /*
                array(
                    "name"=>Yii::t("summary","system update num"),//系统操作次数
                ),
                array(
                    "name"=>Yii::t("summary","staff update num"),//员工操作次数
                ),
                */
                array(
                    "name"=>Yii::t("summary","add num"),//新增次数
                ),
                array(
                    "name"=>Yii::t("summary","update num"),//修改次数
                ),
                array(
                    "name"=>Yii::t("summary","delete num"),//删除次数
                ),
                array(
                    "name"=>Yii::t("summary","add amt"),//新增金额
                ),
                array(
                    "name"=>Yii::t("summary","update amt"),//修改金额
                ),
                array(
                    "name"=>Yii::t("summary","delete amt"),//删除金额
                ),
            );
        }
    }

    public static function clickList(){
        return array(
            "staff_add_num"=>array("title"=>Yii::t("summary","add num"),"type"=>"StaffAdd"),
            "staff_upt_num"=>array("title"=>Yii::t("summary","update num"),"type"=>"StaffUpdate"),
            "staff_del_num"=>array("title"=>Yii::t("summary","delete num"),"type"=>"StaffDelete"),
            "staff_add_amt"=>array("title"=>Yii::t("summary","add num"),"type"=>"StaffAdd"),
            "staff_upt_amt"=>array("title"=>Yii::t("summary","update num"),"type"=>"StaffUpdate"),
            "staff_del_amt"=>array("title"=>Yii::t("summary","delete num"),"type"=>"StaffDelete"),
            "con_add_num"=>array("title"=>Yii::t("summary","add num"),"type"=>"ContractAdd"),
            "con_upt_num"=>array("title"=>Yii::t("summary","update num"),"type"=>"ContractUpdate"),
            "con_del_num"=>array("title"=>Yii::t("summary","delete num"),"type"=>"ContractDelete"),
            "con_add_amt"=>array("title"=>Yii::t("summary","add num"),"type"=>"ContractAdd"),
            "con_upt_amt"=>array("title"=>Yii::t("summary","update num"),"type"=>"ContractUpdate"),
            "con_del_amt"=>array("title"=>Yii::t("summary","delete num"),"type"=>"ContractDelete"),
            //"system_num"=>array("title"=>Yii::t("summary","contract update Count"),"type"=>"ContractChange"),
            //"employee_num"=>array("title"=>Yii::t("summary","contract update Count"),"type"=>"ContractChange"),
        );
    }

    private function tdClick(&$tdClass,$keyStr,$row){
        $expr= " ";
        if(empty($this->searchType)){
            $expr.= " data-table='0' data-search='{$row['username']}' data-titleexp='{$row['username']}'";
        }else{
            $searchID = in_array($keyStr,array("con_del_num","con_del_amt"))?$row['id']:$row['service_id'];
            $expr.= " data-table='{$row['table_type']}' data-search='{$searchID}' data-titleexp='{$row['system_form']}_{$row['service_id']}'";
        }
        $list = $this->clickList();
        if(key_exists($keyStr,$list)){
            $tdClass.=" td_detail";
            $expr.= " data-type='{$list[$keyStr]['type']}'";
            $expr.= " data-title='{$list[$keyStr]['title']}'";
        }

        return $expr;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
        $this->th_sum = 0;
        $topList = self::getTopArr();
        $trOne="";
        $trTwo="";
        $html="<thead>";
        foreach ($topList as $list){
            $clickName=$list["name"];
            $colList=key_exists("colspan",$list)?$list['colspan']:array();
            $style = "";
            $colNum=0;
            if(key_exists("background",$list)){
                $style.="background:{$list["background"]};";
            }
            if(key_exists("color",$list)){
                $style.="color:{$list["color"]};";
            }
            if(!empty($colList)){
                foreach ($colList as $col){
                    $colNum++;
                    $trTwo.="<th style='{$style}'><span>".$col["name"]."</span></th>";
                    $this->th_sum++;
                }
            }else{
                $this->th_sum++;
            }
            $colNum = empty($colNum)?1:$colNum;
            $trOne.="<th style='{$style}' colspan='{$colNum}'";
            if($colNum>1){
                $trOne.=" class='click-th'";
            }
            if(key_exists("rowspan",$list)){
                $trOne.=" rowspan='{$list["rowspan"]}'";
            }
            if(key_exists("startKey",$list)){
                $trOne.=" data-key='{$list['startKey']}'";
            }
            $trOne.=" ><span>".$clickName."</span></th>";
        }
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
        $html.="<tr>{$trOne}</tr>";
        if(empty($trTwo)){
            $html.="<tr>{$trTwo}</tr>";
        }
        $html.="</thead>";
        return $html;
    }

    //設置表格的單元格寬度
    private function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            $width=90;
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        if(empty($this->searchType)){
            $bodyKey = array(
                "city_name","username","disp_name","employee_str","employee_dept",
                "staff_add_num","staff_upt_num","staff_del_num",
                "staff_add_amt","staff_upt_amt","staff_del_amt",
            );
        }else{
            $bodyKey = array(
                "city_name","system_form","service_id","company_str","cust_type",
                "status_dt","status",
                "con_add_num","con_upt_num","con_del_num",
                "con_add_amt","con_upt_amt","con_del_amt",
            );
        }
        return $bodyKey;
    }

    //
    private function getTdClassForColAndRow($keyStr,$cityList){
        $tdClass = "";
        if(in_array($keyStr,array("city_name","system_form","service_id","company_str","cust_type"))){
            $tdClass.= " searchText";
        }
        return $tdClass;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        $pageMax = 100;//分页最大数量
        if(!empty($data)){
            $cityKey=0;
            foreach ($data as $region=>$regionList){
                if(isset($regionList["list"])){
                    foreach ($regionList["list"] as $cityList){
                        $cityKey++;
                        $trClass= "pageTr";
                        $trClass.= $cityKey>$pageMax?" hide":"";
                        $this->resetTdRow($cityList);
                        $html.="<tr class='{$trClass}'>";
                        foreach ($bodyKey as $keyStr){
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            $tdClass = $this->getTdClassForColAndRow($keyStr,$cityList);
                            $dataClick=self::tdClick($tdClass,$keyStr,$cityList);//点击后弹窗详细内容
                            $link = self::getLink($keyStr,$cityList);
                            $this->downJsonText["excel"][$cityKey][$keyStr]=$text;
                            $html.="<td class='{$tdClass}' {$dataClick}><span>{$text}</span>{$link}</td>";
                        }
                        $html.="</tr>";
                    }
                }
            }
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            if($cityKey>$pageMax){//开始分页
                $pageCount = ceil($cityKey/$pageMax);
                $pageList = array();
                for ($i=1;$i<=$pageCount;$i++){
                    $class = "clickPage";
                    $class.=$i==1?" active":"";
                    $pageList[]=array(
                        "htmlOptions"=>array("class"=>$class,"data-page"=>$i,"data-max"=>$pageMax),
                        "label"=>$i,
                        "url"=>"javascript:void(0);",
                    );
                }
                $html.="<tr class='tr-end'>";
                $html.="<td colspan='2'>总共{$cityKey}条记录</td>";
                $html.="<td colspan='".($this->th_sum-2)."'>";
                $html.=TbHtml::pagination($pageList,array("class"=>"pagination-sm no-margin","id"=>"paginationID"));
                $html.="</td></tr>";
            }
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    private function getLink($keyStr,$row){
        if($keyStr=="service_id"&&empty($row["noneBool"])){
            if($row["table_type"]==1){
                $url = Yii::app()->createUrl('service/view',array("index"=>$row["service_id"]));
            }else{
                $url = Yii::app()->createUrl('serviceKA/view',array("index"=>$row["service_id"]));
            }
            return TbHtml::link("查看",$url,array("target"=>"_black"));
        }else{
            return "";
        }
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $this->downJsonText["excel"][$data['city_name']]["count"][]=$text;
            $html.="<td style='font-weight: bold'><span>{$text}</span></td>";
        }
        $html.="</tr>";
        return $html;
    }

    public function tableFooterHtml(){
        $html="<tfoot>";
        $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        $html.="</tfoot>";
        return $html;
    }

    //下載
    public function downExcel($excelData){
        if(!is_array($excelData)){
            $excelData = json_decode($excelData,true);
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->SetHeaderTitle(Yii::t("app","Update Service Count")."（".$this->start_date." ~ ".$this->end_date."）");
        if(empty($this->searchType)){
            $titleTwo = Yii::t('summary','staff update Count');
        }else{
            $titleTwo = Yii::t('summary','contract update Count');
        }
        $excel->colTwo=0;
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setUServiceHeader($headList);
        $excel->setUServiceData($excelData);
        $excel->outExcel($titleTwo);
    }
}