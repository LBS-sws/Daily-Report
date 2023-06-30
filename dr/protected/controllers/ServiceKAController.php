<?php

class ServiceKAController extends Controller
{
	public $function_id = 'A13';
	
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
				'actions'=>array('new','edit','amend','suspend','resume','renew','save','delete','terminate','fileupload','fileremove','filedownload','getcusttypelist','endsendemail'),
				'expression'=>array('ServiceKAController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','filedownload'),
				'expression'=>array('ServiceKAController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new ServiceKAList;
		if (isset($_POST['ServiceKAList'])) {
			$model->attributes = $_POST['ServiceKAList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['serviceKA_01']) && !empty($session['serviceKA_01'])) {
				$criteria = $session['serviceKA_01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSave()
	{
        if (isset($_POST['ServiceKAForm'])) {
            $model = new ServiceKAForm($_POST['ServiceKAForm']['scenario']);
            $model->attributes = $_POST['ServiceKAForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('serviceKA/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
	}

	public function actionView($index)
	{
		$model = new ServiceKAForm('view');
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
		$model = new ServiceKAForm('new');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->reason = '';
            $model->remarks = '';
            $model->surplus = 0;
            $model->all_number = 0;
			$model->org_equip_qty = 0;
			$model->rtn_equip_qty = 0;
            $model->other_commission = null;
            $model->commission = null;
			$model->id = 0;
			$model->files = '';
			$model->docMasterId['service'] = 0;
			$model->removeFileId['service'] = 0;
			$model->no_of_attm['service'] = 0;
		}
        $model->commission=null;
        $model->other_commission=null;
		$model->status = 'N';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionRenew($index=0)
	{
		$model = new ServiceKAForm('renew');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->reason = '';
			$model->remarks = '';
			$model->org_equip_qty = 0;
            $model->surplus = 0;
            $model->all_number = 0;
			$model->rtn_equip_qty = 0;
            $model->other_commission = null;
            $model->commission = null;
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
		$model = new ServiceKAForm('edit');
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
		$model = new ServiceKAForm('amend');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = $model->product_id;
			$model->b4_service = $model->service;
			$model->b4_paid_type = $model->paid_type;
			$model->b4_amt_paid = $model->amt_paid;
			$model->ctrt_period = 0;
			$model->ctrt_end_dt = null;
			$model->cont_info = '';
			$model->first_tech = '';
            $model->surplus = 0;
            $model->all_number = 0;
			$model->reason = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
			$model->org_equip_qty = 0;
			$model->rtn_equip_qty = 0;
			$model->other_commission = null;
			$model->commission = null;
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
		$model = new ServiceKAForm('resume');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->amt_install = 0;
            $model->surplus = 0;
            $model->all_number = 0;
			$model->cont_info = '';
			$model->first_dt = null;
			$model->first_tech = '';
			$model->reason = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
			$model->org_equip_qty = 0;
			$model->rtn_equip_qty = 0;
            $model->other_commission = null;
            $model->commission = null;
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
		$model = new ServiceKAForm('suspend');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
			$model->amt_install = 0;
			$model->ctrt_period = 0;
            $model->surplus = 0;
            $model->all_number = 0;
			$model->ctrt_end_dt = null;
			$model->cont_info = '';
			$model->first_dt = null;
			$model->first_tech = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
            $model->other_commission = null;
            $model->commission = null;
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
		$model = new ServiceKAForm('terminate');
		if ($index!==0 && $model->retrieveData($index)) {
			$model->b4_product_id = 0;
			$model->b4_service = '';
			$model->b4_paid_type = '';
			$model->b4_amt_paid = 0;
            $model->surplus = 0;
            $model->all_number = 0;
			$model->amt_install = 0;
			$model->ctrt_period = 0;
			$model->ctrt_end_dt = null;
			$model->cont_info = '';
			$model->first_dt = null;
			$model->first_tech = '';
			$model->remarks = '';
			$model->equip_install_dt = null;
            $model->other_commission = null;
            $model->commission = null;
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
		$model = new ServiceKAForm('delete');
		if (isset($_POST['ServiceKAForm'])) {
			$model->attributes = $_POST['ServiceKAForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('serviceKA/index'));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('serviceKA/edit',array("index"=>$model->id)));
            }
		}else{
            $this->redirect(Yii::app()->createUrl('serviceKA/index'));
        }
	}

	public function actionFileupload($doctype) {
		$model = new ServiceKAForm();
		if (isset($_POST['ServiceKAForm'])) {
			$model->attributes = $_POST['ServiceKAForm'];
			
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

	public function actionFileRemove($doctype) {
		$model = new ServiceKAForm();
		if (isset($_POST['ServiceKAForm'])) {
			$model->attributes = $_POST['ServiceKAForm'];
			$docman = new DocMan($doctype,$model->id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			$docman->fileRemove($model->removeFileId[strtolower($doctype)]);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}

	public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
		$sql = "select city from swo_service_ka where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan($doctype,$docId,'ServiceKAForm');
				$docman->masterId = $mastId;
				$docman->fileDownload($fileId);
			} else {
				throw new CHttpException(404,'Access right not match.');
			}
		} else {
				throw new CHttpException(404,'Record not found.');
		}
	}

    public function actionGetcusttypelist($group) {
        $rtn = '';
        $rows = ServiceKAForm::getCustTypeList($group);
        foreach ($rows as $key=>$value) {
            $rtn .= "<option value=$key>$value</option>";
        }
        echo $rtn;
    }

	//发送邮件
    public function actionEndsendemail(){
	    $service = new ServiceKAForm();
	    $result = $service->sendemail($_POST['reason'],date('Y',strtotime($_POST['ServiceKAForm']['status_dt'])),date('m',strtotime($_POST['ServiceKAForm']['status_dt'])),$_POST['ServiceKAForm']['company_name'],$_POST['ServiceKAForm']['id']);
	    echo $result;
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='serviceKA-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A13');
	}

	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A13');
	}
}
