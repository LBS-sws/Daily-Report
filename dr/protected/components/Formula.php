<?php
class Formula {
	protected $definition;
	
	protected $cache;
	
	public function __construct() {
		$this->definition = require(Yii::app()->basePath.'/config/formula.php');
	}
	
	public function calculate($string) {
		IF(C79>3,5,IF(C79>1,4,IF((C79>0),3,IF(C79>-1,2,IF((C79>-2),1,0)))))
	}

	protected function parseForIf($string) {
		
	}
}
?>