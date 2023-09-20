<?php

class WeekServiceUController extends Controller
{
	public $function_id='G18';
	
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
				'expression'=>array('WeekServiceUController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('WeekServiceUController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel()
    {
        $model = new WeekServiceUForm('view');
        if (isset($_POST['WeekServiceUForm'])) {
            $model->attributes = $_POST['WeekServiceUForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new WeekServiceUForm('index');
        $session = Yii::app()->session;
        if (isset($session['weekServiceU_c01']) && !empty($session['weekServiceU_c01'])) {
            $criteria = $session['weekServiceU_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        $model = new WeekServiceUForm('view');
        if (isset($_POST['WeekServiceUForm'])) {
            $model->attributes = $_POST['WeekServiceUForm'];
            if ($model->validate()) {
                set_time_limit(0);
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
		return Yii::app()->user->validRWFunction('G18');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G18');
	}
}
