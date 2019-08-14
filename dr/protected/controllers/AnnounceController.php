<?php

class AnnounceController extends Controller 
{
	public $function_id='D05';
	
	public function filters()
	{
		return array(
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
				'expression'=>array('AnnounceController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('AnnounceController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new AnnounceList;
		if (isset($_POST['AnnounceList'])) {
			$model->attributes = $_POST['AnnounceList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_d05']) && !empty($session['criteria_d05'])) {
				$criteria = $session['criteria_d05'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['AnnounceForm'])) {
			$model = new AnnounceForm($_POST['AnnounceForm']['scenario']);
			$model->attributes = $_POST['AnnounceForm'];
			if ($model->validate()) {
				if ($file = CUploadedFile::getInstance($model,'imageA')) {
					$model->image_type = $file->type;
					$content = file_get_contents($file->tempName);
					$model->imageA = base64_encode($content);
				} else {
					$model->imageA = $model->imageA_old;
				}
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('announce/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new AnnounceForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new AnnounceForm('new');
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new AnnounceForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function AnnounceDelete()
	{
		$model = new AnnounceForm('delete');
		if (isset($_POST['AnnounceForm'])) {
			$model->attributes = $_POST['AnnounceForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
			$this->redirect(Yii::app()->createUrl('announce/index'));
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('D05');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('D05');
	}
}
