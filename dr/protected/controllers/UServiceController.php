<?php

class UServiceController extends Controller
{
	public $function_id='G10';
	
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
				'expression'=>array('UServiceController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('UServiceController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel()
    {
        $model = new UServiceForm('view');
        if (isset($_POST['UServiceForm'])) {
            $model->attributes = $_POST['UServiceForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new UServiceForm('index');
        $session = Yii::app()->session;
        if (isset($session['uService_c01']) && !empty($session['uService_c01'])) {
            $criteria = $session['uService_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_year = date("Y");
            $model->search_month = date("n");
            $model->search_start_date = date("Y/m/01");
            $weekDay = date("w");
            if($weekDay==6){
                $model->search_end_date = date("Y/m/d",strtotime("+ 6 day"));
            }else{
                $model->search_end_date = date("Y/m/d",strtotime("+ ".(5-$weekDay)." day"));
            }
            $i = ceil($model->search_month/3);//向上取整
            $model->search_quarter = 3*$i-2;
            $model->city = null;
            $model->condition = array(1,2,3,4);
            $model->seniority_min = 3;
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
	    set_time_limit(0);
        $model = new UServiceForm('view');
        if (isset($_POST['UServiceForm'])) {
            $model->attributes = $_POST['UServiceForm'];
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
		return Yii::app()->user->validRWFunction('G10');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G10');
	}
}
