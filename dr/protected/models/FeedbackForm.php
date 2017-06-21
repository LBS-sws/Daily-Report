<?php

class FeedbackForm extends CFormModel
{
	public $id;
	public $request_dt;
	public $feedback_dt;
	public $status;
	public $status_desc;
	
	public $to;
	public $cc;
	public $rpt_id;

	public $cat_1;
	public $feedback_1;
	public $cat_2;
	public $feedback_2;
	public $cat_3;
	public $feedback_3;
	public $cat_4;
	public $feedback_4;
	public $cat_5;
	public $feedback_5;
	public $cat_6;
	public $feedback_6;
	public $cat_7;
	public $feedback_7;

	public $cats = array(
		'A1~'=>'Customer Service',
		'A2~'=>'Complaint Cases',
		'A3~'=>'Customer Enquiry',
		'A4~'=>'Product Delivery',
		'A5~'=>'QC Record',
		'A6~'=>'Staff Info',
		'A7~'=>'Others',
	);

	public function attributeLabels()
	{
		$lbl = array(
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
			array('id, request_dt, feedback_dt, status, status_desc, to, cc, rpt_id','safe'),
			array($cat_list,'validateType'),
			array($feedback_list,'validateRemarks'),
		);
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
			$this->addError($attribute,$message);
		}
	}

	public function validateRemarks($attribute, $params){
		$field = str_replace('feedback','cat',$attribute);
		if ($this->$field=='Y' && empty($this->$attribute)) {
			$label = $this->attributeLabels();
			$message = $label[$field].' '.Yii::t('feedback','cannot be empty');
			$this->addError($attribute,$message);
		}
	}

	public function retrieveData($index,$mode='edit') {
		$city = Yii::app()->user->city();
		$sql = "select * from swo_mgr_feedback where id=$index and city='$city'"; 
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$this->id = $row['id'];
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
		
		$to = City::model()->getAncestorInChargeList(Yii::app()->user->city());
		$this->to = implode("; ",General::getEmailByUserIdArray($to));
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
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveFeedback($connection);
			$this->saveFeedbackRmk($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveFeedback(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'edit':
				$sql = ($this->status='N') 
						? "update swo_mgr_feedback set
							feedback_dt = now(),
							status = 'Y', 
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
		$command->execute();

		return true;
	}

	protected function saveFeedbackRmk(&$connection)
	{
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
		$incharge = City::model()->getAncestorInChargeList(Yii::app()->user->city());
		$to = General::getEmailByUserIdArray($incharge);
		$to = array_merge($to, Yii::app()->params['bossEmail']);
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
