<?php

class SystemLogController extends Controller
{
	public $function_id='D06';
	
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
				'actions'=>array('fileupload','fileremove','filedownload'),
				'expression'=>array('SystemLogController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','filedownload','fileList'),
				'expression'=>array('SystemLogController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new SystemLogList();
		if (isset($_POST['SystemLogList'])) {
			$model->attributes = $_POST['SystemLogList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['systemLog_c01']) && !empty($session['systemLog_c01'])) {
				$criteria = $session['systemLog_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

    public function actionFileRemove($doctype) {
        $model = new SystemLogList();
        if (isset($_POST['SystemLogList'])) {
            $model->attributes = $_POST['SystemLogList'];
            $docman = new DocMan($doctype,$model->id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from swo_system_log where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'SystemLogList');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }

    public function actionFileupload($doctype) {
        $model = new SystemLogList();
        if (isset($_POST['SystemLogList'])) {
            $model->attributes = $_POST['SystemLogList'];
            if(!empty($model->id)){
                $id = empty($model->id)? 0 : $model->id;
                $docman = new DocMan($doctype,$id,get_class($model));
                $docman->masterId = $model->docMasterId[strtolower($doctype)];
                if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
                $docman->fileUpload();
                echo $docman->genTableFileList(false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileList($doctype) {
        $model = new SystemLogList();
        if (isset($_POST['SystemLogList'])) {
            $model->attributes = $_POST['SystemLogList'];
            $id = empty($model->id)?0:$model->id;
            $docman = new DocMan($doctype,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            echo $docman->genTableFileList(!SystemLogController::allowReadWrite());
        } else {
            echo "NIL";
        }
    }
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('D06');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('D06');
	}
}
