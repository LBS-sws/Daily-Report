<?php

class CurlNotesController extends Controller
{
	public $function_id='D07';
	
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
				'actions'=>array('send','testCompany','testComplaint','testServiceOne','TestServiceFull'),
				'expression'=>array('CurlNotesController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('CurlNotesController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new CurlNotesList();
		if (isset($_POST['CurlNotesList'])) {
			$model->attributes = $_POST['CurlNotesList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['curlNotes_c01']) && !empty($session['curlNotes_c01'])) {
				$criteria = $session['curlNotes_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSend($index)
	{
        $model = new CurlNotesList();
        if($model->sendID($index)){
            Dialog::message(Yii::t('dialog','Information'), "已重新发送");
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "数据异常");
        }
        $this->redirect(Yii::app()->createUrl('curlNotes/index'));
	}

	public function actionTestCompany()
	{
        $model = new CurlNotesList();
        $model->testCompany();
        die();
	}

	public function actionTestComplaint()
	{
        $model = new CurlNotesList();
        $model->testComplaint();
        die();
	}

	public function actionTestServiceOne()
	{
        $model = new CurlNotesList();
        $model->testServiceOne();
        die();
	}

	public function actionTestServiceFull()
	{
        $model = new CurlNotesList();
        $model->testServiceFull();
        die();
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('D07');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('D07');
	}
}
