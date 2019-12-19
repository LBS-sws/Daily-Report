<?php
class DocmanController extends Controller
{
	// By pass System Blocking checking
	public function beforeAction($action) {
		return true;
	}
	
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow', 
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionDownload($index, $token) {
		$url = Yii::app()->createAbsoluteUrl('docman/file',array('index'=>$index,'token'=>$token));
		$this->redirect(array('site/home','url'=>$url));
	}
	
	public function actionFile($index, $token) {
		DocMan::fileDownloadByIdName($index, $token);
	}
}
?>