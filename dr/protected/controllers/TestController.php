<?php
/*
	示範 DocMan::getFileListByDate 提取上載檔案資料 及 docman/download 以供下載檔案
*/
class TestController extends Controller
{
	// By pass System Blocking checking
	public function beforeAction($action) {		// 不是示範部分 , 使用Docman函數時不用根據這設定
		return true;
	}
	
	public function filters()		// 不是示範部分 , 使用Docman函數時不用根據這設定
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()		// 不是示範部分 , 使用Docman函數時不用根據這設定
	{
		return array(
			array('allow', 
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
/*
	以下是示範部分 , 
	列出上載日期由2018-01-01至2019-06-30, 所有SERVICE文件類別的上載檔案
	點擊連結 , 可經系統下載檔案
*/	
	public function actionIndex($type) {
		$result = DocMan::getFileListByDate(array('SERVICE'), '2018-01-01', '2019-06-30');
		var_dump($result);
		foreach ($result as $row) {
			$url = $this->createAbsoluteUrl('docman/download',array('index'=>$row['id'],'token'=>$row['token']));
			echo TbHtml::link($row['fileName'], $url).'<br>';
		}
	}
}
?>