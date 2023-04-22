 <?php
class ServiceStatusCommand extends CConsoleCommand {
	protected $webroot;

	public function actionSuspendToStop() {
		echo "Suspend to Stop ... \n";

		$records = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "
			select a.contract_no, a.service_id, a.status_dt, date_add(date_add(a.status_dt, interval 3 month), interval 1 day) as target_dt, c.city 
			from swo_service_contract_no a
			left outer join swo_service_contract_no b on a.contract_no=b.contract_no and a.status_dt < b.status_dt
			inner join swo_service c on a.service_id=c.id
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
				$city = $row['city'];
				echo "CTRT NO: $ctrt_no / STS_DT: $status_dt / TGT_DT: $target_dt / CITY: $city\n";
				try {
					$connection = Yii::app()->db;
					$transaction=$connection->beginTransaction();
				
					$rid = $this->addStopService($connection, $row['service_id'], $target_dt);
					$this->addStopContractStatus($connection, $rid, $ctrt_no, $target_dt);
					$transaction->commit();
					
					if (!isset($records[$city])) $records[$city] = array();
					$records[$city][] = $row['service_id']; //$rid;
				} catch(Exception $e) {
					$transaction->rollback();
					echo "EXCEPTION ERROR: ".$e->getMessage()."\n";
					Yii::app()->end();
				}
			}

			if (!empty($records)) {
				$data = array();
				$obj = new City();
				foreach ($records as $key=>$rec) {
					$data[$key] = $rec;
					$regions = $obj->getAncestor($key);
					if (!empty($regions)) {
						foreach ($regions as $region) {
							if (strpos('HK/', $region.'/')===false) {
								if (isset($data[$region])) {
									$data[$region] = array_merge($data[$region],$rec);
								} else {
									$data[$region] = $rec;
								}
							}
						}
					}
				}

				$baseCities = General::getCityListWithNoDescendant();
				foreach ($data as $key=>$rec) {
					$this->notifySuspension($key, $rec, array_key_exists($key, $baseCities));
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

	protected function notifySuspension($city, $ids, $isBaseCity) {
		$cityname = City::model()->findByPk($city)->name;
		$to = $this->getReceiver($city, $isBaseCity);
		$cc = array();
		
		$param = array(
				'from_addr'=>Yii::app()->params['systemEmail'],
				'to_addr'=>json_encode($to),
				'cc_addr'=>json_encode($cc),
				'subject'=>"超3个月暂停转终止客户明细 ($cityname)",
				'description'=>'以下客户的服务已暂停超过3个月，系统已把服务转化终止记录',
				'message'=>$this->printSuspensionList($ids, $isBaseCity),
				'test'=>false,
			);
		$connection = Yii::app()->db;
		$this->sendEmail($connection, $param);
	}

	protected function getReceiver($city, $isBaseCity) {
		$rtn = array();
        $suffix = Yii::app()->params['envSuffix'];
        
		if ($isBaseCity) {
			$sql = "SELECT a.email 
				FROM security$suffix.sec_user a,security$suffix.sec_user_access b 
				WHERE a.username=b.username AND a.city='$city' 
				AND b.a_read_write LIKE '%A02%' AND a.status='A' AND b.system_id='drs'
			";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			if ($rows){
				foreach ($rows as $row){
					if(!in_array($row["email"],$rtn)) {
						if ($row["email"]!="") $rtn[] = $row["email"];
					}
				}
			}
		}

		$sql = "SELECT a.email 
			FROM security$suffix.sec_user a,security$suffix.sec_city b 
			WHERE a.username=b.incharge AND b.code='$city' AND a.status='A'
		";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if ($rows){
            foreach ($rows as $row){
                if(!in_array($row["email"],$rtn)) {
					if ($row["email"]!="") $rtn[] = $row["email"];
				}
            }
        }
		
		return $rtn;
	}

	protected function printSuspensionList($ids, $isBaseCity) {
		$output = '';
		if (!empty($ids)) {
			$output = "<table border=1>";
			$output .= "<tr>"
					.($isBaseCity ? "" : "<th>城市</th>")
					."<th>暂停日期"
					."</th><th>客户编号及名称"
					."</th><th>客户类别"
					."</th><th>性质"
					."</th><th>服务内容"
					."</th><th>月金额"
					."</th><th>年金额"
					."</th><th>变动原因"
					."</th></tr>\n";

			$idstring = implode(',',$ids);
			$suffix = Yii::app()->params['envSuffix'];
			$sql = "
				select a.status_dt, a.company_name, b.description as cust_type_desc, c.description as nature_type_desc, a.service, a.amt_paid, a.paid_type, a.reason, d.contract_no, e.name as cityname
				from swo_service a
				left outer join swo_customer_type b on a.cust_type=b.id
				left outer join swo_nature c on a.nature_type=c.id
				left outer join swo_service_contract_no d on a.id=d.service_id
				left outer join security$suffix.sec_city e on a.city=e.code
				where a.id in ($idstring)
				order by a.city, a.status_dt, a.company_name
			";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			if ($rows) {
				foreach ($rows as $row) {
					$output .= "<tr>"
						.($isBaseCity ? "" : "<td>".$row['cityname']."</td>")
						."<td>".date('Y-m-d',strtotime($row['status_dt']))
						."</td><td>".$row['company_name']
						."</td><td>".$row['cust_type_desc']
						."</td><td>".$row['nature_type_desc']
						."</td><td>".$row['service']
						."</td><td align='right'>".($row['paid_type']=='1' ? $row['amt_paid'] : ($row['paid_type']=='M' ? round($row['amt_paid']*1, 2) : round($row['amt_paid']/12, 2)))
						."</td><td align='right'>".($row['paid_type']=='1' ? $row['amt_paid'] : ($row['paid_type']=='M' ? round($row['amt_paid']*12, 2) : round($row['amt_paid']*1, 2)))
						."</td><td>".$row['reason']
						."</td></tr>\n";
				}
			}
			$output .= "</table>\n";
		}
		return $output;
	}

	protected function sendEmail(&$connection, $record=array()) {
		$suffix = Yii::app()->params['envSuffix'];
		$suffix1 = ($suffix=='dev') ? '_w' : $suffix;
		if (isset($record['test']) && $record['test']) {
			$sql = "insert into swoper$suffix1.swo_email_queue
					(from_addr, to_addr, cc_addr, subject, description, message, status, lcu)
					values
					(:from_addr, :to_addr, :cc_addr, :subject, :description, :message, 'X', 'admin')
				";
		} else {
			$sql = "insert into swoper$suffix1.swo_email_queue
					(from_addr, to_addr, cc_addr, subject, description, message, status, lcu)
					values
					(:from_addr, :to_addr, :cc_addr, :subject, :description, :message, 'P', 'admin')
				";
		}
		$command = $connection->createCommand($sql);
		if (strpos($sql,':from_addr')!==false)
			$command->bindParam(':from_addr',$record['from_addr'],PDO::PARAM_STR);
		if (strpos($sql,':to_addr')!==false)
			$command->bindParam(':to_addr',$record['to_addr'],PDO::PARAM_STR);
		if (strpos($sql,':cc_addr')!==false)
			$command->bindParam(':cc_addr',$record['cc_addr'],PDO::PARAM_STR);
		if (strpos($sql,':subject')!==false)
			$command->bindParam(':subject',$record['subject'],PDO::PARAM_STR);
		if (strpos($sql,':description')!==false)
			$command->bindParam(':description',$record['description'],PDO::PARAM_STR);
		if (strpos($sql,':message')!==false)
			$command->bindParam(':message',$record['message'],PDO::PARAM_STR);
		$command->execute();
	}
}
?>