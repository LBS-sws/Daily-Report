<?php

class ServiceCountController extends Controller
{
	public $function_id='A12';
	
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
				'actions'=>array('index','edit','ajaxDetail'),
				'expression'=>array('ServiceCountController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new ServiceCountForm();
            $html =$model->ajaxDetailForHtml();
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('RankingMonth/index'));
        }
    }

	public function actionIndex()
	{
		$model = new ServiceCountForm('index');
		$model->search_year = date("Y");
		$model->city_allow = Yii::app()->user->city();
		$this->render('index',array('model'=>$model));
	}

	public function actionEdit()
	{
		$model = new ServiceCountForm('index');
        $model->attributes = $_POST['ServiceCountForm'];
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('form',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A12');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A12');
	}
}
