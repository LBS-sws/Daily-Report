<?php
class RegisterController extends Controller
{
	public function filters()
	{
		return array(
			'enforceRegisteredStation - activate',
			'enforceSessionExpiration - activate', 
			'enforceNoConcurrentLogin - activate',
			'accessControl - activate', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('new','edit','void','save'),
				'expression'=>array('RegisterController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('RegisterController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionActivate($key) {
		$model = new RegisterActivateForm();
		if (isset($_POST['RegisterActivateForm'])) {
			$model->attributes = $_POST['RegisterActivateForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('register','Station activation completed'));
				$this->redirect(Yii::app()->homeUrl);
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
			}
			$this->render('activate',array('model'=>$model,));
		} else {
			if (!$model->retrieveData($key)) {
				throw new CHttpException(404,Yii::t('register','Record not found or expired.'));
			} else {
				$this->render('activate',array('model'=>$model,));
			}
		}
	}

	public function actionIndex($pageNum=0) {
		$model = new RegisterList;
		if (isset($_POST['RegisterList'])) {
			$model->attributes = $_POST['RegisterList'];
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
	
	public function actionNew()
	{
		$model = new RegisterForm('new');
		$model->req_key = uniqid('sstn_');
		$this->render('form',array('model'=>$model,));
	}

	public function actionEdit($index,$timestamp)
	{
		$model = new RegisterForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			if (strtotime($model->lud2)!=$timestamp) $model->scenario = 'view';
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionView($index)
	{
		$model = new RegisterForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionVoid()
	{
		$model = new RegisterForm('void');
		if (isset($_POST['RegisterForm'])) {
			$model->attributes = $_POST['RegisterForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
		}
		$this->redirect(Yii::app()->createUrl('register/index'));
	}

	public function actionSave()
	{
		if (isset($_POST['RegisterForm'])) {
			$model = new RegisterForm($_POST['RegisterForm']['scenario']);
			$model->attributes = $_POST['RegisterForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('register/edit',array('index'=>$model->req_key)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('D04');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('D04');
	}
}
?>
