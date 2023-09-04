<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class FeedbackTable extends FeedbackForm {

    public $search_month;
    public $start_dt;
    public $end_dt;
    public $city_allow;

    public $month_day;
    public $week_start;
    public $week_end;
    public $week_day;
    public $last_week_start;
    public $last_week_end;
    public $last_week_day;

    private function clickList(){
        return array(
            "feedback_1"=>"serviceList",//客户服务
            "feedback_2"=>"followupList",//投诉个案
            "feedback_3"=>"enquiryList",//客户查询
            "feedback_4"=>"logisticList",//物流配送
            "feedback_5"=>"qcList",//品檢记录
            "feedback_6"=>"employeeList",//人事资料
            'feedback_8'=>'serviceNew',//当月累计新增
            'feedback_9'=>'serviceStop',//当月累计终止
            'feedback_10'=>'servicePause',//当月累计暂停
            'feedback_11'=>'serviceNet',//当月累计净增长
            'feedback_12'=>'salesEffect',//当月累计销售人效

            'new_now_week'=>'new_now_week',//当月累计新增(本周)
            'new_last_week'=>'new_last_week',//当月累计新增(上周)
            'stop_now_week'=>'stop_now_week',//当月累计终止(本周)
            'stop_last_week'=>'stop_last_week',//当月累计终止(上周)
            'pause_now_week'=>'pause_now_week',//当月累计暂停(本周)
            'pause_last_week'=>'pause_last_week',//当月累计暂停(上周)
            'net_now_week'=>'net_now_week',//当月累计净增长(本周)
            'net_last_week'=>'net_last_week',//当月累计净增长(上周)
        );
    }

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $this->city = key_exists("city",$_GET)?$_GET["city"]:0;
        $this->request_dt = key_exists("request_dt",$_GET)?date("Y/m/d",strtotime($_GET["request_dt"])):date("Y/m/d");
        $clickList = self::clickList();
        $type = key_exists("type",$_GET)?$_GET["type"]:"";
        if(key_exists($type,$clickList)){
            $fun = $clickList[$type];
            return $this->$fun();
        }else{
            return "<p>数据异常，请刷新重试</p>";
        }
    }

    //客户服务
    private function serviceList(){
        $html = "";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='110px'>".Yii::t('service','Contract No')."</th>";//合同编号
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
        $html.="<th width='90px'>".Yii::t('service','Customer Type')."</th>";//客户类别
        $html.="<th width='90px'>".Yii::t('service','Nature')."</th>";//性质
        $html.="<th width='120px'>".Yii::t('service','Service')."</th>";//服务内容
        $html.="<th width='90px'>".Yii::t('service','Record Type')."</th>";//记录类别
        $html.="<th width='90px'>".Yii::t('service','Record Date')."</th>";//记录日期
        $html.="<th width='90px'>".Yii::t('service','Monthly')."</th>";//月金额
        $html.="<th width='90px'>".Yii::t('service','Yearly')."</th>";//年金额
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $whereSql = " a.city = '{$this->city}' and DATE_FORMAT(a.status_dt,'%Y/%m/%d')='{$this->request_dt}'";
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.status,a.status_dt,a.amt_paid,a.ctrt_period,a.service
            ,f.description as cust_type_name
            ,g.description as nature_name
            ,n.id as no_id,n.contract_no,a.paid_type
            ,CONCAT(c.code,c.name) as c_company_name")
            ->from("swo_service a")
            ->leftJoin("swo_company c","a.company_id=c.id")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->order("a.status,a.status_dt desc")->queryAll();
        if($rows) {
            $sum = 0;
            $count = 0;
            $html .= "<tbody>";
            foreach ($rows as $row) {
                $count++;
                $link = self::drawEditButton('A02', 'service/edit', 'service/view', array('index' => $row['id']));
                $row["amt_paid"] = is_numeric($row["amt_paid"]) ? floatval($row["amt_paid"]) : 0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"]) ? floatval($row["ctrt_period"]) : 0;

                if ($row["paid_type"] == "M") {//月金额
                    $row["month_amount"] = $row["amt_paid"];
                    $row["year_amount"] = $row["amt_paid"] * $row["ctrt_period"];
                } else {
                    $row["month_amount"] = empty($row["ctrt_period"])?0:$row["amt_paid"]/$row["ctrt_period"];
                    $row["year_amount"] = $row["amt_paid"];
                }
                $row["month_amount"] = round($row["month_amount"], 2);
                $row["year_amount"] = round($row["year_amount"], 2);
                $html .= "<tr data-id='{$row["id"]}'>";
                $html .= "<td>" . $row["contract_no"] . "</td>";
                $html .= "<td>" . $row["c_company_name"] . "</td>";
                $html .= "<td>" . $row["cust_type_name"] . "</td>";
                $html .= "<td>" . $row["nature_name"] . "</td>";
                $html .= "<td>" .$row["service"] . "</td>";
                $html .= "<td>" .GetNameToId::getServiceStatusForKey($row["status"]) . "</td>";
                $html .= "<td>" . General::toDate($row["status_dt"]) . "</td>";
                $html .= "<td class='text-right'>" . $row["month_amount"] . "</td>";
                $html .= "<td class='text-right'>" . $row["year_amount"] . "</td>";
                $html .= "<td>{$link}</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody>";
        }else{
            $html.="<tr><td colspan='10'>无</td></tr>";
        }
        $html.="</table>";
        return $html;
    }

    //投诉个案
    private function followupList(){
        $html = "";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('followup','Date')."</th>";//客诉日期
        $html.="<th width='90px'>".Yii::t('followup','Type')."</th>";//服务类别
        $html.="<th>".Yii::t('followup','Customer')."</th>";//客户编号及名称
        $html.="<th width='130px'>".Yii::t('followup','Content')."</th>";//投诉内容及结果
        $html.="<th width='130px'>".Yii::t('followup','Contact')."</th>";//投诉者及联络电话
        $html.="<th width='130px'>".Yii::t('followup','Resp. Sales')."</th>";//负责销售顾问
        $html.="<th width='130px'>".Yii::t('followup','Technician')."</th>";//负责此客户之技术员
        $html.="<th width='90px'>".Yii::t('followup','Schedule Date')."</th>";//安排跟进日期
        $html.="<th width='130px'>".Yii::t('followup','Follow-up Tech.')."</th>";//跟进(此投诉)技术员
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $whereSql = " a.city = '{$this->city}' and DATE_FORMAT(a.entry_dt,'%Y/%m/%d')='{$this->request_dt}'";
        $rows = Yii::app()->db->createCommand()
            ->select("a.*
            ,CONCAT(c.code,c.name) as c_company_name")
            ->from("swo_followup a")
            ->leftJoin("swo_company c","a.company_id=c.id")
            ->where($whereSql)->order("a.type")->queryAll();
        if($rows) {
            $sum = 0;
            $count = 0;
            $html .= "<tbody>";
            foreach ($rows as $row) {
                $count++;
                $link = self::drawEditButton('A03', 'followup/edit', 'followup/view', array('index' => $row['id']));

                $html .= "<tr data-id='{$row["id"]}'>";
                $html .= "<td>" . General::toDate($row["entry_dt"]) . "</td>";
                $html .= "<td>" . $row["type"] . "</td>";
                $html .= "<td>" . $row["c_company_name"] . "</td>";
                $html .= "<td>" . $row["content"] . "</td>";
                $html .= "<td>" . $row["cont_info"] . "</td>";
                $html .= "<td>" . $row["resp_staff"] . "</td>";
                $html .= "<td>" . $row["resp_tech"] . "</td>";
                $html .= "<td>" . General::toDate($row["sch_dt"]) . "</td>";
                $html .= "<td>" . $row["follow_staff"] . "</td>";
                $html .= "<td>{$link}</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody>";
        }else{
            $html.="<tr><td colspan='10'>无</td></tr>";
        }
        $html.="</table>";
        return $html;
    }

    //客户查询
    private function enquiryList(){
        $html = "";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('enquiry','Contact Date')."</th>";//接听电话日期
        $html.="<th>".Yii::t('enquiry','Customer')."</th>";//客户
        $html.="<th width='90px'>".Yii::t('enquiry','Nature')."</th>";//性质
        $html.="<th width='130px'>".Yii::t('enquiry','Type')."</th>";//查询服务种类
        $html.="<th width='90px'>".Yii::t('enquiry','Source')."</th>";//接收来源
        $html.="<th width='100px'>".Yii::t('enquiry','Record By')."</th>";//登记人
        $html.="<th width='100px'>".Yii::t('enquiry','Resp. Staff')."</th>";//跟进销售同事
        $html.="<th width='90px'>".Yii::t('enquiry','Follow-up Date')."</th>";//跟进日期
        $html.="<th width='130px'>".Yii::t('enquiry','Result')."</th>";//跟进结果
        $html.="<th width='130px'>".Yii::t('enquiry','Remarks')."</th>";//备注
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $whereSql = " a.city = '{$this->city}' and DATE_FORMAT(a.contact_dt,'%Y/%m/%d')='{$this->request_dt}'";
        $rows = Yii::app()->db->createCommand()
            ->select("a.*
            ,b.description as type_name
            ,g.description as nature_name")
            ->from("swo_enquiry a")
            ->leftJoin("swo_customer_type b","a.type=b.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->order("a.type")->queryAll();
        if($rows) {
            $sum = 0;
            $count = 0;
            $html .= "<tbody>";
            foreach ($rows as $row) {
                $count++;
                $link = self::drawEditButton('A04', 'enquiry/edit', 'enquiry/view', array('index' => $row['id']));

                $html .= "<tr data-id='{$row["id"]}'>";
                $html .= "<td>" . General::toDate($row["contact_dt"]) . "</td>";
                $html .= "<td>" . $row["customer"] . "</td>";
                $html .= "<td>" . $row["nature_name"] . "</td>";
                $html .= "<td>" . $row["type_name"] . "</td>";
                $html .= "<td>" . EnquiryForm::getSourceListForCode($row["source_code"],true) . "</td>";
                $html .= "<td>" . $row["record_by"] . "</td>";
                $html .= "<td>" . $row["follow_staff"] . "</td>";
                $html .= "<td>" . General::toDate($row["follow_dt"]) . "</td>";
                $html .= "<td>" . $row["follow_result"] . "</td>";
                $html .= "<td>" . $row["remarks"] . "</td>";
                $html .= "<td>{$link}</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody>";
        }else{
            $html.="<tr><td colspan='11'>无</td></tr>";
        }
        $html.="</table>";
        return $html;
    }

    //物流配送
    private function logisticList(){
        $html = "";
        $html.= "<table class='table table-bordered table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t('logistic','Customer')."</th>";//客户编号及名称
        $html.="<th width='90px'>".Yii::t('logistic','Date')."</th>";//出单日期
        $html.="<th width='120px'>".Yii::t('logistic','Resp. Staff')."</th>";//送货人
        $html.="<th width='100px'>".Yii::t('logistic','Location')."</th>";//地点
        $html.="<th width='130px'>".Yii::t('logistic','Task')."</th>";//任务
        $html.="<th width='100px'>".Yii::t('code','Type')."</th>";//类别
        $html.="<th width='90px'>".Yii::t('logistic','Quantity')."</th>";//数量
        $html.="<th width='90px'>".Yii::t('logistic','Money')."</th>";//单价
        $html.="<th width='90px'>".Yii::t('logistic','All Money')."</th>";//金额
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $whereSql = " a.city = '{$this->city}' and DATE_FORMAT(a.log_dt,'%Y/%m/%d')='{$this->request_dt}'";
        $rows = Yii::app()->db->createCommand()
            ->select("a.*
            ,b.description as location_name")
            ->from("swo_logistic a")
            ->leftJoin("swo_location b","a.location=b.id")
            ->where($whereSql)->order("a.id desc")->queryAll();
        if($rows) {
            $sum = 0;
            $count = 0;
            $html .= "<tbody>";
            foreach ($rows as $row) {
                $count++;
                $link = self::drawEditButton('A05', 'logistic/edit', 'logistic/view', array('index' => $row['id']));
                $detailRows = Yii::app()->db->createCommand()
                    ->select("a.*,b.description as task_name,b.task_type")
                    ->from("swo_logistic_dtl a")
                    ->leftJoin("swo_task b","a.task=b.id")
                    ->where("a.log_id=:id",array(":id"=>$row["id"]))->queryAll();
                $detailCount = $detailRows?count($detailRows)+1:1;
                $html .= "<tr data-id='{$row["id"]}'>";
                $html .= "<td rowspan='{$detailCount}'>" . $row["company_name"] . "</td>";
                $html .= "<td rowspan='{$detailCount}'>" . General::toDate($row["log_dt"]) . "</td>";
                $html .= "<td rowspan='{$detailCount}'>" . $row["follow_staff"] . "</td>";
                $html .= "<td rowspan='{$detailCount}'>" . $row["location_name"] . "</td>";
                $html .= "<td colspan='5' style='height: 0px;border: 0px;line-height: 0px;padding: 0px;overflow: hidden;'>&nbsp;</td>";
                $html .= "<td rowspan='{$detailCount}'>{$link}</td>";
                $html .= "</tr>";
                if($detailRows){
                    foreach ($detailRows as $detailRow){
                        $detailRow["money"] = empty($detailRow["money"])?0:floatval($detailRow["money"]);
                        $detailRow["total"] = $detailRow["qty"]*$detailRow["money"];
                        $html .= "<tr data-id='{$row["id"]}'>";
                        $html .= "<td>" . $detailRow["task_name"] . "</td>";
                        $html .= "<td>" . GetNameToId::getTaskTypeForKey($detailRow["task_type"])."</td>";
                        $html .= "<td>" . $detailRow["qty"] . "</td>";
                        $html .= "<td>" . $detailRow["money"] . "</td>";
                        $html .= "<td>" . $detailRow["total"] . "</td>";
                        $html .= "</tr>";
                    }
                }else{
                    $html .= "<tr data-id='{$row["id"]}'>";
                    $html .= "<td colspan='5'>无</td>";
                    $html .= "</tr>";
                }
            }
            $html .= "</tbody>";
        }else{
            $html.="<tr><td colspan='10'>无</td></tr>";
        }
        $html.="</table>";
        return $html;
    }

    //品檢记录
    private function qcList(){
        $html = "";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('qc','Entry Date')."</th>";//输入日期
        $html.="<th width='130px'>".Yii::t('qc','Resp. Staff')."</th>";//外勤编号及名称
        $html.="<th>".Yii::t('qc','Customer')."</th>";//客户编号及名称
        $html.="<th width='90px'>".Yii::t('qc','Service Type')."</th>";//服务类别
        $html.="<th width='130px'>".Yii::t('qc','Customer Comment')."</th>";//客户意见
        $html.="<th width='90px'>".Yii::t('qc','Total Score')."</th>";//总分
        $html.="<th width='130px'>".Yii::t('qc','Staff-QC')."</th>";//质检部同事
        $html.="<th width='130px'>".Yii::t('qc','Remarks')."</th>";//备注
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $whereSql = " a.city = '{$this->city}' and DATE_FORMAT(a.entry_dt,'%Y/%m/%d')='{$this->request_dt}'";
        $rows = Yii::app()->db->createCommand()
            ->select("a.*")
            ->from("swo_qc a")
            ->where($whereSql)->order("a.service_type,a.id desc")->queryAll();
        if($rows) {
            $sum = 0;
            $count = 0;
            $html .= "<tbody>";
            foreach ($rows as $row) {
                $count++;
                $link = self::drawEditButton('A06', 'qc/edit', 'qc/view', array('index' => $row['id']));

                $style="";
                $html .= "<tr data-id='{$row["id"]}'>";
                $html .= "<td>" . General::toDate($row["entry_dt"]) . "</td>";
                $html .= "<td>" . $row["job_staff"] . "</td>";
                $html .= "<td>" . $row["company_name"] . "</td>";
                $html .= "<td>" . $row["service_type"] . "</td>";
                $html .= "<td>" . $row["cust_comment"] . "</td>";
                $html .= "<td>" . $row["qc_result"] . "</td>";
                $html .= "<td>" . $row["qc_staff"] . "</td>";
                $html .= "<td>" . $row["remarks"] . "</td>";
                $html .= "<td>{$link}</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody>";
        }else{
            $html.="<tr><td colspan='9'>无</td></tr>";
        }
        $html.="</table>";
        return $html;
    }

    //人事资料
    private function employeeList(){
        $suffix = Yii::app()->params['envSuffix'];
        $localOffice = Yii::t("staff","local office");
        $html = "";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='140px'>".Yii::t('staff','Entry Date')."</th>";//输入日期
        $html.="<th width='90px'>".Yii::t('staff','type')."</th>";//记录类别
        $html.="<th width='130px'>".Yii::t('staff','employee')."</th>";//员工编号及名称
        $html.="<th width='90px'>".Yii::t('staff','Office Name')."</th>";//办事处
        $html.="<th width='90px'>".Yii::t('staff','Department')."</th>";//部门
        $html.="<th width='90px'>".Yii::t('staff','Position')."</th>";//岗位
        $html.="<th width='110px'>".Yii::t('staff','job/leave Reason')."</th>";//入职/离职日期
        $html.="<th width='170px'>".Yii::t('staff','Cont. Duration')."</th>";//合同有效日期
        $html.="<th width='130px'>".Yii::t('staff','Cont. Period')."</th>";//合同签订年限(月)
        //$html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $whereSql = " b.city = '{$this->city}' and DATE_FORMAT(a.lcd,'%Y/%m/%d')='{$this->request_dt}'";
        $rows = Yii::app()->db->createCommand()
            ->select("a.employee_id,a.status,max(a.lcd) as entry_dt")
            ->from("hr{$suffix}.hr_employee_history a")
            ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
            ->where("a.status in ('inset','departure') and {$whereSql}")
            ->group("a.employee_id,a.status,DATE_FORMAT(a.lcd,'%Y/%m/%d')")
            ->order("a.status desc,a.lcd desc")->queryAll();
        if($rows) {
            $sum = 0;
            $count = 0;
            $html .= "<tbody>";
            foreach ($rows as $row) {
                $count++;
                //$link = self::drawEditButton('none@', 'hr/employee/edit', 'hr/employee/view', array('index' => $row['employee_id']));

                $style=$row["status"]=="departure"?"color:red;":"";
                $statusDesc=$row["status"]=="departure"?Yii::t("staff","Leave"):Yii::t("staff","job");

                $staffRow = Yii::app()->db->createCommand()
                    ->select("a.id,a.start_time,a.end_time,a.fix_time,a.entry_time,a.leave_time
                    ,CONCAT(a.name,' (',a.code,')') as staff_name
                    ,if(a.office_id=0,'{$localOffice}',office.name) AS office_name
                    ,b.name as department_name
                    ,f.name as position_name")
                    ->from("hr{$suffix}.hr_employee a")
                    ->leftJoin("hr{$suffix}.hr_dept b","a.department=b.id")
                    ->leftJoin("hr{$suffix}.hr_dept f","a.position=f.id")
                    ->leftJoin("hr$suffix.hr_office office"," a.office_id = office.id")
                    ->where("a.id =:id",array(":id"=>$row["employee_id"]))
                    ->queryRow();
                $html .= "<tr data-id='{$staffRow["id"]}' style='{$style}'>";
                $html .= "<td>" . General::toDateTime($row["entry_dt"]) . "</td>";
                $html .= "<td>" . $statusDesc. "</td>";
                $html .= "<td>" . $staffRow["staff_name"] . "</td>";
                $html .= "<td>" . $staffRow["office_name"] . "</td>";
                $html .= "<td>" . $staffRow["department_name"] . "</td>";
                $html .= "<td>" . $staffRow["position_name"] . "</td>";
                if($row["status"]=="departure"){
                    $html .= "<td>" . $staffRow["leave_time"] . "</td>";
                }else{
                    $html .= "<td>" . $staffRow["entry_time"] . "</td>";
                }
                $cont_date = General::toDate($staffRow["start_time"]);
                if($staffRow["fix_time"]=="fixation"){
                    $fix_time=strtotime($staffRow["end_time"])-strtotime($staffRow["start_time"]);
                    $fix_time = $fix_time/(30*24*60*60);
                    $fix_time = intval($fix_time);
                    $cont_date.=" - ".General::toDate($staffRow["end_time"]);
                }else{
                    $fix_time=Yii::t("staff","nofixed");
                }
                $html .= "<td>" . $cont_date . "</td>";
                $html .= "<td>" . $fix_time . "</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody>";
        }else{
            $html.="<tr><td colspan='9'>无</td></tr>";
        }
        $html.="</table>";
        return $html;
    }

    //当月累计新增
    private function serviceNew(){
        $html = "";
        $this->setWeekDate();
        $cityData = $this->setServiceNew();
        $tdTitleNow=Yii::t('summary','now week')."：".date("Y/m/d",$this->week_start)." ~ ".date("Y/m/d",$this->week_end)." (".$this->week_day.")";
        $tdTitleLast=Yii::t('summary','last week')."：".date("Y/m/d",$this->last_week_start)." ~ ".date("Y/m/d",$this->last_week_end)." (".$this->last_week_day.")";
        if($this->last_week_end==strtotime("1999/01/01")){
            $tdTitleLast = Yii::t('summary','last week')."：".Yii::t("summary","none");
        }
        $html.="<p>".$tdTitleNow."</p>";
        $html.="<p>".$tdTitleLast."</p>";

        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th rowspan='2' style='background: #FCD5B4' width='20%' class='text-center'>".$this->start_dt." ~ ".$this->end_dt."</th>";//日期
        $html.="<th colspan='3' style='background: #F2DCDB' width='50%' class='text-center'>".$this->search_month.Yii::t('summary',' month estimate')."</th>";//8月全月预估
        $html.="<th colspan='2' style='background: #DCE6F1' width='30%' class='text-center'>".Yii::t('summary','Target contrast')."</th>";//目标对比
        $html.="</tr>";
        $html.="<tr>";
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','now week')."</th>";//本周
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','last week')."</th>";//上周
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','stop growth')."</th>";//加速增长
        $html.="<th style='background: #DCE6F1'>".Yii::t('summary','Start Target')."</th>";//年初目标
        $html.="<th style='background: #DCE6F1'>".Yii::t('summary','Start Target result')."</th>";//达成目标
        $html.="</tr>";
        $html.="</thead>";
        $html .= "<tbody>";
        $html.="<tr>";
        $html.="<td class='text-right'>".$cityData["service_amt"]."</td>";
        $html.="<td class='text-right td_detail' data-title='{$tdTitleNow}' data-type='new_now_week'>".$cityData["now_week"]."</td>";
        $html.="<td class='text-right td_detail' data-title='{$tdTitleLast}' data-type='new_last_week'>".$cityData["last_week"]."</td>";
        $html.="<td class='text-right'>".$cityData["growth"]."</td>";
        $html.="<td class='text-right'>".$cityData["start_two_gross"]."</td>";
        $html.="<td class='text-right'>".$cityData["start_result"]."</td>";
        $html.="</tr>";
        $html .= "</tbody>";
        $html.="</table>";
        return $html;
    }

    //当月累计终止客户
    private function serviceStop(){
        $html = "";
        $this->setWeekDate();
        $cityData = $this->setServiceStop();
        $tdTitleNow=Yii::t('summary','now week')."：".date("Y/m/d",$this->week_start)." ~ ".date("Y/m/d",$this->week_end)." (".$this->week_day.")";
        $tdTitleLast=Yii::t('summary','last week')."：".date("Y/m/d",$this->last_week_start)." ~ ".date("Y/m/d",$this->last_week_end)." (".$this->last_week_day.")";
        if($this->last_week_end==strtotime("1999/01/01")){
            $tdTitleLast = Yii::t('summary','last week')."：".Yii::t("summary","none");
        }
        $html.="<p>".$tdTitleNow."</p>";
        $html.="<p>".$tdTitleLast."</p>";

        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th rowspan='2' style='background: #FCD5B4' width='20%' class='text-center'>".$this->start_dt." ~ ".$this->end_dt."</th>";//日期
        $html.="<th colspan='3' style='background: #F2DCDB' width='50%' class='text-center'>".$this->search_month.Yii::t('summary',' month estimate')."</th>";//8月全月预估
        $html.="<th colspan='2' style='background: #DCE6F1' width='30%' class='text-center'>".Yii::t('summary','Target contrast')."</th>";//目标对比
        $html.="</tr>";
        $html.="<tr>";
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','now week')."</th>";//本周
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','last week')."</th>";//上周
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','stop growth')."</th>";//加速增长
        $html.="<th style='background: #DCE6F1'>".Yii::t('summary','Start Target')."</th>";//年初目标
        $html.="<th style='background: #DCE6F1'>".Yii::t('summary','Start Target result')."</th>";//达成目标
        $html.="</tr>";
        $html.="</thead>";
        $html .= "<tbody>";
        $html.="<tr>";
        $html.="<td class='text-right'>".$cityData["service_amt"]."</td>";
        $html.="<td class='text-right td_detail' data-title='{$tdTitleNow}' data-type='stop_now_week'>".$cityData["now_week"]."</td>";
        $html.="<td class='text-right td_detail' data-title='{$tdTitleLast}' data-type='stop_last_week'>".$cityData["last_week"]."</td>";
        $html.="<td class='text-right'>".$cityData["growth"]."</td>";
        $html.="<td class='text-right'>".$cityData["start_two_gross"]."</td>";
        $html.="<td class='text-right'>".$cityData["start_result"]."</td>";
        $html.="</tr>";
        $html .= "</tbody>";
        $html.="</table>";
        return $html;
    }

    //当月累计暂停
    private function servicePause(){
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        $html = "";
        $this->setWeekDate();
        $cityData = $this->setServicePause();
        $tdTitleNow=Yii::t('summary','now week')."：".date("Y/m/d",$this->week_start)." ~ ".date("Y/m/d",$this->week_end)." (".$this->week_day.")";
        $tdTitleLast=Yii::t('summary','last week')."：".date("Y/m/d",$this->last_week_start)." ~ ".date("Y/m/d",$this->last_week_end)." (".$this->last_week_day.")";
        if($this->last_week_end==strtotime("1999/01/01")){
            $tdTitleLast = Yii::t('summary','last week')."：".Yii::t("summary","none");
        }
        $html.="<p>".$tdTitleNow."</p>";
        $html.="<p>".$tdTitleLast."</p>";

        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th rowspan='2' style='background: #FCD5B4' width='20%' class='text-center'>".$this->start_dt." ~ ".$this->end_dt."</th>";//日期
        $html.="<th colspan='2' style='background: #F2DCDB' width='50%' class='text-center'>".$this->search_month.Yii::t('summary',' month estimate')."</th>";//8月全月预估
        $html.="</tr>";
        $html.="<tr>";
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','now week')."</th>";//本周
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','last week')."</th>";//上周
        $html.="</tr>";
        $html.="</thead>";
        $html .= "<tbody>";
        $html.="<tr>";
        $html.="<td class='text-right'>".$cityData["service_amt"]."</td>";
        $html.="<td class='text-right td_detail' data-title='{$tdTitleNow}' data-type='pause_now_week'>".$cityData["now_week"]."</td>";
        $html.="<td class='text-right td_detail' data-title='{$tdTitleLast}' data-type='pause_last_week'>".$cityData["last_week"]."</td>";
        $html.="</tr>";
        $html .= "</tbody>";
        $html.="</table>";
        $startDate = date("Y/m/d",strtotime($this->request_dt." - 4 months"));//由于数据量过大所以只查4个月内
        $endDate = date("Y/m/d",strtotime($this->request_dt." - 2 months"));
        //不需要查询暂停之后是否终止，因为要留查看暂停记录
        $pauseRows = SummaryTable::getServiceRows($startDate,$endDate,$city_allow,'S');
        if($pauseRows){
            $html.="<p>以下客户的服务暂停已超过两个月：</p>";
            $html.=SummaryTable::getTableForRows($pauseRows,$city_allow);
        }
        return $html;
    }

    //当月累计净增长
    private function serviceNet(){
        $html = "";
        $this->setWeekDate();
        $cityData = $this->setServiceNet();
        $tdTitleNow=Yii::t('summary','now week')."：".date("Y/m/d",$this->week_start)." ~ ".date("Y/m/d",$this->week_end)." (".$this->week_day.")";
        $tdTitleLast=Yii::t('summary','last week')."：".date("Y/m/d",$this->last_week_start)." ~ ".date("Y/m/d",$this->last_week_end)." (".$this->last_week_day.")";
        if($this->last_week_end==strtotime("1999/01/01")){
            $tdTitleLast = Yii::t('summary','last week')."：".Yii::t("summary","none");
        }
        $html.="<p>".$tdTitleNow."</p>";
        $html.="<p>".$tdTitleLast."</p>";

        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th rowspan='2' style='background: #FCD5B4' width='20%' class='text-center'>".$this->start_dt." ~ ".$this->end_dt."</th>";//日期
        $html.="<th style='background: #FCD5B4' width='20%' class='text-center'>".Yii::t("summary","Actual monthly amount")."</th>";//服务生意额
        $html.="<th colspan='3' style='background: #F2DCDB' width='40%' class='text-center'>".$this->search_month.Yii::t('summary',' month estimate')."</th>";//8月全月预估
        $html.="<th colspan='2' style='background: #DCE6F1' width='20%' class='text-center'>".Yii::t('summary','Target contrast')."</th>";//目标对比
        $html.="</tr>";
        $html.="<tr>";
        $html.="<th style='background: #FCD5B4'>".$this->search_month.Yii::t("summary"," month")."</th>";//
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','now week')."</th>";//本周
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','last week')."</th>";//上周
        $html.="<th style='background: #F2DCDB'>".Yii::t('summary','growth')."</th>";//加速增长
        $html.="<th style='background: #DCE6F1'>".Yii::t('summary','Start Target')."</th>";//年初目标
        $html.="<th style='background: #DCE6F1'>".Yii::t('summary','Start Target result')."</th>";//达成目标
        $html.="</tr>";
        $html.="</thead>";
        $html .= "<tbody>";
        $html.="<tr>";
        $html.="<td class='text-right'>".$cityData["service_amt"]."</td>";
        $html.="<td class='text-right'>".$cityData["u_actual_money"]."</td>";
        $html.="<td class='text-right' data-title='{$tdTitleNow}' data-type='net_now_week'>".$cityData["now_week"]."</td>";
        $html.="<td class='text-right' data-title='{$tdTitleLast}' data-type='net_last_week'>".$cityData["last_week"]."</td>";
        $html.="<td class='text-right'>".$cityData["growth"]."</td>";
        $html.="<td class='text-right'>".$cityData["start_two_gross"]."</td>";
        $html.="<td class='text-right'>".$cityData["start_result"]."</td>";
        $html.="</tr>";
        $html .= "</tbody>";
        $html.="</table>";
        return $html;
    }

    //当月累计销售人效
    private function salesEffect(){
        $html = "<div class='table-responsive'>";
        $model = new SalesAnalysisForm();
        $model->search_date = $this->request_dt;
        $model->validate();
        $model->retrieveData($this->city);
        $html.= $model->salesAnalysisHtml();
        $html.="</div>";
        return $html;
    }

    //当月累计新增(本周)
    private function new_now_week(){
        $html = "";
        $this->setWeekDate();
        $start_date = date("Y/m/d",$this->week_start);
        $end_date = date("Y/m/d",$this->week_end);
        $city = "'{$this->city}'";
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city);
        $rows = SummaryTable::getServiceRowsForAdd($start_date,$end_date,$city_allow);
        $html.=SummaryTable::getTableForRows($rows,$city_allow,array(),$this->week_day,$this->month_day);
        return $html;
    }

    //当月累计新增(上周)
    private function new_last_week(){
        $html = "";
        $this->setWeekDate();
        $start_date = date("Y/m/d",$this->last_week_start);
        $end_date = date("Y/m/d",$this->last_week_end);
        $city = "'{$this->city}'";
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city);
        $rows = SummaryTable::getServiceRowsForAdd($start_date,$end_date,$city_allow);
        $html.=SummaryTable::getTableForRows($rows,$city_allow,array(),$this->last_week_day,$this->month_day);
        return $html;
    }

    //当月累计终止(本周)
    private function stop_now_week(){
        $html = "";
        $this->setWeekDate();
        $start_date = date("Y/m/d",$this->week_start);
        $end_date = date("Y/m/d",$this->week_end);
        $city = "'{$this->city}'";
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city);
        $rows = SummaryTable::getServiceRows($start_date,$end_date,$city_allow,"T");
        $html.=SummaryTable::getTableForRows($rows,$city_allow,array(),$this->week_day,$this->month_day);
        return $html;
    }

    //当月累计终止(上周)
    private function stop_last_week(){
        $html = "";
        $this->setWeekDate();
        $start_date = date("Y/m/d",$this->last_week_start);
        $end_date = date("Y/m/d",$this->last_week_end);
        $city = "'{$this->city}'";
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city);
        $rows = SummaryTable::getServiceRows($start_date,$end_date,$city_allow,"T");
        $html.=SummaryTable::getTableForRows($rows,$city_allow,array(),$this->last_week_day,$this->month_day);
        return $html;
    }

    //当月累计暂停(本周)
    private function pause_now_week(){
        $html = "";
        $this->setWeekDate();
        $start_date = date("Y/m/d",$this->week_start);
        $end_date = date("Y/m/d",$this->week_end);
        $city = "'{$this->city}'";
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city);
        $rows = SummaryTable::getServiceRows($start_date,$end_date,$city_allow,"S");
        $html.=SummaryTable::getTableForRows($rows,$city_allow,array(),$this->week_day,$this->month_day);
        return $html;
    }

    //当月累计暂停(上周)
    private function pause_last_week(){
        $html = "";
        $this->setWeekDate();
        $start_date = date("Y/m/d",$this->last_week_start);
        $end_date = date("Y/m/d",$this->last_week_end);
        $city = "'{$this->city}'";
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city);
        $rows = SummaryTable::getServiceRows($start_date,$end_date,$city_allow,"S");
        $html.=SummaryTable::getTableForRows($rows,$city_allow,array(),$this->last_week_day,$this->month_day);
        return $html;
    }

    private function setServiceNew(){
        $weekStartDate = date("Y/m/d",$this->week_start);
        $weekEndDate = date("Y/m/d",$this->week_end);
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        $citySetList = CitySetForm::getCitySetList($city_allow);
        //服务新增(本年)
        $serviceN = CountSearch::getServiceForType($this->start_dt,$this->end_dt,$city_allow,"N");
        //获取U系统的產品数据(本年)
        $uInvMoney = CountSearch::getUInvMoney($this->start_dt,$this->end_dt,$city_allow);
        //本週數據
        $serviceWeek = CountSearch::getServiceForType($weekStartDate,$weekEndDate,$city_allow,"N");
        //上週數據
        $lastServiceWeek = CountSearch::getServiceForType($lastWeekStartDate,$lastWeekEndDate,$city_allow,"N");
        $diffArr = array(
            "service_amt"=>0,//累计金额
            "now_week"=>0,//本周
            "last_week"=>0,//上周
            "growth"=>0,//加速增长
            "start_two_gross"=>$this->getYearStartMoney("two_gross"),//年初目标
            "start_result"=>0,//达成目标(年初)
        );
        $data=array();
        foreach ($citySetList as $cityRow) {
            $city = $cityRow["code"];
            $defMoreList=$diffArr;
            $defMoreList["add_type"] = $cityRow["add_type"];

            $defMoreList["service_amt"]+=key_exists($city,$serviceN)?$serviceN[$city]:0;
            $defMoreList["service_amt"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;
            $defMoreList["now_week"]+=key_exists($city,$serviceWeek)?$serviceWeek[$city]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastServiceWeek)?$lastServiceWeek[$city]:0;

            self::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $cityData = $data[$this->city];
        self::computeDate($cityData);
        return $cityData;
    }

    private function setServiceStop(){
        $weekStartDate = date("Y/m/d",$this->week_start);
        $weekEndDate = date("Y/m/d",$this->week_end);
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        $citySetList = CitySetForm::getCitySetList($city_allow);
        //终止服务、暂停服务
        $serviceForT = CountSearch::getServiceForST($this->start_dt,$this->end_dt,$city_allow,"T");
        //本週數據
        $serviceWeek = CountSearch::getServiceForST($weekStartDate,$weekEndDate,$city_allow,"T");
        //上週數據
        $lastServiceWeek = CountSearch::getServiceForST($lastWeekStartDate,$lastWeekEndDate,$city_allow,"T");
        $diffArr = array(
            "service_amt"=>0,//累计金额
            "now_week"=>0,//本周
            "last_week"=>0,//上周
            "growth"=>0,//加速增长
            "start_two_gross"=>(-1)*$this->getYearStartMoney("two_gross"),//年初目标
            "start_result"=>0,//达成目标(年初)
        );
        $data=array();
        foreach ($citySetList as $cityRow) {
            $city = $cityRow["code"];
            $defMoreList=$diffArr;
            $defMoreList["add_type"] = $cityRow["add_type"];

            $defMoreList["service_amt"]+=key_exists($city,$serviceForT)?(-1)*$serviceForT[$city]["num_stop"]:0;
            $defMoreList["now_week"]+=key_exists($city,$serviceWeek)?(-1)*$serviceWeek[$city]["num_stop"]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastServiceWeek)?(-1)*$lastServiceWeek[$city]["num_stop"]:0;

            self::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $cityData = $data[$this->city];
        self::computeDate($cityData,true);
        return $cityData;
    }

    private function setServicePause(){
        $weekStartDate = date("Y/m/d",$this->week_start);
        $weekEndDate = date("Y/m/d",$this->week_end);
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        $citySetList = CitySetForm::getCitySetList($city_allow);
        //终止服务、暂停服务
        $serviceForT = CountSearch::getServiceForST($this->start_dt,$this->end_dt,$city_allow,"S");
        //本週數據
        $serviceWeek = CountSearch::getServiceForST($weekStartDate,$weekEndDate,$city_allow,"S");
        //上週數據
        $lastServiceWeek = CountSearch::getServiceForST($lastWeekStartDate,$lastWeekEndDate,$city_allow,"S");
        $diffArr = array(
            "service_amt"=>0,//累计金额
            "now_week"=>0,//本周
            "last_week"=>0,//上周
            "growth"=>0,//加速增长
            "start_two_gross"=>(-1)*$this->getYearStartMoney("two_gross"),//年初目标
            "start_result"=>0,//达成目标(年初)
        );
        $data=array();
        foreach ($citySetList as $cityRow) {
            $city = $cityRow["code"];
            $defMoreList=$diffArr;
            $defMoreList["add_type"] = $cityRow["add_type"];

            $defMoreList["service_amt"]+=key_exists($city,$serviceForT)?(-1)*$serviceForT[$city]["num_pause"]:0;
            $defMoreList["now_week"]+=key_exists($city,$serviceWeek)?(-1)*$serviceWeek[$city]["num_pause"]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastServiceWeek)?(-1)*$lastServiceWeek[$city]["num_pause"]:0;

            self::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $cityData = $data[$this->city];
        self::computeDate($cityData);
        return $cityData;
    }

    private function setServiceNet(){
        $weekStartDate = date("Y/m/d",$this->week_start);
        $weekEndDate = date("Y/m/d",$this->week_end);
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow("'{$this->city}'");
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $lastStartDate = CountSearch::computeLastMonth($this->start_dt);
        $lastEndDate = CountSearch::computeLastMonth($this->end_dt);
        //终止服务、暂停服务
        $serviceForST = CountSearch::getServiceForST($this->start_dt,$this->end_dt,$city_allow);
        //恢復服务
        $serviceForR = CountSearch::getServiceForType($this->start_dt,$this->end_dt,$city_allow,"R");
        //更改服务
        $serviceForA = CountSearch::getServiceForA($this->start_dt,$this->end_dt,$city_allow);
        //服务新增（非一次性 和 一次性)
        $serviceAddForNY = CountSearch::getServiceAddForNY($this->start_dt,$this->end_dt,$city_allow);

        //服务新增（一次性)(上月)
        $lastServiceAddForNY = CountSearch::getServiceAddForY($lastStartDate,$lastEndDate,$city_allow);
        //获取U系统的產品数据(上月)
        $lastUInvMoney = CountSearch::getUInvMoney($lastStartDate,$lastEndDate,$city_allow);
        //获取U系统的服务单数据
        $uServiceMoney = CountSearch::getUServiceMoney($this->start_dt,$this->end_dt,$city_allow);
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getUInvMoney($this->start_dt,$this->end_dt,$city_allow);
        //本週數據
        $serviceWeek = CountSearch::getServiceForAll($weekStartDate,$weekEndDate,$city_allow);
        //上週數據
        $lastServiceWeek = CountSearch::getServiceForAll($lastWeekStartDate,$lastWeekEndDate,$city_allow);
        $diffArr = array(
            "service_amt"=>0,//累计金额
            "u_actual_money"=>0,//服务生意额
            "now_week"=>0,//本周
            "last_week"=>0,//上周
            "growth"=>0,//加速增长
            "start_two_gross"=>$this->getYearStartMoney("two_net"),//年初目标
            "start_result"=>0,//达成目标(年初)
        );
        $data=array();
        foreach ($citySetList as $cityRow) {
            $city = $cityRow["code"];
            $defMoreList=$diffArr;
            $defMoreList["add_type"] = $cityRow["add_type"];
            $defMoreList["u_actual_money"]+=key_exists($city,$uServiceMoney)?$uServiceMoney[$city]:0;
            $defMoreList["u_actual_money"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;
            if(key_exists($city,$serviceForST)){
                $defMoreList["service_amt"]-=$serviceForST[$city]["num_pause"];//暂停
                $defMoreList["service_amt"]-=$serviceForST[$city]["num_stop"];//终止
            }//
            $defMoreList["service_amt"]+=$defMoreList["u_actual_money"];//服务生意额
            $defMoreList["service_amt"]+=key_exists($city,$serviceForR)?$serviceForR[$city]:0;//恢复
            $defMoreList["service_amt"]+=key_exists($city,$serviceForA)?$serviceForA[$city]:0;//更改
            $defMoreList["service_amt"]+=key_exists($city,$serviceAddForNY)?$serviceAddForNY[$city]["num_new"]:0;//本月新增服务（排除一次性）
            $defMoreList["service_amt"]-=key_exists($city,$lastUInvMoney)?$lastUInvMoney[$city]["sum_money"]:0;//上月产品
            $defMoreList["service_amt"]-=key_exists($city,$lastServiceAddForNY)?$lastServiceAddForNY[$city]:0;//上月一次性新增服务

            $defMoreList["now_week"]+=key_exists($city,$serviceWeek)?$serviceWeek[$city]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastServiceWeek)?$lastServiceWeek[$city]:0;

            self::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $cityData = $data[$this->city];
        self::computeDate($cityData);
        return $cityData;
    }

    private function computeDate(&$list,$bool=false){
        $list["now_week"]=($list["now_week"]/$this->week_day)*$this->month_day;
        $list["now_week"]=HistoryAddForm::historyNumber($list["now_week"]);
        $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
        $list["last_week"]=HistoryAddForm::historyNumber($list["last_week"]);
        $list["growth"]=HistoryAddForm::comYes($list["now_week"],$list["last_week"]);
        $list["start_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_two_gross"],$bool);
    }

    private function setWeekDate(){
        $this->city_allow = "'{$this->city}'";
        $timer = strtotime($this->request_dt);
        $this->month_day = date("t",$timer);
        $this->start_dt = date("Y/m/01",$timer);
        $this->end_dt = $this->request_dt;
        $this->search_month = date("n",$timer);
        $this->week_end = $timer;
        $this->week_start = HistoryAddForm::getDateDiffForMonth($timer,6,$this->search_month,false);
        $this->week_day = HistoryAddForm::getDateDiffForDay($this->week_start,$this->week_end);

        $this->last_week_end = HistoryAddForm::getDateDiffForMonth($this->week_start,1,$this->search_month);
        $this->last_week_start = HistoryAddForm::getDateDiffForMonth($this->last_week_end,6,$this->search_month,false);
        $this->last_week_day = HistoryAddForm::getDateDiffForDay($this->last_week_start,$this->last_week_end);
    }

    public static function drawEditButton($access, $writeurl, $readurl, $param) {
        $rw = Yii::app()->user->validRWFunction($access);
        $url = $rw ? $writeurl : $readurl;
        $icon = $rw ? "glyphicon glyphicon-pencil" : "glyphicon glyphicon-eye-open";
        $lnk=Yii::app()->createUrl($url,$param);

        return "<a href=\"$lnk\" target='_blank'><span class=\"$icon\"></span></a>";
    }

    //获取年初目标金额
    private function getYearStartMoney($str="two_gross"){
        $money = Yii::app()->db->createCommand()->select($str)->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=1 and city=:city",
                array(":year"=>date("Y",strtotime($this->start_dt)),":city"=>$this->city)
            )->queryScalar();//查询目标金额
        return $money?$money:0;
    }

    public static function resetData(&$data,$cityRow,$citySet,$defMoreList){
        $notAddList=array("add_type","start_two_gross");
        $city = $cityRow["code"];
        $defMoreList["city"]=$city;
        $defMoreList["city_name"]= $cityRow["city_name"];
        $defMoreList["add_type"]= $cityRow["add_type"];
        $region = $cityRow["region_code"];
        if(key_exists($city,$data)){
            foreach ($defMoreList as $key=>$value){
                if(in_array($key,$notAddList)){
                    if(!isset($data[$city][$key])){
                        $data[$city][$key]=$value;
                    }
                }elseif (is_numeric($value)){
                    $data[$city][$key]+=$value;
                }else{
                    $data[$city][$key]=$value;
                }
            }
        }else{
            $data[$city]=$defMoreList;
        }

        if($cityRow["add_type"]==1&&key_exists($region,$citySet)){//叠加(城市配置的叠加)
            $regionTwo = $citySet[$region];
            self::resetData($data,$regionTwo,$citySet,$defMoreList);
        }
    }
}