<?php

class QualityController extends Controller
{
	public $function_id='E01';

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
				'expression'=>array('QualityController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('QualityController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new QualityList;
		if (isset($_POST['QualityList'])) {
			$model->attributes = $_POST['QualityList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_e01']) && !empty($session['criteria_e01'])) {
				$criteria = $session['criteria_e01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


//	public function actionSave()
//	{
//		if (isset($_POST['QualityForm'])) {
//			$model = new FollowupForm($_POST['QualityForm']['scenario']);
//			$model->attributes = $_POST['QualityForm'];
//			if ($model->validate()) {
//				$model->saveData();
////				$model->scenario = 'edit';
//				if (!$model->retrieveData($model->id)) {
//					throw new CHttpException(404,'The requested page does not exist.');
//				}
//				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
//				$this->redirect(Yii::app()->createUrl('followup/edit',array('index'=>$model->id)));
//			} else {
//				$message = CHtml::errorSummary($model);
//				Dialog::message(Yii::t('dialog','Validation Message'), $message);
//				$this->render('form',array('model'=>$model,));
//			}
//		}
//	}

//	public function actionView($index)
//	{
//		$model = new FollowupForm('view');
//		if (!$model->retrieveData($index)) {
//			throw new CHttpException(404,'The requested page does not exist.');
//		} else {
//			$this->render('form',array('model'=>$model,));
//		}
//	}
//
//	public function actionNew()
//	{
//		$model = new FollowupForm('new');
//		$model->entry_dt = date("Y/m/d");
//		$this->render('form',array('model'=>$model,));
//	}
//
//	public function actionEdit($index)
//	{
//		$model = new FollowupForm('edit');
//		if (!$model->retrieveData($index)) {
//			throw new CHttpException(404,'The requested page does not exist.');
//		} else {
//			$this->render('form',array('model'=>$model,));
//		}
//	}
//
//	public function actionDelete()
//	{
//		$model = new FollowupForm('delete');
//		if (isset($_POST['FollowupForm'])) {
//			$model->attributes = $_POST['FollowupForm'];
//			$model->saveData();
//			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
//		}
//		$this->redirect(Yii::app()->createUrl('followup/index'));
//	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
//	protected function performAjaxValidation($model)
//	{
//		if(isset($_POST['ajax']) && $_POST['ajax']==='followup-form')
//		{
//			echo CActiveForm::validate($model);
//			Yii::app()->end();
//		}
//	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('E01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('E01');
	}
}
