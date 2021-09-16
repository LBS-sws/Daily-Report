<?php

class CustomertypeIDController extends Controller
{
	public $function_id='C10';

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
				'actions'=>array('new','edit','delete','save'),
				'expression'=>array('CustomertypeIDController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('CustomertypeIDController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new CustomertypeIDList;
		if (isset($_POST['CustomertypeIDList'])) {
			$model->attributes = $_POST['CustomertypeIDList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_c10']) && !empty($session['criteria_c10'])) {
				$criteria = $session['criteria_c10'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['CustomertypeIDForm'])) {
			$model = new CustomertypeIDForm($_POST['CustomertypeIDForm']['scenario']);
			$model->attributes = $_POST['CustomertypeIDForm'];
			if ($model->validate()) {
				$model->saveData();
                $type = $model->index_num>1?1:0;
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('CustomertypeID/edit',array('index'=>$model->id,'type'=>$type)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new CustomertypeIDForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new CustomertypeIDForm('new');
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index,$type=0)
	{
		$model = new CustomertypeIDForm('edit');
		if (!$model->retrieveData($index,$type)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new CustomertypeIDForm('delete');
		if (isset($_POST['CustomertypeIDForm'])) {
			$model->attributes = $_POST['CustomertypeIDForm'];
			if ($model->isOccupied($model->id)) {
			    $type = $model->index_num>1?1:0;
				Dialog::message(Yii::t('dialog','Warning'), Yii::t('dialog','This record is already in use'));
				$this->redirect(Yii::app()->createUrl('CustomertypeID/edit',array('index'=>$model->id,'type'=>$type)));
			} else {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
				$this->redirect(Yii::app()->createUrl('CustomertypeID/index'));
			}
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('C10');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('C10');
	}
}
