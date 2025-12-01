<?php

class CheckInMonthController extends Controller
{
	public $function_id='G37';
	
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
				'actions'=>array(''),
				'expression'=>array('CheckInMonthController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('CheckInMonthController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionDownExcel()
    {
        $model = new CheckInMonthForm('view');
        if (isset($_POST['CheckInMonthForm'])) {
            $model->attributes = $_POST['CheckInMonthForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new CheckInMonthForm('index');
        $session = Yii::app()->session;
        if (isset($session['checkInMonth_c01']) && !empty($session['checkInMonth_c01'])) {
            $criteria = $session['checkInMonth_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->start_date = date("Y/m/01");
            $model->end_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
	    set_time_limit(0);
        $model = new CheckInMonthForm('view');
        if (isset($_POST['CheckInMonthForm'])) {
            $model->attributes = $_POST['CheckInMonthForm'];
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
		return Yii::app()->user->validRWFunction('G37');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G37');
	}
}
