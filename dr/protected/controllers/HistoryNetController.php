<?php

class HistoryNetController extends Controller
{
	public $function_id='G09';
	
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
				'expression'=>array('HistoryNetController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('HistoryNetController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel()
    {
        $model = new HistoryNetForm('view');
        if (isset($_POST['HistoryNetForm'])) {
            $model->attributes = $_POST['HistoryNetForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new HistoryNetForm('index');
        $session = Yii::app()->session;
        if (isset($session['historyNet_c01']) && !empty($session['historyNet_c01'])) {
            $criteria = $session['historyNet_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        $model = new HistoryNetForm('view');
        if (isset($_POST['HistoryNetForm'])) {
            $model->attributes = $_POST['HistoryNetForm'];
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
		return Yii::app()->user->validRWFunction('G09');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G09');
	}
}
