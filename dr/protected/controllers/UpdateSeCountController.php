<?php

class UpdateSeCountController extends Controller
{
	public $function_id='G34';
	
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
				'expression'=>array('UpdateSeCountController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel','ajaxDetail'),
				'expression'=>array('UpdateSeCountController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new UpdateSeTable();
            $html =$model->ajaxDetailForHtml();
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('comparison/index'));
        }
    }

    public function actionDownExcel($searchType=0)
    {
        $model = new UpdateSeCountForm('view');
        if (isset($_POST['UpdateSeCountForm'])) {
            $model->attributes = $_POST['UpdateSeCountForm'];
            $model->searchType = $searchType;
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new UpdateSeCountForm('index');
        $session = Yii::app()->session;
        if (isset($session['updateSeCount_c01']) && !empty($session['updateSeCount_c01'])) {
            $criteria = $session['updateSeCount_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->start_date = date("Y/m/01");
            $model->end_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView($searchType=0)
	{
        $model = new UpdateSeCountForm('view');
        if (isset($_POST['UpdateSeCountForm'])) {
            $model->attributes = $_POST['UpdateSeCountForm'];
            if ($model->validate()) {
                set_time_limit(0);
                $model->retrieveData($searchType);
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
		return Yii::app()->user->validRWFunction('G34');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G34');
	}
}
