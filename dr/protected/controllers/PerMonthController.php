<?php

class PerMonthController extends Controller
{
	public $function_id='G17';
	
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
				'expression'=>array('PerMonthController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','add','stop','recover','net','count','downExcel'),
				'expression'=>array('PerMonthController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel($type='')
    {
        $list = array(
            "add"=>"PerMonthAdd",
            "net"=>"PerMonthNet",
            "stop"=>"PerMonthStop",
            "recover"=>"PerMonthRecover",
            "count"=>"PerMonthCount",
        );
        if(key_exists($type,$list)){
            $className = $list[$type];
            $model = new $className('view');
            $session = Yii::app()->session;
            if (isset($session['perMonth_c01']) && !empty($session['perMonth_c01'])) {
                $criteria = $session['perMonth_c01'];
                $model->setCriteria($criteria);
            }else{
                $model->search_date = date("Y/m/d");
            }
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model = new PerMonth('index');
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new PerMonth('index');
        $session = Yii::app()->session;
        if (isset($session['perMonth_c01']) && !empty($session['perMonth_c01'])) {
            $criteria = $session['perMonth_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionAdd()
	{//新增金额
        set_time_limit(0);
        $model = new PerMonthAdd('view');
        if (isset($_POST['PerMonth'])) {
            $model->attributes = $_POST['PerMonth'];
        }else{
            $session = Yii::app()->session;
            if (isset($session['perMonth_c01']) && !empty($session['perMonth_c01'])) {
                $criteria = $session['perMonth_c01'];
                $model->setCriteria($criteria);
            }else{
                $model->search_date = date("Y/m/d");
            }
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('add',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}

	public function actionStop()
	{//停止金额
        set_time_limit(0);
        $model = new PerMonthStop('view');
        $session = Yii::app()->session;
        if (isset($session['perMonth_c01']) && !empty($session['perMonth_c01'])) {
            $criteria = $session['perMonth_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('stop',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}

	public function actionRecover()
	{//净恢复金额
        set_time_limit(0);
        $model = new PerMonthRecover('view');
        $session = Yii::app()->session;
        if (isset($session['perMonth_c01']) && !empty($session['perMonth_c01'])) {
            $criteria = $session['perMonth_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('recover',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}

	public function actionNet()
	{//净增金额
        set_time_limit(0);
        $model = new PerMonthNet('view');
        $session = Yii::app()->session;
        if (isset($session['perMonth_c01']) && !empty($session['perMonth_c01'])) {
            $criteria = $session['perMonth_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('net',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}

	public function actionCount()
	{//时间流失
        set_time_limit(0);
        $model = new PerMonthCount('view');
        $session = Yii::app()->session;
        if (isset($session['perMonth_c01']) && !empty($session['perMonth_c01'])) {
            $criteria = $session['perMonth_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('count',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G17');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G17');
	}
}
