<?php

class QcController extends Controller 
{
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
				'actions'=>array('new','edit','delete','save','fileupload','fileremove','filedownload'),
				'expression'=>array('QcController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','filedownload'),
				'expression'=>array('QcController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new QcList;
		if (isset($_POST['QcList'])) {
			$model->attributes = $_POST['QcList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a06']) && !empty($session['criteria_a06'])) {
				$criteria = $session['criteria_a06'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['QcForm'])) {
			$model = new QcForm($_POST['QcForm']['scenario']);
			$model->attributes = $_POST['QcForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';

				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('qc/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				switch ($model->service_type) {
					case 'IA': $formfile = $model->new_form ? 'formia' : 'form'; break;
					case 'IB' : $formfile = $model->new_form ? 'formib' : 'form'; break;
					default : $formfile = 'form';
				}
				$this->render($formfile,array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new QcForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			switch ($model->service_type) {
				case 'IA': $formfile = $model->new_form ? 'formia' : 'form'; break;
				case 'IB' : $formfile = $model->new_form ? 'formib' : 'form'; break;
				default : $formfile = 'form';
			}
			$this->render($formfile,array('model'=>$model,));
		}
	}
	
	public function actionNew($type='')
	{
		$model = new QcForm('new');
		switch ($type) {
			case 'IA': $formfile = 'formia'; $model->new_form = true; break;
			case 'IB' : $formfile = 'formib'; $model->new_form = true; break;
			default : $formfile = 'form';
		}
		$model->service_type = $type;
		$model->entry_dt = date('Y/m/d');
		$model->qc_dt = date('Y/m/d');
		$model->initData();
		$this->render($formfile,array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new QcForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			switch ($model->service_type) {
				case 'IA': $formfile = $model->new_form ? 'formia' : 'form'; break;
				case 'IB' : $formfile = $model->new_form ? 'formib' : 'form'; break;
				default : $formfile = 'form';
			}
			$this->render($formfile,array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new QcForm('delete');
		if (isset($_POST['QcForm'])) {
			$model->attributes = $_POST['QcForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
		}
		$this->redirect(Yii::app()->createUrl('qc/index'));
	}
	
//	public function actionFileupload() {
//		$model = new QcForm();
//		if (isset($_POST['QcForm'])) {
//			$model->attributes = $_POST['QcForm'];
//			
//			$docman = new DocMan($model->docType,$model->id);
//			if (isset($_FILES['attachment'])) $docman->files = $_FILES['attachment'];
//			$docman->fileUpload();
//			echo $docman->genTableFileList(false);
//		} else {
//			echo "NIL";
//		}
//	}
	
	public function actionFileupload($doctype) {
		$model = new QcForm();
		if (isset($_POST['QcForm'])) {
			$model->attributes = $_POST['QcForm'];
			
			$id = ($_POST['QcForm']['scenario']=='new') ? 0 : $model->id;
			$docman = new DocMan($doctype,$id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
			$docman->fileUpload();
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
//	public function actionFileRemove() {
//		$model = new QcForm();
//		if (isset($_POST['QcForm'])) {
//			$model->attributes = $_POST['QcForm'];
//			
//			$docman = new DocMan($model->docType,$model->id);
//			$docman->fileRemove($model->removeFileId);
//			echo $docman->genTableFileList(false);
//		} else {
//			echo "NIL";
//		}
//	}
	
	public function actionFileRemove($doctype) {
		$model = new QcForm();
		if (isset($_POST['QcForm'])) {
			$model->attributes = $_POST['QcForm'];
			$docman = new DocMan($doctype,$model->id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			$docman->fileRemove($model->removeFileId[strtolower($doctype)]);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
//	public function actionFileDownload($docId, $fileId) {
//		$sql = "select city from swo_qc where id = $docId";
//		$row = Yii::app()->db->createCommand($sql)->queryRow();
//		if ($row!==false) {
//			$citylist = Yii::app()->user->city_allow();
//			if (strpos($citylist, $row['city']) !== false) {
//				$docman = new DocMan('QC', $docId);
//				$docman->fileDownload($fileId);
//			} else {
//				throw new CHttpException(404,'Access right not match.');
//			}
//		} else {
//			throw new CHttpException(404,'Record not found.');
//		}
//	}

	public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
		$sql = "select city from swo_qc where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan($doctype,$docId,'QcForm');
				$docman->masterId = $mastId;
				$docman->fileDownload($fileId);
			} else {
				throw new CHttpException(404,'Access right not match.');
			}
		} else {
				throw new CHttpException(404,'Record not found.');
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='qc-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A06');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A06');
	}
}
