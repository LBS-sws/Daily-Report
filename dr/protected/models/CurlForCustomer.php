<?php
class CurlForCustomer extends CurlForJD{
    protected $info_type="customer";

    //日常费用银行确认
    public function sendJDCurlForCustomer($model){
        $curlData=$this->getDataForCustomerModel($model);
        $data = array("data"=>$curlData);
        $rtn = $this->sendData($data,"/kapi/v2/lbs/basedata/bd_customer/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //日常费用银行确认
    public function sendJDCurlForCustomerData($curlData){
        $data = array("data"=>$curlData);
        $rtn = $this->sendData($data,"/kapi/v2/lbs/basedata/bd_customer/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    public static function getJDCityCodeForCity($city){
        $suffix = Yii::app()->params['envSuffix'];
        $list = Yii::app()->db->createCommand()->select("field_value")
            ->from("security{$suffix}.sec_city_info")
            ->where("code=:code and field_id='JD_city'",array(':code'=>$city))
            ->queryRow();
        if($list){
            return $list["field_value"];
        }else{
            return "";
        }
    }

    public static function getJDSetValueForIAF($table_id,$file_id){
        $suffix = Yii::app()->params['envSuffix'];
        $list = Yii::app()->db->createCommand()->select("field_value")
            ->from("account{$suffix}.acc_send_set_jd")
            ->where("table_id=:table_id and field_id=:field_id",array(':table_id'=>$table_id,':field_id'=>$file_id))
            ->queryRow();
        if($list){
            return $list["field_value"];
        }else{
            return "";
        }
    }

    public static function getEmployeeCodeForID($id){
        $suffix = Yii::app()->params['envSuffix'];
        $list = Yii::app()->db->createCommand()->select("code")
            ->from("hr{$suffix}.hr_employee")
            ->where("id=:id",array(':id'=>$id))
            ->queryRow();
        if($list){
            return $list["code"];
        }else{
            return "";
        }
    }

    public function getDataForCustomerModel($model){
        $curlData=array(
            "lbs_apikey"=>$model->id,
            "number"=>$model->code,//编码
            "name"=>$model->name,//名称
            "status"=>"C",//数据状态 [A:暂存, B:已提交, C:已审核]
            "enable"=>$model->status==2?0:1,//使用状态 [0:禁用, 1:可用]
            "simplename"=>$model->full_name,//简称

            "bizfunction"=>"1,2,3,4",//业务职能 [1:销售, 2:结算, 3:付款, 4:收货]
            "type"=>"1",//伙伴类型 [1:法人企业, 2:非法人企业, 3:非企业单位, 4:个人, 5:个体户]

            "createorg_number"=>"LBSGL",//创建组织.编码
            //"internal_company_number"=>"",//内部业务单元.编码
            //"societycreditcode"=>"",//统一社会信用代码
            "tx_register_no"=>$model->tax_reg_no,//纳税人识别号
            "linkman"=>$model->cont_name,//联系人
            "bizpartner_phone"=>$model->cont_phone,//联系电话
            "postal_code"=>$model->email,//电子邮箱
            "address"=>$model->address,//电子邮箱(金蝶接口未有)
            "group_code"=>$model->group_id,//集团编号(金蝶接口未有)
            "group_name"=>$model->group_name,//集团名称(金蝶接口未有)
            /*
            "entry_groupstandard"=>array(//分类标准
                "groupid_number"=>"",//分类.编码
                "standardid_number"=>"",//分类标准.编码
            ),//分类标准
            */
        );
        return $curlData;
    }
}
