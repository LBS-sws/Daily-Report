<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class UpdateSeTable{
    public $start_date;
    public $end_date;
    public $searchType=0;//0:员工查询、1：合约编号查询
    public $search;//查询
    public $table=1;//1:客户服务、3：ke客户服务

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $this->start_date = key_exists("startDate",$_GET)?$_GET["startDate"]:"";
        $this->end_date = key_exists("endDate",$_GET)?$_GET["endDate"]:"";
        $this->searchType = key_exists("searchType",$_GET)?$_GET["searchType"]:0;
        $this->search = key_exists("search",$_GET)?$_GET["search"]:"";
        $this->table = key_exists("table",$_GET)?$_GET["table"]:1;
        $type = key_exists("type",$_GET)?$_GET["type"]:"";
        $clickList = UpdateSeCountForm::clickList();
        $clickList = array_column($clickList,"type");
        if(in_array($type,$clickList)){
            return $this->$type();
        }else{
            return "<p>数据异常，请刷新重试</p>";
        }
    }

    //新增次数(员工)
    private function StaffAdd(){
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $rows = Yii::app()->db->createCommand()->select("a.id,a.service_id,a.service_type,a.lcu,a.lcd,a.change_amt,a.update_html")
            ->from("swo_service_history a")
            ->where("a.lcu='{$this->search}' and a.update_type!=1 and a.service_type in (1,3) and DATE_FORMAT(a.lcd,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'")
            ->order("a.service_type asc,a.service_id desc,a.lcd desc")
            ->queryAll();
        return self::getHistoryAUTableForRows($rows);
    }

    //修改次数(员工)
    private function StaffUpdate(){
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.id,a.service_id,a.service_type,a.lcu,a.lcd,a.change_amt,a.update_html")
            ->from("swo_service_history a")
            ->where("a.lcu='{$this->search}' and a.update_type=1 and a.service_type in (1,3) and DATE_FORMAT(a.lcd,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'")
            ->order("a.service_type asc,a.service_id desc,a.lcd desc")
            ->queryAll();
        return self::getHistoryAUTableForRows($rows);
    }

    //删除次数(员工)
    private function StaffDelete(){
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.log_user as lcu,a.option_text as update_html,a.lcd,
            IF(a.log_type='ServiceForm',1,3) as service_type,a.change_amt,
            CONCAT(-1) as service_id")
            ->from("swo_system_log a")
            ->where("DATE_FORMAT(a.log_date,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'
             and a.log_type in ('ServiceForm','ServiceKAForm')
             and a.log_user='{$this->search}'
             ")
            ->order("a.lcd desc")
            ->queryAll();
        return self::getHistoryAUTableForRows($rows);
    }

    //新增次数(合约)
    private function ContractAdd(){
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $table = $this->table;
        if($table==1){//客户服务
            $list = Yii::app()->db->createCommand()->select("a.id")->from("swo_service a")
                ->where("a.id=:id",array(":id"=>$this->search))->queryRow();
        }else{
            $list = Yii::app()->db->createCommand()->select("a.id")->from("swo_service_ka a")
                ->where("a.id=:id",array(":id"=>$this->search))->queryRow();
        }
        $whereData="";
        if($list){//如果有客户服务，限制时间查询
            $whereData=" and DATE_FORMAT(a.lcd,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'";
        }
        $rows = Yii::app()->db->createCommand()->select("a.id,a.service_id,a.service_type,a.lcu,a.lcd,a.change_amt,a.update_html")
            ->from("swo_service_history a")
            ->where("a.service_type='{$table}' and a.service_id='{$this->search}' and a.update_type!=1 and a.service_type in (1,3) {$whereData}")
            ->order("a.service_type asc,a.service_id desc,a.lcd desc")
            ->queryAll();
        return self::getHistoryAUTableForRows($rows);
    }

    //修改次数(合约)
    private function ContractUpdate(){
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $table = $this->table;
        if($table==1){//客户服务
            $list = Yii::app()->db->createCommand()->select("a.id")->from("swo_service a")
                ->where("a.id=:id",array(":id"=>$this->search))->queryRow();
        }else{
            $list = Yii::app()->db->createCommand()->select("a.id")->from("swo_service_ka a")
                ->where("a.id=:id",array(":id"=>$this->search))->queryRow();
        }
        $whereData="";
        if($list){//如果有客户服务，限制时间查询
            $whereData=" and DATE_FORMAT(a.lcd,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'";
        }
        $rows = Yii::app()->db->createCommand()->select("a.id,a.service_id,a.service_type,a.lcu,a.lcd,a.change_amt,a.update_html")
            ->from("swo_service_history a")
            ->where("a.service_type='{$table}' and a.service_id='{$this->search}' and a.update_type=1 and a.service_type in (1,3) {$whereData}")
            ->order("a.service_type asc,a.service_id desc,a.lcd desc")
            ->queryAll();
        return self::getHistoryAUTableForRows($rows);
    }

    //删除次数(合约)
    private function ContractDelete(){
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.log_user as lcu,a.option_text as update_html,a.lcd,
            IF(a.log_type='ServiceForm',1,3) as service_type,a.change_amt,
            CONCAT(-1) as service_id")
            ->from("swo_system_log a")
            ->where("DATE_FORMAT(a.log_date,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'
             and a.log_type in ('ServiceForm','ServiceKAForm')
             and a.id='{$this->search}'
             ")
            ->order("a.lcd desc")
            ->queryAll();
        return self::getHistoryAUTableForRows($rows);
    }

    //合约操作统计
    private function ContractChange(){
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $table = $this->table;
        $rows = array();
        if($table==5){
            $delRow = Yii::app()->db->createCommand()
                ->select("a.id,a.log_user as lcu,a.option_text as update_html,a.lcd,
            IF(a.log_type='ServiceForm',1,3) as service_type,a.change_amt,
            CONCAT(-1) as service_id")
                ->from("swo_system_log a")
                ->where("DATE_FORMAT(a.log_date,'%Y/%m/%d') BETWEEN '{$startDate}' and '{$endDate}'
             and a.id='{$this->search}'
             ")->queryRow();
            if($delRow){
                $table =$delRow["service_type"];
                $this->search = UpdateSeCountForm::getServiceIDForRow($delRow);
                $rows[]=$delRow;
            }
        }
        $historyRows = Yii::app()->db->createCommand()
            ->select("a.id,a.service_id,a.service_type,a.lcu,a.lcd,a.change_amt,a.update_html")
            ->from("swo_service_history a")
            ->where("a.service_type=:service_type and a.service_id=:service_id",array(
                ":service_type"=>$table,
                ":service_id"=>$this->search,
            ))
            ->order("a.lcd desc")
            ->queryAll();
        $historyRows = $historyRows?$historyRows:array();
        $rows = array_merge($rows,$historyRows);
        return self::getHistoryAUTableForRows($rows);
    }

    public static function getHistoryAUTableForRows($rows){
        $html="";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('summary','menu name')."</th>";//菜單名稱
        $html.="<th width='90px'>合约ID</th>";//合约ID
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
        $html.="<th width='90px'>".Yii::t('service','Customer Type')."</th>";//客户类别
        $html.="<th width='90px'>操作人</th>";//操作人
        $html.="<th width='90px'>操作时间</th>";//操作时间
        $html.="<th>操作内容</th>";//操作内容
        $html.="<th width='90px'>变动金额</th>";//变动金额
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        if($rows){
            $count=0;
            $countAmt=0;
            $html.="<tbody>";
            $serviceList = array();
            $userList = array();
            foreach ($rows as $row){
                $count++;
                $row["service_id"] = UpdateSeCountForm::getServiceIDForRow($row);
                switch ($row["service_type"]){
                    case "2":
                        $link = SummaryForm::drawEditButton('A11', 'serviceID/edit', 'serviceID/view', array('index'=>$row['service_id']));
                        break;
                    case "3":
                        $link = SummaryForm::drawEditButton('A13', 'serviceKA/edit', 'serviceKA/view', array('index'=>$row['service_id']));
                        break;
                    default:
                        $link = SummaryForm::drawEditButton('A02', 'service/edit', 'service/view', array('index'=>$row['service_id']));
                }
                $serviceKey = $row["service_type"]."_".$row["service_id"];
                if(key_exists($serviceKey,$serviceList)){
                    $serviceRow = $serviceList[$serviceKey];
                }else{
                    $serviceRow=UpdateSeCountForm::getServiceRow($row);
                    $serviceList[$serviceKey]=$serviceRow;
                }
                if(isset($serviceRow["noneBool"])&&$serviceRow["noneBool"]==1){
                    $link="&nbsp;";
                }
                $html.="<tr data-id='{$row["id"]}'>";
                $html.="<td>".$serviceRow["system_form"]."</td>";
                $html.="<td>".$row["service_id"]."</td>";
                $html.="<td>".$serviceRow["city_name"]."</td>";
                $html.="<td>".$serviceRow["company_str"]."</td>";
                $html.="<td>".$serviceRow["cust_type_str"]."</td>";
                $html.="<td>".$row["lcu"]."</td>";
                $html.="<td>".$row["lcd"]."</td>";

                if(substr_count($row['update_html'],"<br/>")>3){
                    $optionTextMin=explode("<br/>",$row['update_html']);
                    array_splice($optionTextMin,3);
                    $optionTextMin = implode("<br/>",$optionTextMin);
                    $optionTextMin.="<br/>......";
                    $html.="<td class='noExl'><div  data-toggle=\"tooltip\" data-placement=\"left\" title=\"{$row["update_html"]}\">".$optionTextMin."</div></td>";
                    $html.="<td class='hide'>".$row["update_html"]."</td>";
                }else{
                    $html.="<td>".$row["update_html"]."</td>";
                }
                if($row["change_amt"]===''||$row["change_amt"]===null){
                    $row["change_amt"] = "未知";
                }else{
                    $countAmt+=floatval($row["change_amt"]);
                }
                $html.="<td>".$row["change_amt"]."</td>";
                $html.="<td>{$link}&nbsp;</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='5' class='text-right'>操作次数：{$count}</td>";
            $html.="<td colspan='4' class='text-right'>变动金额汇总：{$countAmt}</td>";
            $html.="<td>&nbsp;</td>";
            $html.="</tr>";
            $html.=SummaryTable::printTable2Excel(10);
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='9'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return $html;
    }
}