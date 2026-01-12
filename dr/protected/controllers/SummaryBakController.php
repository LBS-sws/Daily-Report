<?php
//強制刷新文件
class SummaryBakController extends Controller
{
	public $function_id='G41';

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
				'expression'=>array('SummaryBakController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel','test','uTest','ajaxDetail','ajaxOffice'),
				'expression'=>array('SummaryBakController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    //办事处列表的異步請求
    public function actionAjaxOffice(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
			set_time_limit(0);
            $model = new SummaryBakForm('index');
            $data =$model->ajaxOfficeForData();
            echo CJSON::encode(array('status'=>1,'list'=>$data));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('summaryBak/index'));
        }
    }

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new SummaryTable();
            $html =$model->ajaxDetailForHtml();
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('summary/index'));
        }
    }

	public function actionTest($startDate="",$endDate=""){
        $startDate=empty($startDate)?date("Y/m/01"):$startDate;
        $endDate=empty($endDate)?date("Y/m/d"):$endDate;
        $arr = SummaryBakForm::getUActualMoney($startDate,$endDate);
        var_dump($arr);
        die();
    }

	public function actionUTest($year="",$month=""){
        $year=empty($year)?date("Y"):$year;
        $month=empty($month)?date("n"):$month;
        $list = Invoice::getActualAmount($year,$month);
        var_dump($list);
        die();
    }

	public function actionIndex()
	{
		$model = new SummaryBakForm('index');
        $session = Yii::app()->session;
        if (isset($session['summary_c01']) && !empty($session['summary_c01'])) {
            $criteria = $session['summary_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_year = date("Y");
            $model->search_month = date("n");
            $model->search_month_end = $model->search_month;
            $model->search_start_date = date("Y/m/01");
            $model->search_end_date = date("Y/m/d");
            $i = ceil($model->search_month/3);//向上取整
            $model->search_quarter = 3*$i-2;
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        set_time_limit(0);
        $model = new SummaryBakForm('view');
        if (isset($_POST['SummaryBakForm'])) {
            $model->attributes = $_POST['SummaryBakForm'];
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
        $model = new SummaryBakForm('view');
        if (isset($_POST['SummaryBakForm'])) {
            $model->attributes = $_POST['SummaryBakForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
	}
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G41');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G41');
	}
}
