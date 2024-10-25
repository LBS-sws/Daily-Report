<?php

class Counter {
	public static function countConfReq() {
		$rtn = 0;

		$wf = new WorkflowPayment;
		$wf->connection = Yii::app()->db;
		$list = $wf->getPendingRequestIdList('PAYMENT', 'PB', Yii::app()->user->id);
		$items = empty($list) ? array() : explode(',',$list);
		$rtn = count($items);

		return $rtn;
	}

	public static function countApprReq() {
		$rtn = 0;

		$wf = new WorkflowPayment;
		$wf->connection = Yii::app()->db;
		$list = $wf->getPendingRequestIdList('PAYMENT', 'PA', Yii::app()->user->id);
		$items = empty($list) ? array() : explode(',',$list);
		$rtn = count($items);

		return $rtn;
	}
	
	public static function countReimb() {
		$rtn = 0;
		
		$wf = new WorkflowPayment;
		$wf->connection = Yii::app()->db;
		$list1 = $wf->getPendingRequestIdList('PAYMENT', 'PR', Yii::app()->user->id);
		$items = empty($list1) ? array() : explode(',',$list1);
		$rtn = count($items);
		
		$list2 = $wf->getPendingRequestIdList('PAYMENT', 'QR', Yii::app()->user->id);
		$items = empty($list2) ? array() : explode(',',$list2);
		$rtn += count($items);
		
		return $rtn;
	}
	
	public static function countSign() {
		$rtn = 0;
		
		$wf = new WorkflowPayment;
		$wf->connection = Yii::app()->db;
		$list = $wf->getPendingRequestIdList('PAYMENT', 'PS', Yii::app()->user->id);
		$items = empty($list) ? array() : explode(',',$list);
		$rtn = count($items);
		
		return $rtn;
	}

	public static function countCrossReq() {
        $uid = Yii::app()->user->id;
        $sql = "select count(id) from swo_cross where status_type=2 and lcu='{$uid}' ";
        $rtn = Yii::app()->db->createCommand($sql)->queryScalar();

		return $rtn;
	}

	public static function countCrossAudit() {
        $city_allow = Yii::app()->user->city_allow();
        $sql = "select count(id) from swo_cross where status_type=1 and (
            (cross_city in ({$city_allow}) and cross_type not in(0,1))
            or (old_city in ({$city_allow}) and cross_type in(0,1))
            or (cross_type=5 and qualification_city in ({$city_allow}))
        )";
        $rtn = Yii::app()->db->createCommand($sql)->queryScalar();

		return $rtn;
	}
}

?>