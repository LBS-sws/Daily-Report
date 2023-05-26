<?php

class SalesAnalysisController extends Controller
{
	public $function_id='G12';
	
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
				'expression'=>array('SalesAnalysisController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('SalesAnalysisController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel()
    {
        $model = new SalesAnalysisForm('view');
        if (isset($_POST['SalesAnalysisForm'])) {
            $model->attributes = $_POST['SalesAnalysisForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new SalesAnalysisForm('index');
        $session = Yii::app()->session;
        if (isset($session['salesAnalysis_c01']) && !empty($session['salesAnalysis_c01'])) {
            $criteria = $session['salesAnalysis_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView(){
        $model = new SalesAnalysisForm('view');
        if (isset($_POST['SalesAnalysisForm'])) {
            $model->attributes = $_POST['SalesAnalysisForm'];
        }else{
            $session = Yii::app()->session;
            if (isset($session['salesAnalysis_c01']) && !empty($session['salesAnalysis_c01'])) {
                $criteria = $session['salesAnalysis_c01'];
                $model->setCriteria($criteria);
            }
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('form',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G12');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G12');
	}
}
