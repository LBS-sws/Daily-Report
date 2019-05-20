<?php

class MonthController extends Controller
{
	public $function_id='H01';

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

	/**·
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('edit','save','send'),
				'expression'=>array('MonthController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','xiazai','summarize'),
				'expression'=>array('MonthController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new MonthList;
		if (isset($_POST['MonthList'])) {
			$model->attributes = $_POST['MonthList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a09']) && !empty($session['criteria_a09'])) {
				$criteria = $session['criteria_a09'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
        $moth=date("n");
//        print_r('<pre>');
//		print_r($model->attributes );
		if(!empty($model['attr'])&&$model->attributes['searchField']!='month_no'&&$model->attributes['searchValue']!=$moth){
            for ($i=1;$i<count($model['attr']);$i++){
                $arr[]=$model['attr'][$i];
            }
            $model['attr']=$arr;

        }

		$this->render('index',array('model'=>$model));
	}


	public function actionSave($city)
	{
		if (isset($_POST['MonthForm'])) {
			$model = new MonthForm($_POST['MonthForm']['scenario']);
			$model->attributes = $_POST['MonthForm'];
//			print_r('<pre>');
//            print_r($model);
//            exit();
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('month/edit',array('index'=>$model->id,'city'=>$_GET['city'])));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('summarize',array('model'=>$model,));
			}
		}
	}

	public function actionView($index,$city)
	{
		$model = new MonthForm('view');
		if (!$model->retrieveData($index,$city)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}

	}

	public function actionXiaZai(){
        $model = new MonthForm;
        $model->attributes = $_POST['MonthForm'];
        $model->retrieveDatas($model);
    }


	public function actionEdit($index,$city)
	{
		$model = new MonthForm('edit');
		if (!$model->retrieveData($index,$city)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('summarize',array('model'=>$model,));
		}

	}

    public function actionSummarize($index,$city){
        $model = new MonthForm('edit');
        $model->retrieveData($index,$city);
//        print_r('<pre/>');
//        print_r($model);
        $this->render('summarize',array('model'=>$model,));
    }

    public function actionSend($city){
        $model = new MonthForm;
        $model->attributes = $_POST['MonthForm'];
        $total=$_POST['MonthForm']['total'];
//        print_r('<pre/>');
//        print_r($total);exit();
        $model->sendDate($model,$total,$city);
        Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Ok'));
        $this->render('summarize',array('model'=>$model,));
    }

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='monthly-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('H01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('H01');
	}
}
