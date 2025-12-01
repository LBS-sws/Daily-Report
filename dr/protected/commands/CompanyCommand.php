<?php
class CompanyCommand extends CConsoleCommand {
    public function run() {
        $date = date_format(date_create(),"Y-m-d H:i");
echo $date."\n";
        if($date=="2024-10-14 17:26"){
            $model = new CustomerForm();
            $model->sendAllCustomerToJD('',122752);
            Yii::app()->end();
        }
        $this->delRepeatCompany();
    }

    protected function delRepeatCompany(){
        $suffix = Yii::app()->params['envSuffix'];
        $lud = "2024-10-14 12:00:00";
        $lists = array(
            array("saveID"=>"24970","delID"=>"6641","code"=>"001FCB1"),
            array("saveID"=>"27630","delID"=>"9888","code"=>"001LS"),
            array("saveID"=>"205","delID"=>"7361","code"=>"001PRB"),
            array("saveID"=>"24974","delID"=>"458","code"=>"001WJB1"),
            array("saveID"=>"457","delID"=>"24989","code"=>"002MDB"),
            array("saveID"=>"25002","delID"=>"150","code"=>"002YCB"),
            array("saveID"=>"61465","delID"=>"38994","code"=>"002YJY2"),
            array("saveID"=>"58152","delID"=>"7369","code"=>"003SLY0"),
            array("saveID"=>"58153","delID"=>"26580","code"=>"003SLY1"),
            array("saveID"=>"24699","delID"=>"698","code"=>"003XFLB"),
            array("saveID"=>"25029","delID"=>"6954","code"=>"003YXJB"),
            array("saveID"=>"25042","delID"=>"1559","code"=>"005LTJ1"),
            array("saveID"=>"28285","delID"=>"2835","code"=>"005MSJ"),
            array("saveID"=>"28286","delID"=>"2841","code"=>"005QH"),
            array("saveID"=>"8212","delID"=>"146","code"=>"005TG01"),
            array("saveID"=>"25058","delID"=>"30330","code"=>"007DFLC"),
            array("saveID"=>"24728","delID"=>"5451","code"=>"012LLB"),
            array("saveID"=>"28076","delID"=>"6820","code"=>"017PY"),
            array("saveID"=>"28077","delID"=>"6818","code"=>"017PY2"),
            array("saveID"=>"57779","delID"=>"54771","code"=>"01AYHM01"),
            array("saveID"=>"58574","delID"=>"57063","code"=>"01FFYB02"),
            array("saveID"=>"58610","delID"=>"54573","code"=>"01XDCS01"),
            array("saveID"=>"62025","delID"=>"55053","code"=>"01XYHJ31"),
            array("saveID"=>"1952","delID"=>"25235","code"=>"028XFB"),
            array("saveID"=>"58429","delID"=>"54938","code"=>"02CBAA01"),
            array("saveID"=>"58376","delID"=>"54749","code"=>"02JLY01"),
            array("saveID"=>"58970","delID"=>"54575","code"=>"02JYXD16"),
            array("saveID"=>"58393","delID"=>"57118","code"=>"02XYGX26"),
            array("saveID"=>"61836","delID"=>"7370","code"=>"031MDB"),
            array("saveID"=>"58319","delID"=>"54572","code"=>"03SSWC01"),
            array("saveID"=>"62029","delID"=>"55084","code"=>"03XYHS04"),
            array("saveID"=>"12298","delID"=>"9563","code"=>"041LKS"),
            array("saveID"=>"59237","delID"=>"33946","code"=>"046SND1"),
            array("saveID"=>"448","delID"=>"748","code"=>"057HYB"),
            array("saveID"=>"25434","delID"=>"973","code"=>"059XWH1"),
            array("saveID"=>"57472","delID"=>"57281","code"=>"05SLSJ55"),
            array("saveID"=>"57558","delID"=>"57433","code"=>"05YJYT02"),
            array("saveID"=>"25475","delID"=>"8145","code"=>"069ZWY1"),
            array("saveID"=>"58335","delID"=>"53548","code"=>"06TQHY04"),
            array("saveID"=>"58337","delID"=>"49991","code"=>"06YHZC01"),
            array("saveID"=>"59632","delID"=>"29515","code"=>"070FBH"),
            array("saveID"=>"25496","delID"=>"1301","code"=>"075YHB"),
            array("saveID"=>"62034","delID"=>"55252","code"=>"07XYKF21"),
            array("saveID"=>"58332","delID"=>"56337","code"=>"07YSKX01"),
            array("saveID"=>"12056","delID"=>"1934","code"=>"080QF1"),
            array("saveID"=>"24938","delID"=>"56204","code"=>"090MDB"),
            array("saveID"=>"38011","delID"=>"25669","code"=>"1188JBZ"),
            array("saveID"=>"36223","delID"=>"36866","code"=>"11NWS"),
            array("saveID"=>"4197","delID"=>"39784","code"=>"1330HL"),
            array("saveID"=>"25883","delID"=>"38223","code"=>"13HYXZDJ"),
            array("saveID"=>"61372","delID"=>"61373","code"=>"14DZGDX1"),
            array("saveID"=>"62086","delID"=>"62087","code"=>"14DZJSK1"),
            array("saveID"=>"60660","delID"=>"60661","code"=>"14SHJMY1"),
            array("saveID"=>"62292","delID"=>"62291","code"=>"14WYNHC1"),
            array("saveID"=>"7873","delID"=>"23493","code"=>"15HELLO"),
            array("saveID"=>"36362","delID"=>"37587","code"=>"16ZYMR01"),
            array("saveID"=>"52780","delID"=>"57676","code"=>"17DHTXXZ"),
            array("saveID"=>"10492","delID"=>"24637","code"=>"17JES"),
            array("saveID"=>"49926","delID"=>"49860","code"=>"CCANG001"),
            array("saveID"=>"49605","delID"=>"49603","code"=>"CCHMG001"),
            array("saveID"=>"58400","delID"=>"56750","code"=>"CCTED001"),
            array("saveID"=>"21519","delID"=>"15060","code"=>"CYTCX00"),
            array("saveID"=>"58477","delID"=>"56892","code"=>"DXSS0001"),
            array("saveID"=>"15981","delID"=>"22089","code"=>"FT0017"),
            array("saveID"=>"7343","delID"=>"16866","code"=>"HDGLC002"),
            array("saveID"=>"65889","delID"=>"65888","code"=>"MHDFJ001"),
            array("saveID"=>"58472","delID"=>"56801","code"=>"XCMHJ001"),
        );
        //array("saveID"=>"58472","delID"=>"56801","code"=>"XCMHJ001"),
        foreach ($lists as $list){
            echo $list['code'].":";
            $setRows = Yii::app()->db->createCommand()->select("id")->from("swo_company")
                ->where("id in ({$list['saveID']},{$list['delID']}) and code=:code",array(":code"=>$list['code']))
                ->queryAll();
            if(count($setRows)==2){
                echo "start~~";
                //删除资料
                Yii::app()->db->createCommand()->delete("swo_company","id=:id",array(":id"=>$list['delID']));
                //修改客户服务(普通合约)
                Yii::app()->db->createCommand()->update("swo_service",array(
                    "company_id"=>$list["saveID"],
                    "lud"=>$lud
                ),"company_id=".$list["delID"]);
                //修改客户服务(KA合约)
                Yii::app()->db->createCommand()->update("swo_service_ka",array(
                    "company_id"=>$list["saveID"],
                    "lud"=>$lud
                ),"company_id=".$list["delID"]);
                //修改客户服务(ID合约)
                Yii::app()->db->createCommand()->update("swo_serviceid",array(
                    "company_id"=>$list["saveID"],
                    "lud"=>$lud
                ),"company_id=".$list["delID"]);
                //修改投诉个案
                Yii::app()->db->createCommand()->update("swo_followup",array(
                    "company_id"=>$list["saveID"],
                    "lud"=>$lud
                ),"company_id=".$list["delID"]);
                //修改物流配送
                Yii::app()->db->createCommand()->update("swo_logistic",array(
                    "company_id"=>$list["saveID"],
                    "lud"=>$lud
                ),"company_id=".$list["delID"]);
                //修改品鉴记录
                Yii::app()->db->createCommand()->update("swo_qc",array(
                    "company_id"=>$list["saveID"],
                    "lud"=>$lud
                ),"company_id=".$list["delID"]);
                //修改付款/收款记录
                Yii::app()->db->createCommand()->update("account{$suffix}.acc_trans_info",array(
                    "field_value"=>$list["saveID"],
                    "lud"=>$lud
                ),"field_id='payer_id' and field_value=".$list["delID"]);
                echo "success!";
            }else{
                echo "error!";
            }
            echo "\n";
        }
    }
}
?>