<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class ServiceKAForm extends ServiceForm
{
	/* User Fields */
	public $docMasterId = array(
							'serviceka'=>0,
						);
	public $removeFileId = array(
							'serviceka'=>0,
						);
	public $no_of_attm = array(
							'serviceka'=>0,
						);

    //驗證该服务是否交叉派单
    public function validateCross($attribute, $params) {
        $id=$this->getScenario()=="new"?0:$this->id;
        if($this->getScenario()=="delete"){
            $row = Yii::app()->db->createCommand()->select("id")->from("swo_cross")
                ->where("table_type=1 and service_id=:id",array(":id"=>$id))->queryRow();
            if($row){
                $this->addError($attribute, "该服务已参加交叉派单，无法删除。({$row["id"]})");
            }
        }
    }

    //驗證该服务是否已经参加销售提成计算
    public function validateID($attribute, $params) {
        $id=$this->getScenario()=="new"?0:$this->id;
        $notUpdate=array("status","status_dt","cust_type","cust_type_name",
            "paid_type","amt_install","all_number","salesman","salesman_id",
            "othersalesman","othersalesman_id","ctrt_period","first_dt",
            "b4_paid_type","b4_amt_paid","surplus","company_name","company_id");
        $row = Yii::app()->db->createCommand()
            ->select("id,".implode(",",$notUpdate))
            ->from("swo_service_ka")
            ->where("(commission is not null or other_commission is not null) and id=:id",array(":id"=>$id))->queryRow();
        if($row){
            if($this->getScenario()=="delete"){
                $this->addError($attribute, "该服务已参加销售提成计算，无法删除");
            }else{
                foreach ($notUpdate as $item){
                    $this->$item = $row[$item];
                }
            }
        }
    }

    //驗證新增時是否有該服務
    public function validateAutoFinish($attribute, $params){
        $this->service_new_id = 0;
        if(in_array($this->getScenario(),array("renew","amend","suspend","terminate","resume"))||($this->getScenario()=="edit"&&$this->status!="N")){
            $this->cust_type_name = empty($this->cust_type_name)?0:$this->cust_type_name;
            $row = Yii::app()->db->createCommand()->select("id")->from("swo_service_ka")
                ->where("status='N' and company_id=:company_id and cust_type=:cust_type and cust_type_name=:cust_type_name",array(
                    ":company_id"=>$this->company_id,
                    ":cust_type"=>$this->cust_type,
                    ":cust_type_name"=>$this->cust_type_name,
                ))->order("sign_dt desc")->queryRow();
            if($row){
                $this->service_new_id = $row["id"];
            }else{
                $this->service_new_id = -1;
            }
        }
        $this->salesman_id = empty($this->salesman_id)?"":$this->salesman_id;
        $this->othersalesman_id = empty($this->othersalesman_id)?0:$this->othersalesman_id;
        $this->technician_id = empty($this->technician_id)?0:$this->technician_id;
        $this->first_tech_id = empty($this->first_tech_id)?0:$this->first_tech_id;
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

    public function dataCopy($index){
        $row = Yii::app()->db->createCommand()->select("a.*,b.contract_no")
            ->from("swo_service_ka a")
            ->leftJoin("swo_service_ka_no b","a.id=b.service_id")
            ->where("a.id=:id",array(":id"=>$index))
            ->queryRow();
        if($row){
            if(empty($row["contract_no"])){
                Dialog::message(Yii::t('dialog','Validation Message'), "合同编号为空，无法复制");
                return false;
            }else{
                $bool = Yii::app()->db->createCommand()->select("a.id,b.contract_no")
                    ->from("swo_service a")
                    ->leftJoin("swo_service_contract_no b","a.id=b.service_id")
                    ->where("a.status_dt=:status_dt and a.status=:status and b.contract_no=:contract_no",array(
                        ":status"=>$row["status"],
                        ":status_dt"=>$row["status_dt"],
                        ":contract_no"=>$row["contract_no"],
                    ))->queryRow();
                if($bool){
                    Dialog::message(Yii::t('dialog','Validation Message'), "客户服务已存在，无法复制：".$bool["id"]);
                    return false;
                }
                $data = $row;
                unset($data["id"]);
                unset($data["contract_no"]);
                Yii::app()->db->createCommand()->insert("swo_service",$data);
                $this->id = Yii::app()->db->getLastInsertID();
                Yii::app()->db->createCommand()->insert("swo_service_contract_no",array(
                    "contract_no"=>$row["contract_no"],
                    "status_dt"=>$row["status_dt"],
                    "status"=>$row["status"],
                    "service_id"=>$this->id,
                ));
                Yii::app()->db->createCommand()->insert("swo_service_history",array(
                    "update_html"=>"<span>复制</span><br><span>复制ka_id：{$index}</span>",
                    "update_type"=>2,
                    "service_type"=>1,
                    "service_id"=>$this->id,
                    "lcu"=>Yii::app()->user->id
                ));
                return true;
            }
        }else{
            return false;
        }
    }

	public function retrieveData($index,$bool=true)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql = "select a.*,f.code as com_code,f.name as com_name, docman$suffix.countdoc('SERVICEKA',a.id) as no_of_attm,b.contract_no from swo_service_ka a
        left outer join swo_service_ka_no b on a.id=b.service_id 
        left outer join swo_company f on a.company_id=f.id 
        where a.id=$index";
        if($bool){
            $sql.=" and a.city in ($city)";
        }
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
//		print_r('<pre>');
//        print_r($rows);
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$this->id = $row['id'];
				$this->service_new_id = $row['service_new_id'];
				$this->commission = $row['commission'];
				$this->other_commission = $row['other_commission'];
				$this->service_no = $row['service_no'];
                $this->customer_code = $row['com_code'];
                $this->customer_name = $row['com_name'];
				$this->company_id = empty($row['company_id'])?"":$row['company_id'];
				$this->company_name = empty($row['com_name'])?$row['company_name']:$row['com_code'].$row['com_name'];
				$this->nature_type = $row['nature_type'];
				$this->nature_type_two = $row['nature_type_two'];
				$this->cust_type = $row['cust_type'];
				$this->product_id = $row['product_id'];
				$this->service = $row['service'];
				$this->paid_type = $row['paid_type'];
				$this->amt_paid = $row['amt_paid'];
				$this->b4_product_id = $row['b4_product_id'];
				$this->b4_service = $row['b4_service'];
				$this->b4_paid_type = $row['b4_paid_type'];
				$this->b4_amt_paid = $row['b4_amt_paid'];
				$this->amt_install = $row['amt_install'];
				$salesman = General::getEmployeeCodeAndNameForID($row['salesman_id']);
				$othersalesman = General::getEmployeeCodeAndNameForID($row['othersalesman_id']);
				$this->salesman = empty($salesman)?$row["salesman"]:$salesman;
				$this->salesman_id = empty($row['salesman_id'])?"":$row['salesman_id'];
                $this->othersalesman = empty($othersalesman)?$row["othersalesman"]:$othersalesman;
                $this->othersalesman_id = $row['othersalesman_id'];
                $this->technician = $row['technician'];
                $this->technician_id = $row['technician_id'];
				$this->sign_dt = General::toDate($row['sign_dt']);
				$this->ctrt_end_dt = General::toDate($row['ctrt_end_dt']);
				$this->ctrt_period = $row['ctrt_period'];
				$this->cont_info = $row['cont_info'];
				$this->first_dt = General::toDate($row['first_dt']);
				$this->first_tech = $row['first_tech'];
				$this->first_tech_id = $row['first_tech_id'];
				$this->reason = $row['reason'];
				$this->tracking = $row['tracking'];
				$this->status_dt = General::toDate($row['status_dt']);
				$this->status = $row['status'];
				$this->remarks = $row['remarks'];
				$this->remarks2 = $row['remarks2'];
				$this->equip_install_dt = General::toDate($row['equip_install_dt']);
				$this->org_equip_qty = $row['org_equip_qty'];
				$this->rtn_equip_qty = $row['rtn_equip_qty'];
				$this->need_install = $row['need_install'];
				$this->no_of_attm['serviceka'] = $row['no_of_attm'];
                $this->city = $row['city'];
                $this->surplus = $row['surplus'];
                $this->all_number = $row['all_number'];
                $this->surplus_edit0 = $row['surplus_edit0'];
                $this->all_number_edit0 = $row['all_number_edit0'];
                $this->surplus_edit1 = $row['surplus_edit1'];
                $this->all_number_edit1 = $row['all_number_edit1'];
                $this->surplus_edit2 = $row['surplus_edit2'];
                $this->all_number_edit2 = $row['all_number_edit2'];
                $this->surplus_edit3 = $row['surplus_edit3'];
                $this->all_number_edit3 = $row['all_number_edit3'];
                //var_dump($row['cust_type_name']);
                $this->cust_type_name = $row['cust_type_name'];
                $this->pieces = $row['pieces'];
                $this->prepay_month = $row['prepay_month'];
                $this->prepay_start = $row['prepay_start'];
                $this->contract_no = $row['contract_no'];
                $this->send = $row['send'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->office_id = $row['office_id'];
                $this->u_system_id = $row['u_system_id'];
//                print_r('<pre>');
//                print_r($this);exit();
				break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->historySave($connection);
			$this->saveService($connection);
			$this->updateServiceContract($connection);
			$this->updateDocman($connection,'SERVICEKA');
            $this->updateContractNoContract($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

    //获取服务操作记录
    public static function getServiceHistoryRows($bot_id){
        $rows = Yii::app()->db->createCommand()->select("update_html,lcu,lcd")
            ->from("swo_service_history")
            ->where("service_id=:service_id and service_type=3",array(":service_id"=>$bot_id))->order("lcd desc")->queryAll();
        return $rows;
    }

    //保存历史记录
    protected function historySave(&$connection){
        $uid = Yii::app()->user->id;
        $list=array("service_id"=>$this->id,"lcu"=>$uid,"service_type"=>3,"update_type"=>1,"update_html"=>array());
        switch ($this->getScenario()){
            case "delete":
                //$connection->createCommand()->delete("swo_service_history", "service_id=:id", array(":id" => $this->id));
                $this->delHistorySave();
                break;
            case "edit":
                $model = new ServiceKAForm();
                $model->retrieveData($this->id);
                $keyArr = parent::historyUpdateList($this->getScenario());
                foreach ($keyArr as $key){
                    if($model->$key!=$this->$key){
                        $list["update_html"][]="<span>".$this->getAttributeLabel($key)."：".self::getNameForValue($key,$model->$key)." 修改为 ".self::getNameForValue($key,$this->$key)."</span>";
                    }
                }
                if(!empty($list["update_html"])){
                    $list["update_html"] = implode("<br/>",$list["update_html"]);
                    $connection->createCommand()->insert("swo_service_history", $list);
                }
                break;
        }
    }

    protected function delHistorySave(){
        $model = new ServiceForm();
        $model->retrieveData($this->id);
        $keyArr = self::historyUpdateList($model->status);
        $delText=array();
        $delText[]="id：".$this->id;
        $delText[]="服务状态：".General::getStatusDesc($model->status);
        foreach ($keyArr as $key){
            $delText[]=$this->getAttributeLabel($key)."：".self::getNameForValue($key,$model->$key);
        }
        $delText= implode("<br/>",$delText);
        $systemLogModel = new SystemLogForm();
        $systemLogModel->log_date=date("Y/m/d H:i:s");
        $systemLogModel->log_user=Yii::app()->user->id;
        $systemLogModel->log_type=get_class($this);
        $systemLogModel->log_type_name="KA客户服务";
        $systemLogModel->option_str="删除";
        $systemLogModel->option_text=$delText;
        $systemLogModel->insertSystemLog("D");
    }

	protected function updateServiceContract(&$connection) {
		if ($this->scenario=='delete') {
			$sql = "delete from swo_service_ka_no where service_id=".$this->id;
			$connection->createCommand($sql)->execute();
		}
	}

    protected function updateContractNoContract(&$connection) {
        if (empty($this->contract_no)&&$this->scenario=='edit') {
            $sql = "delete from swo_service_ka_no where service_id=".$this->id;
            $connection->createCommand($sql)->execute();
        }elseif(!empty($this->contract_no)&&$this->scenario!='delete'){
            $sql = "insert into swo_service_ka_no(contract_no,service_id,status_dt,status)
             values('$this->contract_no','$this->id','$this->status_dt','$this->status') on duplicate 
            key update contract_no='$this->contract_no', status_dt='$this->status_dt', status='$this->status'";
            $connection->createCommand($sql)->execute();
        }
    }

	protected function saveService(&$connection)
	{
		$sql = array();
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_service_ka where id = :id";
				$this->execSql($connection,$sql);
				break;
			case 'renew':
			case 'new':
			case 'amend':
			case 'suspend':
			case 'terminate':
			case 'resume':
				$sql = "insert into swo_service_ka(
							service_new_id,company_id, company_name, product_id, service, nature_type, nature_type_two, cust_type, 
							paid_type, amt_paid, amt_install, need_install, salesman_id, salesman,othersalesman_id,othersalesman,technician_id,technician, sign_dt, b4_product_id,
							b4_service, b4_paid_type, b4_amt_paid, 
							ctrt_period, cont_info, first_dt, first_tech_id, first_tech, reason,tracking,
							status, status_dt, remarks, remarks2, ctrt_end_dt,
							equip_install_dt, org_equip_qty, rtn_equip_qty, cust_type_name,pieces,office_id,u_system_id,
							city, luu, lcu,all_number,surplus,all_number_edit0,surplus_edit0,all_number_edit1,surplus_edit1,all_number_edit2,surplus_edit2,all_number_edit3,surplus_edit3,prepay_month,prepay_start
						) values (
							:service_new_id,:company_id, :company_name, :product_id, :service, :nature_type, :two_nature_type, :cust_type, 
							:paid_type, :amt_paid, :amt_install, :need_install, :salesman_id, :salesman,:othersalesman_id,:othersalesman,:technician_id,:technician, :sign_dt, :b4_product_id,
							:b4_service, :b4_paid_type, :b4_amt_paid, 
							:ctrt_period, :cont_info, :first_dt, :first_tech_id, :first_tech, :reason,:tracking,
							:status, :status_dt, :remarks, :remarks2, :ctrt_end_dt,
							:equip_install_dt, :org_equip_qty, :rtn_equip_qty, :cust_type_name,:pieces,:office_id,:u_system_id,
							:city, :luu, :lcu,:all_number,:surplus,:all_number_edit0,:surplus_edit0,:all_number_edit1,:surplus_edit1,:all_number_edit2,:surplus_edit2,:all_number_edit3,:surplus_edit3,:prepay_month,:prepay_start
						)";
				$this->execSql($connection,$sql);
				$this->id = Yii::app()->db->getLastInsertID();
                Yii::app()->db->createCommand()->update("swo_service_ka",array(
                    "service_no"=>"KA".$this->status.(100000+$this->id)
                ),"id=".$this->id);
				break;
			case 'edit':
				$sql = "update swo_service_ka set                      
							company_id = :company_id, 
							company_name = :company_name, 
							cust_type_name=:cust_type_name,
							cust_type = :cust_type,
							product_id = :product_id, 
							nature_type = :nature_type,
							nature_type_two = :two_nature_type,
							pieces=:pieces,
							service = :service, 
							paid_type = :paid_type, 
							amt_paid = :amt_paid, 
							b4_product_id = :b4_product_id, 
							b4_service = :b4_service, 
							b4_paid_type = :b4_paid_type, 
							b4_amt_paid = :b4_amt_paid, 
							amt_install = :amt_install, 
							need_install = :need_install,
							salesman_id = :salesman_id, 
							salesman = :salesman, 
							othersalesman_id=:othersalesman_id,
							othersalesman=:othersalesman,
							technician_id = :technician_id,
							technician = :technician,
							sign_dt = :sign_dt,
							ctrt_end_dt = :ctrt_end_dt,
							ctrt_period = :ctrt_period, 
							cont_info = :cont_info, 
							first_dt = :first_dt, 
							first_tech_id = :first_tech_id, 
							first_tech = :first_tech, 
							reason = :reason,
							tracking = :tracking,
							remarks = :remarks,
							remarks2 = :remarks2,
							status = :status, 
							status_dt = :status_dt,
							equip_install_dt = :equip_install_dt,
							org_equip_qty = :org_equip_qty,
							rtn_equip_qty = :rtn_equip_qty,
							all_number = :all_number, 
                            surplus = :surplus, 
                            all_number_edit0 = :all_number_edit0, 
                            surplus_edit0 = :surplus_edit0, 
                            all_number_edit1 = :all_number_edit1, 
                            surplus_edit1 = :surplus_edit1, 
                            all_number_edit2 = :all_number_edit2, 
                            surplus_edit2 = :surplus_edit2, 
                            all_number_edit3 = :all_number_edit3, 
                            surplus_edit3 = :surplus_edit3, 
                            prepay_month = :prepay_month, 
                            prepay_start = :prepay_start,                  
                            u_system_id = :u_system_id,                  
							luu = :luu 
						where id = :id 
						";
				$this->execSql($connection,$sql);
				break;
		}

		return true;
	}

	//发送邮件
    public function sendemail($reason_id,$year,$month,$company,$service_id){
        $suffix = Yii::app()->params['envSuffix'];
        //发送邮箱
        $sql1 = "SELECT email FROM swo_company WHERE  concat(`code`,`name`) = '".$company."' order by id desc limit 1";
        $rs = Yii::app()->db->createCommand($sql1)->queryRow();
        $email = $rs['email'];
        if (empty($email)){
            return "<script language=javascript>alert('客户邮箱不存在');history.back();</script>";
        }
        //原因内容
        $sql2 = "SELECT * FROM swo_service_end_reasons WHERE  id=".$reason_id;
        $reason = Yii::app()->db->createCommand($sql2)->queryRow();
        $content = $reason['content'];
        $reason = $reason['reason'];
//        $this->webroot = Yii::app()->params['webroot'];
        $subject = "史伟莎服务暂停或终止邮件通知".date('Y-m-d');
        $message = <<<EOF
        <p>尊敬的客户: </p>
<p style="text-indent:2em;">
贵店由于{$reason}，服务将从{$year}年{$month}月开始暂停，请知悉！</p>
<p style="text-indent:2em;">{$content}</p>
EOF;
//        	<tr height="36">
//			<td colspan="6" height="36" style="height:36px;width:663px;" x:num="44275"><span style="font-size:14px;">{$subject}</span></td>
//		</tr>
        $from_addr = "no-reply@lbsgroup.com.cn";
        $to_addr = "[\"" .$email."\"]";
        $description = "</<br>" .date('Y-m-d');
        $lcu = "admin";

        $aaa = Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
            'request_dt' => date('Y-m-d H:i:s'),
            'from_addr' => $from_addr,
            'to_addr' => $to_addr,
            'subject' => $subject,//郵件主題
            'description' => '',//郵件副題
            'message' => $message,//郵件內容（html）
            'status' => "P",
            'lcu' => $lcu,
            'lcd' => date('Y-m-d H:i:s'),
        ));

        //改变服务邮箱发送状态
        $sql_s="update swo_service_ka set send ='Y'  where id='$service_id'";
        $record = Yii::app()->db->createCommand($sql_s)->execute();
        return "<script language=javascript>alert('发送成功');history.back();</script>";

    }

    public function getReadonly(){
        return $this->scenario=='view'||$this->commission!==null||$this->other_commission!==null;
    }
}
