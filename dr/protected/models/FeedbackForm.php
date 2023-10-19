<?php

class FeedbackForm extends CFormModel
{
	public $id;
	public $city;
	public $request_dt;
	public $feedback_dt;
	public $status;
	public $status_desc;
	
	public $to;
	public $cc;
	public $rpt_id;

	public $cat_1="N";
	public $feedback_1;
	public $cat_2="N";
	public $feedback_2;
	public $cat_3="N";
	public $feedback_3;
	public $cat_4="N";
	public $feedback_4;
	public $cat_5="N";
	public $feedback_5;
	public $cat_6="N";
	public $feedback_6;
	public $cat_7="N";
	public $feedback_7;
	public $cat_8="N";
	public $feedback_8;
	public $cat_9="N";
	public $feedback_9;
	public $cat_10="N";
	public $feedback_10;
	public $cat_11="N";
	public $feedback_11;
	public $cat_12="N";
	public $feedback_12;

	public $cats = array(
		'A1~'=>'Customer Service',
		'A2~'=>'Complaint Cases',
		'A3~'=>'Customer Enquiry',
		'A4~'=>'Product Delivery',
		'A5~'=>'QC Record',
		'A6~'=>'Staff Info',
		'A7~'=>'Others',
		'A8~'=>'Service New',//当月累计新增
		'A9~'=>'Service Stop',//当月累计终止
		'A10~'=>'Service Pause',//当月累计暂停
		'A11~'=>'Service Net',//当月累计净增长
		'A12~'=>'Sales Effect',//当月累计销售人效
	);

	public function attributeLabels()
	{
		$lbl = array(
            'city'=>Yii::t('misc','City'),
			'request_dt'=>Yii::t('feedback','Request Date'),
			'feedback_dt'=>Yii::t('feedback','Feedback Date'),
			'feedback'=>Yii::t('feedback','Feedback'),
			'status_desc'=>Yii::t('feedback','Status'),
			'feedback_cat'=>Yii::t('feedback','Feedback Type'),
			'to'=>Yii::t('feedback','To'),
			'cc'=>Yii::t('feedback','Cc').'<br>('.Yii::t('dialog','Hold down <kbd>Ctrl</kbd> button to select multiple options').')',
		);
		$cnt=0;
		foreach ($this->cats as $cat=>$desc){
			$cnt++;
			$lbl['cat_'.$cnt] = Yii::t('app',$desc);
		}
		return $lbl;
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		$cat_list = '';
		$feedback_list = '';
		$cnt = 0;
		foreach ($this->cats as $cat){
			$cnt++;
			$cat_list .= empty($cat_list) ? 'cat_'.$cnt : ',cat_'.$cnt;
			$feedback_list .= empty($feedback_list) ? 'feedback_'.$cnt : ',feedback_'.$cnt;
		}
		
		return array(
			array('id, city, request_dt, feedback_dt, status, status_desc, to, cc, rpt_id,'.$cat_list,'safe'),
			array("cat_1",'validateType'),
			array($feedback_list,'validateRemarks'),
			array("id",'validateCity'),
			array("cat_9",'validateMustSNN'),
			array("cat_10",'validateMustP'),
			array("cat_12",'validateMustSales'),
		);
	}

	//进入表单自动验证
    public function validateLoad(){
	    if(!$this->hasErrors()){
            $this->validateCity("id","");
            $this->validateMustSNN("cat_9","");
            $this->validateMustP("cat_10","");
            $this->validateMustSales("cat_12","");
        }
    }

	//验证哪些内容必须填写(累计销售人效)
	public function validateMustSales($attribute, $params){
	    if($this->cat_12=="Y"){
	        return true;
        }
        $start_date = date("Y/01/01",strtotime($this->request_dt));
        $search_year = date("Y",strtotime($this->request_dt));
        $search_month = date("n",strtotime($this->request_dt));
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        $lifelineList = LifelineForm::getLifeLineList($city_allow,$this->request_dt);//生命线
        $staffRows = SalesAnalysisForm::getSalesForHr($city_allow,$this->request_dt);//员工信息
        $nowData = SalesAnalysisForm::getNowYearData($start_date,$this->request_dt,$city_allow);//本年度的数据
        foreach ($staffRows as $staffRow){
            $username = $staffRow["user_id"];
            $city = $staffRow["city"];
            //生命线
            $life_num = LifelineForm::getLineValueForC_O($lifelineList,$city,$staffRow["office_id"]);

            $username = $staffRow["user_id"];
            $timer = strtotime($staffRow["entry_time"]);
            $entry_year = date("Y",$timer);
            $entry_month = date("n",$timer);
            $validateList = array();
            for ($i=1;$i<=$search_month;$i++){
                $yearMonth = $search_year."/".($i<10?"0{$i}":$i);
                $value=0;
                if(key_exists($username,$nowData)){
                    $value = key_exists($yearMonth,$nowData[$username])?$nowData[$username][$yearMonth]:0;
                }
                if($entry_year==$search_year){
                    if($entry_month>$i){//未入职显示空
                        $value="";
                    }elseif ($entry_month==$i&&empty($value)){//入职当月且没有签单金额
                        $value="-";
                    }
                }
                if($value!==""&&is_numeric($value)&&$value<$life_num){//有效数据
                    $validateList[$yearMonth]=$staffRow;
                }else{
                    $validateList=array();
                }
                if(count($validateList)>=3){//连续三个月未达标
                    $message = "员工：".$staffRow["user_id"]."连续三个月未达标，请填写原因";
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
    }

	//验证哪些内容必须填写(累积暂停)
	public function validateMustP($attribute, $params){
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        //暂停已超过两个月的验证
        $startDate = date("Y/m/d",strtotime($this->request_dt." - 4 months"));//由于数据量过大所以只查4个月内
        $endDate = date("Y/m/d",strtotime($this->request_dt." - 2 months"));
        $pauseRows = SummaryTable::getServiceRows($startDate,$endDate,$city_allow,'S');
        if($pauseRows&&$this->cat_10=="N"){
            $message = "当月累计暂停超过2个月，请填写原因";
            $this->addError($attribute,$message);
        }
    }

	//验证哪些内容必须填写(累积新增、累积终止、累积净增长)
	public function validateMustSNN($attribute, $params){
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $timer = strtotime($this->request_dt);
        $month_day = date("t",$timer);
        $start_dt = date("Y/m/01",$timer);
        $end_dt = $this->request_dt;
        $search_month = date("n",$timer);
        $week_end = $timer;
        $week_start = HistoryAddForm::getDateDiffForMonth($timer,6,$search_month,false);
        $week_day = HistoryAddForm::getDateDiffForDay($week_start,$week_end);
        $weekStartDate = date("Y/m/d",$week_start);
        $weekEndDate = date("Y/m/d",$week_end);
        $moneyRow = Yii::app()->db->createCommand()->select("two_gross,two_net")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=1 and city=:city",
                array(":year"=>date("Y",strtotime($start_dt)),":city"=>$this->city)
            )->queryRow();//查询目标金额
        $two_gross = $moneyRow?$moneyRow["two_gross"]:0;
        $two_net = $moneyRow?$moneyRow["two_net"]:0;
        //本週數據
        $weekList = CountSearch::getServiceCountForStatus($weekStartDate,$weekEndDate,$city_allow);
        $diffArr = array(
            "add_sum"=>0,
            "pause_sum"=>0,
            "update_sum"=>0,
            "renew_sum"=>0,
            "stop_sum"=>0,
            "net_sum"=>0,
        );
        $data=array();
        foreach ($citySetList as $cityRow) {
            $city = $cityRow["code"];
            if(key_exists($city,$weekList)){
                $defMoreList = $weekList[$city];
            }else{
                $defMoreList = $diffArr;
            }
            FeedbackTable::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $cityData = $data[$this->city];
        $cityData["add_sum"]=($cityData["add_sum"]/$week_day)*$month_day;
        $cityData["add_sum"] = round($cityData["add_sum"],2);
        $cityData["stop_sum"]=($cityData["stop_sum"]/$week_day)*$month_day;
        $cityData["stop_sum"] = round($cityData["stop_sum"],2);
        $cityData["net_sum"]=($cityData["net_sum"]/$week_day)*$month_day;
        $cityData["net_sum"] = round($cityData["net_sum"],2);

        if($this->cat_8=="N"&&HistoryAddForm::comYes($cityData["add_sum"],$two_gross)!==Yii::t("summary","Yes")){
            $message = "当月累计新增未达标，请填写原因";
            $this->addError("cat_8",$message);
        }
        if($this->cat_9=="N"&&HistoryAddForm::comYes($cityData["stop_sum"],-1*$two_gross,true)!==Yii::t("summary","Yes")){
            $message = "当月累计终止未达标，请填写原因";
            $this->addError("cat_9",$message);
        }
        if($this->cat_11=="N"&&HistoryAddForm::comYes($cityData["net_sum"],$two_net)!==Yii::t("summary","Yes")){
            $message = "当月累计净增长未达标，请填写原因";
            $this->addError("cat_11",$message);
        }
    }

	public function validateCity($attribute, $params){
        $city = Yii::app()->user->city();
        $id = empty($this->id)?0:$this->id;
        $sql = "select city from swo_mgr_feedback where id={$id}";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row) {
		    if($row["city"]!=$city){
                $message = "不允许保存其它城市的回馈";
                $this->addError($attribute,$message);
            }else{
		        $this->city = $row["city"];
            }
		}else{
            $message = "回馈不存在，请刷新重试";
            $this->addError($attribute,$message);
        }
	}

	public function validateType($attribute, $params){
		$flag = false;
		$cnt = 0;
		foreach ($this->cats as $cat) {
			$cnt++;
			$field = 'cat_'.$cnt;
			if ($this->$field=='Y') {
				$flag = true;
				break;
			}
		}
		if (!$flag) {
			$message = Yii::t('feedback','No feedback type is selected');
			$this->addError("id",$message);
		}
	}

	public function validateRemarks($attribute, $params){
		$field = str_replace('feedback','cat',$attribute);
		if ($this->$field=='Y' && empty($this->$attribute)) {
			$label = $this->attributeLabels();
			$message = $label[$field].' '.Yii::t('feedback','cannot be empty');
			$this->addError($field,$message);
		}
	}

	public function retrieveData($index,$mode='edit') {
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql = "select * from swo_mgr_feedback where id=$index and city in ($city_allow)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$this->id = $row['id'];
				$this->city = $row['city'];
				$this->request_dt = General::toDate($row['request_dt']);
				$this->feedback_dt = General::toDate($row['feedback_dt']);
				$this->status = $row['status'];
				$this->status_desc = General::getFeedbackStatusDesc($row['status']);
				$this->rpt_id = $row['rpt_id'];
				break;
			}
		} else
			return false;
		
		if ($mode=='edit' && Yii::app()->user->id!=$row['username']) return false;

		$to = City::model()->getAncestorInChargeList($this->city);
		$this->to = implode("; ",General::getEmailByUserIdArray($to));
		if($this->city!="CD"){
            //$this->to.= "; kittyzhou@lbsgroup.com.cn";//2023/09/08额外增加收件人
        }
//		$this->to = implode("; ",Yii::app()->params['bossEmail']);
		
		$sql = "select * from swo_mgr_feedback_rmk where feedback_id=".$index;
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$cnt = 0;
				foreach ($this->cats as $cat=>$desc) {
					$cnt++;
					if ($cat==$row['feedback_cat']) {
						$cat_field = 'cat_'.$cnt;
						$fb_field = 'feedback_'.$cnt;
						$this->$fb_field = $row['feedback'];
						$this->$cat_field = 'Y';
						break;
					}
				}
			}
		}
		
		return true;
	}
	
	public function saveData($type)
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveFeedback($connection,$type);
			$this->saveFeedbackRmk($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveFeedback(&$connection,$type)
	{
		$sql = '';
        $status = 'N';
//        类别为发送的 或者 类型为暂存但是已发送状态的
        if($type == 'send' || ($type == 'temp' && $this->status == 'Y')){
            $status = 'Y';
        }
		switch ($this->scenario) {
			case 'edit':
				$sql = ($this->status='N') 
						? "update swo_mgr_feedback set
							feedback_dt = now(),
							status = :status, 
							feedback_cat_list = :feedback_cat,
							luu = :uid 
						where id = :id and city = :city and username = :uid
						"
						: "update swo_mgr_feedback set
							feedback_cat_list = :feedback_cat,
							luu = :uid 
						where id = :id and city = :city and username = :uid
						"
						;
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		$feedback_cat = '';
		$cnt = 0;
		foreach ($this->cats as $cat=>$desc) {
			$cnt++;
			$field = 'cat_'.$cnt;
			if ($this->$field=='Y') $feedback_cat .= $cat;
		}
		
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':feedback_cat')!==false)
			$command->bindParam(':feedback_cat',$feedback_cat,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':uid')!==false)
			$command->bindParam(':uid',$uid,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$status,PDO::PARAM_STR);
		$command->execute();

		return true;
	}

	protected function saveFeedbackRmk(&$connection)
	{
        $uid = Yii::app()->user->id;
		$sql = "delete from swo_mgr_feedback_rmk where feedback_id=:id";
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		$command->execute();

		$sql = "insert into swo_mgr_feedback_rmk(feedback_id, feedback_cat, feedback, lcu, luu) 
				values(:id, :feedback_cat, :feedback, :uid, :uid)
			";
		$cnt = 0;
		foreach ($this->cats as $cat=>$desc) {
			$cnt++;
			$cfd = 'cat_'.$cnt;
			$ffd = 'feedback_'.$cnt;
			if ($this->$cfd=='Y') {
				$command=$connection->createCommand($sql);
				if (strpos($sql,':id')!==false)
					$command->bindParam(':id',$this->id,PDO::PARAM_INT);
				if (strpos($sql,':feedback_cat')!==false)
					$command->bindParam(':feedback_cat',$cat,PDO::PARAM_STR);
				if (strpos($sql,':feedback')!==false)
					$command->bindParam(':feedback',$this->$ffd,PDO::PARAM_STR);
				if (strpos($sql,':uid')!==false)
					$command->bindParam(':uid',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}

		return true;
	}
	
	public function sendNotification() {
        $suffix = Yii::app()->params['envSuffix'];
		$incharge = City::model()->getAncestorInChargeList(Yii::app()->user->city());
		$city=Yii::app()->user->city();

        $sqlcity="select name from security$suffix.sec_city where code='".$city."'";
        $cityname = Yii::app()->db->createCommand($sqlcity)->queryAll();
		$to = General::getEmailByUserIdArray($incharge);
		$to = array_merge($to, Yii::app()->params['bossEmail']);
		if($city!="CD"){//成都除外
            //$to[]= "kittyzhou@lbsgroup.com.cn";//2023/09/08额外增加收件人
        }
		$cc = empty($this->cc) ? array() : General::getEmailByUserIdArray($this->cc);
		$cc[] = Yii::app()->user->email();
		$subject = Yii::app()->user->city_name().': '.str_replace('{date}',$this->request_dt,Yii::t('feedback','Feedback about All Daily Reports (Date: {date})'));
		$description = Yii::t('feedback','Feedback Content');
        $description.="<br/>城市：".$cityname[0]['name'];
		$message = '';
		$cnt = 0;
		foreach ($this->cats as $cat=>$desc) {
			$cnt++;
			$cfield = 'cat_'.$cnt;
			$ffield = 'feedback_'.$cnt;
			if ($this->$cfield=='Y') {
				$fb = str_replace("\n","<br>",$this->$ffield);
				$ds = Yii::t('app',$desc);
				$message .= "<p>$ds:<br>$fb<br></p>";
			}
		}
		if (!empty($this->rpt_id) && $this->rpt_id!=null) {
			$url = Yii::app()->createAbsoluteUrl('queue/download',array('index'=>$this->rpt_id));
			$msg_url = str_replace('{url}',$url, Yii::t('report',"Please click <a href=\"{url}\" onClick=\"return popup(this,'Daily Report');\">here</a> to download the report."));
			$message .= "<p>&nbsp;</p><p>$msg_url</p>";
		}

		try {
			$sql = "insert into swo_email_queue(from_addr, to_addr, cc_addr, 
						subject, description, message, status, lcu) 
					values(:from_addr, :to_addr, :cc_addr,
						:subject, :description, :message, 'P', :uid)
				";
			$connection = Yii::app()->db;
			$command=$connection->createCommand($sql);
			if (strpos($sql,':from_addr')!==false) {
				$from_addr = Yii::app()->params['adminEmail'];		//Yii::app()->user->email();
				$command->bindParam(':from_addr',$from_addr,PDO::PARAM_STR);
			}
			if (strpos($sql,':to_addr')!==false) {
				$to_addr = json_encode($to);
				$command->bindParam(':to_addr',$to_addr,PDO::PARAM_STR);
			}
			if (strpos($sql,':cc_addr')!==false) {
				$cc_addr = json_encode($cc);
				$command->bindParam(':cc_addr',$cc_addr,PDO::PARAM_STR);
			}
			if (strpos($sql,':subject')!==false)
				$command->bindParam(':subject',$subject,PDO::PARAM_STR);
			if (strpos($sql,':description')!==false)
				$command->bindParam(':description',$description,PDO::PARAM_STR);
			if (strpos($sql,':message')!==false)
				$command->bindParam(':message',$message,PDO::PARAM_STR);
			if (strpos($sql,':uid')!==false) {
				$uid = Yii::app()->user->id;
				$command->bindParam(':uid',$uid,PDO::PARAM_STR);
			}
			$command->execute();
		}
		catch(Exception $e) {
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
		
		return true;
	}
/*	
	public function sendNotification() {
		$to = Yii::app()->params['bossEmail'];
		$cc = empty($this->cc) ? array() : General::getEmailByUserIdArray($this->cc);
		$cc[] = Yii::app()->user->email();
		$subject = Yii::app()->user->city_name().': '.str_replace('{date}',$this->request_dt,Yii::t('feedback','Feedback about All Daily Reports (Date: {date})'));
		$description = Yii::t('feedback','Feedback Content');
		$message = '';
		$cnt = 0;
		foreach ($this->cats as $cat=>$desc) {
			$cnt++;
			$cfield = 'cat_'.$cnt;
			$ffield = 'feedback_'.$cnt;
			if ($this->$cfield=='Y') {
				$fb = str_replace("\n","<br>",$this->$ffield);
				$ds = Yii::t('app',$desc);
				$message .= "<p>$ds:<br>$fb<br></p>";
			}
		}
		if (!empty($this->rpt_id) && $this->rpt_id!=null) {
			$url = Yii::app()->createAbsoluteUrl('queue/download',array('index'=>$this->rpt_id));
			$msg_url = str_replace('{url}',$url, Yii::t('report',"Please click <a href=\"{url}\" onClick=\"return popup(this,'Daily Report');\">here</a> to download the report."));
			$message .= "<p>&nbsp;</p><p>$msg_url</p>";
		}
		
		$mail = new YiiMailer;

		$mail->setView('report');
		$data = array('message' => $message, 'description'=>$description, 'mailer'=>$mail);
		$mail->setData($data);

		$mail->setFrom(Yii::app()->user->email());
		$mail->setSubject($subject);
		$mail->setTo($to);
		$mail->setCc($cc);
//		if (!empty($cc)) {
//			$cclist = explode(';',str_replace(',',';',$cc));
//			foreach ($cclist as $key=>$value) $cclist[$key] = trim($value);
//		}
//		if (!empty($filename) && !empty($attach)) $mail->AddStringAttachment($attach,$filename);

//		$mail->setSmtp('smtp3.securemail.hk', 1025, 'none', true, 'smtp@lbsgroup.com.hk', 'U4gApuat'); // GMail example
		$rtn = $mail->send();
		if ($rtn) 
			return '';
		else
			return $mail->getError();
	}
*/
}
