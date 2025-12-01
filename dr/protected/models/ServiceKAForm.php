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
        $isVivienne = ServiceForm::isVivienne();
        $thisDate = $isVivienne?"0000/00/00":date("Y/m/01");
        $maxDate = $isVivienne?"9999/12/31":date("Y/12/31");
        $status_dt = date("Y/m/d",strtotime($this->status_dt));
        $scenario = $this->getScenario();
        if(in_array($scenario,array("renew","new","amend","suspend","terminate","resume"))){
            $this->ltNowDate = $status_dt<$thisDate;
            //验证新增
            if($this->ltNowDate){
                $this->addError($attribute, "无法新增({$status_dt})时间段的数据");
            }
        }else{
            $id= empty($this->id)?0:$this->id;
            $row = Yii::app()->db->createCommand()->select("a.*,b.contract_no")->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no b","b.service_id=a.id")
                ->where("a.id=:id",array(":id"=>$id))->queryRow();
            if($row){
                $row["status_dt"] = date("Y/m/d",strtotime($row["status_dt"]));
                $this->status_dt = $isVivienne?$this->status_dt:$row["status_dt"];
                $row["first_dt"] = date("Y/m/d",strtotime($row["first_dt"]));
                $this->ltNowDate = $row["status_dt"]<$thisDate;
                if($scenario=="delete"){
                    if(!empty($row["commission"])||!empty($row["other_commission"])){
                        $this->addError($attribute, "该服务已参加销售提成计算，无法删除");
                    }
                    if($this->ltNowDate){
                        $this->addError($attribute, "无法删除({$row["status_dt"]})时间段的数据");
                    }
                }else{
                    if($row["status_dt"]<$maxDate&&$status_dt>$maxDate){
                        $this->addError($attribute, "无法跨年修改，{$row["status_dt"]}无法修改成{$status_dt}");
                    }else{
                        $updateBool = !empty($row["commission"])||!empty($row["other_commission"]);
                        $updateBool = $updateBool||$status_dt<$thisDate;//验证修改后的时间
                        $updateBool = $updateBool||$row["status_dt"]<$thisDate;//验证修改前的时间
                        if($updateBool){//不是特定账号，不允许修改
                            $notUpdate=self::getNotUpdateList();
                            foreach ($notUpdate as $item){
                                $this->$item = $row[$item];
                            }
                        }
                    }
                }
            }else{
                $this->addError($attribute, "数据异常，请刷新重试");
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

    public function dataCopy($index){
        $row = Yii::app()->db->createCommand()->select("a.*,b.contract_no")
            ->from("swo_service_ka a")
            ->leftJoin("swo_service_ka_no b","a.id=b.service_id")
            ->where("a.id=:id",array(":id"=>$index))
            ->queryRow();
        if($row){
            $thisDate = ServiceForm::isVivienne()?"0000/00/00":date("Y/m/01");
            $status_dt = date("Y/m/d",strtotime($row["status_dt"]));
            if(empty($row["contract_no"])){
                Dialog::message(Yii::t('dialog','Validation Message'), "合同编号为空，无法复制");
                return false;
            }elseif($status_dt<$thisDate){
                Dialog::message(Yii::t('dialog','Validation Message'), "无法复制({$status_dt})时间段的数据");
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
                $data["contract_type"]=$data["contract_type"]==1?0:$data["contract_type"];
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
                    "change_amt"=>$this->getHistoryChangeAmt($data),
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
            $thisDate = date("Y/m/01");
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
                $this->ltNowDate = ServiceForm::isVivienne()?false:$this->status_dt<$thisDate;
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
                $this->surplus_amt = $row['surplus_amt']===null?"":floatval($row['surplus_amt']);
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
                $this->contract_type = $row['contract_type'];
                $this->u_system_id = $row['u_system_id'];
                $this->is_intersect = $row['is_intersect'];
                $this->external_source = self::getExternalSourceList($row['external_source'],true);
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
                $keyArr = parent::historyUpdateList($this->status);
                foreach ($keyArr as $key){
                    if($model->$key!=$this->$key){
                        $list["update_html"][]="<span>".$this->getAttributeLabel($key)."：".self::getNameForValue($key,$model->$key)." 修改为 ".self::getNameForValue($key,$this->$key)."</span>";
                    }
                }
                if(!empty($list["update_html"])){
                    $list["update_html"] = implode("<br/>",$list["update_html"]);
                    $list["change_amt"] = $this->getHistoryChangeAmt($this->getAttributes(),$model->getAttributes());
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
        $systemLogModel->city=$this->city;
        $systemLogModel->change_amt=$this->getHistoryChangeAmt($this->getAttributes());
        $systemLogModel->change_amt*=-1;
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
                Yii::app()->db->createCommand()->delete("swo_service_ka_no","service_id=".$this->id);
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
							equip_install_dt, org_equip_qty, rtn_equip_qty, cust_type_name,pieces,office_id,contract_type,u_system_id,
							city, luu, lcu,all_number,surplus,surplus_amt,all_number_edit0,surplus_edit0,all_number_edit1,surplus_edit1,all_number_edit2,surplus_edit2,all_number_edit3,surplus_edit3,prepay_month,prepay_start
						) values (
							:service_new_id,:company_id, :company_name, :product_id, :service, :nature_type, :two_nature_type, :cust_type, 
							:paid_type, :amt_paid, :amt_install, :need_install, :salesman_id, :salesman,:othersalesman_id,:othersalesman,:technician_id,:technician, :sign_dt, :b4_product_id,
							:b4_service, :b4_paid_type, :b4_amt_paid, 
							:ctrt_period, :cont_info, :first_dt, :first_tech_id, :first_tech, :reason,:tracking,
							:status, :status_dt, :remarks, :remarks2, :ctrt_end_dt,
							:equip_install_dt, :org_equip_qty, :rtn_equip_qty, :cust_type_name,:pieces,:office_id,:contract_type,:u_system_id,
							:city, :luu, :lcu,:all_number,:surplus,:surplus_amt,:all_number_edit0,:surplus_edit0,:all_number_edit1,:surplus_edit1,:all_number_edit2,:surplus_edit2,:all_number_edit3,:surplus_edit3,:prepay_month,:prepay_start
						)";
				$this->execSql($connection,$sql);
				$this->id = Yii::app()->db->getLastInsertID();
                Yii::app()->db->createCommand()->update("swo_service_ka",array(
                    "service_no"=>"KA".$this->status.(100000+$this->id)
                ),"id=".$this->id);
                //增加新增记录
                Yii::app()->db->createCommand()->insert("swo_service_history", array(
                    "service_id"=>$this->id,
                    "lcu"=>Yii::app()->user->id,
                    "service_type"=>3,
                    "update_type"=>2,
                    "update_html"=>"新增",
                    "change_amt"=>$this->getHistoryChangeAmt($this->getAttributes())
                ));
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
                            surplus_amt = :surplus_amt, 
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
                            office_id = :office_id,                  
                            contract_type = :contract_type,                  
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
        return $this->scenario=='view'||$this->commission!==null||$this->other_commission!==null||$this->ltNowDate;
    }

    public function resetService(){
        $idList = array(40617,40759,40762,40763,40957,40976,40977,40978,40979,40980,40981,40982,40983,40984,40985,40986,40987,40988,40989,40990,40991,40992,40993,40994,40995,40996,40997,40998,40999,41000,41001,41002,41003,41004,41005,41006,41007,41008,41009,41010,41011,41012,41013,41014,41015,41016,41017,41018,41019,41020,41021,41022,41023,41024,41026,41027,41028,41029,41030,41031,41032,41033,41034,41035,41036,41037,41038,41039,41040,41041,41042,41043,41044,41045,41046,41047,41048,41049,41050,41051,41052,41053,41054,41055,41061,41062,41063,41064,41065,41066,41067,41068,41069,41070,41071,41072,41073,41074,41075,41076,41077,41078,41079,41080,41056,41057,41058,41059,41060,41365,41366,41367,41368,41369,41370,41371,41372,41373,41374,41375,41376,41377,41378,41379,41380,41381,41382,41383,41384,41385,41386,41387,41388,41389,41390,41391,41392,41393,41394,41395,41396,41397,41398,41399,41400,41401,41402,41403,41404,41405,41406,41407,41408,41409,41410,41411,41412,41413,41414,41415,41416,41417,41418,41419,41420,41421,41422,41423,41424,41425,41426,41427,41428,41429,41430,41431,41432,41433,41434,41435,41436,41437,41438,41439,41440,41441,41442,41443,41444,41445,41446,41447,41448,41449,41450,41451,41452,41453,41454,41455,41456,41457,41458,41459,41460,41461,41462,41463,41464,41465,41466,41467,41468,41469,41470,41471,41472,41473,41474,41475,41476,41477,41478,41479,41480,41481,41482,41483,41484,41485,41486,41488,41489,41490,41491,41492,41493,41494,41495,41496,41497,41498,41499,41500,41501,41502,41503,41504,41525,41526,41527,41528,41529,41530,41531,41532,41533,41534,41535,41536,41537,41538,41539,41540,41541,41542,41543,41544,41505,41506,41507,41508,41509,41510,41511,41512,41513,41514,41515,41516,41517,41518,41519,41520,41521,41522,41523,41524,41565,41566,41567,41568,41569,41570,41571,41572,41573,41574,41575,41576,41577,41579,41580,41581,41582,41583,41584,41545,41546,41547,41548,41549,41550,41551,41552,41553,41554,41555,41556,41557,41558,41559,41560,41561,41562,41563,41564,41605,41606,41607,41608,41609,41610,41611,41612,41613,41614,41615,41616,41617,41618,41619,41620,41621,41622,41623,41624,41585,41586,41587,41588,41589,41590,41591,41592,41593,41594,41595,41596,41597,41598,41599,41600,41601,41602,41603,41604,41625,41626,41627,41628,41629,41630,41631,41632,41633,41634,41635,41636,41637,41638,41639,41640,41641,41642,41643,41644,41645,41646,41647,41648,41649,41650,41651,41652,41653,41654,41655,41656,41657,41658,41659,41660,41661,41662,41663,41664,41685,41686,41687,41688,41689,41690,41691,41693,41694,41695,41696,41697,41698,41699,41700,41701,41702,41703,41704,41665,41666,41667,41668,41669,41670,41671,41672,41673,41674,41675,41676,41677,41678,41679,41680,41681,41682,41683,41684,41783,42667,42668,42669,42670,42671,42672,42673,42675,42676,42678,42679,42680,42681,42683,42684,42685,42686,42687,42688,42689,42690,42691,42692,42693,42694,42695,42696,42697,42698,42699,42700,42701,42702,42706,42707,42674,44180,44250,44251,44252,45600,45713,45515,45517,45520,45551,45620,45640,45480,45497,45499,45473,45587,45510,45524,45526,45576,45613,45633,45667,45703,45706,45656,45591,45624,45695,45700,45718,45470,45584,45592,45479,45541,45658,45554,45571,45623,45492,45493,45506,45628,45662,45663,45669,45693,45724,45535,45468,45471,45474,45475,45478,45482,45483,45484,45485,45489,45490,45491,45495,45496,45498,45500,45501,45502,45503,45504,45505,45507,45508,45509,45511,45512,45513,45516,45518,45519,45521,45523,45525,45527,45530,45531,45533,45534,45537,45538,45539,45540,45544,45546,45545,45548,45549,45550,45553,45555,45556,45558,45559,45560,45561,45562,45565,45569,45568,45572,45573,45575,45578,45579,45581,45582,45583,45585,45588,45593,45594,45595,45597,45598,45599,45601,45603,45605,45606,45607,45608,45609,45610,45612,45615,45614,45616,45617,45621,45625,45626,45627,45629,45630,45632,45634,45635,45639,45641,45642,45643,45645,45654,45657,45659,45660,45666,45665,45664,45668,45671,45672,45674,45673,45677,45681,45683,45682,45689,45688,45692,45694,45696,45697,45702,45707,45710,45712,45711,45715,45714,45717,45719,45721,45722,45723,45528,45529,45542,45577,45596,45611,45646,45670,45685,45698,45699,45705,45494,45543,45469,45477,45481,45868,45888,45945,45966,46039,45970,46034,45846,45898,45905,45941,45884,45894,45912,45915,45921,45982,45984,45989,46013,46022,46028,46030,45993,45759,45880,45985,45992,46000,46011,45862,45756,45943,45990,45961,45924,46007,45744,45745,45746,45747,45749,45751,45755,45757,45758,45760,45761,45762,45764,45763,45766,45769,45783,45784,45785,45845,45859,45858,45861,45867,45865,45866,45869,45872,45877,45882,45883,45889,45890,45891,45892,45895,45896,45900,45902,45908,45909,45910,45913,45918,45919,45922,45923,45925,45926,45929,45930,45931,45932,45936,45937,45938,45939,45942,45944,45947,45946,45949,45952,45954,45955,45957,45958,45959,45960,45965,45963,45967,45968,45969,45973,45974,45976,45975,45978,45979,45980,45981,45987,45988,45991,45995,45996,45998,45999,46001,46002,46005,46008,46009,46014,46015,46016,46020,46018,46021,46024,46025,46027,46029,46031,46032,46037,46038,46040,45750,45753,45765,45860,45864,45875,45886,45893,45934,45940,45983,45997,46019,45797,46139,46118,46254,46132,46137,46188,46187,46184,46209,46228,46120,46147,46150,46246,46121,46203,46215,46250,46130,46167,46154,46206,46204,46220,46241,46281,46284,46119,46122,46124,46128,46135,46136,46138,46141,46142,46143,46144,46146,46151,46152,46153,46155,46156,46159,46158,46160,46161,46162,46163,46164,46165,46166,46168,46169,46170,46171,46173,46174,46175,46176,46177,46178,46179,46180,46181,46183,46185,46186,46189,46190,46191,46192,46193,46194,46196,46197,46199,46201,46200,46205,46208,46207,46210,46211,46212,46214,46213,46216,46218,46219,46222,46223,46224,46227,46225,46226,46229,46231,46232,46234,46235,46237,46239,46244,46242,46248,46252,46256,46258,46260,46263,46265,46268,46266,46270,46272,46273,46276,46140,46149,46182,46195,46198,46221,46279,46290,46603,50882,50887,50889,50922,53578,40793,41705,41706,41707,41708,41709,41710,41711,41712,41713,41714,41715,41716,41717,41718,41719,41720,41721,41722,41723,41724,41725,41726,41727,41728,41729,41730,41731,41732,41733,41734,41735,41736,41737,41738,41739,41740,41741,41742,41743,41744,41763,41764,41765,41766,41767,41768,41769,41770,41771,41772,41773,41774,41775,41776,41777,41778,41779,41780,41781,41782,41745,41746,41747,41748,41749,41750,41751,41752,41753,41754,41755,41756,41757,41758,41759,41760,41761,41762,42648,42649,42650,42651,42652,42653,42654,42656,42657,42658,42659,42660,42661,42662,42663,45563,45564,45566,45567,45570,45580,45602,45604,45618,45619,45622,45631,45636,45638,45661,45675,45678,45679,45680,45684,45686,45687,45701,45704,45708,45709,45716,45547,45590,45644,45653,45720,45871,45748,45754,45782,45807,45847,45863,45876,45878,45903,45911,45916,45914,45917,45933,45948,45951,45953,45964,45962,45971,45977,45986,45994,46004,46003,46006,46010,46017,46023,46033,46035,46036,45752,45767,45786,45874,45904,45920,45927,45956,45972,46058,46059,46060,46061,46064,46066,46068,46069,46070,46071,46072,46073,46075,46078,46079,46081,46083,46085,46089,46092,46063,50881,41785,53033,53046,53047,53048,53077,53056,53057,53058,53059,53060,53061,53062,53063,53249,53250,53254,53318,53349,53352,53355,53357,53358,53415,53416,53421,53422,53424,53433,53435,53437,53438,53777,52972,53049,53050,53051,53052,53054,53055,53064,53716,53701,53703,53706,53720,53721,52955);

        $idStr = implode(",",$idList);
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("swo_service_ka")->where("id in ({$idStr})")->queryAll();
        $uid = "updateAdmin";
        if($rows){
            foreach ($rows as $row){
                $oldRow = $row;
                $nowRow = $row;
                $nowRow["paid_type"]=1;
                $nowRow["ctrt_period"]=1;
                $list=array("service_id"=>$row["id"],"lcu"=>$uid,"service_type"=>3,"update_type"=>1,"update_html"=>array());

                $list["update_html"][]="<span>服务金额：".self::getNameForValue("paid_type",$oldRow["paid_type"])." 修改为 ".self::getNameForValue("paid_type",$nowRow["paid_type"])."</span>";
                $list["update_html"][]="<span>合同年限(月)：".self::getNameForValue("ctrt_period",$oldRow["ctrt_period"])." 修改为 ".self::getNameForValue("ctrt_period",$nowRow["ctrt_period"])."</span>";

                $list["update_html"] = implode("<br/>",$list["update_html"]);
                $list["change_amt"] = $this->getHistoryChangeAmt($nowRow,$oldRow);
                Yii::app()->db->createCommand()->insert("swo_service_history", $list);
                Yii::app()->db->createCommand()->update("swo_service_ka",array(
                    "paid_type"=>1,
                    "ctrt_period"=>1,
                ),"id=".$row["id"]);
            }
        }
    }
}
