<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class GroupForm extends CFormModel
{
	/* User Fields */
	public $temp_id;
	public $system_id;
	public $temp_name;
	public $rights = array();

	public $extfields = array();
	private $systems;
	private $localelabels;

	public function init() {
		parent::init();
		$this->systems = General::getInstalledSystemFunctions();
		foreach($this->systems as $id=>$value) {
			if (isset($value['item']['zzexternal']['XX01']['fields'])) {
				$fldsfunc = 'UserFormEx::'.$value['item']['zzexternal']['XX01']['fields'];
				$fldsarray = call_user_func($fldsfunc);
				if (!empty($fldsarray) && is_array($fldsarray)) {
					foreach($fldsarray as $fldid=>$fldtype) {
						$this->extfields[$fldid]['type'] = $fldtype;
						$this->extfields[$fldid]['value'] = ($fldtype=='json' ? array() : '');
						$this->extfields[$fldid]['sysid'] = $id;
					}
				}
			}
		}
		$this->localelabels = General::getLocaleAppLabels();
		$this->initAccessRights();
	}

	public function attributeLabels() {
		return array(
			'temp_id'=>Yii::t('template','Template ID'),
			'temp_name'=>Yii::t('group','Template Name'),
			'system_id'=>Yii::t('group','System Name'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()	{
		return array(
			array('temp_id,rights, extfields','safe'),
			array('temp_name,system_id','required'),
		);
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

	public function retrieveData($index) {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.* from security$suffix.sec_template a where a.temp_id=".$index;
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->temp_id = $row['temp_id'];
			$this->temp_name = $row['temp_name'];
			$this->system_id = $row['system_id'];
			$ro = $row['a_read_only'];
			$rw = $row['a_read_write'];
			$cn = $row['a_control'];

			$a_sys = $this->systemMappingArray();
			$sid = array_search($this->system_id, $a_sys);
			foreach ($this->rights[$sid] as $key=>$value) {
				$this->rights[$sid][$key] = $this->getAccessRightValue($key, $rw, $ro, $cn);
			}
			
			$sql = "select field_id, field_value from security$suffix.sec_template_info where temp_id=$index";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			if (count($rows) > 0) {
				foreach ($rows as $row) {
					switch ($this->extfields[$row['field_id']]['type']) {
						case 'json':
							$this->extfields[$row['field_id']]['value'] = json_decode($row['field_value']);
							break;
						default: 
							$this->extfields[$row['field_id']]['value'] = $row['field_value'];
					}
				}
			}
		}
		return true;
	}

	public function getExternalSystemLayout($systemId) {
		return isset($this->systems[$systemId]['item']['zzexternal']['XX01']['layout']) 
			? $this->systems[$systemId]['item']['zzexternal']['XX01']['layout'] 
			: '';
	}

	protected function systemMappingArray() {
		$rtn = array();
		foreach (General::systemMapping() as $key=>$value) {
			$rtn[] = $key;
		}
		return $rtn;
	}

	public function functionLabels($sid, $key) {
		return (!empty($this->localelabels[$sid]) && isset($this->localelabels[$sid][$key]) ? $this->localelabels[$sid][$key] : $key);
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
			$rtn[$id] = $this->functionLabels($systemId, $value['name']).' '.$value['tag'];
		}
		return $rtn;
	}

	protected function getAccessRightValue($key, $rwlist, $rolist, $cnlist) {
		$rtn = 'NA';
		if (strpos($cnlist,$key)!==false) $rtn = 'CN';
		if (strpos($rolist,$key)!==false) $rtn = 'RO';
		if (strpos($rwlist,$key)!==false) $rtn = 'RW';
		return $rtn;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGroup($connection);
			$this->saveGroupInfo($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveGroup(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from security$suffix.sec_template where temp_id = :temp_id";
				break;
			case 'new':
				$sql = "insert into security$suffix.sec_template(
							temp_name, system_id, a_read_only, a_read_write, a_control, lcu, luu
						) values (
							:temp_name, :system_id, :read_only, :read_write, :control, :lcu, :luu)
					";
				break;
			case 'edit':
				$sql = "update security$suffix.sec_template 
						set temp_name = :temp_name, 
							luu = :luu, 
							a_read_only = :read_only, 
							a_read_write = :read_write, 
							a_control = :control 
						where temp_id = :temp_id
					";
				break;
		}

		$a_sys = $this->systemMappingArray();
		$sid = array_search($this->system_id, $a_sys);
		$read_only = '';
		$read_write = '';
		$control = '';
		foreach ($this->rights[$sid] as $key=>$value) {
			$read_write .= ($value=='RW') ? $key : '';
			$read_only .= ($value=='RO') ? $key : '';
			$control .= ($value=='CN') ? $key : '';
		}

		$uid = Yii::app()->user->id;
		
		$command=$connection->createCommand($sql);
		if (strpos($sql,':temp_id')!==false)
			$command->bindParam(':temp_id',$this->temp_id,PDO::PARAM_INT);
		if (strpos($sql,':temp_name')!==false)
			$command->bindParam(':temp_name',$this->temp_name,PDO::PARAM_STR);
		if (strpos($sql,':system_id')!==false)
			$command->bindParam(':system_id',$this->system_id,PDO::PARAM_STR);
		if (strpos($sql,':read_only')!==false)
			$command->bindParam(':read_only',$read_only,PDO::PARAM_STR);
		if (strpos($sql,':read_write')!==false)
			$command->bindParam(':read_write',$read_write,PDO::PARAM_STR);
		if (strpos($sql,':control')!==false)
			$command->bindParam(':control',$control,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->temp_id = Yii::app()->db->getLastInsertID();
		return true;
	}

	protected function saveGroupInfo(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];

		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from security$suffix.sec_template_info where temp_id = :username";
				break;
			case 'new':
			case 'edit':
				$sql = "insert into security$suffix.sec_template_info 
							(temp_id, field_id, field_value, lcu, luu)
						values 
							(:temp_id, :field_id, :field_value, :lcu, :luu)
						on duplicate key update 
							field_value = :field_value, luu = :luu
					";
				break;
		}

		$uid = Yii::app()->user->id;
		foreach($this->extfields as $fldid=>$item) {
			if ($item['sysid']==$this->system_id && !empty($item['value']) && $this->scenario!='delete') {
				$value = $item['type']=='json' ? json_encode($item['value']) : $item['value'];
				$command=$connection->createCommand($sql);
				if (strpos($sql,':temp_id')!==false)
					$command->bindParam(':temp_id',$this->temp_id,PDO::PARAM_INT);
				if (strpos($sql,':field_id')!==false)
					$command->bindParam(':field_id',$fldid,PDO::PARAM_STR);
				if (strpos($sql,':field_value')!==false)
					$command->bindParam(':field_value',$value,PDO::PARAM_STR);
				if (strpos($sql,':lcu')!==false)
					$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
				if (strpos($sql,':luu')!==false)
					$command->bindParam(':luu',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}
	}
	
	public function isOccupied($index) {
		$rtn = false;
		$sql = "select a.username from swo_user a where a.group_id=".$index." limit 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn = true;
			break;
		}
		return $rtn;
	}
}
