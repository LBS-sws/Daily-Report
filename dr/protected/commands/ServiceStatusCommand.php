 <?php
class ServiceStatusCommand extends CConsoleCommand {
	protected $webroot;

	public function actionSuspendToStop() {
		echo "Suspend to Stop ... \n";

		$suffix = Yii::app()->params['envSuffix'];
		$sql = "
			select a.contract_no, a.service_id, a.status_dt, date_add(date_add(a.status_dt, interval 1 month), interval 1 day) as target_dt 
			from swo_service_contract_no a
			left outer join swo_service_contract_no b on a.contract_no=b.contract_no and a.status_dt < b.status_dt
			where a.status='S' and a.status_dt >= '2023-01-01' 
			and date_add(date_add(a.status_dt, interval 3 month), interval 1 day) <= curdate() 
			and b.id is null
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$ctrt_no = $row['contract_no'];
				$status_dt = $row['status_dt'];
				$target_dt = $row['target_dt'];
				echo "CTRT NO: $ctrt_no / STS_DT: $status_dt / TGT_DT: $target_dt\n";
				try {
					$connection = Yii::app()->db;
					$transaction=$connection->beginTransaction();
				
					$rid = $this->addStopService($connection, $row['service_id'], $target_dt);
					$this->addStopContractStatus($connection, $rid, $ctrt_no, $target_dt);
					$transaction->commit();
				} catch(Exception $e) {
					$transaction->rollback();
					echo "EXCEPTION ERROR: ".$e->getMessage()."\n";
					Yii::app()->end();
				}
			}
		}
	}
	
	protected function addStopService(&$connection, $id, $dt) {
		$sql = "
			insert into swo_service(
				service_no,	service_new_id,	company_id,	company_name, nature_type, cust_type, product_id,
				b4_product_id, b4_service, b4_freq, b4_paid_type, b4_amt_paid, b4_cust_type_end, b4_pieces, b4_amt_money,
				service, freq, paid_type, amt_paid, amt_install, need_install, technician, technician_id, 
				othersalesman, othersalesman_id, salesman, salesman_id, sign_dt, ctrt_end_dt, surplus,
				all_number_edit0, surplus_edit0, all_number_edit1, surplus_edit1, all_number_edit2, surplus_edit2,
				all_number_edit3, surplus_edit3, all_number, all_number_edit, ctrt_period, cont_info,
				first_dt, first_tech, first_tech_id, pieces, cust_type_name, cust_type_end, pay_week, amt_money, 
				cust_type_four, cust_type_three, reason, target, other_commission, commission, royaltys, royalty,
				status, status_copy, status_dt, remarks, equip_install_dt, org_equip_qty, rtn_equip_qty, remarks2, 
				city, prepay_month, prepay_start, send, wage_type, change_money, lcu, luu 
			)
			select 
				service_no, service_new_id, company_id, company_name, nature_type, cust_type, product_id,
				b4_product_id, b4_service, b4_freq, b4_paid_type, b4_amt_paid, b4_cust_type_end, b4_pieces, b4_amt_money,
				service, freq, paid_type, amt_paid, amt_install, need_install, technician, technician_id,
				othersalesman, othersalesman_id, salesman, salesman_id, sign_dt, ctrt_end_dt, surplus,
				all_number_edit0, surplus_edit0, all_number_edit1, surplus_edit1, all_number_edit2, surplus_edit2,
				all_number_edit3, surplus_edit3, all_number, all_number_edit, ctrt_period, cont_info,
				first_dt, first_tech, first_tech_id, pieces, cust_type_name, cust_type_end, pay_week, amt_money,
				cust_type_four, cust_type_three, reason, target, other_commission, commission, royaltys, royalty,
				'T', status_copy, :status_dt, remarks, equip_install_dt, org_equip_qty, rtn_equip_qty, concat(:remarks2, coalesce(remarks2,'')), 
				city, prepay_month, prepay_start, send, wage_type, change_money, 'admin', 'admin'
			from swo_service
			where id = :id
		";
		$rmk = "系统操作\n";
    	$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false) $command->bindParam(':id',$id,PDO::PARAM_INT);
		if (strpos($sql,':status_dt')!==false) $command->bindParam(':status_dt',$dt,PDO::PARAM_STR);
		if (strpos($sql,':remarks2')!==false) $command->bindParam(':remarks2',$rmk,PDO::PARAM_STR);
		$command->execute();
		return Yii::app()->db->getLastInsertID();
	}
	
	protected function addStopContractStatus(&$connection, $id, $cno, $dt) {
		$sql = "
			insert into swo_service_contract_no(
				contract_no, service_id, status_dt, status
			) 
			values(
				:contract_no, :service_id, :status_dt, 'T'
			)
		";
		$command=$connection->createCommand($sql);
		if (strpos($sql,':contract_no')!==false) $command->bindParam(':contract_no',$cno,PDO::PARAM_STR);
		if (strpos($sql,':service_id')!==false) $command->bindParam(':service_id',$id,PDO::PARAM_INT);
		if (strpos($sql,':status_dt')!==false) $command->bindParam(':status_dt',$dt,PDO::PARAM_STR);
		$command->execute();
	}
}
?>