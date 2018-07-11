<?php

class SupplierController extends Controller 
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

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
/*		
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','new','edit','delete','save'),
				'users'=>array('@'),
			),
*/
			array('allow', 
				'actions'=>array('new','edit','delete','save','edits'),
				'expression'=>array('SupplierController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('SupplierController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new SupplierList;
		if (isset($_POST['SupplierList'])) {
			$model->attributes = $_POST['SupplierList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a10']) && !empty($session['criteria_a10'])) {
				$criteria = $session['criteria_a10'];
				$model->setCriteria($criteria);
			}
		}

		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
//        print_r("<pre>");
//        print_r($model);
		$this->render('index',array('model'=>$model));
	}

//	public function actionIndexs($pageNum=0)
//    {
//        $models = new SupplierList;
//        if (isset($_POST['SupplierList'])) {
//            $models->attributes = $_POST['SupplierList'];
//        } else {
//            $session = Yii::app()->session;
//            if (isset($session['criteria_a10']) && !empty($session['criteria_a10'])) {
//                $criteria = $session['criteria_a10'];
//                $models->setCriteria($criteria);
//            }
//        }
//        $models->determinePageNum($pageNum);
//        $models->retrieveDataByPages($models->pageNum);
//        $this->render('index',array('models'=>$models));
//    }


	public function actionSave()
	{
		if (isset($_POST['SupplierForm'])) {
			$model = new SupplierForm($_POST['SupplierForm']['scenario']);
			$model->attributes = $_POST['SupplierForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('supplier/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
//			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionView($index)
	{
		$model = new SupplierForm('view');

		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {

			$this->render('form',array('model'=>$model,));
		}

	}
	
	public function actionNew()
	{
		$model = new SupplierForm('new');
		$this->render('form',array('model'=>$model,));
	}


	public function actionEdit($index,$pageNum=0)
	{
		$model = new SupplierForm('edit');
        $session = Yii::app()->session;

        if (isset($session['criteria_a14']) && !empty($session['criteria_a14'])) {
            $criteria = $session['criteria_a14'];
            $model->setCriteria($criteria);
        }
        $model->determinePageNum($pageNum);
//$model->retrieveDataByPage($model->pageNum);
		if (!$model->retrieveData($index,$model->pageNum)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
            print_r($session['criteria_xa14']);
			$this->render('form',array('model'=>$model,));
		}
	}



    public function actionEdits($index)
    {
        $model = new SupplierForm('edit');

        if (!$model->retrieveDatas($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
           // print_r($model);
            $this->render('edits',array('model'=>$model,));
        }
    }
	
	public function actionDelete()
	{
		$model = new SupplierForm('delete');
		if (isset($_POST['SupplierForm'])) {
			$model->attributes = $_POST['SupplierForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
		}
//		$this->actionIndex();
		$this->redirect(Yii::app()->createUrl('supplier/index'));
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='supplier-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A10');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A10');
	}
}
