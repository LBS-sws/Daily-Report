<?php

class SystemLogList extends CListPageModel
{

    public $id;
    public $optionType;
    public $no_of_attm = array(
        'slog'=>0
    );
    public $docType = 'SLOG';
    public $docMasterId = array(
        'slog'=>0
    );
    public $files;
    public $removeFileId = array(
        'slog'=>0
    );

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'log_code'=>Yii::t('log','log code'),
			'log_date'=>Yii::t('log','log date'),
			'log_user'=>Yii::t('log','log user'),
			'log_type_name'=>Yii::t('log','log type name'),
			'option_str'=>Yii::t('log','option type'),
			'option_text'=>Yii::t('log','option text'),
			'city'=>Yii::t('log','option city'),
		);
	}

    public function rules()
    {
        return array(
            array('id,attr, optionType, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
            array('files, removeFileId, docMasterId','safe'),
        );
    }
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.id,a.leave_log,a.log_code,a.log_date,a.log_user,a.log_type_name,a.option_str,a.option_text,b.name as city_name,
                 docman$suffix.countdoc('SLOG',a.id) as doc_num
				from swo_system_log a  
				left join security{$suffix}.sec_city b on a.city=b.code  
				where a.show_bool=1 and a.city in ($city_allow) 
			";
		$sql2 = "select count(a.id)
				from swo_system_log a 
				left join security{$suffix}.sec_city b on a.city=b.code  
				where a.show_bool=1 and a.city in ($city_allow) 
			";
		$clause = "";
		if(!empty($this->optionType)){
            $svalue = str_replace("'","\'",$this->optionType);
		    $clause.=" and a.log_type_name='{$svalue}'";
        }
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'log_type_name':
					$clause .= General::getSqlConditionClause('a.log_type_name',$svalue);
					break;
				case 'log_code':
					$clause .= General::getSqlConditionClause('a.log_code',$svalue);
					break;
				case 'option_str':
					$clause .= General::getSqlConditionClause('a.option_str',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $optionTextMin=explode("<br/>",$record['option_text']);
			    if(count($optionTextMin)>3){
			        array_splice($optionTextMin,3);
                    $optionTextMin = implode("<br/>",$optionTextMin);
                    $optionTextMin.="<br/>......";
                }else{
                    $optionTextMin=$record['option_text'];
                }
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'log_code'=>$record['log_code'],
                    'log_date'=>$record['log_date'],
                    'log_user'=>$record['log_user'],
                    'log_type_name'=>$record['log_type_name'],
                    'option_str'=>$record['option_str'],
                    'option_text'=>$record['option_text'],
                    'option_text_min'=>$optionTextMin,
                    'city'=>$record['city_name'],
                    'leave_log'=>$record['leave_log'],
                    'doc_num'=>$record['doc_num'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['systemLog_c01'] = $this->getCriteria();
		return true;
	}

	public static function getOptionTypeList(){
	    $list = array(""=>"-- 全部模块 --");
        $rows = Yii::app()->db->createCommand()->select("log_type_name")->from("swo_system_log")
            ->where("show_bool=1")->group("log_type_name")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["log_type_name"]]=$row["log_type_name"];
            }
        }
        return $list;
    }

    public function getCriteria() {
        return array(
            'searchField'=>$this->searchField,
            'optionType'=>$this->optionType,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'city'=>$this->city,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }
}
