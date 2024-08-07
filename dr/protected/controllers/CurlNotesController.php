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
				'actions'=>array('send','testOffice','testCompany','testComplaint','testServiceOne','TestServiceFull','testIp','testCrossOne','TestCrossFull','System'),
				'expression'=>array('CurlNotesController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','getAjaxStr'),
				'expression'=>array('CurlNotesController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionGetAjaxStr()
    {
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new CurlNotesList();
            $id = key_exists("id",$_POST)?$_POST["id"]:0;
            $type = key_exists("type",$_POST)?$_POST["type"]:0;
            $content = $model->getCurlTextForID($id,$type);
            echo CJSON::encode(array("content"=>$content));
        }else{
            $this->redirect(Yii::app()->createUrl('curlNotes/index'));
        }
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

	public function actionTestOffice()
	{
        $model = new CurlNotesList();
        $model->testOffice();
        die();
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

	public function actionTestCrossOne($index=3)
	{
        $model = new CurlNotesList();
        $model->testCrossOne($index);
        die();
	}

	public function actionTestCrossFull($idStr='3,4,5')
	{
        $model = new CurlNotesList();
        $model->testCrossFull($idStr);
        die();
	}

	public function actionTestServiceFull()
	{
        $model = new CurlNotesList();
        $model->testServiceFull();
        die();
	}

	public function actionTestIp()
	{
        $model = new CurlNotesList();
        $model->testIp();
        die();
	}

	public function actionSystem($type)
	{
	    set_time_limit(0);
        $model = new CurlNotesList();
        $model->systemU($type);
        die();
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('D07');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('D07');
	}
}
