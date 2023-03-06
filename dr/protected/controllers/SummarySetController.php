<?php

class SummarySetController extends Controller
{
	public $function_id='G04';
	
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
				'actions'=>array('ajaxSave'),
				'expression'=>array('SummarySetController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('SummarySetController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new SummarySetList();
		if (isset($_POST['SummarySetList'])) {
			$model->attributes = $_POST['SummarySetList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['summarySet_c01']) && !empty($session['summarySet_c01'])) {
				$criteria = $session['summarySet_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionAjaxSave()
	{
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new SummarySetForm();
            $model->attributes = $_POST;
            if ($model->validate()) {
                $model->saveData();
                echo CJSON::encode(array("status"=>1));
            } else {
                $message = CHtml::errorSummary($model);
                echo CJSON::encode(array("status"=>0,"message"=>$message));
            }
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G04');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G04');
	}
}
