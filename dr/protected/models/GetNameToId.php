<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/5/23 0023
 * Time: 11:00
 */
class GetNameToId{
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

    //获取需安装名字
    public static function getNeedInstallForId($id){
        $list = array(
            ''=>Yii::t('misc','No'),
            'Y'=>Yii::t('misc','Yes')
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
}