<?php

class SummaryController extends Controller
{
	public $function_id='G03';
	
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
				'actions'=>array('ajaxSave'),
				'expression'=>array('SummaryController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('SummaryController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$model = new SummaryForm('index');
        $session = Yii::app()->session;
        if (isset($session['summary_c01']) && !empty($session['summary_c01'])) {
            $criteria = $session['summary_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->start_date = date("Y/01/01");
            $model->end_date = date("Y/m/t");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        $model = new SummaryForm('view');
        if (isset($_POST['SummaryForm'])) {
            $model->attributes = $_POST['SummaryForm'];
            $model->retrieveData();
            $this->render('form',array('model'=>$model));
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G03');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G03');
	}
}
