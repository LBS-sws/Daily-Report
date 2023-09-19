<?php

class CustomerenqController extends Controller 
{
	public $function_id='G01';

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
			array('allow', 
				'actions'=>array('new','edit','delete','save'),
				'expression'=>array('CustomerController','allowReadWrite'),
			),
*/
			array('allow', 
				'actions'=>array('index','ajaxDetail'),
				'expression'=>array('CustomerenqController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $company_id = key_exists("id",$_GET)?$_GET["id"]:0;
            $model = new CustomerEnqList();
            $html =$model->getServiceHtmlTr($company_id);
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('customerenq/index'));
        }
    }

	public function actionIndex($pageNum=1,$show=1) 
	{
		$model = new CustomerEnqList;
		if (isset($_POST['CustomerEnqList'])) {
			$model->attributes = $_POST['CustomerEnqList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session[$model->criteriaName()]) && !empty($session[$model->criteriaName()])) {
				$criteria = $session[$model->criteriaName()];
				$model->setCriteria($criteria);
			}
		}
		$model->show = $show;
		if ($show!=0) {
			$model->determinePageNum($pageNum);
			$model->retrieveDataByPage($model->pageNum);
		}
		$this->render('index',array('model'=>$model));
	}

	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('G01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('G01');
	}
}
