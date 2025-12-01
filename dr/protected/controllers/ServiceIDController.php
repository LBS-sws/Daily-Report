<?php

class ServiceIDController extends Controller
{
	public $function_id='A11';
	
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
				'actions'=>array('new','edit','delete','save','fileupload','fileremove'),
				'expression'=>array('ServiceIDController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','filedownload'),
				'expression'=>array('ServiceIDController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new ServiceIDList;
		if (isset($_POST['ServiceIDList'])) {
			$model->attributes = $_POST['ServiceIDList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['serviceID_c01']) && !empty($session['serviceID_c01'])) {
				$criteria = $session['serviceID_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['ServiceIDForm'])) {
			$model = new ServiceIDForm($_POST['ServiceIDForm']['scenario']);
			$model->attributes = $_POST['ServiceIDForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('serviceID/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new ServiceIDForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew($type="N",$id="",$city="")
	{
        $city = empty($city)?Yii::app()->user->city():$city;
		$model = new ServiceIDForm('new');
        if(!empty($id)){
            $model->service_new_id = $id;
            $model->retrieveData($id);
        }
        if(key_exists($type,$model->getStatusList())){
            $model->status = $type;
        }
        $model->resetAttrLabel();
        $model->ltNowDate=false;
        if($model->city!=$city){
            $model->company_id=null;
            $model->company_name=null;
        }
        $model->city=$city;
		$this->render('form',array('model'=>$model));
	}
	
	public function actionEdit($index)
	{
		$model = new ServiceIDForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
    public function actionFileupload($doctype) {
        $model = new ServiceIDForm();
        if (isset($_POST['ServiceIDForm'])) {
            $model->attributes = $_POST['ServiceIDForm'];

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
        $model = new ServiceIDForm();
        if (isset($_POST['ServiceIDForm'])) {
            $model->attributes = $_POST['ServiceIDForm'];
            $docman = new DocMan($doctype,$model->id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from swo_serviceid where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'ServiceIDForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
	
	public function actionDelete()
	{
		$model = new ServiceIDForm('delete');
		if (isset($_POST['ServiceIDForm'])) {
			$model->attributes = $_POST['ServiceIDForm'];
			if ($model->isOccupied($model->id)) {
				Dialog::message(Yii::t('dialog','Warning'), Yii::t('dialog','This record is already in use'));
				$this->redirect(Yii::app()->createUrl('serviceID/edit',array('index'=>$model->id)));
			} else {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
				$this->redirect(Yii::app()->createUrl('serviceID/index'));
			}
		}
//		$this->actionIndex();
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='code-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A11');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A11');
	}
}
