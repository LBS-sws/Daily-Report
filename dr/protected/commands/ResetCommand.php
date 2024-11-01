<?php
class ResetCommand extends CConsoleCommand {


    //客户资料广州敏捷转移到佛山敏捷
    public function actionShiftGF(){
        $suffix = Yii::app()->params['envSuffix'];
        $codeStr = "'HN23078','HN24216','HN24132','HN24170','HN23103','HN24198','HN24212','HN24208','HN24156','HN23091','HN23100','HN23104','HN23112','HN23113','HN24120','HN24123','HN24125','HN24130','HN24168','HN24141','HN24196','HN24147','HN24148','HN24150','HN24158','HN24163','HN24167','HN24169','HN24181','HN24188','HN24215','HN24218','HD24063','HD24083','HD24058','HD24070','HD23021','HD23035','HD24068','HD24074','HD24076','HD23040','HD24078','HD094','HD24082','HD24062','HD24059','HD24077','HD24087','HD24080','HD23031','HN241161','HN241351','HD240731','HD240751','HN241311','HD240841','HN241452','HO24199','HN24241','HN24242','HN24237','HN24249'";
        echo "start:\n";
        $CSRows = Yii::app()->db->createCommand()->select("id,code")->from("swo_company")
            ->where("city='GZMJ' and code in ({$codeStr})")->queryAll();
        $lud = "2024-10-12 12:00:00";
        if($CSRows){
            foreach ($CSRows as $CSRow){
                echo "ID:{$CSRow["id"]}; CS Code:{$CSRow["code"]}; ";
                $ZZRow = Yii::app()->db->createCommand()->select("id,code")->from("swo_company")
                    ->where("city='FSMJ' and code=:code",array(":code"=>$CSRow["code"]))->queryRow();
                if($ZZRow){//如果佛山敏捷存在该编号
                    //删除佛山敏捷资料
                    Yii::app()->db->createCommand()->delete("swo_company","id=:id",array(":id"=>$ZZRow["id"]));
                    echo "\n Delete:{$ZZRow["id"]}！";
                    //修改客户服务(普通合约)
                    $aa=Yii::app()->db->createCommand()->update("swo_service",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service:{$aa}; ";
                    //修改客户服务(KA合约)
                    $aa=Yii::app()->db->createCommand()->update("swo_service_ka",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service_ka:{$aa}; ";
                    //修改客户服务(ID合约)
                    $aa=Yii::app()->db->createCommand()->update("swo_serviceid",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "service_id:{$aa}; ";
                    //修改投诉个案
                    $aa=Yii::app()->db->createCommand()->update("swo_followup",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "followup:{$aa}; ";
                    //修改物流配送
                    $aa=Yii::app()->db->createCommand()->update("swo_logistic",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "logistic:{$aa}; ";
                    //修改品鉴记录
                    $aa=Yii::app()->db->createCommand()->update("swo_qc",array(
                        "company_id"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"company_id=".$ZZRow["id"]);
                    echo "qc:{$aa}; ";
                    //修改付款/收款记录
                    $aa=Yii::app()->db->createCommand()->update("account{$suffix}.acc_trans_info",array(
                        "field_value"=>$CSRow["id"],
                        "lud"=>$lud
                    ),"field_id='payer_id' and field_value=".$ZZRow["id"]);
                    echo "trans_info:{$aa}; ";
                }
                echo "\n Update FSMJ! ";
                //修改广州敏捷客户到佛山敏捷
                $aa=Yii::app()->db->createCommand()->update("swo_company",array(
                    "city"=>"FSMJ",
                    "lud"=>$lud
                ),"id=:id",array(":id"=>$CSRow["id"]));
                echo "company:{$aa}; ";
                //修改客户服务(普通合约)
                $aa=Yii::app()->db->createCommand()->update("swo_service",array(
                    "city"=>"FSMJ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service:{$aa}; ";
                //修改客户服务(KA合约)
                $aa=Yii::app()->db->createCommand()->update("swo_service_ka",array(
                    "city"=>"FSMJ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service_ka:{$aa}; ";
                //修改客户服务(ID合约)
                $aa=Yii::app()->db->createCommand()->update("swo_serviceid",array(
                    "city"=>"FSMJ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "service_id:{$aa}; ";
                //修改投诉个案
                $aa=Yii::app()->db->createCommand()->update("swo_followup",array(
                    "city"=>"FSMJ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "followup:{$aa}; ";
                //修改物流配送
                $aa=Yii::app()->db->createCommand()->update("swo_logistic",array(
                    "city"=>"FSMJ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "logistic:{$aa}; ";
                //修改品鉴记录
                $aa=Yii::app()->db->createCommand()->update("swo_qc",array(
                    "city"=>"FSMJ",
                    "lud"=>$lud
                ),"company_id=".$CSRow["id"]);
                echo "qc:{$aa}; \n\n";
            }
        }
        echo "end:\n";
    }

    //同步LBS客户资料的唯一标识改成U系统id
    public function actionUCompanyID(){
        $rows = Yii::app()->db->createCommand()->select("id,code,city,jd_customer_id")
            ->from("swo_company")->where("u_customer_id is null")->queryAll();
        echo "start\n";
        if($rows){
            foreach ($rows as $row){
                echo "ID:".$row["id"];
                $uRow = Yii::app()->db->createCommand()->select("customer_id")
                    ->from("lbs_company_customer")
                    ->where("customer_code=:code",array(":code"=>$row["code"]."-".$row["city"]))->queryRow();
                if($uRow){
                    echo " - U_ID:".$uRow["customer_id"];
                    Yii::app()->db->createCommand()->update("swo_company",array(
                        "u_customer_id"=>$uRow["customer_id"],
                        "del_num"=>0,
                    ),"id=".$row["id"]);
                    Yii::app()->db->createCommand()->update("lbs_company_customer",array(
                        "lbs_id"=>$row["id"]
                    ),"customer_id=".$uRow["customer_id"]);
                }else{
                    echo " - U_ID:none";
                }
                $jdRow = Yii::app()->db->createCommand()->select("id,field_value")
                    ->from("swo_send_set_jd")
                    ->where("table_id='{$row['id']}' and set_type='customer' and field_id='jd_customer_id'")->queryRow();
                if($jdRow){
                    Yii::app()->db->createCommand()->update("swo_company",array(
                        "jd_customer_id"=>$jdRow["field_value"]
                    ),"id=".$row["id"]);
                    Yii::app()->db->createCommand()->delete("swo_send_set_jd","id=".$jdRow["id"]);
                }
                echo "\n";
            }
        }
        echo "end\n";
    }
}
?>