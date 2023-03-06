<?php

class ComparisonSetController extends Controller
{
	public $function_id='G06';
	
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
				'expression'=>array('ComparisonSetController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('ComparisonSetController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new ComparisonSetList();
		if (isset($_POST['ComparisonSetList'])) {
			$model->attributes = $_POST['ComparisonSetList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['comparisonSet_c01']) && !empty($session['comparisonSet_c01'])) {
				$criteria = $session['comparisonSet_c01'];
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
            $model = new ComparisonSetForm();
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
		return Yii::app()->user->validRWFunction('G06');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G06');
	}
}
