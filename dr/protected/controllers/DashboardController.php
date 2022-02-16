<?php

class DashboardController extends Controller
{
	public $interactive = false;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl - checksession', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
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
				'actions'=>array('ranklist'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionRanklist() {
        $model = new RankMonthList();
        $model->retrieveDataByPage();
        $rtn = $model->attr;
		echo json_encode($rtn);
	}

	public function actionShowranklist() {
		$this->layout = "main_nm";
		$this->render('//dashboard/ranklist',array('popup'=>true));
	}

}

?>