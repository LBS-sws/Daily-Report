<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class UserForm extends CFormModel
{
	/* User Fields */
	public $username;
	public $password;
	public $disp_name;
	public $logon_time;
	public $logoff_time;
	public $status='A';
	public $city;
	public $fail_count;
	public $lock;
	public $email;
	public $rights = array();

	public $extfields = array();
	public $oriextfields = array();
	public $oriextrights = array();
	
	public $info_fields = array(
							'signature'=>'blob',
							'signature_file_type'=>'value',
							'staff_id'=>'value',
							'staff_name'=>'value',
						);
	public $signature;
	public $signature_file_type;
	
	public $staff_id;
	public $staff_name;
	
	private $systems;
	private $localelabels;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>Yii::t('user','User ID'),
			'password'=>Yii::t('user','Password'),
			'disp_name'=>Yii::t('user','Display Name'),
			'status'=>Yii::t('user','Status'),
			'logon_time'=>Yii::t('user','Logon Time'),
			'logoff_time'=>Yii::t('user','Logoff Time'),
			'group_id'=>Yii::t('user','Group'),
			'city'=>Yii::t('user','City'),
			'lock'=>Yii::t('user','Lock'),
			'email'=>Yii::t('user','Email'),
			'signature'=>Yii::t('user','Signature'),
			'staff_id'=>Yii::t('user','Staff Code'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('username, disp_name, city','required'),
			array('username','filter','filter'=>'trim'),
			array('username','unique','allowEmpty'=>false,
					'attributeName'=>'username',
					'caseSensitive'=>false,
					'className'=>'User',
					'on'=>'new'
				),
			array('status','in','range'=>array('A','I'),'allowEmpty'=>false),
			array('logon_time, logoff_time, fail_count, lock, rights','safe'), 
			array('signature_file_type, staff_id, staff_name','safe'), 
			array('password','required','on'=>'new'),
			array('password','safe','on'=>'edit, delete'),
			array('email','email','allowEmpty'=>true,),
			array('signature','file','types'=>'jpg, png','allowEmpty'=>true),
			array('extfields, oriextfields, oriextrights','safe'),
		);
	}

	public function init() {
		parent::init();
		$formEx = new UserFormEx();
		$this->systems = General::getInstalledSystemFunctions();
		foreach($this->systems as $id=>$value) {
			if (isset($value['item']['zzexternal']['XX01']['fields'])) {
				$fldsfunc = 'UserFormEx::'.$value['item']['zzexternal']['XX01']['fields'];
				$fldsarray = call_user_func($fldsfunc);
				if (!empty($fldsarray) && is_array($fldsarray)) {
					foreach($fldsarray as $fldid=>$fldtype) {
						$this->extfields[$fldid]['type'] = $fldtype;
						$this->extfields[$fldid]['value'] = ($fldtype=='json' ? array() : '');
					}
				}
				$this->oriextrights[$id]['XX01'] = 'NA';
			}
		}
		$this->oriextfields = $this->extfields;
		$this->localelabels = General::getLocaleAppLabels();
		$this->initAccessRights();
	}

	protected function initAccessRights() {
		$cnt = 0;
		foreach($this->systems as $sid=>$items) {
			$this->rights[$cnt] = array();
			foreach($items['item'] as $group=>$func) {
				foreach($func as $fid=>$fname) {
					$this->rights[$cnt][$fid] = 'NA';
				}
			}
			$cnt++;
		}
	}

	public function functionLabels($key) {
		return (!empty($this->localelabels) && isset($this->localelabels[$key]) ? $this->localelabels[$key] : $key);
	}

	public function installedSystem() {
		$rtn = array();
		foreach($this->systems as $id=>$value) {
			$rtn[$id] = Yii::t('app',$value['name']);
		}
		return $rtn;
	}

	public function installedSystemGroup($systemId) {
		$rtn = array();
		foreach($this->systems[$systemId]['item'] as $group=>$value) {
			$rtn[] = $group;
		}
		return $rtn;
	}

	public function installedSystemItems($systemId, $groupName) {
		$rtn = array();
		foreach($this->systems[$systemId]['item'][$groupName] as $id=>$value) {
			$rtn[$id] = $this->functionLabels($value['name']).' '.$value['tag'];
		}
		return $rtn;
	}

	public function getExternalSystemLayout($systemId) {
		return isset($this->systems[$systemId]['item']['zzexternal']['XX01']['layout']) 
			? $this->systems[$systemId]['item']['zzexternal']['XX01']['layout'] 
			: '';
	}
	
	public function getTemplateData($id) {
		$rtn = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select system_id, a_read_only, a_read_write, a_control from security$suffix.sec_template
				where temp_id=$id 
			";
		$record = Yii::app()->db->createCommand($sql)->queryRow();
		if ($record!==false) {
			$ro = $record['a_read_only'];
			$rw = $record['a_read_write'];
			$cn = $record['a_control'];
			$sid = $record['system_id'];

			$a_sys = $this->systemMappingArray();
			$idx = array_search($sid, $a_sys);

			foreach($this->rights[$idx] as $key=>$value) {
				$access = (strpos($rw,$key)!==false) ? 'RW' :
											((strpos($ro,$key)!==false) ? 'RO' :
											((strpos($cn,$key)!==false) ? 'CN' : 'NA'
											));
				$rtn[] = array('idx'=>$idx,'id'=>$key,'value'=>$access,'extra'=>false,'sysid'=>$sid,'type'=>'');
			}
			
			$sql = "select field_id, field_value from security$suffix.sec_template_info where temp_id=$id";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			if (count($rows) > 0) {
				if (isset($this->systems[$sid]['item']['zzexternal']['XX01']['fields'])) {
					$fldsfunc = 'UserFormEx::'.$this->systems[$sid]['item']['zzexternal']['XX01']['fields'];
					$fldsarray = call_user_func($fldsfunc);
					$x = array();
					foreach($fldsarray as $fldid=>$fldtype) {
						$x[$fldid]= $fldtype;
					}
					foreach ($rows as $row) {
						$type = isset($x[$row['field_id']]) ? $x[$row['field_id']] : '';
						$rtn[] = array('idx'=>$idx,'id'=>$row['field_id'],'value'=>$row['field_value'],'extra'=>true,'sysid'=>$sid,'type'=>$type);
					}
				}
			}
		}
		return $rtn;
	}

	public function retrieveData($index) {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.* from security$suffix.sec_user a where a.username='$index' and a.username<>'admin'";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$this->username = $row['username'];
				$this->password = '';
				$this->disp_name = $row['disp_name'];
				$this->status = $row['status'];
				$this->logon_time = $row['logon_time'];
				$this->logoff_time = $row['logoff_time'];
				$this->city = $row['city'];
				$this->fail_count = $row['fail_count'];
				$this->lock = (Yii::app()->params['noOfLoginRetry']>0 && Yii::app()->params['noOfLoginRetry']<=$this->fail_count) ?
						Yii::t('misc','Yes') :
						Yii::t('misc','No');
				$this->email = $row['email'];
				break;
			}

			$sql = "select system_id, a_read_only, a_read_write, a_control 
						from security$suffix.sec_user_access
						where username='$index'
				";
			$dtls = Yii::app()->db->createCommand($sql)->queryAll();
			if (count($dtls) > 0){
				$a_sys = $this->systemMappingArray();

				foreach ($dtls as $dtl) {
					$sid = $dtl['system_id'];
					$idx = array_search($sid, $a_sys);
					if ($idx!==false) {
						foreach($this->rights[$idx] as $key=>$value) {
							$this->rights[$idx][$key] = (strpos($dtl['a_read_write'],$key)!==false) ? 'RW' :
														((strpos($dtl['a_read_only'],$key)!==false) ? 'RO' :
														((strpos($dtl['a_control'],$key)!==false) ? 'CN' : 'NA'
														));
							isset($this->oriextrights[$sid]['XX01']) && $key=='XX01' && $this->oriextrights[$sid]['XX01'] = $this->rights[$idx][$key];
						}
					}
				}
			}
			
			$sql = "select field_id, field_value, field_blob
					from security$suffix.sec_user_info
					where username='$index'
				";
			$info = Yii::app()->db->createCommand($sql)->queryAll();
			if (count($info) > 0){
				foreach ($info as $rec) {
					switch ($rec['field_id']) {
						case 'signature': $this->signature = $rec['field_blob']; break;
						case 'signature_file_type': $this->signature_file_type = $rec['field_value']; break;
						case 'staff_id': $this->staff_id = $rec['field_value']; break;
						case 'staff_name': $this->staff_name = $rec['field_value']; break;
						default: 
							switch ($this->extfields[$rec['field_id']]['type']) {
								case 'json':
									$this->extfields[$rec['field_id']]['value'] = json_decode($rec['field_value']);
									break;
								default: 
									$this->extfields[$rec['field_id']]['value'] = $rec['field_value'];
							}
					}
				}
			}
			$this->oriextfields = $this->extfields;
		}
		return true;
	}
	
	protected function systemMappingArray() {
		$rtn = array();
		foreach (General::systemMapping() as $key=>$value) {
			$rtn[] = $key;
		}
		return $rtn;
	}

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveUser($connection);
			$this->saveRights($connection);
			foreach($this->systems as $id=>$value) {
				if (isset($value['item']['zzexternal']['XX01']['update'])) {
					$func = 'UserFormEx::'.$value['item']['zzexternal']['XX01']['update'];
					if (!call_user_func_array($func, array(&$connection, &$this))) {
						throw new Exception('Update external system fail.');
					}
				}
			}
			$this->saveInfo($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveUser(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$userobj = User::model();
		$hashPass = $this->password == '' ? '' :
			$userobj->hashPassword($this->password,$userobj->salt);

		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from security$suffix.sec_user where username = :username";
				break;
			case 'new':
				$sql = "insert into security$suffix.sec_user
							(username, password, disp_name, email, status, lcu, luu, city)
						values 
							(:username, :password, :dispname, :email, :status, :lcu, :luu, :city)
					";
				break;
			case 'edit':
				$sql = "update security$suffix.sec_user set ";
				if ($hashPass !== '') $sql .= "password = :password, ";
				$sql .= "disp_name = :dispname, email = :email, city = :city, "
					. (($this->lock==Yii::t('misc','Yes') && $this->fail_count==0) ? "fail_count = 0, " : "")
					. "luu = :luu, status = :status where username = :username";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		$command->bindParam(':username',$this->username,PDO::PARAM_STR);
		if (strpos($sql,':dispname')!==false)
			$command->bindParam(':dispname',$this->disp_name,PDO::PARAM_STR);
		if (strpos($sql,':email')!==false)
			$command->bindParam(':email',$this->email,PDO::PARAM_STR);
		if (strpos($sql,':status')!==false)
			$command->bindParam(':status',$this->status,PDO::PARAM_STR);
		if (strpos($sql,':password')!==false)
			$command->bindParam(':password',$hashPass,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$this->city,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		return true;
	}

	protected function saveRights(&$connection) {
		$suffix = Yii::app()->params['envSuffix'];

		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from security$suffix.sec_user_access where username = :username and system_id=:system_id";
				break;
			case 'new':
			case 'edit':
				$sql = "insert into security$suffix.sec_user_access 
							(username, system_id, a_read_only, a_read_write, a_control, lcu, luu)
						values 
							(:username, :system_id, :a_read_only, :a_read_write, :a_control, :lcu, :luu)
						on duplicate key update a_read_only = :a_read_only, a_read_write = :a_read_write,
							a_control = :a_control, luu = :luu
					";
				break;
		}

		$uid = Yii::app()->user->id;
		$a_sys = $this->systemMappingArray();
		foreach($this->rights as $idx=>$funcs) {
			$ro = '';
			$rw = '';
			$cn = '';
			foreach($funcs as $aid=>$aval) {
				$rw .= ($aval=='RW') ? $aid : '';
				$ro .= ($aval=='RO') ? $aid : '';
				$cn .= ($aval=='CN') ? $aid : '';
			}
			$sid = $a_sys[$idx];
			$command=$connection->createCommand($sql);
			if (strpos($sql,':username')!==false)
				$command->bindParam(':username',$this->username,PDO::PARAM_STR);
			if (strpos($sql,':system_id')!==false)
				$command->bindParam(':system_id',$sid,PDO::PARAM_STR);
			if (strpos($sql,':a_read_only')!==false)
				$command->bindParam(':a_read_only',$ro,PDO::PARAM_STR);
			if (strpos($sql,':a_read_write')!==false)
				$command->bindParam(':a_read_write',$rw,PDO::PARAM_STR);
			if (strpos($sql,':a_control')!==false)
				$command->bindParam(':a_control',$cn,PDO::PARAM_STR);
			if (strpos($sql,':lcu')!==false)
				$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
			if (strpos($sql,':luu')!==false)
				$command->bindParam(':luu',$uid,PDO::PARAM_STR);
			$command->execute();
		}
	}
	
	protected function saveInfo(&$connection) {
		$suffix = Yii::app()->params['envSuffix'];

		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from security$suffix.sec_user_info where username = :username";
				break;
			case 'new':
			case 'edit':
				$sql = "insert into security$suffix.sec_user_info 
							(username, field_id, field_value, field_blob, lcu, luu)
						values 
							(:username, :field_id, :field_value, :field_blob, :lcu, :luu)
						on duplicate key update 
							field_value = :field_value, field_blob = :field_blob, luu = :luu
					";
				break;
		}

		$uid = Yii::app()->user->id;
		foreach($this->info_fields as $fldid=>$fldtype) {
			if (($fldid!='signature' && $fldid!='signature_file_type') || !empty($this->$fldid) || $this->scenario=='delete') {
				$value = ($fldtype=='value') ? $this->$fldid : '';
				$blob = ($fldtype=='blob') ? $this->$fldid : '';
			
				$command=$connection->createCommand($sql);
				if (strpos($sql,':username')!==false)
					$command->bindParam(':username',$this->username,PDO::PARAM_STR);
				if (strpos($sql,':field_id')!==false)
					$command->bindParam(':field_id',$fldid,PDO::PARAM_STR);
				if (strpos($sql,':field_value')!==false)
					$command->bindParam(':field_value',$value,PDO::PARAM_STR);
				if (strpos($sql,':field_blob')!==false)
					$command->bindParam(':field_blob',$blob,PDO::PARAM_LOB);
				if (strpos($sql,':lcu')!==false)
					$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
				if (strpos($sql,':luu')!==false)
					$command->bindParam(':luu',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}
	
		foreach($this->extfields as $fldid=>$item) {
			if (!empty($item['value']) && $this->scenario!='delete') {
				$value = $item['type']=='json' ? json_encode($item['value']) : $item['value'];
				$blob = '';
				$command=$connection->createCommand($sql);
				if (strpos($sql,':username')!==false)
					$command->bindParam(':username',$this->username,PDO::PARAM_STR);
				if (strpos($sql,':field_id')!==false)
					$command->bindParam(':field_id',$fldid,PDO::PARAM_STR);
				if (strpos($sql,':field_value')!==false)
					$command->bindParam(':field_value',$value,PDO::PARAM_STR);
				if (strpos($sql,':field_blob')!==false)
					$command->bindParam(':field_blob',$blob,PDO::PARAM_LOB);
				if (strpos($sql,':lcu')!==false)
					$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
				if (strpos($sql,':luu')!==false)
					$command->bindParam(':luu',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}
	}
	
	public function getSignatureString() {
		$rtn = '';
		if (!empty($this->signature)) {
			$type = ($this->signature_file_type=='jpg') ? 'jpeg' : $this->signature_file_type;
			$rtn = "data:image/$type;base64,".$this->signature;
		}
		return $rtn;
	}
}
