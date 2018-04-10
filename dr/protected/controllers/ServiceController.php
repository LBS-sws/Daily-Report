<?php

class ServiceController extends Controller 
{
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete,fileremove', // we only allow deletion via POST request
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
				'actions'=>array('new','edit','amend','suspend','resume','renew','save','delete','terminate','fileupload','fileremove','filedownload'),
				'expression'=>array('ServiceController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','filedownload'),
				'expression'=>array('ServiceController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new ServiceList;
		if (isset($_POST['ServiceList'])) {
			$model->attributes = $_POST['ServiceList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a02']) && !empty($session['criteria_a02'])) {
				$criteria = $session['criteria_a02'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSave()
	{
		if (isset($_POST['ServiceForm'])) {
			$model = new ServiceForm($_POST['ServiceForm']['scenario']);
			$model->attributes = $_POST['ServiceForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('service/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new ServiceForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->status_desc = $model->getStatusDesc();
			$model->backlink = Yii::app()->request->urlReferrer;
			$this->render('form',array('model'=>$model,));
		}
	}
		
	public function actionNew($index=0)
	{
		$model = new ServiceForm('new');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->reason = '';
			$model->remarks = '';
			$model->org_equip_qty = 0;
			$model->rtn_equip_qty = 0;
			$model->id = 0;
			$model->files = '';
			$model->docMasterId['service'] = 0;
			$model->removeFileId['service'] = 0;
			$model->no_of_attm['service'] = 0;
		}
		$model->status = 'N';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionRenew($index=0)
	{
		$model = new ServiceForm('renew');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->reason = '';
			$model->remarks = '';
			$model->org_equip_qty = 0;
			$model->rtn_equip_qty = 0;
			$model->sign_dt = null;
			$model->equip_install_dt = null;
			$model->ctrt_end_dt = null;
			$model->first_dt = null;
			$model->first_tech = '';
			$model->id = 0;
			$model->files = '';
			$model->docMasterId['service'] = 0;
			$model->removeFileId['service'] = 0;
			$model->no_of_attm['service'] = 0;
		}
		$model->status = 'C';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionEdit($index)
	{
		$model = new ServiceForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->status_desc = $model->getStatusDesc();
			$model->backlink = Yii::app()->request->urlReferrer;
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionAmend($index=0)
	{
		$model = new ServiceForm('amend');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = $model->product_id;
			$model->b4_service = $model->service;
			$model->b4_paid_type = $model->paid_type;
			$model->b4_amt_paid = $model->amt_paid;
			$model->ctrt_period = 0;
			$model->ctrt_end_dt = null;
			$model->cont_info = '';
			$model->first_tech = '';
			$model->reason = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
			$model->org_equip_qty = 0;
			$model->rtn_equip_qty = 0;
			$model->id = 0;
			$model->files = '';
			$model->docMasterId['service'] = 0;
			$model->removeFileId['service'] = 0;
			$model->no_of_attm['service'] = 0;
		}
		$model->status = 'A';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionResume($index=0)
	{
		$model = new ServiceForm('resume');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->amt_install = 0;
			$model->cont_info = '';
			$model->first_dt = null;
			$model->first_tech = '';
			$model->reason = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
			$model->org_equip_qty = 0;
			$model->rtn_equip_qty = 0;
			$model->id = 0;
			$model->files = '';
			$model->docMasterId['service'] = 0;
			$model->removeFileId['service'] = 0;
			$model->no_of_attm['service'] = 0;
		}
		$model->status = 'R';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionSuspend($index=0)
	{
		$model = new ServiceForm('suspend');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->amt_install = 0;
			$model->ctrt_period = 0;
			$model->ctrt_end_dt = null;
			$model->cont_info = '';
			$model->first_dt = null;
			$model->first_tech = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
			$model->id = 0;
			$model->files = '';
			$model->docMasterId['service'] = 0;
			$model->removeFileId['service'] = 0;
			$model->no_of_attm['service'] = 0;
		}
		$model->status = 'S';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionTerminate($index=0)
	{
		$model = new ServiceForm('terminate');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->amt_install = 0;
			$model->ctrt_period = 0;
			$model->ctrt_end_dt = null;
			$model->cont_info = '';
			$model->first_dt = null;
			$model->first_tech = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
			$model->id = 0;
			$model->files = '';
			$model->docMasterId['service'] = 0;
			$model->removeFileId['service'] = 0;
			$model->no_of_attm['service'] = 0;
		}
		$model->status = 'T';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionDelete()
	{
		$model = new ServiceForm('delete');
		if (isset($_POST['ServiceForm'])) {
			$model->attributes = $_POST['ServiceForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
		}
		$this->redirect(Yii::app()->createUrl('service/index'));
	}
/*
	public function actionFileupload() {
		$model = new ServiceForm();
		if (isset($_POST['ServiceForm'])) {
			$model->attributes = $_POST['ServiceForm'];
			
			$docman = new DocMan($model->docType,$model->id);
			if (isset($_FILES['attachment'])) $docman->files = $_FILES['attachment'];
			$docman->fileUpload();
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
*/	
	public function actionFileupload($doctype) {
		$model = new ServiceForm();
		if (isset($_POST['ServiceForm'])) {
			$model->attributes = $_POST['ServiceForm'];
			
			$id = empty($model->id) ? 0 : $model->id;
			$docman = new DocMan($doctype,$id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
			$docman->fileUpload();
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
/*
	public function actionFileRemove() {
		$model = new ServiceForm();
		if (isset($_POST['ServiceForm'])) {
			$model->attributes = $_POST['ServiceForm'];
			
			$docman = new DocMan($model->docType,$model->id);
			$docman->fileRemove($model->removeFileId);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
*/
	public function actionFileRemove($doctype) {
		$model = new ServiceForm();
		if (isset($_POST['ServiceForm'])) {
			$model->attributes = $_POST['ServiceForm'];
			$docman = new DocMan($doctype,$model->id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			$docman->fileRemove($model->removeFileId[strtolower($doctype)]);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
/*	
	public function actionFileDownload($docId, $fileId) {
		$sql = "select city from swo_service where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan('SERVICE', $docId);
				$docman->fileDownload($fileId);
			} else {
				throw new CHttpException(404,'Access right not match.');
			}
		} else {
			throw new CHttpException(404,'Record not found.');
		}
	}
*/
	public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
		$sql = "select city from swo_service where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan($doctype,$docId,'ServiceForm');
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
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
/*
	 public function loadModel($id)
	{
		$model = new UserForm;
		if (!$model->retrieveData($id))
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
*/

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='service-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A02');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A02');
	}
}
