<?php

class MonthController extends Controller
{
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
//			print_r('<pre>');
//            print_r($model) ;
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a09']) && !empty($session['criteria_a09'])) {
				$criteria = $session['criteria_a09'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
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
				$this->redirect(Yii::app()->createUrl('month/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('summarize',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new MonthForm('view');
		if (!$model->retrieveData($index)) {
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


	public function actionEdit($index)
	{
		$model = new MonthForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {

			$this->render('summarize',array('model'=>$model,));
		}
	}

    public function actionSummarize($index){
        $model = new MonthForm('edit');
        $model->retrieveData($index);
//        print_r('<pre/>');
//        print_r($model);
        $this->render('summarize',array('model'=>$model,));
    }

    public function actionSend(){
        $model = new MonthForm;
        $model->attributes = $_POST['MonthForm'];
        $model->sendDate($model);
//        print_r('<pre/>');
//        print_r($model);
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