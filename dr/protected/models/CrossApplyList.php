<?php

class CrossApplyList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'table_type'=>Yii::t('summary','menu name'),
			'contract_no'=>Yii::t('service','Contract No'),
			'apply_date'=>Yii::t('service','Apply date'),
			'month_amt'=>Yii::t('service','Monthly'),
			'rate_num'=>Yii::t('service','accept rate'),
			'rate_amt'=>Yii::t('service','accept amt'),
			'old_city'=>Yii::t('service','City'),
			'cross_city'=>Yii::t('service','Cross city'),
			'status_type'=>Yii::t('service','status type'),

			'cross_type'=>Yii::t('service','Cross type'),
            'company_name'=>Yii::t('service','Customer name'),
            'qualification_ratio'=>Yii::t('service','Qualification ratio'),
            'qualification_city'=>Yii::t('service','Qualification city'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $uid = Yii::app()->user->id;
		$sql1 = "select a.*,
                  b.name as old_city_name,f.name as cross_city_name 
				from swo_cross a
				LEFT JOIN security{$suffix}.sec_city b ON a.old_city=b.code
				LEFT JOIN security{$suffix}.sec_city f ON a.cross_city=f.code
				where a.lcu='{$uid}' 
			";
		$sql2 = "select count(a.id)
				from swo_cross a
				LEFT JOIN security{$suffix}.sec_city b ON a.old_city=b.code
				LEFT JOIN security{$suffix}.sec_city f ON a.cross_city=f.code
				where a.lcu='{$uid}' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'contract_no':
					$clause .= General::getSqlConditionClause('a.contract_no',$svalue);
					break;
				case 'apply_date':
					$clause .= General::getSqlConditionClause('a.apply_date',$svalue);
					break;
				case 'old_city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'cross_city':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
				case 'company_name':
					$companySql = self::searchCompanySql($svalue);
                    $clause .= "and ({$companySql}) ";
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
			    $companyName = self::getCompanyNameToCrossList($record);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'contract_no'=>$record['contract_no'],
                    'apply_date'=>General::toDate($record['apply_date']),
                    'month_amt'=>floatval($record['month_amt']),
                    'rate_num'=>$record['rate_num']===null?"":floatval($record['rate_num'])."%",
                    'old_city'=>$record['old_city_name'],
                    'cross_city'=>$record['cross_city_name'],
                    'qualification_city'=>empty($record['qualification_city'])?"":General::getCityName($record['qualification_city']),
                    'qualification_ratio'=>$record['qualification_ratio']===null?"":floatval($record['qualification_ratio'])."%",
                    'status_type'=>$record['status_type'],
                    'company_name'=>$companyName,
                    'table_type'=>CrossApplyForm::getCrossTableTypeNameForKey($record["table_type"]),
                    'cross_type_name'=>CrossApplyForm::getCrossTypeStrToKey($record["cross_type"]),
                    'status_str'=>self::getStatusStrForStatusType($record),
                    'color'=>self::getColorForStatusType($record['status_type']),
                );
			}
		}
		$session = Yii::app()->session;
		$session['crossApply_c01'] = $this->getCriteria();
		return true;
	}


    public static function getColorForStatusType($statusType){
        $statusType="".$statusType;
        $list = array(
            1=>"text-primary",//待审核
            2=>"text-danger",//已拒绝
            3=>"text-yellow",//待U系统确认
            5=>"text-muted",//已完成
            6=>"text-danger",//U系统已拒绝
        );
        if(key_exists($statusType,$list)){
            return $list[$statusType];
        }else{
            return $statusType;
        }
    }

    public static function getStatusStrForStatusType($row){
        if($row['status_type']==5&&$row['cross_num']>=2){//两次以上的交叉派单显示已审批
            return Yii::t("service","Approved");//已审批
        }
        $statusType="".$row['status_type'];
        $list = array(
            1=>Yii::t("service","pending review"),//待审核
            2=>Yii::t("service","rejected"),//已拒绝
            3=>Yii::t("service","pending U System"),//待U系统确认
            5=>Yii::t("service","finish"),//已完成
            6=>Yii::t("service","rejected U System"),//U系统已拒绝
        );
        if(key_exists($statusType,$list)){
            return $list[$statusType];
        }else{
            return $statusType;
        }
    }

    public static function getCompanyNameToCrossList($record){
        if($record["table_type"]==0){
            $tableNameOne="swo_service";
        }else{
            $tableNameOne="swo_service_ka";
        }
        $row = Yii::app()->db->createCommand()->select("a.company_id,b.code,b.name")->from("{$tableNameOne} a")
            ->leftJoin("swo_company b","a.company_id=b.id")
            ->where("a.id=:id",array(":id"=>$record["service_id"]))->queryRow();
        if($row){
            return $row["name"];
        }else{
            return "";
        }
    }

    public static function searchCompanySql($svalue){
        $companyRows = Yii::app()->db->createCommand()->select("id")->from("swo_company")
            ->where("name like '%{$svalue}%'")->queryAll();
        $companyId=array();
        if($companyRows){
            foreach ($companyRows as $companyRow){
                $companyId[]=$companyRow["id"];
            }
        }
        if(empty($companyId)){
            return "(1=2)";//没有查到公司名称
        }else{
            $serviceSql = "";
            $companyId = implode(",",$companyId);
            $oneRows = Yii::app()->db->createCommand()->select("a.id")->from("swo_service a")
                ->where("a.company_id in ({$companyId})")->queryAll();
            if($oneRows){
                $oneID = array();
                foreach ($oneRows as $oneRow){
                    $oneID[]=$oneRow["id"];
                }
                $oneID = implode(",",$oneID);
                $serviceSql = "a.table_type=0 and a.service_id in ({$oneID})";
            }

            $twoRows = Yii::app()->db->createCommand()->select("a.id")->from("swo_service_ka a")
                ->where("a.company_id in ({$companyId})")->queryAll();
            if($twoRows){
                $twoID = array();
                foreach ($twoRows as $twoRow){
                    $twoID[]=$twoRow["id"];
                }
                $twoID = implode(",",$twoID);
                if(empty($serviceSql)){
                    $serviceSql = "a.table_type=1 and a.service_id in ({$twoID})";
                }else{
                    $serviceSql = "({$serviceSql}) or (a.table_type=1 and a.service_id in ({$twoID}))";
                }
            }

            if(empty($serviceSql)){
                return "(1=2)";//没有查到合约
            }else{
                return $serviceSql;
            }
        }
    }
}
