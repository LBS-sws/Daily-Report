<?php

class CrossApplyController extends Controller
{
	public $function_id='CD01';
	
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
				'actions'=>array('newSave','newFull','edit','delete','save'),
				'expression'=>array('CrossApplyController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('CrossApplyController','allowReadOnly'),
			),
			array('allow',
				'actions'=>array('ajaxCross'),
				'expression'=>array('CrossApplyController','allowAll'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new CrossApplyList();
		if (isset($_POST['CrossApplyList'])) {
			$model->attributes = $_POST['CrossApplyList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['crossApply_c01']) && !empty($session['crossApply_c01'])) {
				$criteria = $session['crossApply_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['CrossApplyForm'])) {
			$model = new CrossApplyForm($_POST['CrossApplyForm']['scenario']);
			$model->attributes = $_POST['CrossApplyForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('crossApply/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new CrossApplyForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNewSave()
	{
		$model = new CrossApplyForm('new');
        if (isset($_POST['CrossApply'])) {
            $model->attributes = $_POST['CrossApply'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('crossApply/edit',array('index'=>$model->id)));
            } else {
                $url = $model->table_type==0?'service/edit':'serviceka/edit';
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl($url,array('index'=>$model->service_id)));
            }
        }
	}

    //详情列表的異步請求
    public function actionAjaxCross(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new CrossApplyForm();
            $model->attributes = $_POST['CrossApply'];
            $list=$model->validateFull();
            $html = $model->getCrossFullHtml($list);
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('RankingMonth/index'));
        }
    }

	public function actionNewFull()
	{
		$model = new CrossApplyForm('new');
        if (isset($_POST['CrossApply'])) {
            $model->attributes = $_POST['CrossApply'];
            $list=$model->validateFull();
            $rtn = $model->saveCrossFull($list);
            $url = $model->table_type==0?'service/index':'serviceKA/index';
            Dialog::message(Yii::t('dialog','Information'),"批量交叉派单成功。成功数量：".$rtn["success"]);
            $this->redirect(Yii::app()->createUrl($url));
        }
	}
	
	public function actionEdit($index)
	{
		$model = new CrossApplyForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new CrossApplyForm('delete');
		if (isset($_POST['CrossApplyForm'])) {
			$model->attributes = $_POST['CrossApplyForm'];
			if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('crossApply/index'));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('CD01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('CD01');
	}

	public static function allowAll() {
		return true;
	}
}
