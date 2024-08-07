<?php

class CurlNotesList extends CListPageModel
{
    public $info_type;

    public function rules()
    {
        return array(
            array('info_type,attr, pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public function getCriteria() {
        return array(
            'info_type'=>$this->info_type,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'city'=>$this->city,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'status_type'=>Yii::t('curl','status type'),
			'info_type'=>Yii::t('curl','info type'),
			'info_url'=>Yii::t('curl','info url'),
			'data_content'=>Yii::t('curl','data content'),
			'out_content'=>Yii::t('curl','out content'),
			'message'=>Yii::t('curl','message'),
			'lcu'=>Yii::t('curl','lcu'),
			'lcd'=>Yii::t('curl','lcd'),
			'lud'=>Yii::t('curl','lud'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select * 
				from datasync{$suffix}.sync_api_curl 
				where 1=1 
			";
		$sql2 = "select count(id)
				from  datasync{$suffix}.sync_api_curl  
				where 1=1 
			";
		$clause = "";
		if(!empty($this->info_type)){
            $svalue = str_replace("'","\'",$this->info_type);
            //"ServiceFull"=>"服务合约",
            if($svalue=="ServiceOne"){
                $clause.=" and info_type in ('ServiceOne','ServiceFull') ";
            }else{
                $clause.=" and info_type='$svalue' ";
            }
        }
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'id':
					$clause .= General::getSqlConditionClause('id',$svalue);
					break;
				case 'status_type':
					$clause .= General::getSqlConditionClause('status_type',$svalue);
					break;
				case 'info_type':
					$clause .= General::getSqlConditionClause('info_type',$svalue);
					break;
				case 'data_content':
					$clause .= General::getSqlConditionClause('data_content',$svalue);
					break;
				case 'out_content':
					$clause .= General::getSqlConditionClause('out_content',$svalue);
					break;
				case 'message':
					$clause .= General::getSqlConditionClause('message',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
					$this->attr[] = array(
						'id'=>$record['id'],
						'info_type'=>self::getInfoTypeList($record['info_type'],true),
						'status_type'=>$record['status_type'],
						'data_content'=>$record['data_content'],
						'out_content'=>$record['out_content'],
						//'data_content'=>urldecode($record['data_content']),
						//'out_content'=>urldecode($record['out_content']),
						'message'=>$record['message'],
						'lcu'=>$record['lcu'],
						'lcd'=>$record['lcd'],
						'lud'=>$record['lud'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['curlNotes_c01'] = $this->getCriteria();
		return true;
	}

	//翻译curl的类型
	public static function getInfoTypeList($key="",$bool=false){
        $list = array(
            "Office"=>"办事处",
            "ServiceOne"=>"服务合约",
            "ServiceFull"=>"批量修改",
            "Company"=>"客户公司",
            "CompanyFull"=>"批量客户公司",
            "Complaint"=>"跟进单",
            "Cross"=>"交叉派单",
            "CrossFull"=>"批量交叉派单",
        );
        if($bool){
            if(key_exists($key,$list)){
                return $list[$key];
            }else{//ServiceFull"=>"服务合约",
                return $key=="ServiceFull"?"服务合约":$key;
            }
        }else{
            return $list;
        }
    }

	public function sendID($index){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("datasync{$suffix}.sync_api_curl")
            ->where("id=:id and status_type!='P'", array(':id'=>$index))->queryRow();
        if($row){
            Yii::app()->db->createCommand()->update("datasync{$suffix}.sync_api_curl",array(
                "status_type"=>"P",
                "lcu"=>$uid,
            ),"id={$index}");
            return true;
        }else{
            return false;
        }
    }

    private function serviceData($num){
        return array(
            "status"=>1,//客户服务的状态
            "city"=>"ZH",//城市
            "status_dt"=>"2024-05-".($num>20?$num%20:$num)." 10:33:28",//记录时间
            "contract_no"=>"8558-1{$num}",//合约编号
            "contract_type"=>"普通合约",//合约类型（普通合约、KA合约）
            "company_code"=>"dddd112-ZH",//客户公司编号
            "company_type"=>2,//客户公司类型
            "service_type"=>10,//服务类型
            "lbs_type_one"=>2,//LBS对应id（一级栏位）
            "lbs_type_two"=>74,//LBS对应id（二级栏位）
            "month_cycle"=>8191,//服务內容
            "week_cycle"=>78,//服务內容
            //"week_cycle"=>15,//服务內容
            "day_cycle"=>4,//服务內容
            "service_name_rec"=>"常驻灭虫",//服务內容
            "service_type_rec"=>13,//服务內容
            "amt_paid"=>12000*$num,//发票金额
            "sales_code"=>"800002",//销售编号
            "amt_install"=>$num%2==0?100*$num:null,//安装金额
            "item_04"=>$num%2==0?100*$num:null,//是否需要安装费用
            "item_05"=>null,//是否需要安装费用
            "item_06"=>null,//是否需要安装费用
            "item_07"=>null,//是否需要安装费用
            "item_08"=>null,//是否需要安装费用
            "item_09"=>null,//是否需要安装费用
            "item_10"=>null,//是否需要安装费用
            "item_13"=>null,//是否需要安装费用
            "surplus"=>$num,//剩余次数
            "other_sales_code"=>"789444",//跨区销售员编号
            "sign_dt"=>"2024-01-09",//签约日期
            "ctrt_end_dt"=>"",//合同终止日期
            "first_dt"=>"2024-01-11",//首次日期
            "technician_01"=>"400002",//首次技术员1编号
            "technician_02"=>null,//首次技术员2编号
            "technician_03"=>null,//首次技术员3编号
            "technician_arr"=>array("900001","900002","900003","900004"),//负责技术员数组
            "first_tech_arr"=>array("900002","900003"),//首次技术员数组
            "reason"=>"没有原因",//原因
            "remarks2"=>"客户位置比较偏僻",//备注
            "prepay_month"=>$num%2==0?100*$num:null,//预付月数
            "prepay_start"=>$num%2==0?100*$num:null,//预付起始月
            "stop_dt"=>null,//终止日期
            "office_id"=>4,//办事处（U系统id）
            "contract_id"=>11114,//办事处（U系统id）
            "beforeData"=>array(),//客户服务修改之前的数据(没有数据时)
            /*
            "beforeData"=>array(//客户服务修改之前的数据(有数据时)
                "amt_paid"=>14000,//发票金额
                "status"=>1,//客户服务的状态
                "month_cycle"=>8191,//服务內容
                "week_cycle"=>31,//服务內容
                "day_cycle"=>64,//服务內容
                "ctrt_end_dt"=>"0000-00-00",//合同终止日期
                //"ctrt_end_dt"=>"2025-01-09",//合同终止日期
                "stop_dt"=>null,//终止日期
                "service_type"=>10,//服务类型
                "lbs_type_one"=>2,//LBS对应id（一级栏位）
                "lbs_type_two"=>74,//LBS对应id（二级栏位）
                "service_name_rec"=>"常驻灭虫",//服务內容
                "service_type_rec"=>13,//服务內容
            ),
            */
        );
    }

    public function testOffice(){
	    $data = array(
            "type"=>"add",//操作类型
            "name"=>"珠海办事处",//办事处名称
            "city"=>"ZH",//城市
            "u_id"=>"3",//U系统id
        );
	    $this->sendCurl("/sync/office",$data);
    }

    public function testCompany(){
	    $data = array(
            "code"=>"CCCCC01-ZH",//公司编号
            "name"=>"CCCCC01ddddd",//公司名称
            "city"=>"ZH",//公司城市
            "full_name"=>"CCCCC01ddddd-HZ",//公司全称
            "cont_name"=>"客户联系人",//客户联系人
            "cont_phone"=>"客户电话",//客户电话
            "address"=>"客户地址",//客户地址
            "tax_reg_no"=>"纳税人登记号",//纳税人登记号
            "group_id"=>"aaaa",//集团id
            "group_name"=>"aaaatest",//集团名称
            "status"=>2,//客户状态
            "email"=>"客户邮箱",//客户邮箱
        );
	    $this->sendCurl("/sync/company",$data);
    }

    public function testComplaint(){
	    $data = array(
            "entry_dt"=>"2024-2-10 12:16:35",//客诉日期
            "type"=>7,//服务类别
            "followup_id"=>23,//U系统的客诉id(唯一标识)
            "status"=>1,//投诉个案的状态
            "city"=>"ZH",//城市
            "service_name"=>"飄盈香",//客户服务
            "service_code"=>"IA",//客户服务
            "company_name"=>"客户名称",//客户名称
            "company_code"=>"sdasda",//客户编号
            "content"=>"投诉测试~~~~~~~~测试",//投诉内容
            "job_report"=>"处理结果~~~~~~~~测试",//处理结果
            "contact_name"=>"某某公司",//投诉者
            "contact_tel"=>"17777777",//投诉者联络电话
            "resp_staff"=>"400002",//负责销售顾问
            "resp_tech"=>"400003",//负责此客户之技术员
            "sch_dt"=>"2024-1-11",//安排跟进日期
            "staff_01"=>"400003",//跟进(此投诉)技术员
            "staff_02"=>"400002",//跟进(此投诉)技术员
            "staff_03"=>null,//跟进(此投诉)技术员
            "staff_arr"=>array("400002","400003"),//跟进(此投诉)技术员
        );
        $data = array(
            "followup_id"=> 95968,
            "entry_dt"=> "2024-02-26 11:52:23",
            "type"=> 15,
            "company_code"=> "QXYKN001-NN",
            "company_name"=> "一口泥炉烤肉（凤翔店）",
            "sch_dt"=> "2024-2-26",
            "city"=> "NN",
            "status"=> 2,
            "service_code"=> "INV",
            "service_name"=> "送皂液",
            "content"=> "配送一桶洗地易。",
            "job_report"=> "",
            "contact_name"=> "潘峻成",
            "contact_tel"=> "19858159662",
            "resp_staff"=> "",
            "resp_tech"=> "",
            "staff_arr"=> ["402218"]
        );
	    $this->sendCurl("/sync/complaint",$data);
    }

    public function testServiceOne(){
	    $data = self::serviceData(1);
	    $this->sendCurl("/sync/serviceOne",$data);
    }

    public function testCrossOne($index){
        $data = array(
            "lbs_id"=> $index,
            "status_type"=>1,
            "u_update_user"=> "40002_测试",
            "u_update_date"=> "2024-5-22 10:53:23",
            "office_id"=> null,
        );
	    $this->sendCurl("/sync/crossOne",$data);
    }

    public function testCrossFull($idStr){
        $idList = explode(",",$idStr);
        $data=array();
        foreach ($idList as $id){
            $data[] = array(
                "lbs_id"=> $id,
                "status_type"=>1,
                "u_update_user"=> "40002_测试",
                "u_update_date"=> "2024-8-22 10:53:23",
                "office_id"=> null,
            );
        }
	    $this->sendCurl("/sync/crossFull",$data);
    }

    public function testServiceFull(){
	    $data =array();
	    for ($i=1;$i<=10;$i++){
	        $data[]=self::serviceData($i);
        }
	    $this->sendCurl("/sync/serviceFull",$data);
    }

    public function testIp(){
	    $data =array();
	    $this->sendCurl("/sync/ip",$data);
    }

    public function systemU($type){
        //$city = Yii::app()->user->city();
        $city = "CD";
        $list = array(
            ////获取发票内容
            "getData"=>array("args"=>array("city"=>"'{$city}'","start"=>"2023-01-01", "end"=>"2024-02-01", "customer"=>"")),
            //获取INV类型的详情
            "getInvDataDetail"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取INV类型的城市汇总
            "getInvDataCityAmount"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取INV类型的城市(月份)汇总
            "getInvDataCityMonth"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取INV类型的城市(周)汇总
            "getInvDataCityWeek"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取服务单月数据
            "getUServiceMoney"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取服务单月数据（月為鍵名)
            "getUServiceMoneyToMonth"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取服务单月数据（周為鍵名)
            "getUServiceMoneyToWeek"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取技术员金额（技术员编号為鍵名)
            "getTechnicianMoney"=>array("args"=>array("start"=>"2024-03-01","end"=>"2024-03-31","city"=>"'{$city}'")),
            //获取技术员金额U系统详情（需要自己分开服务单）
            "getTechnicianDetail"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取技术员的创新金额、夜单金额、服务金额
            "getTechnicianSNC"=>array("args"=>array("year"=>"2023","month"=>"2","city"=>"'{$city}'")),
            //获取IAIB总数/remote/countlAlB.php
            "countIAIB"=>array("args"=>array("year"=>"2024","month"=>"5")),
        );
        if(key_exists($type,$list)){
            $params=array();
            if(!empty($list[$type]["args"])){
                foreach ($list[$type]["args"] as $item=>$value){
                    $params[$item] = key_exists($item,$_GET)?$_GET[$item]:"";
                    $params[$item] = !empty($params[$item])?$params[$item]:$value;
                }
            }
            $params["printBool"] = true;
            $func_name = "SystemU::".$type;
            $json = call_user_func_array($func_name, $params);
        }else{
            echo "404";
        }
    }

    private function sendCurl($url,$data){
        $data = json_encode($data);
        $url = Yii::app()->params['curlLink'].$url;
        $svrkey = Yii::app()->params['SvrKey'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data),
            'Authorization: SvrKey '.$svrkey,
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if ($out===false) {
            echo 'Error: '.curl_error($ch);
        } else {
            var_dump($out);
        }
        curl_close($ch);
    }


    public function getCurlTextForID($id,$type=0){
        $type = "".$type;
        $list = array(
            0=>"data_content",//请求内容
            1=>"out_content",//响应的内容
            //2=>"cmd_content",//执行结果
        );
        $selectStr = key_exists($type,$list)?$list[$type]:$list[0];
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select($selectStr)->from("datasync{$suffix}.sync_api_curl")
            ->where("id=:id", array(':id'=>$id))->queryRow();
        if($row){
            $searchList = array("\\r","\\n","\\t");
            $replaceList = array("\r","\n","\t");
            return str_replace($searchList,$replaceList,$row[$selectStr]);
        }else{
            return "";
        }
    }
}
