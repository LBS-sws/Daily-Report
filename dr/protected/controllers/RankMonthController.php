<?php

class RankMonthController extends Controller
{
	public $function_id='T01';
	
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
				'expression'=>array('RankMonthController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('RankMonthController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new RankMonthList();
		if (isset($_POST['RankMonthList'])) {
			$model->attributes = $_POST['RankMonthList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankMonth_c01']) && !empty($session['rankMonth_c01'])) {
				$criteria = $session['rankMonth_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('T01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('T01');
	}
}
