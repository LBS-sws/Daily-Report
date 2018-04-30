<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class QcForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $entry_dt;
	public $job_staff;
	public $team;
	public $month;
	public $company_id;
	public $company_name;
	public $service_score;
	public $cust_score;
	public $cust_comment;
	public $qc_result;
	public $env_grade;
	public $qc_dt;
	public $cust_sign;
	public $qc_staff;
	public $remarks;
	public $service_type;
	public $new_form = false;
	public $qc_score;

	public $docType = 'QC';
	public $files;

	public $docMasterId = array(
							'qc'=>0,
							'qcphoto'=>0,
						);
	public $removeFileId = array(
							'qc'=>0,
							'qcphoto'=>0,
						);
	public $no_of_attm = array(
							'qc'=>0,
							'qcphoto'=>0,
						);
	
	public $info = array();
	
	public $ia_fields = array(
//							'score_fan','score_paper','score_soap','score_sink','score_toilet','score_urinal',
							'score_machine','score_sink','score_toilet',
							'score_sticker','score_enzyme','score_bluecard',
							'sticker_cltype','sticker_clno','sticker_matype','sticker_mano','sticker_bgtype','sticker_bgno',
							'sticker_reqno','sticker_actno',
							'sign_cust','sign_tech','sign_qc','improve','praise','service_dt',
						);
	
	public $ib_fields = array(
							'score_uniform','score_tools','score_greet','score_comm',
							'score_ratcheck','score_ratdispose','score_ratboard','score_rathole','score_ratwarn','score_ratdrug',
							'score_roachcheck','score_roachdrug','score_roachexdrug','score_roachtoxin',
							'score_flycup','score_flylamp','score_flycntl','score_flyspray','score_afterwork',
							'score_safety','freq_rat','freq_roach','freq_fly',
							'sign_cust','sign_tech','service_dt','qc_score'
						);
						
	public $blob_fields = array(
							'sign_cust', 'sign_tech', 'sign_qc',
						);
	
	public $score_max = array(
							'score_machine'=>14,
							'score_sink'=>6,
							'score_toilet'=>50,
							'score_sticker'=>10,
							'score_enzyme'=>5,
							'score_bluecard'=>5,
							'cust_score'=>10,
							
							'score_uniform'=>10,
							'score_tools'=>10,
							'score_greet'=>20,
							'score_comm'=>20,
							'score_ratcheck'=>25,
							'score_ratdispose'=>15,
							'score_ratboard'=>15,
							'score_rathole'=>15,
							'score_ratwarn'=>15,
							'score_ratdrug'=>15,
							'score_roachcheck'=>25,
							'score_roachdrug'=>25,
							'score_roachexdrug'=>25,
							'score_roachtoxin'=>25,
							'score_flycup'=>25,
							'score_flylamp'=>25,
							'score_flycntl'=>25,
							'score_flyspray'=>25,
							'score_safety'=>10,
							'score_afterwork'=>30,
						);

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('qc','Record ID'),
			'entry_dt'=>Yii::t('qc','Entry Date'),
			'job_staff'=>Yii::t('qc','Resp. Staff'),
			'team'=>Yii::t('qc','Team'),
			'month'=>Yii::t('qc','Month'),
			'input_dt'=>Yii::t('qc','Input Date'),
			'company_name'=>Yii::t('qc','Customer'),
			'service_score'=>Yii::t('qc','Service Score'),
			'cust_score'=>Yii::t('qc','Customer Score'),
			'cust_score1'=>Yii::t('qc','Customer Score').'('.$this->maxscore('cust_score').')',
			'cust_comment'=>Yii::t('qc','Customer Comment'),
			'qc_comment'=>Yii::t('qc','QC Comment'),
			'qc_result'=>Yii::t('qc','QC Result'),
			'env_grade'=>Yii::t('qc','Env. Grade').' ABC',
			'qc_dt'=>Yii::t('qc','QC Date'),
			'cust_sign'=>Yii::t('qc','Signature'),
			'signature'=>Yii::t('qc','Signatures'),
			'qc_staff'=>Yii::t('qc','QC Staff'),
			'remarks'=>Yii::t('qc','Remarks'),
			'service_type'=>Yii::t('qc','Service Type'),
			'service_dt'=>Yii::t('qc','Service Date'),
			'score_machine'=>Yii::t('qc','Machine Score').'('.$this->maxscore('score_machine').')',
			'score_toilet'=>Yii::t('qc','Toilet Score').'('.$this->maxscore('score_toilet').')',
			'score_sink'=>Yii::t('qc','Sink Score').'('.$this->maxscore('score_sink').')',
			'sticker_clno'=>Yii::t('qc','Toilet Sticker'),
			'sticker_mano'=>Yii::t('qc','Machine Sticker'),
			'sticker_bgno'=>Yii::t('qc','Large Sticker'),
			'sticker_reqno'=>Yii::t('qc','Sticker Request No.'),
			'sticker_actno'=>Yii::t('qc','Sticker Actual No.'),
			'score_sticker'=>Yii::t('qc','Sticker Score').'('.$this->maxscore('score_sticker').')',
			'score_enzyme'=>Yii::t('qc','Enzyme Score').'('.$this->maxscore('score_enzyme').')',
			'score_bluecard'=>Yii::t('qc','Blue Card Score').'('.$this->maxscore('score_bluecard').')',
			'improve'=>Yii::t('qc','Need to Improve'),
			'praise'=>Yii::t('qc','Praise'),
			'sign_cust'=>Yii::t('qc','Customer Signature'),
			'sign_tech'=>Yii::t('qc','Technician Signature'),
			'sign_qc'=>Yii::t('qc','QC Signature'),

			'score_uniform'=>Yii::t('qc','Uniform').'('.$this->maxscore('score_uniform').')',
			'score_tools'=>Yii::t('qc','Equipment').'('.$this->maxscore('score_tools').')',
			'score_greet'=>Yii::t('qc','Greeting').'('.$this->maxscore('score_greet').')',
			'score_comm'=>Yii::t('qc','On-site Communication').'('.$this->maxscore('score_comm').')',
			'score_ratcheck'=>Yii::t('qc','Rat Checking').'('.$this->maxscore('score_ratcheck').')',
			'score_ratdispose'=>Yii::t('qc','Rat Disposal').'('.$this->maxscore('score_ratdispose').')',
			'score_ratboard'=>Yii::t('qc','Sticky Board').'('.$this->maxscore('score_ratboard').')',
			'score_rathole'=>Yii::t('qc','Hole Closure').'('.$this->maxscore('score_rathole').')',
			'score_ratwarn'=>Yii::t('qc','Warning Label').'('.$this->maxscore('score_ratwarn').')',
			'score_ratdrug'=>Yii::t('qc','Drug (Rat)').'('.$this->maxscore('score_ratdrug').')',
			'score_roachcheck'=>Yii::t('qc','Cockroach Checking').'('.$this->maxscore('score_roachcheck').')',
			'score_roachdrug'=>Yii::t('qc','Drug (Cockroach)').'('.$this->maxscore('score_roachdrug').')',
			'score_roachexdrug'=>Yii::t('qc','Expired Drug').'('.$this->maxscore('score_roachexdrug').')',
			'score_roachtoxin'=>Yii::t('qc','Toxin').'('.$this->maxscore('score_roachtoxin').')',
			'score_flycup'=>Yii::t('qc','Fly Cup').'('.$this->maxscore('score_flycup').')',
			'score_flylamp'=>Yii::t('qc','Fly Lamp').'('.$this->maxscore('score_flylamp').')',
			'score_flycntl'=>Yii::t('qc','Fly Control').'('.$this->maxscore('score_flycntl').')',
			'score_flyspray'=>Yii::t('qc','Spray').'('.$this->maxscore('score_flyspray').')',
			'score_safety'=>Yii::t('qc','Safety Score').'('.$this->maxscore('score_safety').')',
			'score_afterwork'=>Yii::t('qc','Effect Score').'('.$this->maxscore('score_afterwork').')',
			'freq_rat'=>Yii::t('qc','Rat Freq.'),
			'freq_roach'=>Yii::t('qc','Cockroach Freq.'),
			'freq_fly'=>Yii::t('qc','Fly Freq.'),

		);
	}

	public function maxscore($field) {
		return isset($this->score_max[$field]) ? $this->score_max[$field] : 0;
	}
	
	public function description() {
		return array(
			'env_grade'=>Yii::t('qc','desc101'),
			'score_fan'=>Yii::t('qc','desc102'),
			'score_paper'=>Yii::t('qc','desc103'),
			'score_soap'=>Yii::t('qc','desc104'),
			'score_sink'=>Yii::t('qc','desc105'),
			'score_toilet'=>Yii::t('qc','desc106'),
			'score_urinal'=>Yii::t('qc','desc107'),
			'score_machine'=>Yii::t('qc','desc108'),
			
			'score_uniform'=>Yii::t('qc','desc201'),
			'score_tools'=>Yii::t('qc','desc202'),
			'score_greet'=>Yii::t('qc','desc203'),
			'score_comm'=>Yii::t('qc','desc204'),
			'score_ratcheck'=>Yii::t('qc','desc205'),
			'score_ratdispose'=>Yii::t('qc','desc206'),
			'score_ratboard'=>Yii::t('qc','desc207'),
			'score_rathole'=>Yii::t('qc','desc208'),
			'score_ratwarn'=>Yii::t('qc','desc209'),
			'score_ratdrug'=>Yii::t('qc','desc210'),
			'score_roachcheck'=>Yii::t('qc','desc211'),
			'score_roachdrug'=>Yii::t('qc','desc212'),
			'score_roachexdrug'=>Yii::t('qc','desc213'),
			'score_roachtoxin'=>Yii::t('qc','desc214'),
			'score_flycup'=>Yii::t('qc','desc215'),
			'score_flylamp'=>Yii::t('qc','desc216'),
			'score_flycntl'=>Yii::t('qc','desc217'),
			'score_flyspray'=>Yii::t('qc','desc218'),
			'score_safety'=>Yii::t('qc','desc219'),
			'cust_score'=>Yii::t('qc','desc220'),
		);
	}

	public function getDescription($field) {
		$desc = $this->description();
		return isset($desc[$field]) ? $desc[$field] : '';
	}
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, team, month, env_grade, cust_sign, qc_staff, company_id, cust_comment, 
				remarks, service_type, new_form, qc_score
				','safe'),
//			array('docType, files, removeFileId, no_of_attm','safe'),
			array('files, removeFileId, docMasterId, no_of_attm','safe'), 
			array('job_staff, company_name','required'),
			array('entry_dt','date','allowEmpty'=>false,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('qc_dt','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('service_score, qc_result, cust_score','numerical','allowEmpty'=>true,'integerOnly'=>false),
			array('cust_score','numerical','allowEmpty'=>true,'integerOnly'=>true),
			array('cust_score','in','range'=>range(0,$this->maxscore('cust_score'))),
			array('info','validateDetailRecords'),
		);
	}

	public function validateDetailRecords($attribute, $params) {
		$rows = $this->$attribute;
		if (is_array($rows)) {
			foreach ($rows as $key=>$value) {
				switch ($key) {
					case 'score_machine': 
					case 'score_toilet': 
					case 'score_sink': 
					case 'score_enzyme': 
					case 'score_bluecard': 
					case 'score_sticker': 
					case 'score_uniform':
					case 'score_tools':
					case 'score_greet':
					case 'score_comm':
					case 'score_ratcheck':
					case 'score_ratdispose':
					case 'score_ratboard':
					case 'score_rathole':
					case 'score_ratwarn':
					case 'score_ratdrug':
					case 'score_roachcheck': 
					case 'score_roachdrug':
					case 'score_roachexdrug':
					case 'score_roachtoxin':
					case 'score_flycup':
					case 'score_flylamp':
					case 'score_flycntl':
					case 'score_flyspray':
					case 'score_safety':
						$label = $this->attributeLabels();
						if (!empty($value)&&!is_numeric($value)) 
							$this->addError($attribute, $label[$key].' - '.Yii::t('qc','Invalid Value'));
						elseif ($value < 0 || $value > $this->maxscore($key))
							$this->addError($attribute, $label[$key].Yii::t('qc','is not between 0 and ').$this->maxscore($key));
						break;
					case 'sticker_mano': 
					case 'sticker_bgno': 
					case 'sticker_reqno': 
					case 'sticker_actno': 
						$label = $this->attributeLabels();
						if (!empty($value)&&!is_numeric($value)) 
							$this->addError($attribute, $label[$key].' - '.Yii::t('qc','Invalid Value'));
						break;
				}
			}
		}
	}

	public function initData() {
		$infotypes = $this->service_type=='IA'
					? $this->ia_fields
					: ($this->service_type=='IB' ? $this->ib_fields : array());
		if (!empty($infotypes)) {
			foreach($infotypes as $infotype) {
				$this->info[$infotype] = '';
			}
		}
	}
	
	public function retrieveData($index)
	{
		$user = Yii::app()->user->id;
		$allcond = Yii::app()->user->validFunction('CN02') ? "" : "and lcu='$user'";
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql = "select *,docman$suffix.countdoc('QC',id) as no_of_attm, docman$suffix.countdoc('QCPHOTO',id) as no_of_photo from swo_qc where id=$index and city in ($city) $allcond ";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$qid = $this->id;
			$this->entry_dt = General::toDate($row['entry_dt']);
			$this->job_staff = $row['job_staff'];
			$this->team = $row['team'];
			$this->month = $row['month'];
			$this->company_id = $row['company_id'];
			$this->company_name = $row['company_name'];
			$this->service_score = $row['service_score'];
			$this->cust_comment = $row['cust_comment'];
			$this->cust_score = $row['cust_score'];
			$this->qc_result = $row['qc_result'];
			$this->env_grade = $row['env_grade'];
			$this->qc_dt = General::toDate($row['qc_dt']);
			$this->cust_sign = $row['cust_sign'];
			$this->qc_staff = $row['qc_staff'];
			$this->remarks = $row['remarks'];
			$this->service_type = $row['service_type'];
			$this->no_of_attm['qc'] = $row['no_of_attm'];
			$this->no_of_attm['qcphoto'] = $row['no_of_photo'];
			
			$infotypes = $this->service_type=='IA'
						? $this->ia_fields
						: ($this->service_type=='IB' ? $this->ib_fields : array());
			if (!empty($infotypes)) {
				foreach($infotypes as $infotype) {
					$this->info[$infotype] = '';
				}
				
				$sql = "select field_id, field_value, field_blob from swo_qc_info
						where qc_id = $qid 
					";
				$rows = Yii::app()->db->createCommand($sql)->queryAll();
				foreach($rows as $row) {
					$this->new_form = true;
					$this->info[$row['field_id']] = !empty($row['field_blob']) ? $row['field_blob'] : $row['field_value'];
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
			$this->saveQc($connection);
			if ($this->service_type=='IA' || $this->service_type=='IB')
				$this->saveQcInfo($connection);
			$this->updateDocman($connection,'QC');
			$this->updateDocman($connection,'QCPHOTO');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	protected function saveQc(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_qc where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into swo_qc(
							entry_dt, job_staff, team, month, input_dt, company_id, company_name, service_score,
							cust_comment, qc_result, env_grade, qc_dt, cust_sign, qc_staff, cust_score, remarks, 
							service_type, city, luu, lcu
						) values (
							:entry_dt, :job_staff, :team, :month, null, :company_id, :company_name, :service_score,
							:cust_comment, :qc_result, :env_grade, :qc_dt, :cust_sign, :qc_staff, :cust_score, :remarks, 
							:service_type, :city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_qc set
							entry_dt = :entry_dt, 
							job_staff = :job_staff, 
							team = :team, 
							month = :month, 
							company_id = :company_id, 
							company_name = :company_name, 
							service_score = :service_score,
							cust_score = :cust_score, 
							cust_comment = :cust_comment, 
							qc_result = :qc_result, 
							env_grade = :env_grade, 
							qc_dt = :qc_dt, 
							cust_sign = :cust_sign, 
							qc_staff = :qc_staff, 
							remarks = :remarks,
							service_type = :service_type,
							luu = :luu 
						where id = :id and city = :city
						";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':entry_dt')!==false) {
			$edate = General::toMyDate($this->entry_dt);
			$command->bindParam(':entry_dt',$edate,PDO::PARAM_STR);
		}
		if (strpos($sql,':job_staff')!==false)
			$command->bindParam(':job_staff',$this->job_staff,PDO::PARAM_STR);
		if (strpos($sql,':team')!==false)
			$command->bindParam(':team',$this->team,PDO::PARAM_STR);
		if (strpos($sql,':month')!==false)
			$command->bindParam(':month',$this->month,PDO::PARAM_STR);
		if (strpos($sql,':company_id')!==false) {
			$cid = General::toMyNumber($this->company_id);
			$command->bindParam(':company_id',$cid,PDO::PARAM_INT);
		}
		if (strpos($sql,':company_name')!==false)
			$command->bindParam(':company_name',$this->company_name,PDO::PARAM_STR);
		if (strpos($sql,':service_type')!==false)
			$command->bindParam(':service_type',$this->service_type,PDO::PARAM_STR);
		if (strpos($sql,':service_score')!==false)
			$command->bindParam(':service_score',$this->service_score,PDO::PARAM_STR);
		if (strpos($sql,':cust_score')!==false)
			$command->bindParam(':cust_score',$this->cust_score,PDO::PARAM_STR);
		if (strpos($sql,':cust_comment')!==false)
			$command->bindParam(':cust_comment',$this->cust_comment,PDO::PARAM_STR);
		if (strpos($sql,':qc_result')!==false)
			$command->bindParam(':qc_result',$this->qc_result,PDO::PARAM_STR);
		if (strpos($sql,':env_grade')!==false)
			$command->bindParam(':env_grade',$this->env_grade,PDO::PARAM_STR);
		if (strpos($sql,':qc_dt')!==false) {
			$qdate = General::toMyDate($this->qc_dt);
			$command->bindParam(':qc_dt',$qdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':cust_sign')!==false)
			$command->bindParam(':cust_sign',$this->cust_sign,PDO::PARAM_STR);
		if (strpos($sql,':qc_staff')!==false)
			$command->bindParam(':qc_staff',$this->qc_staff,PDO::PARAM_INT);
		if (strpos($sql,':remarks')!==false)
			$command->bindParam(':remarks',$this->remarks,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		return true;
	}

	protected function saveQcInfo(&$connection)
	{
		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;

		foreach ($this->info as $key=>$value) {
			$sql = '';
			switch ($this->scenario) {
				case 'delete':
					$sql = "delete from swo_qc_info where qc_id = :qc_id";
					break;
				case 'new':
					$sql = "insert into swo_qc_info(
								qc_id, field_id, field_value, field_blob, luu, lcu
							) values (
								:qc_id, :field_id, :field_value, :field_blob, :luu, :lcu
							)";
					break;
				case 'edit':
					$sql = "insert into swo_qc_info(
								qc_id, field_id, field_value, field_blob, luu, lcu
							) values (
								:qc_id, :field_id, :field_value, :field_blob, :luu, :lcu
							)
							on duplicate key update
								field_value = :field_value, field_blob = :field_blob, luu = :luu
							";
					break;
			}
			if ($sql != '') {
				$command=$connection->createCommand($sql);
				if (strpos($sql,':qc_id')!==false)
					$command->bindParam(':qc_id',$this->id,PDO::PARAM_INT);
				if (strpos($sql,':field_id')!==false) 
					$command->bindParam(':field_id',$key,PDO::PARAM_STR);
				if (strpos($sql,':field_value')!==false) {
					$val1 = in_array($key, $this->blob_fields) ? '' : $value;
					$command->bindParam(':field_value',$val1,PDO::PARAM_STR);
				}
				if (strpos($sql,':field_blob')!==false) {
					$val2 = in_array($key, $this->blob_fields) ? $value : '';
					$command->bindParam(':field_blob',$val2,PDO::PARAM_LOB);
				}
				if (strpos($sql,':luu')!==false)
					$command->bindParam(':luu',$uid,PDO::PARAM_STR);
				if (strpos($sql,':lcu')!==false)
					$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}
		return true;
	}
	
//	public function saveFiles() {
//		$docman = new DocMan();
//		foreach ($this->files as $file) {
//			$docman->save($data);
//		}
//	}
	
	protected function updateDocman(&$connection, $doctype) {
		if ($this->scenario=='new') {
			$docidx = strtolower($doctype);
			if ($this->docMasterId[$docidx] > 0) {
				$docman = new DocMan($doctype,$this->id,get_class($this));
				$docman->masterId = $this->docMasterId[$docidx];
				$docman->updateDocId($connection, $this->docMasterId[$docidx]);
			}
		}
	}

	public function readonly() {
		if ($this->scenario!='new' && ($this->service_type=='IA' || $this->service_type=='IB')) {
			$flag = (isset($this->info['sign_cust']) && !empty($this->info['sign_cust'])) ||
					(isset($this->info['sign_tech']) && !empty($this->info['sign_tech'])) ||
					(isset($this->info['sign_qc']) && !empty($this->info['sign_qc']));
			return ($this->scenario=='view' || $flag);
		} else {
			return ($this->scenario=='view');
		}
	}

	public function readonlySP() {
		if ($this->scenario!='new' && ($this->service_type=='IA' || $this->service_type=='IB')) {
			$flag = (isset($this->info['sign_cust']) && !empty($this->info['sign_cust'])) ||
					(isset($this->info['sign_tech']) && !empty($this->info['sign_tech'])) ||
					(isset($this->info['sign_qc']) && !empty($this->info['sign_qc']));
			return ($this->scenario=='view' || ($flag && !empty($this->qc_staff)));
		} else {
			return ($this->scenario=='view');
		}
	}
}
