<?php

class CrossSearchController extends Controller
{
	public $function_id='CD03';
	
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
				'actions'=>array('edit'),
				'expression'=>array('CrossSearchController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('CrossSearchController','allowReadOnly'),
			),
			array('allow',
				'actions'=>array('delete','deleteAll'),
				'expression'=>array('CrossSearchController','allowDelete'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new CrossSearchList();
		if (isset($_POST['CrossSearchList'])) {
			$model->attributes = $_POST['CrossSearchList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['crossSearch_c01']) && !empty($session['crossSearch_c01'])) {
				$criteria = $session['crossSearch_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionReject()
	{
		if (isset($_POST['CrossSearchForm'])) {
			$model = new CrossSearchForm($_POST['CrossSearchForm']['scenario']);
			$model->attributes = $_POST['CrossSearchForm'];
            $model->setScenario("reject");
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Denied'));
				$this->redirect(Yii::app()->createUrl('crossSearch/index'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionDelete()
	{
		if (isset($_POST['CrossSearchForm'])) {
			$model = new CrossSearchForm($_POST['CrossSearchForm']['scenario']);
			$model->attributes = $_POST['CrossSearchForm'];
            $model->setScenario("delete");
			if($model->retrieveData($model->id)){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('crossSearch/index'));
            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), "数据异常,请刷新重试");
                $this->redirect(Yii::app()->createUrl('crossSearch/edit',array("index"=>$model->id)));
            }
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['CrossSearchForm'])) {
			$model = new CrossSearchForm($_POST['CrossSearchForm']['scenario']);
			$model->attributes = $_POST['CrossSearchForm'];
            $model->setScenario("audit");
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Approved'));
                $this->redirect(Yii::app()->createUrl('crossSearch/index'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new CrossSearchForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index)
	{
		$model = new CrossSearchForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('CD03');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('CD03');
	}

	public static function allowDelete() {
		return Yii::app()->user->validFunction('CN30');
	}
}
