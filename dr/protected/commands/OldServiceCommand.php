<?php
class OldServiceCommand extends CConsoleCommand {

    public function run() {
        $rows = Yii::app()->db->createCommand()
            ->select("b.id,a.contract_no")
            ->from("swo_service_contract_no a")
            ->leftJoin("swo_service b","a.service_id=b.id")
            ->where("b.u_system_id is null and a.status='N'")
            ->queryAll();
        echo "service start:\n";
        if($rows){
            foreach ($rows as $row){
                $uList = Yii::app()->db->createCommand()
                    ->select("contract_id")
                    ->from("lbs_service_contract")
                    ->where("contract_number='{$row["contract_no"]}'")
                    ->queryRow();
                if($uList){
                    Yii::app()->db->createCommand()->update("swo_service",array(
                        "u_system_id"=>$uList["contract_id"]
                    ),"id=:id",array(":id"=>$row["id"]));
                }else{
                    echo "   id:{$row["id"]},contract_no:{$row["contract_no"]},error:not find\n";
                }
            }
        }
        echo "service end:\n";

        $rows = Yii::app()->db->createCommand()
            ->select("b.id,a.contract_no")
            ->from("swo_service_ka_no a")
            ->leftJoin("swo_service_ka b","a.service_id=b.id")
            ->where("b.u_system_id is null and a.status='N'")
            ->queryAll();
        echo "service ka start:\n";
        if($rows){
            foreach ($rows as $row){
                $uList = Yii::app()->db->createCommand()
                    ->select("contract_id")
                    ->from("lbs_service_contract")
                    ->where("contract_number='{$row["contract_no"]}'")
                    ->queryRow();
                if($uList){
                    Yii::app()->db->createCommand()->update("swo_service_ka",array(
                        "u_system_id"=>$uList["contract_id"]
                    ),"id=:id",array(":id"=>$row["id"]));
                }else{
                    echo "   id:{$row["id"]},contract_no:{$row["contract_no"]},error:not find\n";
                }
            }
        }
        echo "service ka end:\n";
    }
}
?>