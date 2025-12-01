<?php

class CheckInStaffController extends Controller
{
	public $function_id='G35';
	
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
				'expression'=>array('CheckInStaffController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('CheckInStaffController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionDownExcel()
    {
        $model = new CheckInStaffForm('view');
        if (isset($_POST['CheckInStaffForm'])) {
            $model->attributes = $_POST['CheckInStaffForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new CheckInStaffForm('index');
        $session = Yii::app()->session;
        if (isset($session['checkInStaff_c01']) && !empty($session['checkInStaff_c01'])) {
            $criteria = $session['checkInStaff_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_start_date = date("Y/m/01");
            $model->search_end_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
	    set_time_limit(0);
        $model = new CheckInStaffForm('view');
        if (isset($_POST['CheckInStaffForm'])) {
            $model->attributes = $_POST['CheckInStaffForm'];
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
		return Yii::app()->user->validRWFunction('G35');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G35');
	}
}
