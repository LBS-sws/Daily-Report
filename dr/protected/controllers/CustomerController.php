<?php

class CustomerController extends Controller 
{
	public $function_id='A01';
	
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
				'actions'=>array('new','edit','delete','save','sendAllToJD'),
				'expression'=>array('CustomerController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('CustomerController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSendAllToJD($city="")
	{
		$model = new CustomerForm();
        $model->sendAllCustomerToJD($city);
        Yii::app()->end();
	}

	public function actionIndex($pageNum=0)
	{
		$model = new CustomerList;
		if (isset($_POST['CustomerList'])) {
			$model->attributes = $_POST['CustomerList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a01']) && !empty($session['criteria_a01'])) {
				$criteria = $session['criteria_a01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['CustomerForm'])) {
			$model = new CustomerForm($_POST['CustomerForm']['scenario']);
			$model->attributes = $_POST['CustomerForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('customer/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
//			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionView($index)
	{
		$model = new CustomerForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new CustomerForm('new');
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new CustomerForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new CustomerForm('delete');
		if (isset($_POST['CustomerForm'])) {
			$model->attributes = $_POST['CustomerForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
		}
//		$this->actionIndex();
		$this->redirect(Yii::app()->createUrl('customer/index'));
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='customer-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A01');
	}
}
