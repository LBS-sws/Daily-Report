<?php

class ApiController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', 
			'postOnly + service, company, invoice', //  allow via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('service','company'),
				'ips'=>array('218.103.39.9'),
			),
			array('deny',  // deny all users
				'ips'=>array('*'),
			),
		);
	}

	public function actionService() {
		if (isset($_POST['data'])) {
			$data = json_decode($_POST['data']);
			foreach ($data as $key=>$value) {
				
			}
		}
		Yii::app()->end();
	}

	public function actionCompany() {
		if (isset($_POST['data']) && isset($_POST['lastid'])) {
			try {
				$this->saveData('company',$_POST['lastid'],$_POST['data']);
				echo 'success';
			} catch(Exception $e) {
				echo 'Error: '.$e->getMessage();
			}
		}
		Yii::app()->end();
	}

	public function actionInvoice() {
		if (isset($_POST['data'])) {
			$data = json_decode($_POST['data']);
			foreach ($data as $key=>$value) {
				
			}
		}
		Yii::app()->end();
	}

	protected function saveData($cat, $id, $data) {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "insert into datatxn$suffix.txnrecord(
					cat, last_id, data, status) values (
					:cat, :id, :data, 'P')";

		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cat',$cat,PDO::PARAM_STR);
		$command->bindParam(':id',$id,PDO::PARAM_INT);
		$command->bindParam(':data',$data,PDO::PARAM_STR);
		$command->execute();
	}
}
