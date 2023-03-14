<?php

class ComparisonController extends Controller
{
	public $function_id='G05';
	
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
				'expression'=>array('ComparisonController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('ComparisonController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel()
    {
        $model = new ComparisonForm('view');
        if (isset($_POST['ComparisonForm'])) {
            $model->attributes = $_POST['ComparisonForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new ComparisonForm('index');
        $session = Yii::app()->session;
        if (isset($session['comparison_c01']) && !empty($session['comparison_c01'])) {
            $criteria = $session['comparison_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->start_date = date("Y/01/01");
            $model->end_date = date("Y/m/t");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        $model = new ComparisonForm('view');
        if (isset($_POST['ComparisonForm'])) {
            $model->attributes = $_POST['ComparisonForm'];
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
		return Yii::app()->user->validRWFunction('G05');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G05');
	}
}
