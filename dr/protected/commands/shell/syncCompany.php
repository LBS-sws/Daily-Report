<?php
$sql = "select * from txnCustomerCompany where txn_id>";
$url = "http://118.89.46.224/dr-uat/index.php/api/company";
$type = 'company';

$link = mysql_connect('localhost','root','ai051121');
if ($link) {   
	mysql_set_charset('utf8',$link);
	if (mysql_select_db('txn')) {
		$rows = mysql_query("select last_id from txn_table where cat='$type'");
		$row = mysql_fetch_array($rows);
		$last_id = $row['last_id'];
		
		$out = array();
		$cnt = 0;
		$rtn = '';
		$sql .= $last_id;
		$result = mysql_query($sql);
		while ($r=mysql_fetch_array($result)) {
			$last_id = $r['txn_id'];
			$out[] = $r;
			$cnt++;

			
			if ($cnt > 500) {
				echo "ID: $last_id";
				$json = json_encode($out);
				$data = array('lastid'=>$last_id, 'data'=>$json);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, $url);
				$rtn = curl_exec($ch);
				curl_close($ch);
				
				if ($rtn=='success') {
					$sql = "update txn_table set last_id=$last_id where cat='$type'";
					$rtn = mysql_query($sql);
				} else {
					echo $rtn;
				}

				$cnt = 0;
				$out = array();
			}
		}
		
		if ($cnt > 0) {
			echo "ID: $last_id";
			$json = json_encode($out);
			$data = array('lastid'=>$last_id, 'data'=>$json);
				
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			$rtn = curl_exec($ch);
			var_dump($rtn);
			curl_close($ch);
			
			if ($rtn=='success') {
				$sql = "update txn_table set last_id=$last_id where cat='$type'";
				$rtn = mysql_query($sql);
			} else {
				echo $rtn;
			}
		}
	}
}
?>