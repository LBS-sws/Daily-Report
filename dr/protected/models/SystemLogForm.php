<?php

class SystemLogForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $model;
	public $log_code;
	public $log_date;
	public $log_user;
	public $log_type;
	public $log_type_name;
	public $option_str;
	public $option_text="";
	public $city;
	public $show_bool=1;
	public $leave_log=1;

	public function insertSystemLog(){
	    if(!empty($this->option_text)){
            $arr = array(
                "log_date"=>$this->log_date,
                "log_user"=>$this->log_user,
                "log_type"=>$this->log_type,
                "log_type_name"=>$this->log_type_name,
                "option_str"=>$this->option_str,
                "option_text"=>$this->option_text,
                "show_bool"=>$this->show_bool,
                "leave_log"=>$this->leave_log,
                "city"=>Yii::app()->user->city(),
                "lcu"=>Yii::app()->user->id,
            );
            Yii::app()->db->createCommand()->insert("swo_system_log",$arr);
            $this->id = Yii::app()->db->getLastInsertID();
            $this->lenStr();
            Yii::app()->db->createCommand()->update('swo_system_log', array(
                'log_code'=>$this->log_code
            ), 'id=:id', array(':id'=>$this->id));
        }
    }

    public function setOptionTextForModel($model,$row,$keyList=array(),$labelList=array()){
	    $optionText = "";
        $attrRows = $model->getAttributes();
        $keyList = empty($keyList)?array_keys($row):$keyList;
        foreach ($keyList as $key){
            if(key_exists($key,$row)&&key_exists($key,$attrRows)){
                if($row[$key]!=$attrRows[$key]){
                    $optionText.=empty($optionText)?"":"<br/>";
                    $text = $model->getAttributeLabel($key).":".$row[$key];
                    $text.= " 修改为 ".$attrRows[$key];
                    $optionText.=$text;
                }
            }
        }
        $this->option_text = $optionText;
    }

    private function lenStr(){
        $code = strval($this->id);
        $this->log_code = "S";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->log_code.="0";
        }
        $this->log_code .= $code;
    }
}