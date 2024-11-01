<?php
class ResetCommand extends CConsoleCommand {

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
                        "u_customer_id"=>$uRow["customer_id"]
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