<?php

class FeedbackController extends Controller 
{
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
				'actions'=>array('edit','save'),
				'expression'=>array('FeedbackController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('FeedbackController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new FeedbackList;
		if (isset($_POST['FeedbackList'])) {
			$model->attributes = $_POST['FeedbackList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a08']) && !empty($session['criteria_a08'])) {
				$criteria = $session['criteria_a08'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['FeedbackForm'])) {
			$model = new FeedbackForm($_POST['FeedbackForm']['scenario']);
			$model->attributes = $_POST['FeedbackForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->sendNotification();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done and Submit to Sent Notification'));
					
				$this->redirect(Yii::app()->createUrl('feedback/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new FeedbackForm('view');
		if (!$model->retrieveData($index,'view')) {
			throw new CHttpException(403,Yii::t('feedback','Unable to open this record. Maybe you don\'t have corresponding access right.'));
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index)
	{
		$model = new FeedbackForm('edit');
		if (!$model->retrieveData($index,'edit')) {
			throw new CHttpException(403,Yii::t('feedback','Unable to open this record. Maybe you don\'t have corresponding access right.'));
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='feedback-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A08');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A08');
	}
}
