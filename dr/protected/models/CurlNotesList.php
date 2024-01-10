<?php

class CurlNotesList extends CListPageModel
{
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
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
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
						'status_type'=>$record['status_type'],
						'info_type'=>$record['info_type'],
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

	public function sendID($index){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("datasync{$suffix}.sync_api_curl")
            ->where("id=:id and status_type!='P'", array(':id'=>$index))->queryRow();
        if($row){
            Yii::app()->db->createCommand()->update("datasync{$suffix}.sync_api_curl",array("status_type"=>"P"),"id={$index}");
            return true;
        }else{
            return false;
        }
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
            "entry_dt"=>"2024-1-10 12:16:35",//客诉日期
            "type"=>7,//服务类别
            "followup_id"=>22,//U系统的客诉id(唯一标识)
            "status"=>1,//投诉个案的状态
            "city"=>"ZH",//城市
            "service_name"=>"飄盈香",//客户服务
            "service_code"=>"IA",//客户服务
            "company_name"=>"客户名称",//客户名称
            "company_code"=>"dddd112-ZH",//客户编号
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
        );
	    $this->sendCurl("/sync/complaint",$data);
    }

    public function testServiceOne(){
	    $data = array(
            "status"=>1,//客户服务的状态
            "city"=>"ZH",//城市
            "status_dt"=>"2024-1-10 16:03:28",//记录时间
            "contract_no"=>"888888-1",//合约编号
            "contract_type"=>"普通合约",//合约类型（普通合约、KA合约）
            "company_code"=>"dddd112-ZH",//客户公司编号
            "company_type"=>2,//客户公司类型
            "service_type"=>10,//服务类型
            "month_cycle"=>8191,//服务內容
            "week_cycle"=>15,//服务內容
            "day_cycle"=>4,//服务內容
            "service_name_rec"=>"常驻灭虫",//服务內容
            "service_type_rec"=>13,//服务內容
            "amt_paid"=>16000,//发票金额
            "sales_code"=>"400002",//销售编号
            "amt_install"=>100,//安装金额
            "item_04"=>100,//是否需要安装费用
            "item_05"=>null,//是否需要安装费用
            "item_06"=>null,//是否需要安装费用
            "item_07"=>null,//是否需要安装费用
            "item_08"=>null,//是否需要安装费用
            "item_09"=>null,//是否需要安装费用
            "item_10"=>null,//是否需要安装费用
            "item_13"=>null,//是否需要安装费用
            "surplus"=>2,//剩余次数
            "other_sales_code"=>null,//跨区销售员编号
            "sign_dt"=>"2024-01-09",//签约日期
            "ctrt_end_dt"=>"2025-01-09",//合同终止日期
            "first_dt"=>"2024-01-11",//首次日期
            "technician_01"=>"400002",//首次技术员1编号
            "technician_02"=>null,//首次技术员2编号
            "technician_03"=>null,//首次技术员3编号
            "reason"=>"没有原因",//原因
            "remarks2"=>"客户位置比较偏僻",//备注
            "prepay_month"=>2,//预付月数
            "prepay_start"=>0,//预付起始月
            "stop_dt"=>null,//终止日期
            "beforeData"=>array(),//客户服务修改之前的数据(没有数据时)
            /*
            "beforeData"=>array(//客户服务修改之前的数据(有数据时)
                "amt_paid"=>14000,//发票金额
                "status"=>1,//客户服务的状态
                "month_cycle"=>8191,//服务內容
                "week_cycle"=>31,//服务內容
                "day_cycle"=>64,//服务內容
                "ctrt_end_dt"=>"2025-01-09",//合同终止日期
                "stop_dt"=>null,//终止日期
                "service_type"=>10,//服务类型
                "service_name_rec"=>"常驻灭虫",//服务內容
                "service_type_rec"=>13,//服务內容
            ),*/
        );
	    $this->sendCurl("/sync/serviceOne",$data);
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
}
