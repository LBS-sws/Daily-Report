<?php
//获取新版U系统数据的所有接口
class SystemU {

    public static function getCurlIP(){
        return Yii::app()->params['uCurlIP'];
    }

    //获取发票内容
    public static function getData($city, $start, $end, $customer='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetInvoice/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>self::resetCityForPre($city)
        );
        if (!empty($customer)) $data['customer'] = $customer;
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取INV类型的详情
    public static function getInvDataDetail($start, $end, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetInvInvoice/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取INV类型的城市汇总
    public static function getInvDataCityAmount($start, $end, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetInvInvoiceCityAmount/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取INV类型的城市汇总 - 办事处
    public static function getInvDataOfficeCityAmount($start, $end, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetInvInvoiceOfficeAmount/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取INV类型的城市(月份)汇总
    public static function getInvDataCityMonth($start, $end, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetInvInvoiceCityMonth/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取INV类型的城市(周)汇总
    public static function getInvDataCityWeek($start, $end, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetInvInvoiceCityWeek/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取服务单月数据
    public static function getUServiceMoney($start, $end, $city='',$printBool=false,$type=0) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetUServiceMoney/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "type"=>$type,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取服务单月数据 - 办事处
    public static function getUServiceOfficeMoney($start, $end, $city='',$printBool=false,$type=0) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetUServiceMoneyForOffice/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "type"=>$type,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取U系统的服务单数据(外包人员)-汇总
    public static function getOutsourceCountMoney($start, $end, $staffList='', $city='',$printBool=false,$type=0) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetOutsourcingUServiceMoney/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "staffs"=>$staffList,
            "type"=>$type,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取U系统的服务单数据(外包人员)-详情
    public static function getOutsourceServiceMoney($start, $end, $staffList='', $city='',$printBool=false,$type=0) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetOutsourcingDetailUServiceMoney/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "staffs"=>$staffList,
            "type"=>$type,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取服务单月数据（月為鍵名)
    public static function getUServiceMoneyToMonth($start, $end, $city='',$printBool=false,$type=0) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetUServiceMoneyToMonthEx/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "type"=>$type,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取服务单月数据（周為鍵名)
    public static function getUServiceMoneyToWeek($start, $end, $city='',$printBool=false,$type=1) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetUServiceMoneyForWeek/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "type"=>$type,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取技术员金额（技术员编号為鍵名)
    public static function getTechnicianMoney($start, $end, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetTechnicianMoney/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取技术员金额U系统详情（需要自己分开服务单）
    public static function getTechnicianDetail($start, $end, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetTechnicianDetail/index';
        $data = array(
            "key"=>$key,
            "begin"=>$start,
            "end"=>$end,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //获取技术员的创新金额、夜单金额、服务金额
    public static function getTechnicianSNC($year, $month, $city='',$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.GetUServiceMoneyMonth/index';
        $data = array(
            "key"=>$key,
            "year"=>$year,
            "month"=>$month,
            "city"=>empty($city)||$city=="all"?"":self::resetCityForPre($city)
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //今月(IA、IB)服务数目
    public static function countIAIB($year, $month,$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.'/api/lbs.CountIAIB/index';
        $data = array(
            "key"=>$key,
            "year"=>$year,
            "month"=>$month
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out, true);
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['data'] = $json["data"];
                $rtn['message'] = self::getJsonError(json_last_error());
            }else{
                $rtn['data'] = array();
                $rtn['message'] = isset($json["message"])?$json["message"]:$out;
                $out="Url:".$url."\r\n".$out;
                Yii::log("Url:{$url};\r\nDataStr:{$data_string}",CLogger::LEVEL_WARNING);
                throw new CHttpException("派单系统异常",$out);
            }
        }
        return $rtn;
    }

    //老版查询，暂时不使用
    public static function getActualAmount($year, $month,$printBool=false) {
        $rtn = array('message'=>'', 'data'=>array());

        $key = Yii::app()->params['unitedKey'];
        $root = Yii::app()->params['unitedRootURL'];
        $url = $root.'/remote/getActualAmount.php';
        $data = array(
            "key"=>$key,
            "year"=>$year,
            "month"=>$month,
        );
        $data_string = json_encode($data);
        $curlStartDate = date_format(date_create(),"Y/m/d H:i:s");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        $out = curl_exec($ch);
        if($printBool){//测试专用
            self::printCurl($url,$data,$out,$curlStartDate);
        }
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $json = json_decode($out);
            $rtn['data'] = json_decode($out, true);
            $rtn['message'] = self::getJsonError(json_last_error());
        }

        return $rtn;
    }

    //给U系统发送交叉派单的数据
    public static function sendUForCross($data) {
        $rtn = array('message'=>'', 'code'=>400);
        $key = self::generate_key();
        $root = Yii::app()->params['uCurlRootURL'];
        //$url = $root.'/index.php/api/lbs.CrossAudit/getAuditInfo';//单条记录
        $url = $root.'/index.php/api/lbs.CrossAudit/getAuditInfo';//N条记录
        $data["key"] = $key;
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data_string),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
            $rtn['outData'] = $rtn['message'];
        } else {
            $rtn['outData'] = $out;
            $json = json_decode($out, true);
            $rtn['message'] = isset($json["msg"])?$json["msg"]:"";
            if(isset($json["code"])&&$json["code"]==200){
                $rtn['code'] = 200;
            }
        }

        $rtn["message"] = mb_strlen($rtn["message"],'UTF-8')>250?mb_substr($rtn["message"],0,250,'UTF-8'):$rtn["message"];
        $sqlData=array(
            "status_type"=>$rtn['code']==200?"C":"E",
            "info_type"=>"cross",
            "info_url"=>$url,
            "data_content"=>json_encode($data),
            "out_content"=>$rtn['outData'],
            "message"=>$rtn['message'],
            "lcu"=>Yii::app()->user->id,
        );
        $suffix = Yii::app()->params['envSuffix'];
        Yii::app()->db->createCommand()->insert("hr{$suffix}.hr_api_curl",$sqlData);
        return $rtn;
    }

    public static function getJsonError($error) {
        switch ($error) {
            case JSON_ERROR_NONE:
                return 'Success';
            case JSON_ERROR_DEPTH:
                return ' - Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return ' - Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return ' - Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return ' - Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return' - Unknown error ('.$error.')';
        }
    }

    //生成key,每10分钟一变
    public static function generate_key(){
        $ip = self::getCurlIP();
        $interval = 600; // 10分钟的秒数
        $secret_key = '5dd6f4b8ea2eda324a5629325e8868a8'; // 加密密钥

        //生成key
        $salt = floor(time() / $interval) * $interval; // 使用10分钟为间隔的时间戳作为盐

        $ip_split = explode('.', $ip);
        $hexip = sprintf('%02x%02x%02x%02x', $ip_split[0], $ip_split[1], $ip_split[2], $ip_split[3]);
        $key = hash('sha256', $ip . $salt . $hexip);

        //加密发送时间戳
        $encryptedData = openssl_encrypt($salt, 'AES-128-ECB', $secret_key, OPENSSL_RAW_DATA);
        $encrypted = base64_encode($encryptedData);

        return $key.'.'.$encrypted;
    }

    private static function printCurl($url,$data,$out,$curlStartDate){
        $curlEndDate = date_format(date_create(),"Y/m/d H:i:s");
        $curlDateLength = strtotime($curlEndDate)-strtotime($curlStartDate);
        echo "请求时间：".$curlStartDate;
        echo "<br/>";
        echo "响应时间：".$curlEndDate;
        echo "<br/>";
        echo "响应时长：".$curlDateLength."(秒)";
        echo "<br/>";
        echo "请求IP：".self::getCurlIP();
        echo "<br/>";
        echo "请求url：{$url}";
        echo "<br/>";
        echo "请求data：";
        echo "<br/>";
        var_dump($data);
        echo "<br/>";
        echo "<br/>";
        $bool = true;
        if(json_decode($out,true)!==false){
            $json = json_decode($out,true);
            if(isset($json["code"])&&isset($json["data"])&&$json["code"]==200){
                echo "返回数组：";
                echo "<br/>";
                var_dump($json["data"]);
            }
        }
        if($bool){
            echo "<br/>";
            echo "<br/>";
            echo "响应数据：";
            echo "<br/>";
            echo $out;
            echo "<br/>";
            echo "<br/>";
            echo "<br/>";
        }
        die();
    }

    private static function resetCityForPre($city){
        return str_replace("'","",$city);
    }
}
?>