<?php
session_start();
$loggedin = isset($_SESSION['userID']) ;
if(!$loggedin && isset($_POST['lbstwid']) && isset($_POST['lbstwkey'])){
	$id= $_POST['lbstwid'];
	$key= $_POST['lbstwkey'];
	$sesskey = $_POST['lbstwskey'];
	$flag = false;

	$staffid = explode(':',$id);
	if ($staffid!==false && !empty($staffid)) {
		$nid = isset($staffid[0]) ? $staffid[0] : '';
		$oid = isset($staffid[1]) ? $staffid[1] : $nid;
		$salt = 'lbscorp168';
		$str = md5($nid.$salt.$sesskey.$oid);
		$flag = ($str==$key);
	}

	if ($flag) {
		$username = $oid;
		$link = mysql_connect('localhost','root','lbsgroup168');
		if ($link) {   
    			mysql_set_charset('utf8',$link);
	    		if (mysql_select_db('lbs-tw')) {
				$result = mysql_query("SELECT StaffPermission.Permission1, StaffPermission.Permission2,a.StaffName,a.Office,a.StaffID from (SELECT StaffName, StaffPost, Office,StaffID FROM `Staff` WHERE Office!=0 and StaffID = '$username' and Status in (1,2)) a LEFT JOIN StaffPermission on a.StaffPost = StaffPermission.StaffPost");
				if (!$result || mysql_num_rows($result) != 0) {
					$_SESSION['userID']=mysql_result($result,0,'StaffID');
					$_SESSION['StaffID']=mysql_result($result,0,'StaffID');
					$_SESSION['StaffName'] = mysql_result($result,0,'StaffName');
					$_SESSION['Office'] = mysql_result($result,0,'Office');
					$_SESSION['timeout'] = time();
					$_SESSION['Right1'] = mysql_result($result,0,'Permission1');
					$_SESSION['Right2'] = mysql_result($result,0,'Permission2');
					$_SESSION['wrongPass'] = 0;

					if (isset($_POST['lbstwlang'])){
						$_SESSION['lang'] = $_POST['lbstwlang'];
					} else {
						$_SESSION['lang'] = 'zhtw';
					}

					$_SESSION['timeout'] = time();
				}
			}
		}
	}
}
$url = '/web/';
header('Location: '.$url);
?>
