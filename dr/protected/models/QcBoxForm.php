<?php

class QcBoxForm extends CFormModel
{
	/* User Fields */
    public $id;
    public $city;
    public $entry_dt;
    public $job_staff;
    public $team;
    public $month;
    public $company_id;
    public $company_name;
    public $company_addr;
    public $service_score;
    public $cust_score;
    public $cust_comment;
    public $qc_result;
    public $env_grade;
    public $qc_dt;
    public $cust_sign;
    public $qc_staff;
    public $remarks;
    public $service_type;
    public $new_form = false;
    public $qc_score;
    public $lcu;
    public $luu;
    public $lcd;
    public $lud;

    public $docType = 'QC';
    public $files;

    public $docMasterId = array(
        'qc'=>0,
        'qcphoto'=>0,
    );
    public $removeFileId = array(
        'qc'=>0,
        'qcphoto'=>0,
    );
    public $no_of_attm = array(
        'qc'=>0,
        'qcphoto'=>0,
    );

    public $info = array(
    );


    public $blob_fields = array(
        'sign_cust','sign_qc'
    );

    protected $defineInfo = array(//默认通用的字段
        "sign_qc"=>"",//质检员签字
        "sign_cust"=>"",//客户签字
        "service_dt"=>"",//服务时间
        "cust_sfn"=>"",//客户满意度
    );

    public $ia_fields = array(//IA字段
        "image"=>array(//形象资质
            "name"=>'Image Qualification',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>6,//项目总分
            "list"=>array(
                array("title"=>"image_01","name"=>"image_01","max_num"=>3),
                array("title"=>"image_02","name"=>"image_02","max_num"=>3),
            )
        ),
        "security"=>array(//安全措施
            "name"=>'Security',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>8,//项目总分
            "list"=>array(
                array("title"=>"security_01","name"=>"security_01","max_num"=>3),
                array("title"=>"security_02","name"=>"security_02","max_num"=>5),
            )
        ),
        "flow"=>array(//服务流程
            "name"=>'Service Flow',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>8,//项目总分
            "list"=>array(
                array("title"=>"flow_01","name"=>"flow_01","max_num"=>5),
                array("title"=>"flow_02","name"=>"flow_02","max_num"=>3),
            )
        ),
        "report"=>array(//沟通报告
            "name"=>'Communication Report',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>12,//项目总分
            "list"=>array(
                array("title"=>"report_01","name"=>"report_01","max_num"=>5),
                array("title"=>"report_02","name"=>"report_02","max_num"=>2),
                array("title"=>"report_03","name"=>"report_03","max_num"=>5),
            )
        ),
        "machine"=>array(//检查机器
            "name"=>'Check Machine',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>7,//项目总分
            "list"=>array(
                array("title"=>"machine_01","name"=>"machine_01","max_num"=>2),
                array("title"=>"machine_02","name"=>"machine_02","max_num"=>2),
                array("title"=>"machine_03","name"=>"machine_03","max_num"=>3),
            )
        ),
        "wash"=>array(//洗手盆清洁
            "name"=>'wash basin',//项目名称
            "type"=>2,//项目类型 1：必填 2：允许设置
            "totalSum"=>10,//项目总分
            "list"=>array(
                array("title"=>"wash_01","name"=>"wash_01","max_num"=>3),
                array("title"=>"wash_02","name"=>"wash_02","max_num"=>2),
                array("title"=>"wash_03","name"=>"wash_03","max_num"=>3),
                array("title"=>"wash_04","name"=>"wash_04","max_num"=>2),
            )
        ),
        "latrine"=>array(//蹲厕清洁
            "name"=>'latrine',//项目名称
            "type"=>2,//项目类型 1：必填 2：允许设置
            "totalSum"=>16,//项目总分
            "list"=>array(
                array("title"=>"latrine_01","name"=>"latrine_01","max_num"=>3),
                array("title"=>"latrine_02","name"=>"latrine_02","max_num"=>3),
                array("title"=>"latrine_03","name"=>"latrine_03","max_num"=>3),
                array("title"=>"latrine_04","name"=>"latrine_04","max_num"=>2),
                array("title"=>"latrine_05","name"=>"latrine_05","max_num"=>2),
                array("title"=>"latrine_06","name"=>"latrine_06","max_num"=>3),
            )
        ),
        "lavatory"=>array(//坐厕清洁
            "name"=>'lavatory',//项目名称
            "type"=>2,//项目类型 1：必填 2：允许设置
            "totalSum"=>17,//项目总分
            "list"=>array(
                array("title"=>"lavatory_01","name"=>"lavatory_01","max_num"=>3),
                array("title"=>"lavatory_02","name"=>"lavatory_02","max_num"=>3),
                array("title"=>"lavatory_03","name"=>"lavatory_03","max_num"=>2),
                array("title"=>"lavatory_04","name"=>"lavatory_04","max_num"=>2),
                array("title"=>"lavatory_05","name"=>"lavatory_05","max_num"=>2),
                array("title"=>"lavatory_06","name"=>"lavatory_06","max_num"=>2),
                array("title"=>"lavatory_07","name"=>"lavatory_07","max_num"=>3),
            )
        ),
        "urine"=>array(//尿缸清洁
            "name"=>'Urine',//项目名称
            "type"=>2,//项目类型 1：必填 2：允许设置
            "totalSum"=>16,//项目总分
            "list"=>array(
                array("title"=>"urine_01","name"=>"urine_01","max_num"=>3),
                array("title"=>"urine_02","name"=>"urine_02","max_num"=>3),
                array("title"=>"urine_03","name"=>"urine_03","max_num"=>3),
                array("title"=>"urine_04","name"=>"urine_04","max_num"=>2),
                array("title"=>"urine_05","name"=>"urine_05","max_num"=>2),
                array("title"=>"urine_06","name"=>"urine_06","max_num"=>3),
            )
        ),
    );

    public $ib_fields = array(//IB字段
        "image"=>array(//形象资质
            "name"=>'Image Qualification',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>9,//项目总分
            "list"=>array(
                array("title"=>"image_ib_01","name"=>"image_ib_01","max_num"=>3),
                array("title"=>"image_ib_02","name"=>"image_ib_02","max_num"=>3),
                array("title"=>"image_ib_03","name"=>"image_ib_03","max_num"=>3),
            )
        ),
        "security"=>array(//安全管理
            "name"=>'Security IB',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>12,//项目总分
            "list"=>array(
                array("title"=>"security_ib_01","name"=>"security_ib_01","max_num"=>3),
                array("title"=>"security_ib_02","name"=>"security_ib_02","max_num"=>3),
                array("title"=>"security_ib_03","name"=>"security_ib_03","max_num"=>3),
                array("title"=>"security_ib_04","name"=>"security_ib_04","max_num"=>3),
            )
        ),
        "report"=>array(//沟通管理
            "name"=>'Communication IB',//项目名称
            "type"=>1,//项目类型 1：必填 2：允许设置
            "totalSum"=>10,//项目总分
            "list"=>array(
                array("title"=>"report_ib_01","name"=>"report_ib_01","max_num"=>2),
                array("title"=>"report_ib_02","name"=>"report_ib_02","max_num"=>3),
                array("title"=>"report_ib_03","name"=>"report_ib_03","max_num"=>2),
                array("title"=>"report_ib_04","name"=>"report_ib_04","max_num"=>3),
            )
        ),
        "rat"=>array(//鼠防治管理
            "name"=>'Rat IB',//项目名称
            "type"=>2,//项目类型 1：必填 2：允许设置
            "totalSum"=>27,//项目总分
            "list"=>array(
                array("title"=>"rat_ib_01","name"=>"rat_ib_01","max_num"=>5),
                array("title"=>"rat_ib_02","name"=>"rat_ib_02","max_num"=>3),
                array("title"=>"rat_ib_03","name"=>"rat_ib_03","max_num"=>3),
                array("title"=>"rat_ib_04","name"=>"rat_ib_04","max_num"=>3),
                array("title"=>"rat_ib_05","name"=>"rat_ib_05","max_num"=>3),
                array("title"=>"rat_ib_06","name"=>"rat_ib_06","max_num"=>3),
                array("title"=>"rat_ib_07","name"=>"rat_ib_07","max_num"=>2),
                array("title"=>"rat_ib_08","name"=>"rat_ib_08","max_num"=>5),
            )
        ),
        "cockroach"=>array(//蟑螂防治管理
            "name"=>'Cockroach IB',//项目名称
            "type"=>2,//项目类型 1：必填 2：允许设置
            "totalSum"=>22,//项目总分
            "list"=>array(
                array("title"=>"cockroach_ib_01","name"=>"cockroach_ib_01","max_num"=>5),
                array("title"=>"cockroach_ib_02","name"=>"cockroach_ib_02","max_num"=>2),
                array("title"=>"cockroach_ib_03","name"=>"cockroach_ib_03","max_num"=>2),
                array("title"=>"cockroach_ib_04","name"=>"cockroach_ib_04","max_num"=>3),
                array("title"=>"cockroach_ib_05","name"=>"cockroach_ib_05","max_num"=>3),
                array("title"=>"cockroach_ib_06","name"=>"cockroach_ib_06","max_num"=>2),
                array("title"=>"cockroach_ib_07","name"=>"cockroach_ib_07","max_num"=>5),
            )
        ),
        "flying"=>array(//飞虫防治管理
            "name"=>'Flying insects',//项目名称
            "type"=>2,//项目类型 1：必填 2：允许设置
            "totalSum"=>20,//项目总分
            "list"=>array(
                array("title"=>"flying_ib_01","name"=>"flying_ib_01","max_num"=>3),
                array("title"=>"flying_ib_02","name"=>"flying_ib_02","max_num"=>3),
                array("title"=>"flying_ib_03","name"=>"flying_ib_03","max_num"=>2),
                array("title"=>"flying_ib_04","name"=>"flying_ib_04","max_num"=>2),
                array("title"=>"flying_ib_05","name"=>"flying_ib_05","max_num"=>3),
                array("title"=>"flying_ib_06","name"=>"flying_ib_06","max_num"=>2),
                array("title"=>"flying_ib_07","name"=>"flying_ib_07","max_num"=>5),
            )
        ),
    );

    public static $effectDate='2025-05-06';//该功能生效时间
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('qc','Record ID'),
            'city'=>Yii::t('app','City'),
            'entry_dt'=>Yii::t('qc','Entry Date'),
            'job_staff'=>Yii::t('qc','Resp. Staff'),
            'team'=>Yii::t('qc','Team'),
            'month'=>Yii::t('qc','Month'),
            'input_dt'=>Yii::t('qc','Input Date'),
            'company_name'=>Yii::t('qc','Customer'),
            'service_score'=>Yii::t('qc','Service Score'),
            'cust_score'=>Yii::t('qc','Customer Score'),
            'qc_comment'=>Yii::t('qc','QC Comment'),
            'qc_staff'=>Yii::t('qc','QC Staff'),
            'remarks'=>Yii::t('qc','Remarks'),
            'cust_comment'=>Yii::t('qc','Customer Comment'),
            'signature'=>Yii::t('qc','Signatures'),
            'service_dt'=>Yii::t('qc','Service Date'),
            'qc_dt'=>Yii::t('qc','QC Date'),
            'qc_result'=>Yii::t('qc','Score'),
            'sign_cust'=>Yii::t('qc','Customer Signature'),
            'sign_qc'=>Yii::t('qc','QC Signature'),
            'cust_sfn'=>Yii::t('qc','Customer satisfaction'),
        );
    }

	/**
	 * Declares the validation rules.
	 */
    public function rules()
    {
        return array(
            array('id, city, team, month, env_grade, cust_sign, qc_staff, company_id, cust_comment, 
				remarks, service_type, new_form
				','safe'),
//			array('docType, files, removeFileId, no_of_attm','safe'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
            array('city,qc_staff,job_staff, company_name','required'),
            array('entry_dt','date','allowEmpty'=>false,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
            ),
            array('qc_dt','date','allowEmpty'=>true,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
            ),
            array('qc_result','numerical','allowEmpty'=>true,'integerOnly'=>false),
            array('info','validateDetailRecords'),
        );
    }

    public function validateDetailRecords($attribute, $params) {
        $infoRows = $this->$attribute;
        $saveArr =array();
        $arr=array();
        $qc_result = 0;
        if($this->service_type=="IA"){
            $arr = $this->ia_fields;
        }elseif ($this->service_type=="IB"){
            $arr = $this->ib_fields;
        }
        foreach ($this->defineInfo as $defineKey=>$defineValue){
            $saveArr[$defineKey]=isset($infoRows[$defineKey])?$infoRows[$defineKey]:'';
        }
        if (is_array($infoRows)&&!empty($arr)) {
            foreach ($arr as $code=>$rows){
                if($rows["type"]==2){
                    if(!isset($infoRows[$code."_all"])){
                        $this->addError($attribute, Yii::t('qc',$rows["name"])."不存在");
                        return false;
                    }
                    $saveArr[$code."_all"] = $infoRows[$code."_all"];//是否包含 1:包含
                }
                foreach ($rows["list"] as $row){
                    $name = $row["name"];
                    $saveArr[$name]='';
                    $saveArr[$name."_remark"]=isset($infoRows[$name."_remark"])?$infoRows[$name."_remark"]:"";
                    if(!isset($infoRows[$name])){
                        $this->addError($attribute, Yii::t('qc',$row["title"])."不存在");
                        return false;
                    }
                    if($rows["type"]==2&&$infoRows[$code."_all"]!=1){//不包含
                        $saveArr[$name]='';
                        $qc_result+= $row["max_num"];
                    }else{
                        if($infoRows[$name]>$row["max_num"]){
                            $this->addError($attribute, Yii::t('qc',$row["title"])."不能大于".$row["max_num"]);
                            return false;
                        }
                        $saveArr[$name]=empty($infoRows[$name])?0:floatval($infoRows[$name]);
                        $qc_result+= $saveArr[$name];
                    }
                }
            }
        }
        $this->qc_result = $qc_result;
        $this->info = $saveArr;
    }

    public function initData($infoRow=array()){
        $arr=array();
        if($this->service_type=="IA"){
            $arr = $this->ia_fields;
        }elseif ($this->service_type=="IB"){
            $arr = $this->ib_fields;
        }
        foreach ($this->defineInfo as $defineKey=>$defineValue){
            $this->info[$defineKey]=isset($infoRow[$defineKey])?$infoRow[$defineKey]:$defineValue;
        }
        $this->qc_result = 0;
        if(!empty($arr)){
            foreach ($arr as $code=>$rows){
                if($rows["type"]==2){
                    $this->info[$code."_all"]=isset($infoRow[$code."_all"])?$infoRow[$code."_all"]:1;//是否包含 1:包含
                    if($this->info[$code."_all"]==2){
                        $this->qc_result+=$rows["totalSum"];
                    }
                }
                foreach ($rows["list"] as $row){
                    $this->info[$row["name"]]=isset($infoRow[$row["name"]])?$infoRow[$row["name"]]:"";
                    $this->info[$row["name"]."_remark"]=isset($infoRow[$row["name"]."_remark"])?$infoRow[$row["name"]."_remark"]:"";
                    if($rows["type"]!=2||$this->info[$code."_all"]==1){
                        $this->qc_result+=empty($this->info[$row["name"]])?0:$this->info[$row["name"]];
                    }
                }
            }
        }
    }

    public function printInfoHtml(){
        $html="<table style='min-width: 955px' class=\"table table-fixed table-condensed table-bordered table-hover\">";
        $html.="<thead>";
        $html.="</tr>";
        $html.="<th width='30px'>&nbsp;</th>";
        $html.="<th width='600px'>".Yii::t("qc","Assessment items")."</th>";
        $html.="<th width='105px'>".Yii::t("qc","score value")."</th>";
        $html.="<th width='110px'>".Yii::t("qc","execution environment")."</th>";
        $html.="<th width='110px'>".Yii::t("qc","text note")."</th>";
        $html.="</tr>";
        $html.="</thead><tbody>";
        $arr=array();
        if($this->service_type=="IA"){
            $arr = $this->ia_fields;
        }elseif ($this->service_type=="IB"){
            $arr = $this->ib_fields;
        }
        if(!empty($arr)){
            $qc_amt = 0;//质检总分
            $total_amt = 0;//技术员打分总分
            $className = get_class($this);
            $strNum = 64;
            foreach ($arr as $code=>$rows){
                $strNum++;
                $rowspan = count($rows["list"])+1;
                $html.="<tr>";
                $html.="<td rowspan='{$rowspan}' class='text-center' style='vertical-align: middle'><b>".chr($strNum)."</b></td>";
                $html.="<td class='text-center' style='background-color: #FDE9D9'><b>".Yii::t("qc",$rows["name"])."</b></td>";
                $ready = $this->readonly();
                if($rows["type"]==2){
                    if($this->info[$code."_all"]==1){
                        $totalSum = "";
                    }else{
                        $totalSum =$rows["totalSum"];
                        $total_amt+=$totalSum;
                        $ready = true;
                    }
                    $html.="<td style='background-color: #FDE9D9'>";
                    $html.=TbHtml::dropDownList("{$className}[info][{$code}_all]",$this->info[$code."_all"],array(1=>"包含",2=>"不包含"),array("class"=>"changeAll","data-name"=>$code,"data-sum"=>$rows["totalSum"]));
                    $html.="</td>";
                    $html.="<td class='text-center' style='background-color: #FDE9D9'>{$totalSum}</td>";
                }else{
                    $html.="<td style='background-color: #FDE9D9'>&nbsp;</td>";
                    $html.="<td style='background-color: #FDE9D9'>&nbsp;</td>";
                }
                $html.="<td style='background-color: #FDE9D9'>&nbsp;</td>";
                $html.="</tr>";
                foreach ($rows["list"] as $row){
                    $name = $row["name"];
                    $qc_amt+=$row["max_num"];
                    if($rows["type"]!=2||$this->info[$code."_all"]==1){
                        $total_amt+=empty($this->info[$name])?0:$this->info[$name];
                    }
                    $html.="<tr>";
                    $html.="<td>".Yii::t("qc",$row["title"])."</td>";
                    $html.="<td class='text-center'>".$row["max_num"]."</td>";
                    $html.="<td>";
                    $html.=TbHtml::numberField("{$className}[info][{$name}]",$this->info[$name],array("min"=>0,"max"=>$row["max_num"],"class"=>"{$code}_val changeAmt","readonly"=>$ready,"data-off"=>$ready?1:0));
                    $html.="</td>";
                    $html.="<td>";
                    $html.=TbHtml::textArea("{$className}[info][{$name}_remark]",$this->info[$name."_remark"],array("class"=>"{$code}_rmk","readonly"=>$ready));
                    $html.="</td>";
                    $html.="</tr>";
                }
            }
            $html.="<tfoot>";
            $html.="<tr>";
            $html.="<th class='text-right' colspan='2'>合计</th>";
            $html.="<th class='text-center'><b>{$qc_amt}</b></th>";
            $html.="<th class='text-center'><b class='totalAmtText'>{$total_amt}</b></th>";
            $html.="<th>&nbsp;</th>";
            $html.="</tr>";
            $html.="</tfoot>";
        }
        $html.="</tbody></table>";
        return $html;
    }

    public function retrieveData($index)
    {
        $user = Yii::app()->user->id;
        $staffcode = $this->getStaffCode();
        $allcond = Yii::app()->user->validFunction('CN02') ? "" : (empty($staffcode) ? "and lcu='$user'" : "and (lcu='$user' or job_staff like '%$staffcode%')");
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city_allow();
        $sql = "select *,docman$suffix.countdoc('QC',id) as no_of_attm, docman$suffix.countdoc('QCPHOTO',id) as no_of_photo from swo_qc where id=$index and city in ($city) $allcond ";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $this->id = $row['id'];
            $qid = $this->id;
            $this->entry_dt = General::toDate($row['entry_dt']);
            $this->job_staff = $row['job_staff'];
            $this->team = $row['team'];
            $this->month = $row['month'];
            $this->company_id = $row['company_id'];
            $this->company_name = $row['company_name'];
            $this->service_score = $row['service_score'];
            $this->cust_comment = $row['cust_comment'];
            $this->cust_score = $row['cust_score'];
            $this->qc_result = $row['qc_result'];
            $this->env_grade = $row['env_grade'];
            $this->qc_dt = General::toDate($row['qc_dt']);
            $this->cust_sign = $row['cust_sign'];
            $this->qc_staff = $row['qc_staff'];
            $this->remarks = $row['remarks'];
            $this->service_type = $row['service_type'];
            $this->city = $row['city'];
            $this->lcu = $row['lcu'];
            $this->luu = $row['luu'];
            $this->lcd = $row['lcd'];
            $this->lud = $row['lud'];
            $this->no_of_attm['qc'] = $row['no_of_attm'];
            $this->no_of_attm['qcphoto'] = $row['no_of_photo'];

            $sql = "select field_id, field_value, field_blob from swo_qc_info
						where qc_id = $qid 
					";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $infoRows = array();
            if(!empty($rows)){
                foreach ($rows as $infoRow){
                    if(in_array($infoRow["field_id"],$this->blob_fields)){
                        $infoRows[$infoRow["field_id"]]=$infoRow["field_blob"];
                    }else{
                        $infoRows[$infoRow["field_id"]]=$infoRow["field_value"];
                    }
                }
            }
            $this->initData($infoRows);
            return true;
        }else{
            return false;
        }
    }


    protected function getStaffCode() {
        $user = Yii::app()->user->id;
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select b.code from hr$suffix.hr_binding a, hr$suffix.hr_employee b
				where a.user_id='$user' and a.employee_id=b.id and a.city='$city'
				order by a.id
				limit 1
		";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        return $row===false ? '' : $row['code'];
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveQcBoxData($connection);
            if ($this->service_type=='IA' || $this->service_type=='IB') {
                $this->saveQcInfo($connection);
            }
            $this->updateDocman($connection,'QC');
            $this->updateDocman($connection,'QCPHOTO');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

    protected function saveQcBoxData(&$connection)
    {
        $sql = '';
        $city_allow = Yii::app()->user->city_allow();
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from swo_qc where id = :id and city in ({$city_allow})";
                break;
            case 'new':
                $sql = "insert into swo_qc(
							entry_dt, job_staff, team, month, input_dt, company_id, company_name, service_score,
							cust_comment, qc_result, env_grade, qc_dt, cust_sign, qc_staff, cust_score, remarks, 
							service_type, city, luu, lcu
						) values (
							:entry_dt, :job_staff, :team, :month, null, :company_id, :company_name, :service_score,
							:cust_comment, :qc_result, :env_grade, :qc_dt, :cust_sign, :qc_staff, :cust_score, :remarks, 
							:service_type, :city, :luu, :lcu
						)";
                break;
            case 'edit':
                $sql = "update swo_qc set
							entry_dt = :entry_dt, 
							job_staff = :job_staff, 
							team = :team, 
							month = :month, 
							company_id = :company_id, 
							company_name = :company_name, 
							service_score = :service_score,
							cust_score = :cust_score, 
							cust_comment = :cust_comment, 
							qc_result = :qc_result, 
							env_grade = :env_grade, 
							qc_dt = :qc_dt, 
							cust_sign = :cust_sign, 
							qc_staff = :qc_staff, 
							remarks = :remarks,
							city = :city,
							service_type = :service_type,
							luu = :luu 
						where id = :id and city in ({$city_allow})
						";
                break;
        }

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':entry_dt')!==false) {
            $edate = General::toMyDate($this->entry_dt);
            $command->bindParam(':entry_dt',$edate,PDO::PARAM_STR);
        }
        if (strpos($sql,':job_staff')!==false)
            $command->bindParam(':job_staff',$this->job_staff,PDO::PARAM_STR);
        if (strpos($sql,':team')!==false)
            $command->bindParam(':team',$this->team,PDO::PARAM_STR);
        if (strpos($sql,':month')!==false)
            $command->bindParam(':month',$this->month,PDO::PARAM_STR);
        if (strpos($sql,':company_id')!==false) {
            $cid = General::toMyNumber($this->company_id);
            $command->bindParam(':company_id',$cid,PDO::PARAM_INT);
        }
        if (strpos($sql,':company_name')!==false)
            $command->bindParam(':company_name',$this->company_name,PDO::PARAM_STR);
        if (strpos($sql,':service_type')!==false)
            $command->bindParam(':service_type',$this->service_type,PDO::PARAM_STR);
        if (strpos($sql,':service_score')!==false)
            $command->bindParam(':service_score',$this->service_score,PDO::PARAM_STR);
        if (strpos($sql,':cust_score')!==false)
            $command->bindParam(':cust_score',$this->cust_score,PDO::PARAM_STR);
        if (strpos($sql,':cust_comment')!==false)
            $command->bindParam(':cust_comment',$this->cust_comment,PDO::PARAM_STR);
        if (strpos($sql,':qc_result')!==false)
            $command->bindParam(':qc_result',$this->qc_result,PDO::PARAM_STR);
        if (strpos($sql,':env_grade')!==false)
            $command->bindParam(':env_grade',$this->env_grade,PDO::PARAM_STR);
        if (strpos($sql,':qc_dt')!==false) {
            $qdate = General::toMyDate($this->qc_dt);
            $command->bindParam(':qc_dt',$qdate,PDO::PARAM_STR);
        }
        if (strpos($sql,':cust_sign')!==false)
            $command->bindParam(':cust_sign',$this->cust_sign,PDO::PARAM_STR);
        if (strpos($sql,':qc_staff')!==false)
            $command->bindParam(':qc_staff',$this->qc_staff,PDO::PARAM_INT);
        if (strpos($sql,':remarks')!==false)
            $command->bindParam(':remarks',$this->remarks,PDO::PARAM_STR);
        if (strpos($sql,':city')!==false){
            $this->city=empty($this->city)?$city:$this->city;
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        }
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();
        return true;
    }

    protected function saveQcInfo(&$connection)
    {
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        foreach ($this->info as $key=>$value) {
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from swo_qc_info where qc_id = :qc_id";
                    break;
                case 'new':
                    $sql = "insert into swo_qc_info(
								qc_id, field_id, field_value, field_blob, luu, lcu
							) values (
								:qc_id, :field_id, :field_value, :field_blob, :luu, :lcu
							)";
                    break;
                case 'edit':
                    $sql = "insert into swo_qc_info(
								qc_id, field_id, field_value, field_blob, luu, lcu
							) values (
								:qc_id, :field_id, :field_value, :field_blob, :luu, :lcu
							)
							on duplicate key update
								field_value = :field_value, field_blob = :field_blob, luu = :luu
							";
                    break;
            }
            if ($sql != '') {
                $command=$connection->createCommand($sql);
                if (strpos($sql,':qc_id')!==false)
                    $command->bindParam(':qc_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':field_id')!==false)
                    $command->bindParam(':field_id',$key,PDO::PARAM_STR);
                if (strpos($sql,':field_value')!==false) {
                    $val1 = in_array($key, $this->blob_fields) ? '' : $value;
                    $command->bindParam(':field_value',$val1,PDO::PARAM_STR);
                }
                if (strpos($sql,':field_blob')!==false) {
                    $val2 = in_array($key, $this->blob_fields) ? $value : '';
                    $command->bindParam(':field_blob',$val2,PDO::PARAM_LOB);
                }
                if (strpos($sql,':luu')!==false)
                    $command->bindParam(':luu',$uid,PDO::PARAM_STR);
                if (strpos($sql,':lcu')!==false)
                    $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
                $command->execute();
            }
        }
        return true;
    }

    protected function updateDocman(&$connection, $doctype) {
        if ($this->scenario=='new') {
            $docidx = strtolower($doctype);
            if (isset($this->docMasterId[$docidx]) && $this->docMasterId[$docidx] > 0) {
                $docman = new DocMan($doctype,$this->id,get_class($this));
                $docman->masterId = $this->docMasterId[$docidx];
                $docman->updateDocId($connection, $this->docMasterId[$docidx]);
            }
        }
    }

    public function readonly() {
        if ($this->scenario!='new' && ($this->service_type=='IA' || $this->service_type=='IB')) {
            $flag = (isset($this->info['sign_cust']) && !empty($this->info['sign_cust'])) &&
//					(isset($this->info['sign_tech']) && !empty($this->info['sign_tech'])) &&
                (isset($this->info['sign_qc']) && !empty($this->info['sign_qc']));
            return ($this->scenario=='view' || $flag);
        } else {
            return ($this->scenario=='view');
        }
    }

    public function readonlys() {
        if ($this->scenario!='new' && ($this->service_type=='IA' || $this->service_type=='IB')) {
            return true;
        } else {
            return false;
        }
    }

    public function readonlySP() {
        if ($this->scenario!='new' && ($this->service_type=='IA' || $this->service_type=='IB')) {
            $flag = (isset($this->info['sign_cust']) && !empty($this->info['sign_cust'])) ||
                (isset($this->info['sign_tech']) && !empty($this->info['sign_tech'])) ||
                (isset($this->info['sign_qc']) && !empty($this->info['sign_qc']));
            return ($this->scenario=='view' || ($flag && !empty($this->qc_staff)));
        } else {
            return ($this->scenario=='view');
        }
    }

    public function remove($model){
        $id=$model['id'];
        $sql = "delete from swo_qc_info where qc_id =:qc_id and field_id='sign_cust'";
        $connection = Yii::app()->db;
        $command=$connection->createCommand($sql);
        if (strpos($sql,':qc_id')!==false){
            $command->bindParam(':qc_id',$id,PDO::PARAM_INT);
        }
        $command->execute();
    }

	public function isOccupied($index) {
		$rtn = false;
		return $rtn;
	}

	public static function CustSfnList() {
		return array(
		    "1"=>Yii::t("qc","Dissatisfied"),//不满意
		    "2"=>Yii::t("qc","general"),//一般
		    "3"=>Yii::t("qc","satisfied"),//满意
		    "4"=>Yii::t("qc","Very satisfied"),//非常满意
        );
	}

	public static function getCustSfnStrForKey($key) {
        $key="".$key;
        $list = self::CustSfnList();
        if(key_exists($key,$list)){
            return $list[$key];
        }else{
            return $key;
        }
	}

	public static function getInLookStrForKey($key) {
        $key="".$key;
        $list = array(
            "1"=>"包含",
            "2"=>"不包含",
        );
        if(key_exists($key,$list)){
            return $list[$key];
        }else{
            return $key;
        }
	}

	public function getCompanyAddr(){
        if(!empty($this->company_id)){
            $row = Yii::app()->db->createCommand()->select("*")->from("swo_company")
                ->where("id=:id",array(":id"=>$this->company_id))->queryRow();
            if($row){
                $this->company_addr = $row["address"];
            }
        }
    }

	public function printPDF($type="D"){ //调试：I
        $pdf = new MyPDF2('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle($this->company_name);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetFont('stsongstdlight', '', 10);
        $t_margin= $pdf->getHeaderHeight()+7;
        $r_margin=5;
        $l_margin=5;
        $pdf->SetMargins($l_margin, $t_margin, $r_margin);
        $h_margin=15;
        $pdf->SetHeaderMargin($h_margin);
        $f_margin=5;
        $pdf->SetFooterMargin($f_margin);
        // set auto page breaks
        $b_margin=15;
        $pdf->SetAutoPageBreak(TRUE, $b_margin);
        // 设置行高
        $pdf->setCellHeightRatio(2);
        $pdf->AddPage();
        $tbl=$this->getTableHtmlForPDF();
        $pdf->writeHTML($tbl, true, false, false, false, '');
        //$pdf->Ln(10);
        ob_end_clean();
        $pdf->Output($this->service_type.'.pdf',$type);
        die();
    }

    protected function getTableHtmlForPDF(){
	    if($this->service_type=="IA"){
	        $titleName="清洁客户质检表";
        }else{
            $titleName="餐饮灭虫服务质检表";
        }
        $html="<div style=\"text-align: center;font-size: 15px;line-height: 25px;\"><b>{$titleName}</b></div>";
	    $html.="<table align=\"left\" border=\"1\" cellpadding=\"2px\" cellspacing=\"0\" width=\"568px\" >";
        $html.="<colgroup>
					<col width=\"70px\" />
					<col width=\"288px\" />
					<col width=\"60px\" />
					<col width=\"60px\" />
					<col width=\"90px\" />
				</colgroup>";
        $html.="<tr>";
        $html.="<td width=\"70px\"><b>客户名称</b></td>";
        $html.="<td width=\"288px\">".$this->company_name."</td>";
        $html.="<td width=\"60px\" rowspan=\"3\"><b>质检员签名</b></td>";
        $html.="<td width=\"150px\" rowspan=\"3\" colspan=\"2\">";
        $html.=TbHtml::image($this->info['sign_cust'],'QcBoxForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>100,'height'=>50,));;
        $html.="</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><b>客户地址</b></td>";
        $html.="<td>".$this->company_addr."</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><b>评估人</b></td>";
        $html.="<td>".$this->qc_staff."</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><b>评估日期</b></td>";
        $html.="<td colspan=\"4\">".$this->qc_dt."</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><b>最近一次服务日期</b></td>";
        $html.="<td>".$this->info["service_dt"]."</td>";
        $html.="<td rowspan=\"2\"><b>客户签名</b></td>";
        $html.="<td width=\"150px\" rowspan=\"2\" colspan=\"2\">";
        $html.=TbHtml::image($this->info['sign_qc'],'QcBoxForm_info_sign_qc_img',array('id'=>'QcForm_info_sign_qc_img','width'=>100,'height'=>50,));;
        $html.="</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><b>被评估人</b></td>";
        $html.="<td>".$this->job_staff."</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td>&nbsp;</td>";
        $html.="<td style=\"text-align: center;\"><b>考核項目</b></td>";
        $html.="<td style=\"text-align: center;\"><b>分值</b></td>";
        $html.="<td style=\"text-align: center;\"><b>执行情况</b></td>";
        $html.="<td style=\"text-align: center;\"><b>备注</b></td>";
        $html.="</tr>";

        if($this->service_type=="IA"){
            $arr = $this->ia_fields;
        }elseif ($this->service_type=="IB"){
            $arr = $this->ib_fields;
        }
        if(!empty($arr)){
            $qc_amt = 0;//质检总分
            $total_amt = 0;//技术员打分总分
            $strNum = 64;
            foreach ($arr as $code=>$rows){
                $strNum++;
                $rowspan = count($rows["list"])+1;
                $html.="<tr>";
                $html.="<td rowspan=\"{$rowspan}\" style=\"vertical-align: middle;text-align: center;\"><b>".chr($strNum)."</b></td>";
                $html.="<td style=\"background-color: #FDE9D9;text-align: center;\"><b>".Yii::t("qc",$rows["name"])."</b></td>";
                if($rows["type"]==2){
                    if($this->info[$code."_all"]==1){
                        $totalSum = "";
                    }else{
                        $totalSum =$rows["totalSum"];
                        $total_amt+=$totalSum;
                    }
                    $html.="<td style=\"background-color: #FDE9D9;text-align: center;\">";
                    $html.=self::getInLookStrForKey($this->info[$code."_all"]);
                    $html.="</td>";
                    $html.="<td style=\"background-color: #FDE9D9;text-align: center;\">{$totalSum}</td>";
                }else{
                    $html.="<td style=\"background-color: #FDE9D9;text-align: center;\">&nbsp;</td>";
                    $html.="<td style=\"background-color: #FDE9D9;text-align: center;\">&nbsp;</td>";
                }
                $html.="<td style=\"background-color: #FDE9D9;text-align: center;\">&nbsp;</td>";
                $html.="</tr>";
                foreach ($rows["list"] as $row){
                    $name = $row["name"];
                    $qc_amt+=$row["max_num"];
                    if($rows["type"]!=2||$this->info[$code."_all"]==1){
                        $total_amt+=empty($this->info[$name])?0:$this->info[$name];
                    }
                    $html.="<tr>";
                    $html.="<td>".Yii::t("qc",$row["title"])."</td>";
                    $html.="<td style=\"text-align: center;\">".$row["max_num"]."</td>";
                    $html.="<td style=\"text-align: center;\">".$this->info[$name]."</td>";
                    $html.="<td>".$this->info[$name."_remark"]."</td>";
                    $html.="</tr>";
                }
            }
            $html.="<tr>";
            $html.="<td style=\"text-align: right;\" colspan=\"2\">合计</td>";
            $html.="<td style=\"text-align: center;\"><b>{$qc_amt}</b></td>";
            $html.="<td style=\"text-align: center;\"><b>{$total_amt}</b></td>";
            $html.="<td>&nbsp;</td>";
            $html.="</tr>";
        }
        $html.="<tr>";
        $html.="<td><b>客户满意度</b></td>";
        $html.="<td colspan=\"4\">".self::getCustSfnStrForKey($this->info["cust_sfn"])."</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><b>客户意见</b></td>";
        $html.="<td colspan=\"4\">{$this->cust_comment}</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><b>备注</b></td>";
        $html.="<td colspan=\"4\">{$this->remarks}</td>";
        $html.="</tr>";

        $html.="</table>";
	    return $html;
    }
}
