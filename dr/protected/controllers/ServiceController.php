<?php

class ServiceController extends Controller 
{
	public $function_id = 'A02';
	
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
				'expression'=>array('ServiceController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('copy'),
				'expression'=>array('ServiceController','allowCopy'),
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
			//新增限制修改日期
//            $xianzhi = false;// || ($_POST['ServiceForm']['scenario']!='new' && date('Y-m',strtotime($_POST['ServiceForm']['status_dt']))<=date('Y-m',strtotime("-2 month")))
//            if ($_POST['ServiceForm']['status_desc']=='新增' && date('Y-m',strtotime($_POST['ServiceForm']['first_dt']))<=date('Y-m',strtotime("-2 month"))){
//                $xianzhi = true;
//                if ($_POST['ServiceForm']['scenario']=='new'){
//                    $message ="首次日期不能早于".date('Y-m-01',strtotime("-1 month"));
//                }else{
//                    $message ="该月资料不允许修改，以免影响过往积分数据导致激励点变动";
//                }
//            }
//			if ($xianzhi){
//
//                Dialog::message(Yii::t('dialog','Validation Message'), $message);
//                $this->render('form',array('model'=>$model,));
//            }else{}
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
		
	public function actionNew($city='',$index=0)
	{
        $city = empty($city)?Yii::app()->user->city():$city;
		$model = new ServiceForm('new');
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
		if($model->city!=$city){
            $model->company_id=null;
            $model->company_name=null;
        }
        $model->city=$city;
        $model->commission=null;
        $model->other_commission=null;
		$model->status = 'N';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionCopy($index=0)
	{
		$model = new ServiceForm('edit');
        $bool = $model->dataCopy($index);
        if($bool){
            Dialog::message(Yii::t('dialog','Information'), Yii::t('service','Record Copy'));
            $this->redirect(Yii::app()->createUrl('serviceKA/edit',array("index"=>$model->id)));
        }else{
            $this->redirect(Yii::app()->createUrl('service/edit',array("index"=>$index)));
        }
	}

	public function actionRenew($city='',$index=0)
	{
        $city = empty($city)?Yii::app()->user->city():$city;
		$model = new ServiceForm('renew');
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
        if($model->city!=$city){
            $model->company_id=null;
            $model->company_name=null;
        }
        $model->city=$city;
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
	
	public function actionAmend($city='',$index=0)
	{
        $city = empty($city)?Yii::app()->user->city():$city;
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
        if($model->city!=$city){
            $model->company_id=null;
            $model->company_name=null;
        }
        $model->city=$city;
		$model->status = 'A';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionResume($city='',$index=0)
	{
        $city = empty($city)?Yii::app()->user->city():$city;
		$model = new ServiceForm('resume');
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
        if($model->city!=$city){
            $model->company_id=null;
            $model->company_name=null;
        }
        $model->city=$city;
		$model->status = 'R';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionSuspend($city='',$index=0)
	{
        $city = empty($city)?Yii::app()->user->city():$city;
		$model = new ServiceForm('suspend');
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
        if($model->city!=$city){
            $model->company_id=null;
            $model->company_name=null;
        }
        $model->city=$city;
		$model->status = 'S';
		$model->status_desc = $model->getStatusDesc();
		$model->status_dt = date('Y/m/d');
		$model->backlink = Yii::app()->request->urlReferrer;
		$this->render('form',array('model'=>$model,));
	}

	public function actionTerminate($city='',$index=0)
	{
        $city = empty($city)?Yii::app()->user->city():$city;
		$model = new ServiceForm('terminate');
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
        if($model->city!=$city){
            $model->company_id=null;
            $model->company_name=null;
        }
        $model->city=$city;
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
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('service/index'));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('service/edit',array("index"=>$model->id)));
            }
		}else{
            $this->redirect(Yii::app()->createUrl('service/index'));
        }
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

    public function actionGetcusttypelist($group) {
        $rtn = '';
        $rows = ServiceForm::getCustTypeList($group);
        foreach ($rows as $key=>$value) {
            $rtn .= "<option value=$key>$value</option>";
        }
        echo $rtn;
    }
	//发送邮件
    public function actionEndsendemail(){
	    $service = new ServiceForm();
	    $result = $service->sendemail($_POST['reason'],date('Y',strtotime($_POST['ServiceForm']['status_dt'])),date('m',strtotime($_POST['ServiceForm']['status_dt'])),$_POST['ServiceForm']['company_name'],$_POST['ServiceForm']['id']);
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

    public static function allowCopy() {
        return Yii::app()->user->validFunction('D07');
    }
}
