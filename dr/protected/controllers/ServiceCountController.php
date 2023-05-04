<?php

class ServiceCountController extends Controller
{
	public $function_id='A12';
	
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
				'actions'=>array('index','edit'),
				'expression'=>array('ServiceCountController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$model = new ServiceCountForm('index');
		$this->render('form',array('model'=>$model));
	}

	public function actionEdit($year="2022",$cust_type="2",$status="N",$city="CN")
	{
		$model = new ServiceCountForm('index');
		$model->search_year = $year;
		$model->city_allow = $city;
		$model->cust_type = $cust_type;
		$model->status = $status;
		$model->retrieveData();
		$this->render('form',array('model'=>$model));
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A12');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A12');
	}
}
