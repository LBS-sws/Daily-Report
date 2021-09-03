<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class ServiceIDForm extends CFormModel
{
    /* User Fields */
    public $id;
    public $service_new_id;//新增的id
    public $service_no;//服务编号
    public $company_id;//客户id
    public $company_name;//客户名称
    public $nature_type;//性质
    public $cust_type;//客户类型（一级栏位）
    public $cust_type_name;//客户类型（二级栏位）
    public $cust_type_three;//客户类型（三级栏位）
    public $cust_type_four;//客户类型（四级栏位）
    public $cust_type_end;//机器型号（最后的栏位）
    public $pieces=0;//机器数量
    public $product_id;//服务內容id
    public $service;//服务內容
    public $pay_week;//付款周期
    //public $paid_type="M";//金额类型 M：月金额 Y：年金额 1：一次性
    public $amt_paid;//月金额
    public $amt_money;//总金额
    public $b4_amt_paid;//月金额(更改前)
    public $b4_amt_money;//总金额(更改前)
    public $b4_pieces;//机器数量(更改前)
    public $b4_cust_type_end;//机器型号(更改前)
    public $amt_install;//押金金额
    public $need_install;//是否需要押金
    public $salesman_id;
    public $salesman;//业务员
    public $technician_id;
    public $technician;//负责技术员
    public $othersalesman_id;
    public $othersalesman;//被跨区业务员
    public $sign_dt;//合同开始日期
    public $ctrt_end_dt;//合同月份
    public $ctrt_period=12;//合同终止日期
    public $cont_info;//客户联系/电话
    public $first_dt;//服务日期
    public $status="N";//N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止
    public $status_dt;//新增日期
    public $remarks;
    public $remarks2;
    public $city;
    public $all_number=0;//实际发放月数
    public $surplus=0;//剩余月数
    public $status_desc;//记录类别
    //public $backlink;
    public $prepay_month=0;//预付月数
    public $prepay_start=0;//预付起始月
    public $sign;//货币图形

    public $service_info=array(
        array('id'=>0,
            'serviceID_id'=>'',
            'back_date'=>'',
            'back_money'=>'',
            'put_month'=>'',
            'out_month'=>'',
            'uflag'=>'Y',
        ),
    );//回款数组

    public $files;

    public $docMasterId = array(
        'serviceid'=>0,
    );
    public $removeFileId = array(
        'serviceid'=>0,
    );
    public $no_of_attm = array(
        'serviceid'=>0,
    );

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        $arr = array(
            'id'=>Yii::t('service','Record ID'),
            'service_no'=>Yii::t('service','service no'),
            'company_name'=>Yii::t('service','Customer'),
            'service'=>Yii::t('service','Service'),
            'nature_type'=>Yii::t('service','Nature'),
            'cust_type'=>Yii::t('service','Customer Type'),
            'amt_paid'=>Yii::t('service','Monthly'),
            'amt_install'=>Yii::t('service','deposit machine'),
            'need_install'=>Yii::t('service','Whether to charge deposit'),
            'salesman'=>Yii::t('service','Resp. Sales'),
            'othersalesman'=>Yii::t('service','OtherSalesman'),
            'technician'=>Yii::t('service','Resp. Tech.'),
            'sign_dt'=>Yii::t('service','Sign Date'),
            'ctrt_end_dt'=>Yii::t('service','Contract End Date'),
            'ctrt_period'=>Yii::t('service','Contract Period'),
            'cont_info'=>Yii::t('service','Contact'),
            'first_dt'=>Yii::t('service','Service Date'),
            'first_tech'=>Yii::t('service','First Service Tech.'),
            'reason'=>Yii::t('service','Reason'),//
            'status'=>Yii::t('service','Record Type'),
            'status_dt'=>Yii::t('service','Record Date'),
            'remarks'=>Yii::t('service','Cross Area Remarks'),
            'remarks2'=>Yii::t('service','Remarks'),
            'b4_service'=>Yii::t('service','Service (Before)'),
            'af_service'=>Yii::t('service','Service (After)'),
            'af_amt_paid'=>Yii::t('service','Paid Amt (After)'),
            'equip_install_dt'=>Yii::t('service','Installation Date'),
            'new_dt'=>Yii::t('service','New Date'),
            'renew_dt'=>Yii::t('service','Renew Date'),
            'amend_dt'=>Yii::t('service','Amend Date'),
            'resume_dt'=>Yii::t('service','Resume Date'),
            'suspend_dt'=>Yii::t('service','Suspend Date'),
            'terminate_dt'=>Yii::t('service','Terminate Date'),
            'all_number'=>Yii::t('service','put month'),
            'surplus'=>Yii::t('service','surplus month'),
            'pieces'=>Yii::t('service','machine number'),
            'prepay_month'=>Yii::t('service','Prepay Month'),
            'prepay_start'=>Yii::t('service','Prepay Start'),
            'contract_no'=>Yii::t('service','Contract No'),
            'pay_week'=>Yii::t('service','pay week'),
            'amt_money'=>Yii::t('service','all money'),
            'back_date'=>Yii::t('service','back date'),
            'back_money'=>Yii::t('service','back money'),
            'put_month'=>Yii::t('service','put month'),
            'out_month'=>Yii::t('service','out month'),
            'service_new_id'=>Yii::t('service','shortcuts'),
            'cust_type_end'=>Yii::t('service','Customer type end'),
            'b4_cust_type_end'=>Yii::t('service','Before:').Yii::t('service','Customer type end'),
            'b4_pieces'=>Yii::t('service','Before:').Yii::t('service','machine number'),
            'b4_amt_paid'=>Yii::t('service','Before:').Yii::t('service','Monthly'),
            'b4_amt_money'=>Yii::t('service','Before:').Yii::t('service','all money'),
        );

        switch ($this->status){
            case "A":
                $arr["cust_type_end"] = Yii::t('service','After:').$arr["cust_type_end"];
                $arr["pieces"] = Yii::t('service','After:').$arr["pieces"];
                $arr["amt_paid"] = Yii::t('service','After:').$arr["amt_paid"];
                $arr["amt_money"] = Yii::t('service','After:').$arr["amt_money"];
                break;
        }
        return $arr;
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            /*
                        array('id, salesman, cont_info, first_tech, reason, remarks, remarks2, nature_type, cust_type,
                            status, status_desc, company_id, product_id,  fresh, city,
                            b4_product_id, b4_service, docType, files, removeFileId, downloadFileId, need_install, no_of_attm','safe'),
            */

            //service_new_id,service_no,company_id,company_name,nature_type,cust_type
            //cust_type_name,cust_type_three,cust_type_four,cust_type_end,pieces,product_id
            //service,pay_week,amt_paid,money,amt_install,need_install,salesman_id
            //salesman,technician_id,technician,othersalesman_id,othersalesman,sign_dt
            //ctrt_end_dt,ctrt_period,cont_info,first_dt,status,status_dt
            //remarks,remarks2,surplus,prepay_month,prepay_start
            array('id,service_new_id,service_no,company_id,company_name,nature_type,cust_type,
                cust_type_name,cust_type_three,cust_type_four,cust_type_end,pieces,product_id,
                service,pay_week,b4_amt_paid,amt_paid,b4_amt_money,amt_money,amt_install,need_install,salesman_id,
                salesman,technician_id,technician,othersalesman_id,othersalesman,sign_dt,
                ctrt_end_dt,ctrt_period,cont_info,first_dt,status,status_dt,
                b4_cust_type_end,b4_pieces,all_number,
                remarks,remarks2,surplus,prepay_month,prepay_start,service_info','safe'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
            array('company_name,amt_paid,amt_money,salesman, service,status_dt,sign_dt,ctrt_period,ctrt_end_dt','required'),
            array('ctrt_period','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('amt_paid, amt_install','numerical','allowEmpty'=>true),
            //array('b4_amt_paid','numerical','allowEmpty'=>true),
            array('sign_dt, ctrt_end_dt, first_dt','date','allowEmpty'=>true,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
            ),
            array('status_dt','validateVisitDt','on'=>array('new')),
            array('cust_type','validateCustType','on'=>array('new')),
        );
    }
    public function validateCustType($attribute, $params) {
        $this->cust_type_end = $this->cust_type_name;
        if(!empty($this->cust_type_three)){
            $this->cust_type_end = $this->cust_type_three;
        }else{
            $this->cust_type_three=0;
        }
        if(!empty($this->cust_type_four)){
            $this->cust_type_end = $this->cust_type_four;
        }else{
            $this->cust_type_four=0;
        }
        $row = Yii::app()->db->createCommand()->select("*")->from("swo_serviceid")
            ->where("company_id=:company_id and cust_type=:cust_type and cust_type_name=:cust_type_name and cust_type_three=:cust_type_three and cust_type_four=:cust_type_four",
                array(":company_id"=>$this->company_id,":cust_type"=>$this->cust_type,":cust_type_name"=>$this->cust_type_name,":cust_type_three"=>$this->cust_type_three,":cust_type_four"=>$this->cust_type_four)
            )->order("status_dt desc")->queryRow();
        if(!$row&&$this->status=="A"){//更改服务可以修改服务类型
            $row = Yii::app()->db->createCommand()->select("*")->from("swo_serviceid")
                ->where("id=:id",array(":id"=>$this->service_new_id)
                )->queryRow();
        }
        if($row||$this->status=="N"){
            $this->service_new_id = $row?$row["id"]:0;
            //N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止
            switch ($this->status){
                case "N":
                    break;
                case "A":
                    $this->b4_cust_type_end = $row["cust_type_end"];
                    break;
                default:
            }
        }else{
            $this->addError($attribute, "没有找到新增的ID服务");
        }
    }

    public function validateVisitDt($attribute, $params) {
        $visit_dt = date("Y-m-d",strtotime($this->status_dt));
        $nowDate = date("Y-m-d");
        $firstDate = date("Y-m-01",strtotime($nowDate));
        $firstDate = date("Y-m-01",strtotime("$firstDate - 2 month"));
        if($visit_dt<$firstDate){
            $this->addError($attribute, "新增日期必须大于".$firstDate);
        }
    }

//N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止
    public static function getStatusList(){
        return array(
            "N"=>Yii::t("service","New"),
            "C"=>Yii::t("service","Renew"),
            "A"=>Yii::t("service","Amend"),
            "S"=>Yii::t("service","Suspend"),
            "R"=>Yii::t("service","Resume"),
            "T"=>Yii::t("service","Terminate"),
        );
    }

    public function resetAttrLabel(){
        //N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止
        if(time()>strtotime($this->status_dt)){
            $this->status_dt = date("Y/m/d");
        }
        switch ($this->status){
            case "A":
                $this->b4_cust_type_end = $this->cust_type_end;
                $this->b4_pieces = $this->pieces;
                $this->b4_amt_paid = $this->amt_paid;
                $this->b4_amt_money = $this->amt_money;
                break;
            case "T":
                $this->all_number = $this->ctrt_period;
                break;
            default:
        }
    }

    public static function getServiceIDHistory($id){
        //service_new_id
        $city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()
            ->select("g.name as company_name,a.id,a.service_no,a.status,a.status_dt,f.cust_type_name,b.description")
            ->from("swo_serviceid a")
            ->leftJoin("swo_company g","g.id=a.company_id")
            ->leftJoin("swo_customer_type_id b","b.id=a.cust_type")
            ->leftJoin("swo_customer_type_info f","f.id=a.cust_type_end")
            ->where("(a.service_new_id=:id or a.id=:id) and a.city in ($city)",array(":id"=>$id))
            ->order("a.status_dt asc,a.id asc")->queryAll();
        return $row;
    }

    public function retrieveData($index)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('SERVICEID',id) as no_of_attm")->from("swo_serviceid")
            ->where("id=:id and city in ($city)",array(":id"=>$index))->queryRow();
//		print_r('<pre>');
//        print_r($rows);
        if ($row) {
            $this->id = $row['id'];
            $this->service_new_id = $row['service_new_id'];
            $this->company_id = $row['company_id'];
            $this->company_name = $row['company_name'];
            $this->nature_type = $row['nature_type'];
            $this->nature_type = $row['nature_type'];
            $this->cust_type = $row['cust_type'];
            $this->cust_type_name = $row['cust_type_name'];
            $this->cust_type_three = $row['cust_type_three'];
            $this->cust_type_four = $row['cust_type_four'];
            $this->cust_type_end = CustomertypeIDForm::getCustTypeInfoNameForId($row['cust_type_end']);
            $this->product_id = $row['product_id'];
            $this->service = $row['service'];
            $this->amt_paid = $row['amt_paid'];
            $this->amt_money = floatval($row['amt_money']);
            $this->b4_amt_paid = $row['b4_amt_paid'];
            $this->b4_amt_money = floatval($row['b4_amt_money']);
            $this->b4_pieces = $row['b4_pieces'];
            $this->b4_cust_type_end = CustomertypeIDForm::getCustTypeInfoNameForId($row['b4_cust_type_end']);
            $this->amt_install = $row['amt_install'];
            $this->salesman_id = $row['salesman_id'];
            $this->salesman = $row['salesman'];
            $this->othersalesman_id = $row['othersalesman_id'];
            $this->othersalesman = $row['othersalesman'];
            $this->technician_id = $row['technician_id'];
            $this->technician = $row['technician'];
            $this->service_no = $row['service_no'];
            $this->pay_week = $row['pay_week'];
            $this->sign_dt = General::toDate($row['sign_dt']);
            $this->ctrt_end_dt = General::toDate($row['ctrt_end_dt']);
            $this->ctrt_period = $row['ctrt_period'];
            $this->cont_info = $row['cont_info'];
            $this->first_dt = General::toDate($row['first_dt']);
            $this->status_dt = General::toDate($row['status_dt']);
            $this->status = $row['status'];
            $this->remarks = $row['remarks'];
            $this->remarks2 = $row['remarks2'];
            $this->need_install = $row['need_install'];
            $this->no_of_attm['serviceid'] = $row['no_of_attm'];
            $this->city = $row['city'];
            $this->surplus = $row['surplus'];
            //var_dump($row['cust_type_name']);
            $this->pieces = $row['pieces'];
            $this->prepay_month = $row['prepay_month'];
            $this->prepay_start = $row['prepay_start'];
            $details = Yii::app()->db->createCommand()->select("*")->from("swo_serviceid_info")
                ->where("serviceID_id=:id",array(":id"=>$this->id))->order("back_date asc")->queryAll();
            if($details){
                $this->service_info = array();
                foreach ($details as $detail){
                    $this->service_info[] = array(
                        'id'=>$detail["id"],
                        'serviceID_id'=>$this->id,
                        'back_date'=>General::toDate($detail["back_date"]),
                        'back_money'=>$detail["back_money"],
                        'put_month'=>$detail["put_month"],
                        'out_month'=>$detail["out_month"],
                        'uflag'=>'N',
                    );
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
            $this->saveServiceID($connection);
            $this->updateServiceInfo();
            $this->updateDocman($connection,'SERVICEID');
            $transaction->commit();
        }catch(Exception $e) {
            var_dump($e);
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update.');
        }
    }

    protected function updateServiceInfo() {
        if(empty($this->service_info)){
            return false;
        }
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "new":
                foreach ($this->service_info as $row) {
                    if (!empty($row["back_date"])) {
                        Yii::app()->db->createCommand()->insert("swo_serviceid_info",
                            array(
                                "serviceID_id"=>$this->id,
                                "back_date"=>$row["back_date"],
                                "back_money"=>$row["back_money"],
                                "put_month"=>$row["put_month"],
                                "out_month"=>$row["out_month"],
                                "lcu"=>$uid,
                            )
                        );
                    }
                }
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete("swo_serviceid_info",
                    "serviceID_id=:serviceID_id",
                    array(":serviceID_id"=>$this->id)
                );
                break;
            default:
                foreach ($this->service_info as $row){
                    if(!empty($row["back_date"])){
                        switch ($row["uflag"]){
                            case "Y"://新增
                                Yii::app()->db->createCommand()->insert("swo_serviceid_info",
                                    array(
                                        "serviceID_id"=>$this->id,
                                        "back_date"=>$row["back_date"],
                                        "back_money"=>$row["back_money"],
                                        "put_month"=>$row["put_month"],
                                        "out_month"=>$row["out_month"],
                                        "lcu"=>$uid,
                                    )
                                );
                                break;
                            case "N"://修改
                                Yii::app()->db->createCommand()->update("swo_serviceid_info",
                                    array(
                                        "back_date"=>$row["back_date"],
                                        "back_money"=>$row["back_money"],
                                        "put_month"=>$row["put_month"],
                                        "out_month"=>$row["out_month"],
                                        "luu"=>$uid,
                                    ),
                                    "id=:id and serviceID_id=:serviceID_id",
                                    array(":id"=>$row["id"],":serviceID_id"=>$this->id)
                                );
                                break;
                            case "D"://刪除
                                Yii::app()->db->createCommand()->delete("swo_serviceid_info",
                                    "id=:id and serviceID_id=:serviceID_id",
                                    array(":id"=>$row["id"],":serviceID_id"=>$this->id)
                                );
                                break;
                        }
                    }
                }
        }
    }

    protected function getDataForModel(){
        $arr = array();
/*        $list = "id,service_new_id,service_no,company_id,company_name,nature_type,cust_type,
                cust_type_name,cust_type_three,cust_type_four,cust_type_end,pieces,product_id,
                service,pay_week,amt_paid,money,amt_install,need_install,salesman_id,
                salesman,technician_id,technician,othersalesman_id,othersalesman,sign_dt,
                ctrt_end_dt,ctrt_period,cont_info,first_dt,status,status_dt,
                remarks,remarks2,surplus,prepay_month,prepay_start";*/
        //$arr["service_no"] = $this->service_no;
        $arr["nature_type"] = empty($this->nature_type)?0:$this->nature_type;
        if($this->getScenario()=="new"){ //客戶及客戶類型進新增允許修改
            $arr["service_new_id"] = $this->service_new_id;
            $arr["company_id"] = $this->company_id;
            $arr["company_name"] = $this->company_name;
            $arr["cust_type"] = $this->cust_type;
            $arr["cust_type_name"] = $this->cust_type_name;
            $arr["cust_type_three"] = $this->cust_type_three;
            $arr["cust_type_four"] = $this->cust_type_four;
            $arr["cust_type_end"] = $this->cust_type_end;
        }
        $arr["pieces"] = $this->pieces;
        $arr["product_id"] = $this->product_id;
        $arr["service"] = $this->service;
        $arr["pay_week"] = empty($this->pay_week)?0:$this->pay_week;
        $arr["amt_paid"] = $this->amt_paid;
        $arr["amt_money"] = $this->amt_money;
        if($this->status == "A"){//更改
            $arr["b4_amt_paid"] = $this->b4_amt_paid;
            $arr["b4_amt_money"] = $this->b4_amt_money;
            $arr["b4_pieces"] = $this->b4_pieces;
            $arr["b4_cust_type_end"] = $this->b4_cust_type_end;
        }
        if($this->status == "T"){//终止
            $arr["all_number"] = $this->all_number;
            $arr["surplus"] = $this->surplus;
        }
        $arr["amt_install"] = $this->amt_install;
        $arr["need_install"] = $this->need_install;
        $arr["salesman_id"] = $this->salesman_id;
        $arr["salesman"] = $this->salesman;
        $arr["technician_id"] = empty($this->technician_id)?0:$this->technician_id;
        $arr["technician"] = $this->technician;
        $arr["othersalesman_id"] = empty($this->othersalesman_id)?0:$this->othersalesman_id;
        $arr["othersalesman"] = $this->othersalesman;
        $arr["sign_dt"] = $this->sign_dt;
        $arr["ctrt_end_dt"] = $this->ctrt_end_dt;
        $arr["ctrt_period"] = $this->ctrt_period;
        $arr["cont_info"] = $this->cont_info;
        $arr["first_dt"] = $this->first_dt;
        $arr["status"] = $this->status;
        $arr["status_dt"] = $this->status_dt;
        $arr["remarks"] = $this->remarks;
        $arr["remarks2"] = $this->remarks2;
        $arr["prepay_month"] = $this->prepay_month;
        $arr["prepay_start"] = $this->prepay_start;
        return $arr;
    }

    protected function saveServiceID(&$connection)
    {
        $data = $this->getDataForModel();
        if(empty($data)){
            var_dump(1);
            die();
        }
        $city =Yii::app()->user->city(); 	//Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        switch ($this->scenario) {
            case 'delete':
                Yii::app()->db->createCommand()->delete("swo_serviceid",
                    "id=:id and city=:city",
                    array(":id"=>$this->id,":city"=>$city)
                );
                break;
            case 'new':
                $data["city"] = $city;
                $data["lcu"] = $uid;
                Yii::app()->db->createCommand()->insert("swo_serviceid",$data);
                $this->id = Yii::app()->db->getLastInsertID();
                $this->service_no = $this->getServiceNo();
                Yii::app()->db->createCommand()->update("swo_serviceid",
                    array("service_no"=>$this->service_no),
                    "id=:id",
                    array(":id"=>$this->id)
                );
                break;
            case 'edit':
                $data["luu"] = $uid;
                Yii::app()->db->createCommand()->update("swo_serviceid",$data,
                    "id=:id and city=:city",
                    array(":id"=>$this->id,":city"=>$city)
                );
                break;
        }

        return true;
    }

    protected function getServiceNo(){
        $code = $this->status;
        $num = $this->id+100000;
        return $code.$num;
    }

    protected function updateDocman(&$connection, $doctype) {
        if ($this->scenario=='new'||$this->scenario=='renew'||$this->scenario=='amend'||$this->scenario=='suspend'||$this->scenario=='terminate'||$this->scenario=='resume') {
            $docidx = strtolower($doctype);
            if ($this->docMasterId[$docidx] > 0) {
                $docman = new DocMan($doctype,$this->id,get_class($this));
                $docman->masterId = $this->docMasterId[$docidx];
                $docman->updateDocId($connection, $this->docMasterId[$docidx]);
            }
        }
    }

    public function getCustTypeList($a=1) {
        $city = Yii::app()->user->city();
        $rtn = array(''=>Yii::t('misc','-- None --'));
        $sql = "select id, cust_type_name from swo_customer_type_twoname where  cust_type_id=$a order by cust_type_name";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach($rows as $row) {
                $rtn[$row['id']] = $row['cust_type_name'];
            }
        }
        return $rtn;
    }

    public function getServiceAllForNew() {
        $city = Yii::app()->user->city();
        $rtn = array(''=>"-- ".Yii::t('service','service new id')." --");
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.service_no,e.name,b.description,f.cust_type_name")
            ->from("swo_serviceid a")
            ->leftJoin("swo_company e","a.company_id=e.id")
            ->leftJoin("swo_customer_type_id b","a.cust_type=b.id")
            ->leftJoin("swo_customer_type_info f","a.cust_type_name=f.id")
            ->where("a.status='N' and a.city='$city'")
            ->queryAll();
        if (count($rows) > 0) {
            foreach($rows as $row) {
                $rtn[$row['id']] = $row['service_no']." - ".$row['name']." - ".$row['description']." - ".$row['cust_type_name'];
            }
        }
        return $rtn;
    }

    public function getStatusDesc() {
        return General::getStatusDesc($this->status);
    }

    public function isReadOnly(){
        return $this->getScenario()=="view";
    }

    public function isOccupied($id){
        $city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("id")->from("swo_serviceid")
            ->where("id=:id and city='$city' and wage_type=0",array(":id"=>$id))->queryRow();
        if($row){
            return false;
        }else{
            return true;
        }
    }
}
