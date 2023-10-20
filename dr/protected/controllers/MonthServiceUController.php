<?php

class MonthServiceUController extends Controller
{
	public $function_id='G22';
	
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
				'expression'=>array('MonthServiceUController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('MonthServiceUController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel()
    {
        $model = new MonthServiceUForm('view');
        if (isset($_POST['MonthServiceUForm'])) {
            $model->attributes = $_POST['MonthServiceUForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new MonthServiceUForm('index');
        $session = Yii::app()->session;
        if (isset($session['monthServiceU_c01']) && !empty($session['monthServiceU_c01'])) {
            $criteria = $session['monthServiceU_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        $model = new MonthServiceUForm('view');
        if (isset($_POST['MonthServiceUForm'])) {
            $model->attributes = $_POST['MonthServiceUForm'];
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
		return Yii::app()->user->validRWFunction('G22');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G22');
	}
}
