<?php

class SalesMonthCountController extends Controller
{
	public $function_id='G20';
	
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
				'expression'=>array('SalesMonthCountController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('SalesMonthCountController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionDownExcel()
    {
        $model = new SalesMonthCountForm('view');
        if (isset($_POST['SalesMonthCountForm'])) {
            $model->attributes = $_POST['SalesMonthCountForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new SalesMonthCountForm('index');
        $session = Yii::app()->session;
        if (isset($session['salesMonthCount_c01']) && !empty($session['salesMonthCount_c01'])) {
            $criteria = $session['salesMonthCount_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_year = date("Y");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
	    set_time_limit(0);
        $model = new SalesMonthCountForm('view');
        if (isset($_POST['SalesMonthCountForm'])) {
            $model->attributes = $_POST['SalesMonthCountForm'];
            if ($model->validate()) {
                $model->retrieveData();
                $this->render('form',array('model'=>$model));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('index',array('model'=>$model));
            }
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G20');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G20');
	}
}
