<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class FeedbackTable extends FeedbackForm {

    private function clickList(){
        return array(
            "feedback_1"=>"serviceList",//客户服务
            "feedback_2"=>"followupList",//投诉个案
            "feedback_3"=>"enquiryList",//客户查询
            "feedback_4"=>"logisticList",//物流配送
            "feedback_5"=>"qcList",//品檢记录
            "feedback_6"=>"employeeList",//人事资料
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

    public static function drawEditButton($access, $writeurl, $readurl, $param) {
        $rw = Yii::app()->user->validRWFunction($access);
        $url = $rw ? $writeurl : $readurl;
        $icon = $rw ? "glyphicon glyphicon-pencil" : "glyphicon glyphicon-eye-open";
        $lnk=Yii::app()->createUrl($url,$param);

        return "<a href=\"$lnk\" target='_blank'><span class=\"$icon\"></span></a>";
    }
}