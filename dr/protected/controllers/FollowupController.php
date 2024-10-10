<?php

class FollowupController extends Controller 
{
	public $function_id='A03';

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
/*		
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','new','edit','delete','save'),
				'users'=>array('@'),
			),
*/
			array('allow', 
				'actions'=>array('new','edit','delete','save'),
				'expression'=>array('FollowupController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','resetCompany'),
				'expression'=>array('FollowupController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionResetCompany()
	{
		$model = new FollowupForm();
		$model->resetCompany();
		Yii::app()->end();
	}

	public function actionIndex($pageNum=0)
	{
		$model = new FollowupList;
		if (isset($_POST['FollowupList'])) {
			$model->attributes = $_POST['FollowupList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a03']) && !empty($session['criteria_a03'])) {
				$criteria = $session['criteria_a03'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['FollowupForm'])) {
			$model = new FollowupForm($_POST['FollowupForm']['scenario']);
			$model->attributes = $_POST['FollowupForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';
				if (!$model->retrieveData($model->id)) {
					throw new CHttpException(404,'The requested page does not exist.');
				}
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('followup/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new FollowupForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new FollowupForm('new');
		$model->entry_dt = date("Y/m/d");
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new FollowupForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new FollowupForm('delete');
		if (isset($_POST['FollowupForm'])) {
			$model->attributes = $_POST['FollowupForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
		}
		$this->redirect(Yii::app()->createUrl('followup/index'));
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='followup-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A03');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A03');
	}
}
