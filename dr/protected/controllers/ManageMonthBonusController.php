<?php

class ManageMonthBonusController extends Controller
{
	public $function_id='MM01';
	
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
				'expression'=>array('ManageMonthBonusController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel','ajaxDetail'),
				'expression'=>array('ManageMonthBonusController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            //$model = new ManageMonthBonusTable();
            //$html =$model->ajaxDetailForHtml();
            $html ='';
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('manageMonthBonus/index'));
        }
    }

	public function actionIndex()
	{
		$model = new ManageMonthBonusForm('index');
        $session = Yii::app()->session;
        if (isset($session['manageMonthBonus_c01']) && !empty($session['manageMonthBonus_c01'])) {
            $criteria = $session['manageMonthBonus_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_year = date("Y");
            $model->search_month = date("n");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        $model = new ManageMonthBonusForm('view');
        if (isset($_POST['ManageMonthBonusForm'])) {
            $model->attributes = $_POST['ManageMonthBonusForm'];
            if ($model->validate()) {
                $model->retrieveData();
                $this->render('form',array('model'=>$model));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('index',array('model'=>$model));
            }
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
	}

	public function actionDownExcel()
	{
        $model = new ManageMonthBonusForm('view');
        if (isset($_POST['ManageMonthBonusForm'])) {
            $model->attributes = $_POST['ManageMonthBonusForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('MM01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('MM01');
	}
}
