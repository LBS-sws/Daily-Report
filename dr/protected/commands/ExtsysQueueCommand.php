<?php
class ExtsysQueueCommand extends CConsoleCommand {
	protected $curl;
	protected $ok = true;
	protected $onlib_URL = 'https://onlib.lbsapps.com/seeddms/restapi/index.php/';
	protected $system;
	
	public function actionRunOnlib() {
		$this->system = 'onlib';
		$rows = $this->getQueueRecord();
		if (count($rows) > 0) {
			$this->curl = curl_init();
			if ($this->onlibAdminLogin()) {
				foreach ($rows as $row) {
					$ts = $row['ts'];
					if ($this->markStatus($row['id'], $ts, 'I')) {
						$ts = $this->getTimeStamp($row['id']);
						$mesg = "ID:".$row['id']." SYS:".$row['sys_id']." UID:".$row['username']." ACTION:".$row['action']."\n";
						echo $mesg;

						$this->ok = true;
						$this->onlibRun($row['username'], $row['action'], $row['new_param'], $row['old_param']);
						if ($this->ok) {
							$this->markStatus($row['id'], $ts, 'C');
							echo "\t-Done (default)\n";
						} else {
							$this->markStatus($row['id'], $ts, 'F');
							echo "\t-FAIL\n";
						}
					}
				}
				$this->onlibAdminLogout();
			}
			curl_close($this->curl);
		}
	}
	
	protected function getQueueRecord() {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.id, a.ts, a.sys_id, a.action, a.username, a.req_dt, a.old_param, a.new_param  
					from security$suffix.sec_extsys_queue a
				where a.status='P' and a.sys_id='".$this->system."' order by a.id";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		return $rows;
	}
	
	protected function getTimeStamp($id) {
		$ts = '';
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select ts from security$suffix.sec_extsys_queue where id=".$id;
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) $ts = $row['ts'];
		return $ts;
	}

	protected function markStatus($id, $ts, $sts) {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = ($sts=='C' || $sts=='F') 
			? "update security$suffix.sec_extsys_queue set fin_dt=now(), status=:status where id=:id and ts=:ts";
			: "update security$suffix.sec_extsys_queue set status=:status where id=:id and ts=:ts";
		$command=Yii::app()->db->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$id,PDO::PARAM_INT);
		if (strpos($sql,':status')!==false)
			$command->bindParam(':status',$sts,PDO::PARAM_STR);
		if (strpos($sql,':ts')!==false)
			$command->bindParam(':ts',$ts,PDO::PARAM_STR);
		$cnt = $command->execute();
		return ($cnt>0);
	}

	private function restAPI($type, $url, $data='') {
		var_dump($type);
		var_dump($url);
//		curl_reset($this->curl);
		$this->curl = curl_init();
		switch($type) {
			case 'POST':
				curl_setopt($this->curl, CURLOPT_URL, $url);
				curl_setopt($this->curl, CURLOPT_POST, 1);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
				break;
			case 'PUT':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($this->curl, CURLOPT_URL, $url);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
				break;
			case 'DELETE':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($this->curl, CURLOPT_URL, $url);
				break;
			default:
				$url2 = $url.($data=='' ? '' : '?').$data;
				curl_setopt($this->curl, CURLOPT_URL, $url2);
		}
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, "/tmp/cookie".$this->system.".txt");
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, "/tmp/cookie".$this->system.".txt");
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curl, CURLOPT_FAILONERROR, true);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($this->curl);
		$httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		if ($httpCode==404) {
			echo 'Data not found';
			return false;
		} elseif ($httpCode > 299) {
			echo curl_error($this->curl);
			return json_decode($json);
		} else {
			return json_decode($json);
		}
	}
	
	private function onlibRun($userid, $action, $nparam, $oparam) {
		$n_param = json_decode($nparam);
		$o_param = json_decode($oparam);
		
		switch ($action) {
			case 'new':
				$n_param->right=='CN' && $this->onlibNewUser($userid, $n_param->disp_name, $n_param->email, $n_param->role);
				break;
			case 'edit':
				$userobj = $this->onlibGetUser($userid);
				if ($userobj===false) {
					$this->onlibNewUser($userid, $n_param->disp_name, $n_param->email, $n_param->role);
				} else {
					$n_param->right!=$o_param->right &&	$this->onlibSetUserStatus($userid, ($n_param->right!='CN'));
					$n_param->role!=$o_param->role && $this->onlibChangeUserInGroup($userid, $n_param->role, $o_param->role);
				}
				break;
			case 'delete':
				$this->onlibDeleteUser($userid);
				break;
		}
	}

	private function onlibCheckRtn($rtn) {
		if ($rtn===false) {
			return false;
		} else {
			if (!$rtn->success) {
				echo "API Error: ".$rtn->message."\n"; 
				$this->ok = false;
			}
			return $rtn->success;
		}
	}
	
	private function onlibAdminLogin() {
		$data = "user=admin&pass=admin";
		$rtn = $this->restAPI('POST', $this->onlib_URL.'login', $data);
		return $this->onlibCheckRtn($rtn);
	}
	
	private function onlibAdminLogout() {
		$rtn = $this->restAPI('GET', $this->onlib_URL.'logout');
		return $this->onlibCheckRtn($rtn);
	}

	private function onlibGetUser($userid) {
		echo "Get User: $userid\n";
		$rtn = $this->restAPI('GET', $this->onlib_URL.'users/'.$userid);
		$this->onlibCheckRtn($rtn);
		return $rtn;
	}
	
	private function onlibNewUser($userid, $username, $email, $role) {
		$pwd = md5($userid.'$1688');
		$data = "user=$userid&pass=$pwd&name=$username&email=$email&language=zh_CN&theme=bootstrap&comment=Created_by_DMS&role=user";

		echo "New User\tDATA: $data\n";
		$rtn = $this->restAPI('POST', $this->onlib_URL.'users', $data);
		$this->onlibCheckRtn($rtn);
		
		if ($rtn!==false) {
//		$rtn = $this->onlibGetUser($userid);
			if ($rtn->success) {
				$this->onlibAddUserToGroup($userid, $role);
			}
		}
	}

	private function onlibDeleteUser($userid) {
		echo "Delete User: $userid\n";
		$rtn = $this->restAPI('DELETE', $this->onlib_URL.'users/'.$userid);
		$this->onlibCheckRtn($rtn);
	}
	
	private function onlibChangeUserInGroup($userid, $nrole, $orole) {
		$this->onlibRemoveUserFromGroup($userid, $orole);
		$this->onlibAddUserToGroup($userid, $nrole);
	}
	
	private function onlibAddUserToGroup($userid, $role) {
		$data = "userid=".$userid;
		foreach ($role as $group) {
			echo "Add User To Group: $group\n";
			$rtn = $this->restAPI('PUT', $this->onlib_URL.'groups/'.$group.'/addUser', $data);
			$this->onlibCheckRtn($rtn);
		}
	}
	
	public function onlibRemoveUserFromGroup($userid, $role) {
		$data = "userid=".$userid;
		foreach ($role as $group) {
			echo "Remove User From Group: $group\n";
			$rtn = $this->restAPI('PUT', $this->onlib_URL.'groups/'.$group.'/removeUser', $data);
			$this->onlibCheckRtn($rtn);
		}
	}
	
	private function onlibSetUserStatus($userid, $status) {
		echo "Set User Status: $userid $status\n";
		$data = "disable=".($status ? 'true' : 'false');
		$rtn = $this->restAPI('PUT', $this->onlib_URL.'users/'.$userid.'/disable', $data);
		return $this->onlibCheckRtn($rtn); 
	}
}
?>