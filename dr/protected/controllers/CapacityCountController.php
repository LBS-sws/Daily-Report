<?php

class CapacityCountController extends Controller
{
	public $function_id='G21';
	
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
				'expression'=>array('CapacityCountController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','region','area','downExcel'),
				'expression'=>array('CapacityCountController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel($type='')
    {
        $list = array(
            "region"=>"CapacityRegion",
            "area"=>"CapacityArea",
        );
        if(key_exists($type,$list)){
            $className = $list[$type];
            $model = new $className('view');
            $session = Yii::app()->session;
            if (isset($session['capacity_c01']) && !empty($session['capacity_c01'])) {
                $criteria = $session['capacity_c01'];
                $model->setCriteria($criteria);
            }else{
                $model->search_date = date("Y/m/d");
            }
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model = new CapacityRegion('index');
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new CapacityRegion('index');
        $session = Yii::app()->session;
        if (isset($session['capacity_c01']) && !empty($session['capacity_c01'])) {
            $criteria = $session['capacity_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_date = date("Y/m/d");
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionRegion()
	{//区域产能
        set_time_limit(0);
        $model = new CapacityRegion('view');
        if (isset($_POST['CapacityRegion'])) {
            $model->attributes = $_POST['CapacityRegion'];
        }else{
            $session = Yii::app()->session;
            if (isset($session['capacity_c01']) && !empty($session['capacity_c01'])) {
                $criteria = $session['capacity_c01'];
                $model->setCriteria($criteria);
            }else{
                $model->search_date = date("Y/m/d");
            }
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('region',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}

	public function actionArea()
	{//地区产能
        set_time_limit(0);
        $model = new CapacityArea('view');
        if (isset($_POST['CapacityRegion'])) {
            $model->attributes = $_POST['CapacityRegion'];
        }else{
            $session = Yii::app()->session;
            if (isset($session['capacity_c01']) && !empty($session['capacity_c01'])) {
                $criteria = $session['capacity_c01'];
                $model->setCriteria($criteria);
            }else{
                $model->search_date = date("Y/m/d");
            }
        }
        if ($model->validate()) {
            $model->retrieveData();
            $this->render('area',array('model'=>$model));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G21');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G21');
	}
}
