<?php

class CrossAuditController extends Controller
{
	public $function_id='CD02';
	
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
				'actions'=>array('audit','edit','reject','auditFull','rejectFull'),
				'expression'=>array('CrossAuditController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('CrossAuditController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new CrossAuditList();
		if (isset($_POST['CrossAuditList'])) {
			$model->attributes = $_POST['CrossAuditList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['crossAudit_c01']) && !empty($session['crossAudit_c01'])) {
				$criteria = $session['crossAudit_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionReject()
	{
		if (isset($_POST['CrossAuditForm'])) {
			$model = new CrossAuditForm($_POST['CrossAuditForm']['scenario']);
			$model->attributes = $_POST['CrossAuditForm'];
            $model->setScenario("reject");
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Denied'));
				$this->redirect(Yii::app()->createUrl('crossAudit/index'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAuditFull(){
        $model = new CrossAuditForm("audit");
        $model->auditFull();
        $this->redirect(Yii::app()->createUrl('crossAudit/index'));
    }

	public function actionRejectFull(){
        $rejectNote = key_exists("reject_note",$_POST)?$_POST["reject_note"]:"";
        $model = new CrossAuditForm("reject");
        if(empty($rejectNote)){
            Dialog::message(Yii::t('dialog','Validation Message'), "拒绝原因不能为空");
        }else{
            $model->rejectFull();
            Dialog::message(Yii::t('dialog','Information'), "已批量拒绝");
        }
        $this->redirect(Yii::app()->createUrl('crossAudit/index'));
    }

	public function actionAudit()
	{
		if (isset($_POST['CrossAuditForm'])) {
			$model = new CrossAuditForm($_POST['CrossAuditForm']['scenario']);
			$model->attributes = $_POST['CrossAuditForm'];
            $model->setScenario("audit");
			if ($model->validate()) {
				$bool = $model->saveData();
				//Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Approved'));
                if($bool){
                    $this->redirect(Yii::app()->createUrl('crossAudit/index'));
                }else{
                    $this->redirect(Yii::app()->createUrl('crossAudit/edit',array("index"=>$model->id)));
                }
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new CrossAuditForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index)
	{
		$model = new CrossAuditForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('CD02');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('CD02');
	}
}
