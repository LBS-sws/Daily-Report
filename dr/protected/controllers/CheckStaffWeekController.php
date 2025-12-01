<?php

class CheckStaffWeekController extends Controller
{
	public $function_id='G38';
	
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
				'expression'=>array('CheckStaffWeekController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('CheckStaffWeekController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionDownExcel()
    {
        $model = new CheckStaffWeekForm('view');
        if (isset($_POST['CheckStaffWeekForm'])) {
            $model->attributes = $_POST['CheckStaffWeekForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new CheckStaffWeekForm('index');
        $session = Yii::app()->session;
        if (isset($session['checkStaffWeek_c01']) && !empty($session['checkStaffWeek_c01'])) {
            $criteria = $session['checkStaffWeek_c01'];
            $model->setCriteria($criteria);
        }else{
            $thisweek_start = date("Y/m/d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
            $thisweek_end = date("Y/m/d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));
            $model->start_date = $thisweek_start;
            $model->end_date = $thisweek_end;
            $model->city = null;
            $model->condition = array(1,2,3,4,5);
            $model->seniority_min = 0;
            $model->staff_type = 0;
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
	    set_time_limit(0);
        $model = new CheckStaffWeekForm('view');
        if (isset($_POST['CheckStaffWeekForm'])) {
            $model->attributes = $_POST['CheckStaffWeekForm'];
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
		return Yii::app()->user->validRWFunction('G38');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G38');
	}
}
