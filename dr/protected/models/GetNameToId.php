<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/5/23 0023
 * Time: 11:00
 */
class GetNameToId{
    //获取办事处名字
    public static function getStaticOfficeType(){
        return array(
			"all"=>"全部",
			"office_one"=>"本部",
			"office_two"=>"办事处",
		);
    }
	
    //获取办事处名字
    public static function getOfficeNameForID($id){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("name")
            ->from("hr{$suffix}.hr_office")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["name"];
        }
        return "本部";
    }
	
    //获取办事处列表
    public static function getOfficeNameListForCity($city){
		$data = array(""=>"本部");
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("id,name")
            ->from("hr{$suffix}.hr_office")
            ->where("city=:city",array(":city"=>$city))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $data[$row["id"]]=$row["name"];
            }
        }
        return $data;
    }
	
    //获取员工名字
    public static function getEmployeeNameForId($id){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("code,name")
            ->from("hr{$suffix}.hr_employee")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["name"]." ({$row["code"]})";
        }
        return $id;
    }
    //获取员工名字(多个员工)
    public static function getEmployeeNameForStr($str){
        $suffix = Yii::app()->params['envSuffix'];
        $search = explode("~",$str);
        $search = implode(",",$search);
        $search = empty($search)?0:$search;
        $rows = Yii::app()->db->createCommand()->select("code,name")
            ->from("hr{$suffix}.hr_employee")
            ->where("id in ($search)")->queryAll();
        if($rows){
            $staff="";
            foreach ($rows as $row){
                $staff.=$row["name"]." ({$row["code"]})";
            }
            return $staff;
        }
        return $str;
    }

    //获取公司名字
    public static function getCompanyNameForId($id){
        $row = Yii::app()->db->createCommand()->select("code,name")->from("swo_company")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["code"].$row["name"];
        }
        return $id;
    }

    //获取性质1名字
    public static function getNatureOneNameForId($id){
        $row = Yii::app()->db->createCommand()->select("description")->from("swo_nature")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["description"];
        }
        return $id;
    }

    //获取性质2名字
    public static function getNatureTwoNameForId($id){
        $row = Yii::app()->db->createCommand()->select("name")->from("swo_nature_type")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["name"];
        }
        return $id;
    }

    //获取客户类别1名字
    public static function getCustOneNameForId($id){
        $row = Yii::app()->db->createCommand()->select("description")->from("swo_customer_type")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["description"];
        }
        return $id;
    }

    //获取客户类别2名字
    public static function getCustTwoNameForId($id){
        $row = Yii::app()->db->createCommand()->select("cust_type_name")->from("swo_customer_type_twoname")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["cust_type_name"];
        }
        return $id;
    }

    //获取服务内容名字
    public static function getProductNameForId($id){
        $row = Yii::app()->db->createCommand()->select("code,description")->from("swo_product")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            return $row["code"]." ".$row["description"];
        }
        return $id;
    }

    //获取服务金额类型名字
    public static function getPaidTypeForId($id){
        $list = array(
            'M'=>Yii::t('service','Monthly'),
            'Y'=>Yii::t('service','Yearly'),
            '1'=>Yii::t('service','One time'),
        );
        if(key_exists($id,$list)){
            return $list[$id];
        }
        return $id;
    }

    //获取客户服务状态
    public static function getServiceStatusList(){
        $list = array(
            'N'=>Yii::t('report','New'),
            'C'=>Yii::t('report','Renewal'),
            'S'=>Yii::t('report','Suspended'),
            'R'=>Yii::t('report','Resume'),
            'A'=>Yii::t('report','Amendment'),
            'T'=>Yii::t('report','Terminate'),
        );
        return $list;
    }

    //获取客户服务状态
    public static function getServiceStatusForKey($id){
        $list = self::getServiceStatusList();
        if(key_exists($id,$list)){
            return $list[$id];
        }
        return $id;
    }

    //获取物流配送任务的类别
    public static function getTaskTypeList(){
        $list = array(
            'NIL'=>Yii::t('code','Nil'),
            'PAPER'=>Yii::t('code','Paper'),
            'SOAP'=>Yii::t('code','Soap'),
            'FLOOR'=>Yii::t('code','Floor Cleaner'),
            'MAINT'=>Yii::t('code','Maintenance'),
            'UNINS'=>Yii::t('code','Uninstallion'),
            'RELOC'=>Yii::t('code','Relocation'),
            'REPLA'=>Yii::t('code','Replacement'),
            'PURIS'=>Yii::t('code','Puriscent'),
            'PERFU'=>Yii::t('code','Perfume'),
            'OTHER'=>Yii::t('code','Other'),
        );
        return $list;
    }

    //获取物流配送任务的类别
    public static function getTaskTypeForKey($id){
        $list = self::getTaskTypeList();
        if(key_exists($id,$list)){
            return $list[$id];
        }
        return $id;
    }

    //获取需安装名字
    public static function getNeedInstallForId($id){
        $list = array(
            'N'=>Yii::t('misc','No'),
            ''=>Yii::t('misc','No'),
            'Y'=>Yii::t('misc','Yes')
        );
        if(key_exists($id,$list)){
            return $list[$id];
        }
        return $id;
    }

    //获取需安装名字
    public static function getUserStatusForKey($id){
        $list = array(
            'A'=>Yii::t('misc','Active'),
            'I'=>Yii::t('misc','Inactive')
        );
        if(key_exists($id,$list)){
            return $list[$id];
        }
        return $id;
    }

    //获取管辖城市下的所有客户名称
    public static function getCompanyList($city_allow="",$type="id"){
        $list=array();
        $whereSql = "a.id>0";
        if(!empty($city_allow)){
            $whereSql = "a.city in ({$city_allow})";
        }
        $companyRows = Yii::app()->db->createCommand()->select("a.*")
            ->from("swo_company a")
            ->where($whereSql)
            ->queryAll();
        if($companyRows){
            foreach ($companyRows as $row){
                $row["codeAndName"]=$row["code"].$row["name"];
                $list[$row[$type]] = $row;
            }
        }
        return $list;
    }

    //获取需安装名字
    public static function getContractTypeList($type="all"){
        switch ($type){
            case "all":
                $list = array(
                    '0'=>Yii::t('service','normal contract'),//普通合约
                    '1'=>Yii::t('service','ka contract'),//KA合约
                    '9'=>Yii::t('service','cross contract')//外包合约
                );
                break;
            case "ka":
                $list = array(
                    '1'=>Yii::t('service','ka contract'),//KA合约
                    '9'=>Yii::t('service','cross contract')//外包合约
                );
                break;
            default:
                $list = array(
                    '0'=>Yii::t('service','normal contract'),//普通合约
                    '9'=>Yii::t('service','cross contract')//外包合约
                );
        }
        return $list;
    }

    //获取需安装名字
    public static function getContractTypeNameForType($type){
        $list = self::getContractTypeList("all");
        $type = "".$type;
        if(key_exists($type,$list)){
            return $list[$type];
        }
        return $type;
    }
}