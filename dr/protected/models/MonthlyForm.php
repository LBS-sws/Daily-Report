<?php

class MonthlyForm extends CFormModel
{
	public $id;
	public $year_no;
	public $month_no;
	public $record = array();

	public function attributeLabels()
	{
		return array(
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, year_no, month_no','safe'),
			array('record','validateRecord'),
		);
	}

	public function validateRecord($attribute, $params){
		$message = '';
		foreach ($this->record as $data) {
			if (isset($data['updtype']) && $data['updtype']!='Y') {
				if (isset($data['fieldtype'])) {
					switch($data['fieldtype']) {
						case 'N':
							if (isset($data['datavalue']) && !empty($data['datavalue']) && !is_numeric($data['datavalue'])) {
								$message = $data['name'].Yii::t('monthly',' is invalid');
								$this->addError($attribute,$message);
							}
						break;
					}
				}
			}
		}
	}

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$sql = "select a.year_no, a.month_no, b.id, b.hdr_id, b.data_field, b.data_value, c.name, c.upd_type, c.field_type, b.manual_input    
				from swo_monthly_hdr a, swo_monthly_dtl b, swo_monthly_field c 
				where a.id=$index and a.city='$city'
				and a.id=b.hdr_id and b.data_field=c.code
				and c.status='Y'
				order by c.excel_row
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$hid = 0;
			foreach ($rows as $row) {
				if ($hid!=$row['hdr_id']) {
					$hid = $row['hdr_id'];
					$this->id = $hid;
					$this->year_no = $row['year_no'];
					$this->month_no = $row['month_no'];
				}
				$temp = array();
				$temp['id'] = $row['id'];
				$temp['code'] = $row['data_field'];
				$temp['name'] = $row['name'];
				$temp['datavalue'] = $row['data_value'];
				$temp['datavalueold'] = $row['data_value'];
				$temp['updtype'] = $row['upd_type'];
				$temp['fieldtype'] = $row['field_type'];
				$temp['manualinput'] = $row['manual_input'];
				$this->record[$row['data_field']] = $temp;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveMonthly($connection);
			$this->systemLog();
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function systemLog(){
        $datamonth = strval($this->year_no).substr('0'.strval($this->month_no),-2);
        $targetmonth = date('Ym',strtotime(date('Y-m-1').' -2 month'));
	    if($datamonth<=$targetmonth){//修改两个月以前的数据需要记录
            $systemLogModel = new SystemLogForm();
            $systemLogModel->log_date = date("Y-m-d H:i:s");
            $systemLogModel->log_user = Yii::app()->user->id;
            $systemLogModel->log_type = get_class($this);
            $systemLogModel->log_type_name = "月报表数据输入";
            $systemLogModel->option_str = "修改";
            $systemLogModel->leave_log = 2;
            $optionText = "";
            foreach ($this->record as $row){
                if($row["datavalueold"]!=$row["datavalue"]){
                    $optionText.=empty($optionText)?"":"<br/>";
                    $optionText.=$row["num_i"].".".$row["name"].":".$row["datavalueold"]." 修改为 ".$row["datavalue"];
                }
            }
            if(!empty($optionText)){
                $optionText = "id:{$this->id}  （{$this->year_no}年{$this->month_no}月）<br/>".$optionText;
            }
            $systemLogModel->option_text = $optionText;
            $systemLogModel->insertSystemLog();
        }
    }

	protected function saveMonthly(&$connection) {
		$sql = '';
		switch ($this->scenario) {
			case 'edit':
				$sql = "update swo_monthly_dtl set
							data_value = :data_value,
							manual_input = :manual_input,
							luu = :uid 
						where id = :id
					";
				break;
		}
		if (empty($sql)) return false;

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		
		$select = "select code from swo_monthly_field 
					where status = 'Y'
					order by code
				";
		$rows = Yii::app()->db->createCommand($select)->queryAll();
		foreach ($rows as $row) {
			$code = $row['code'];
			$command=$connection->createCommand($sql);
			if (isset($this->record[$code])) {
				if (strpos($sql,':id')!==false)
					$command->bindParam(':id',$this->record[$code]['id'],PDO::PARAM_INT);
				if (strpos($sql,':data_value')!==false)
					$command->bindParam(':data_value',$this->record[$code]['datavalue'],PDO::PARAM_STR);
				if (strpos($sql,':manual_input')!==false) {
					$input = empty($this->record[$code]['manualinput']) ? 'N' : $this->record[$code]['manualinput'];
					if ($this->record[$code]['updtype']!='Y' && $this->record[$code]['datavalueold']!=$this->record[$code]['datavalue']) {
						$input = 'Y';
					} 
//					else {
//						if ($this->record[$code]['updtype']!='M') {
//							$input = (empty($this->record[$code]['datavalue']) || $this->record[$code]['datavalue']=='0') ? 'N' : 'Y';
//						}
//					}
					$command->bindParam(':manual_input',$input,PDO::PARAM_STR);
				}
				if (strpos($sql,':uid')!==false)
					$command->bindParam(':uid',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}
		return true;
	}
}
