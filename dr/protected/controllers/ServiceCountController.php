<?php

class ServiceCountController extends Controller
{
	public $function_id='A12';
	
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
				'actions'=>array('index','edit'),
				'expression'=>array('ServiceCountController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$model = new ServiceCountForm('index');
		$model->search_year = date("Y");
		$model->city_allow = Yii::app()->user->city();
		$this->render('index',array('model'=>$model));
	}

	public function actionEdit()
	{
		$model = new ServiceCountForm('index');
        $model->attributes = $_POST['ServiceCountForm'];
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
		return Yii::app()->user->validRWFunction('A12');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A12');
	}
}
