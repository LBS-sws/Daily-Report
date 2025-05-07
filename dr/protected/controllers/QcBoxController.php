<?php

class QcBoxController extends Controller
{
	public $function_id='A06';

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
				'actions'=>array('new','edit','delete','save','downs','remove',"templates",'fileupload','fileremove','filedownload'),
				'expression'=>array('QcBoxController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('view','filedownload'),
				'expression'=>array('QcBoxController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSave()
	{
		if (isset($_POST['QcBoxForm'])) {
			$model = new QcBoxForm($_POST['QcBoxForm']['scenario']);
			$model->attributes = $_POST['QcBoxForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('qcBox/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				switch ($model->service_type) {
					case 'IA': $formfile = 'boxia'; break;
					case 'IB' :$formfile = 'boxia'; break;
					default :
                        $this->redirect(Yii::app()->createUrl('qc/view',array("index"=>$model->id)));
                        return false;
				}
				$this->render($formfile,array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new QcBoxForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
            if($model->lcd<QcBoxForm::$effectDate){
                $this->redirect(Yii::app()->createUrl('qc/view',array("index"=>$index)));
                return false;
            }
			switch ($model->service_type) {
				case 'IA': $formfile = 'boxia'; break;
				case 'IB' :$formfile = 'boxia'; break;
				default :
                    $this->redirect(Yii::app()->createUrl('qc/view',array("index"=>$index)));
                    return false;
			}
			$this->render($formfile,array('model'=>$model,));
		}
	}

    public function actionNew($type='')
	{
		$model = new QcBoxForm('new');
        $model->city = Yii::app()->user->city();
		switch ($type) {
			case 'IA': $formfile = 'boxia'; $model->new_form = true; break;
			case 'IB' : $formfile = 'boxia'; $model->new_form = true; break;
			default :
                $this->redirect(Yii::app()->createUrl('qc/new',array("type"=>$type)));
                return false;
		}
		$model->service_type = $type;
		$model->entry_dt = date('Y/m/d');
		$model->qc_dt = date('Y/m/d');
		$model->initData();
		$this->render($formfile,array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new QcBoxForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
		    if($model->lcd<QcBoxForm::$effectDate){
                $this->redirect(Yii::app()->createUrl('qc/edit',array("index"=>$index)));
                return false;
            }
			switch ($model->service_type) {
				case 'IA': $formfile = 'boxia'; break;
				case 'IB' : $formfile = 'boxia'; break;
				default :
                    $this->redirect(Yii::app()->createUrl('qc/edit',array("index"=>$index)));
                    return false;
			}

			$this->render($formfile,array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new QcBoxForm('delete');
		if (isset($_POST['QcBoxForm'])) {
			$model->attributes = $_POST['QcBoxForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
		}
		$this->redirect(Yii::app()->createUrl('qc/index'));
	}

	public function actionDowns($index)
	{
        $model = new QcBoxForm('view');
        if($model->retrieveData($index)){
            $model->getCompanyAddr();
            $model->printPDF();
        }else{
            throw new CHttpException(404,'The requested page does not exist.');
        }
	}

    public function actionRemove()
    {
        $model = new QcBoxForm('remove');
        if (isset($_POST['QcBoxForm'])) {
            $model->attributes = $_POST['QcBoxForm'];

        }
        $model->remove($model);
        echo "<script>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
//        $this->redirect(Yii::app()->createUrl('qc/index'));
   }
	
	public function actionFileupload($doctype) {
		$model = new QcBoxForm();
		if (isset($_POST['QcBoxForm'])) {
			$model->attributes = $_POST['QcBoxForm'];
			
			$id = ($_POST['QcBoxForm']['scenario']=='new') ? 0 : $model->id;
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
		$model = new QcBoxForm();
		if (isset($_POST['QcBoxForm'])) {
			$model->attributes = $_POST['QcBoxForm'];
			$docman = new DocMan($doctype,$model->id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			$docman->fileRemove($model->removeFileId[strtolower($doctype)]);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}

	public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
		$sql = "select city from swo_qc where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan($doctype,$docId,'QcBoxForm');
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
