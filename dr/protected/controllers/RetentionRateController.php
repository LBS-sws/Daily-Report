<?php

class RetentionRateController extends Controller
{
	public $function_id='G30';
	
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
				'expression'=>array('RetentionRateController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel','ajaxDetail'),
				'expression'=>array('RetentionRateController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new RetentionRateTable();
            $html =$model->ajaxDetailForHtml();
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('retentionRate/index'));
        }
    }

    public function actionDownExcel()
    {
        $model = new RetentionRateForm('view');
        if (isset($_POST['RetentionRateForm'])) {
            $model->attributes = $_POST['RetentionRateForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new RetentionRateForm('index');
        $session = Yii::app()->session;
        if (isset($session['retentionRate_c01']) && !empty($session['retentionRate_c01'])) {
            $criteria = $session['retentionRate_c01'];
            $model->setCriteria($criteria);
        }else{
            $search_year = date("Y");
            $search_month = date("n");
            $model->search_year = $search_month>=2?$search_year:($search_year-1);
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
	    set_time_limit(0);
        $model = new RetentionRateForm('view');
        if (isset($_POST['RetentionRateForm'])) {
            $model->attributes = $_POST['RetentionRateForm'];
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
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G30');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G30');
	}
}
