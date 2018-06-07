<?php

class DataenqController extends Controller 
{
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
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
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('DataenqController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex() 
	{
		$model = new DataEnqList;
		if (isset($_POST['DataEnqList'])) {
			$model->attributes = $_POST['DataEnqList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session[$model->criteriaName()]) && !empty($session[$model->criteriaName()])) {
				$criteria = $session[$model->criteriaName()];
				$model->setCriteria($criteria);
			}
		}
		$model->retrieveData();
		$this->render('index',array('model'=>$model));
	}

	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G02');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G02');
	}
}
