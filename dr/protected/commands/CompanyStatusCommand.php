 <?php
class CompanyStatusCommand extends CConsoleCommand {
	public function run($args) {
		$date = empty($args) ? date("Y-m-d") : $args[0];
		$sql = "select company_id, company_name, city from swo_service where lcd>='$date' or lud>='$date'";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		$ids = array();
		foreach ($rows as $row) {
			if (!empty($row['company_id'])) {
				if (!in_array($row['company_id'], $ids)) $ids[] = $row['company_id'];
			} else {
				$temp = $this->findCompanyId($row['company_name'],$row['city']);
				foreach ($temp as $val) {
					if (!in_array($val, $ids)) $ids[] = $val;
				}
			}
		}
		
		if (!empty($ids)) {
			$records = $this->getCompanyRecord($ids);
			foreach ($records as $record) {
				echo "ID:".$record['id']."/CODE:".$record['code']."/NAME:".$record['name']."/CITY:".$record['city']."/STS:".$record['cust_sts']."/TYPE:".$record['cust_type']."\n";
				if (!$this->update($record['id'], $record['cust_sts'], $record['cust_type'])) echo "--NO UPDATE!!\n";
			}
		}
	}
	
	protected function findCompanyId($name, $city) {
		$rtn = array();
		$name = str_replace("'","\'",$name);
		$sql = "select id from swo_company where city='$city' and substring('$name', 1, char_length(code))=code";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn[] = $row['id'];
		}
		return $rtn;
	}

	protected function getCompanyRecord($ids) {
		if (empty($ids)) return array();
		$id_list = implode(',',$ids);
		$sql = "select id, code, name, city, CustomerStatus(id, code, name, city) as cust_sts, CustomerType(id, code, name, city) as cust_type
				from swo_company where id in ($id_list)
			";
		return Yii::app()->db->createCommand($sql)->queryAll();
	}
	
	protected function update($id, $sts, $type) {
		$sql = "insert into swo_company_status(id, status, type_list)
				values (:id, :status, :type_list)
				on duplicate key update
				status=:status, type_list=:type_list
			";
		$command=Yii::app()->db->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$id,PDO::PARAM_INT);
		if (strpos($sql,':status')!==false)
			$command->bindParam(':status',$sts,PDO::PARAM_STR);
		if (strpos($sql,':type_list')!==false)
			$command->bindParam(':type_list',$type,PDO::PARAM_STR);
		$cnt = $command->execute();
		return ($cnt>0);
	}
}
?>