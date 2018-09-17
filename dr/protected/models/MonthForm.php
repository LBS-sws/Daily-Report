<?php

class MonthForm extends CFormModel
{
    public $id;
    public $year_no;
    public $month_no;
    public $record = array();
    public $excel=array();
    public $market;
    public $legwork;
    public $service;
    public $personnel;
    public $finance;
    public $other;

    public function attributeLabels()
    {
        return array(
            'year_no'=>Yii::t('report','Year'),
            'month_no'=>Yii::t('report','Month'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, year_no, month_no,market,legwork,service,personnel,finance,other','safe'),
            array('record','validateRecord'),
        );
    }

    public function validateRecord($attribute, $params){
        $message = '';
        foreach ($this->record as $data) {
            if (isset($data['updtype']) && $data['updtype']=='M') {
                if (isset($data['fieldtype'])) {
                    switch($data['fieldtype']) {
                        case 'N':
                            if (isset($data['datavalue']) && !empty($data['datavalue']) && !is_numeric($data['datavalue'])) {
                                $message = $data['name'].Yii::t('monthly',' is invalid');
                                $this->addError($attribute,$message);
                            }
                            break;
                    }
                }
            }
        }
    }
    public function  retrieveZong($index){



    }
    public function retrieveData($index,$city) {
        $sql = "select a.year_no, a.month_no, b.id, b.hdr_id, b.data_field, b.data_value, c.name, c.upd_type, c.field_type, b.manual_input , c.excel_row  
				from swo_monthly_hdr a, swo_monthly_dtl b, swo_monthly_field c 
				where a.id=$index and a.city='$city'
				and a.id=b.hdr_id and b.data_field=c.code
				and c.status='Y'
				order by c.excel_row
			";
        $rowss = Yii::app()->db->createCommand($sql)->queryAll();

        $sql="select b.month_no, c.excel_row, a.data_value, c.field_type ,c.name
                    from 
                        swo_monthly_dtl a, swo_monthly_hdr b, swo_monthly_field c  				  
                    where b.id='$index' and b.city='$city'
                    and b.id=a.hdr_id and a.data_field=c.code
                    and c.status='Y'
                    order by c.excel_row ";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $sql="select * from swo_monthly_comment where hdr_id=$index";
        $ros = Yii::app()->db->createCommand($sql)->queryAll();
        if(empty($rows[64])){
            $b3=intval($rows[0]['data_value']);
            $b4=intval($rows[1]['data_value']);
            $b5=intval($rows[2]['data_value']);
            $b6=intval($rows[3]['data_value']);
            $b7=intval($rows[4]['data_value']);
            $b8=intval($rows[5]['data_value']);
            $b9=intval($rows[6]['data_value']);
            $b10=intval($rows[7]['data_value']);
            $b11=intval($rows[8]['data_value']);
            $b12=intval($rows[9]['data_value']);
            $b13=intval($rows[10]['data_value']);
            $b14=intval($rows[11]['data_value']);
            $b15=intval($rows[12]['data_value']);
            $b16=intval($rows[13]['data_value']);
            $b17=intval($rows[14]['data_value']);
            $b18=intval($rows[15]['data_value']);
            $b19=intval($rows[16]['data_value']);
            $b20=intval($rows[17]['data_value']);
            $b21=intval($rows[18]['data_value']);
            $b22=intval($rows[19]['data_value']);
            $b23=intval($rows[20]['data_value']);
            $b24=intval($rows[21]['data_value']);
            $b25=intval($rows[22]['data_value']);
            $b26=intval($rows[23]['data_value']);
            $b27=intval($rows[24]['data_value']);
            $b28=intval($rows[25]['data_value']);
            $b29=intval($rows[26]['data_value']);
            $b31=intval($rows[27]['data_value']);
            $b32=intval($rows[28]['data_value']);
            $b33=intval($rows[29]['data_value']);
            $b34=intval($rows[30]['data_value']);
            $b36=intval($rows[31]['data_value']);
            $b37=intval($rows[32]['data_value']);
            $b38=intval($rows[33]['data_value']);
            $b40=intval($rows[34]['data_value']);
            $b41=intval($rows[35]['data_value']);
            $b42=intval($rows[36]['data_value']);
            $b43=intval($rows[37]['data_value']);
            $b44=intval($rows[38]['data_value']);
            $b45=intval($rows[39]['data_value']);
            $b46=intval($rows[40]['data_value']);
            $b47=intval($rows[41]['data_value']);
            $b48=intval($rows[42]['data_value']);
            $b49=intval($rows[43]['data_value']);
            $b50=intval($rows[44]['data_value']);
            $b51=intval($rows[45]['data_value']);
            $b53=intval($rows[46]['data_value']);
            $b54=intval($rows[47]['data_value']);
            $b55=intval($rows[48]['data_value']);
            $b56=intval($rows[49]['data_value']);
            $b57=intval($rows[50]['data_value']);
            $b58=intval($rows[51]['data_value']);
            $b59=intval($rows[52]['data_value']);
            $b61=intval($rows[53]['data_value']);
            $b62=intval($rows[54]['data_value']);
            $b63=intval($rows[55]['data_value']);
            $b64=intval($rows[56]['data_value']);
            $b65=intval($rows[57]['data_value']);
            $b66=intval($rows[58]['data_value']);
            $b67=intval($rows[59]['data_value']);
            $b68=intval($rows[60]['data_value']);
            $b69=intval($rows[61]['data_value']);
            $b70=intval($rows[62]['data_value']);
            $b71=intval($rows[63]['data_value']);

            $c76=($b8-$b7)/abs($b7==0?1:$b7);
            $c77=($b8-$b9)/abs($b9==0?1:$b9);
            $c78=($b32-$b31)/abs($b31==0?1:$b31);
            $c79=($b32-$b34)/abs($b34==0?1:$b34);
            $c80=($b11-$b10)/abs($b10==0?1:$b10);
            $c81=($b11-$b12)/abs($b12==0?1:$b12);
            $c82=($b16-$b15)/abs($b15==0?1:$b15);
            $c83=($b16-$b17)/abs($b17==0?1:$b17);
            $c84=$b13/($b14==0?1:$b14);
            $c85=$b5/($b6==0?1:$b6);
            $c86=$b19/($b4==0?1:$b4);
            $c88=($b20-30000)/30000;
            $c89=($b21-30000)/30000;
            $c90=$b21;
            $c91=$b25/($b5==0?1:$b5);
            $c92=$b26/($b6==0?1:$b6);
            $c93=$b36/($b65==0?1:$b65);
            $c94=$b37.":".$b38;
            $c96=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:$b5+$b6);
            $c97=$b28/($b4==0?1:$b4);
            $c98=$b23/($b3==0?1:$b3);
            $c99=$b29;
            $c100=$b22/($b4==0?1:$b4);
            $c102=$b51/($b32==0?1:$b32);
            $c103=$b56/($b55==0?1:$b55);
            $c104=$b58/($b57==0?1:$b57);
            $c106=$b59/100;
            $c105=$b53.":".$b54;
            $c107=$b50/($b33==0?1:$b33);
            $c108=$b47/(($b18==0?1:$b18)/(1500*12));
            $c109=$b48/($b47==0?1:$b47);
            $c110=$b49;
            $c111=($b41-$b40)/abs($b40==0?1:$b40);
            $c112=$b43/($b41==0?1:$b41);
            $c113=$b45/($b41==0?1:$b41);
            $c114=$b44/($b41==0?1:$b41);
            $c115=$b46;
            $c117=$b61;
            $c118=$b62/($b68==0?1:$b68);
            $c119=$b71/($b70==0?1:$b70);
            $c120=$b63/($b65==0?1:$b65);
            $c121=$b66/(($b65==0?1:$b65)/6);
            $c122=$b67/(($b65==0?1:$b65)/30);
            $c124=$b64/($b69==0?1:$b69);

            $e76=($c76>0.2?5:($c76>0.1?4:($c76>0?3:($c76>-0.1?2:($c76>-0.2?1:0)))));
            $e77=($c77>0.2?5:($c77>0.1?4:($c77>0?3:($c77>-0.1?2:($c77>-0.2?1:0)))));
            $e78=($c78>0.4?5:($c78>0.2?4:($c78>0?3:($c78>-0.2?2:($c78>-0.4?1:0)))));
            $e79=($c79>0.4?5:($c79>0.2?4:($c79>0?3:($c79>-0.2?2:($c79>-0.4?1:0)))));
            $e80=($c80>3?5:($c80>1?4:($c80>0?3:($c80>-1?2:($c80>-2?1:0)))));
            $e81=($c81>3?5:($c81>1?4:($c81>0?3:($c81>-1?2:($c81>-2?1:0)))));
            $e82=($c82>0.2?5:($c82>0.1?4:($c82>0?3:($c82>-0.1?2:($c82>-0.2?1:0)))));
            $e83=($c83>0.2?5:($c83>0.1?4:($c83>0?3:($c83>-0.1?2:($c83>-0.2?1:0)))));
            $e84=($c84>2.3?1:($c84>1.5?3:($c84>=1?5:($c84>0.7?4:($c84>0.4?2:($c84>0.2?1:0))))));
            $e85=($c85>2.3?1:($c85>1.5?3:($c85>=1?5:($c85>0.7?4:($c85>0.4?2:($c85>0.2?1:0))))));
            $e86=($c86>0.032?1:($c86>0.024?2:($c86>0.016?3:($c86>0.008?4:($c86>0?5:5)))));
            $e88=($c88>0.2?5:($c88>0?4:($c88>-0.1?3:($c88>-0.2?2:($c88>-0.3?1:0)))));
            $e89=($c89>0.7?5:($c89>0.3?4:($c89>0.1?3:0)));
            $e90='NIL';
            $e91=($c91>0.3?0:($c91>0.25?1:($c91>0.2?2:($c91>0.15?3:($c91>0.1?4:5)))));
            $e92=($c92>0.25?0:($c92>0.2?1:($c92>0.15?2:($c92>0.1?3:($c92>0.05?4:5)))));
            $e93=($c93>0.2?5:($c93>0.1?3:($c93>0.05?1:0)));
            $e94='NIL';
            $e96=($c96>0.555?5:($c96>0.5?4:($c96>0.45?3:($c96>0.4?2:($c96>0.35?1:0)))));
            $e97=($c97>0.35?1:($c97>0.3?2:($c97>0.28?3:($c97>0.25?3:($c97>0.2?5:0)))));
            $e98=($c98>1?5:($c98>0.95?4:($c98>0.9?3:($c98>0.85?2:($c98>0.8?1:0)))));
            $e99='NIL';
            $e100=($c100>0.7?0:($c100>0.6?1:($c100>0.5?2:($c100>0.4?3:($c100>0.3?4:5)))));
            $e102=($c102>0.95?5:($c102>0.9?4:($c102>0.85?3:($c102>0.8?2:($c102>=0.75?1:0)))));
            $e103=($c103>0.95?5:($c103>0.9?4:($c103>0.85?3:($c103>0.8?2:($c103>=0.75?1:0)))));
            $e104=($c104>0.95?5:($c104>0.9?4:($c104>0.85?3:($c104>0.8?2:($c104>=0.75?1:0)))));
            $e105='NIL';
            $e106=($c106>1.08?0:($c106>1.04?1:($c106>1?3:($c106>0.96?5:($c106>0.92?3:($c106>0.88?1:0))))));
            $e107=($c107>0.95?5:($c107>0.9?4:($c107>0.85?3:($c107>0.8?2:($c107>=0.75?1:0)))));
            $e108=($c108>0.9?5:($c108>0.7?4:($c108>0.5?3:($c108>0.3?2:($c108>0.1?1:0)))));
            $e109=($c109>0.2?3:($c109>0.1?5:($c109>=0?1:0)));
            $e110='NIL';
            $e111=($c111>0.05?0:($c111>0?1:($c111>-0.1?2:($c111>-0.2?3:($c111>-0.3?4:5)))));
            $e112=($c112>0.95?5:($c112>0.9?4:($c112>0.85?3:($c112>0.8?2:($c112>=0.75?1:0)))));
            $e113=($c113>0.15?5:($c113>0.1?3:($c113>0.05?1:0)));
            $e114=($c114>0.95?5:($c114>0.9?4:($c114>0.85?3:($c114>0.8?2:($c114>=0.75?1:0)))));
            $e115='NIL';
            $e117=($c117>5?0:($c117>3?3:($c117>1?4:5)));
            $e118=($c118>0.3?0:($c118>0.2?1:($c118>0.1?3:5)));
            $e119=($c119>0.6?1:($c119>0.2?3:5));
            $e120=($c120>0.15?0:($c120>0.1?1:($c120>0.05?3:5)));
            $e121=($c121>1?5:($c121>0.8?3:1));
            $e122=($c122>1?5:($c122>0.8?3:1));
            $e124=($c124>0.3?0:($c124>0.2?1:($c124>0.1?3:5)));
            $f75=round($e76+$e77+$e78+$e79+$e80+$e81+$e82+$e83+$e84+$e85+$e86,2);
            $f87=round($e88+$e89+$e90+$e91+$e92+$e93+$e94,2);
            $f95=round($e100+$e96+$e97+$e98+$e99,2);
            $f101=round($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114,2);
            $f116=round($e122+$e117+$e118+$e119+$e120+$e121+$e124,2);
            $f74=round(($f75+$f87+$f95+$f101+$f116)/190*100,2);
        }
        else{
            $b3=intval($rows[0]['data_value']);
            $b4=intval($rows[1]['data_value']);
            $b5=intval($rows[2]['data_value']);
            $b6=intval($rows[3]['data_value']);
            $b7=intval($rows[4]['data_value']);
            $b8=intval($rows[5]['data_value']);
            $b9=intval($rows[6]['data_value']);
            $b10=intval($rows[7]['data_value']);
            $b11=intval($rows[8]['data_value']);
            $b12=intval($rows[9]['data_value']);
            $b13=intval($rows[10]['data_value']);
            $b14=intval($rows[11]['data_value']);
            $b15=intval($rows[12]['data_value']);
            $b16=intval($rows[13]['data_value']);
            $b17=intval($rows[14]['data_value']);
            $b18=intval($rows[15]['data_value']);
            $b19=intval($rows[16]['data_value']);
            $b20=intval($rows[17]['data_value']);
            $b21=intval($rows[18]['data_value']);
            $b22=intval($rows[19]['data_value']);
            $b23=intval($rows[20]['data_value']);
            $b24=intval($rows[21]['data_value']);
            $b25=intval($rows[22]['data_value']);
            $b26=intval($rows[23]['data_value']);
            $b27=intval($rows[24]['data_value']);
            $b28=intval($rows[25]['data_value']);
            $b29=intval($rows[26]['data_value']);
            $b31=intval($rows[27]['data_value']);
            $b32=intval($rows[28]['data_value']);
            $b33=intval($rows[29]['data_value']);
            $b35=intval($rows[31]['data_value']);
            $b37=intval($rows[32]['data_value']);
            $b38=intval($rows[33]['data_value']);
            $b39=intval($rows[34]['data_value']);
            $b41=intval($rows[35]['data_value']);
            $b42=intval($rows[36]['data_value']);
            $b43=intval($rows[37]['data_value']);
            $b44=intval($rows[38]['data_value']);
            $b45=intval($rows[39]['data_value']);
            $b46=intval($rows[40]['data_value']);
            $b47=intval($rows[41]['data_value']);
            $b48=intval($rows[42]['data_value']);
            $b49=intval($rows[43]['data_value']);
            $b50=$rows[44]['data_value'];
            $b51=intval($rows[45]['data_value']);
            $b52=intval($rows[46]['data_value']);
            $b54=intval($rows[47]['data_value']);
            $b55=intval($rows[48]['data_value']);
            $b56=intval($rows[49]['data_value']);
            $b57=intval($rows[50]['data_value']);
            $b58=intval($rows[51]['data_value']);
            $b59=intval($rows[52]['data_value']);
            $b60=intval($rows[53]['data_value']);
            $b62=intval($rows[54]['data_value']);
            $b63=intval($rows[55]['data_value']);
            $b64=intval($rows[56]['data_value']);
            $b65=intval($rows[57]['data_value']);
            $b66=intval($rows[58]['data_value']);
            $b67=intval($rows[59]['data_value']);
            $b68=intval($rows[60]['data_value']);
            $b69=intval($rows[61]['data_value']);
            $b70=intval($rows[62]['data_value']);
            $b71=intval($rows[63]['data_value']);
            $b72=intval($rows[64]['data_value']);
            $c76=($b8-$b7)/abs($b7==0?1:$b7);
            $c77=($b8-$b9)/abs($b9==0?1:$b9);
            $c78=($b32-$b31)/abs($b31==0?1:$b31);
            $c79=($b32-$b35)/abs($b35==0?1:$b35);
            $c80=($b11-$b10)/abs($b10==0?1:$b10);
            $c81=($b11-$b12)/abs($b12==0?1:$b12);
            $c82=($b16-$b15)/abs($b15==0?1:$b15);
            $c83=($b16-$b17)/abs($b17==0?1:$b17);
            $c84=$b13/($b14==0?1:$b14);
            $c85=$b5/($b6==0?1:$b6);
            $c86=$b19/($b4==0?1:$b4);
            $c88=($b20-30000)/30000;
            $c89=($b21-30000)/30000;
            $c90=$b21;
            $c91=$b25/($b5==0?1:$b5);
            $c92=$b26/($b6==0?1:$b6);
            $c93=$b37/($b66==0?1:$b66);
            $c94=$b38.":".$b39;
            $c96=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:($b5+$b6));
            $c97=$b28/($b4==0?1:$b4);
            $c98=$b23/($b3==0?1:$b3);
            $c99=$b29;
            $c100=$b22/($b4==0?1:$b4);
            $c102=$b52/($b32==0?1:$b32);
            $c103=$b57/($b56==0?1:$b56);
            $c104=$b59/($b58==0?1:$b58);
            $c105=0;
            $c106=$b60/100;
            $c107=$b51/($b33==0?1:$b33);
            $c108=$b48/(($b18==0?1:$b18)/(1500*12));
            $c109=$b49/($b48==0?1:$b48);
            $c110=$b50;
            $c111=($b42-$b41)/abs($b41==0?1:$b41);
            $c112=$b44/($b42==0?1:$b42);
            $c113=$b46/($b42==0?1:$b42);
            $c114=$b45/($b42==0?1:$b42);
            $c115=$b47;
            $c117=$b62;
            $c118=$b63/($b69==0?1:$b69);
            $c119=$b72/($b71==0?1:$b71);
            $c120=$b64/($b66==0?1:$b66);
            $c121=$b67/(($b66==0?1:$b66)/6);
            $c122=$b68/(($b66==0?1:$b66)/30);
            $c123=0;
            $c124=$b65/($b70==0?1:$b70);
            $e76=($c76>0.2?5:($c76>0.1?4:($c76>0?3:($c76>-0.1?2:($c76>-0.2?1:0)))));
            $e77=($c77>0.2?5:($c77>0.1?4:($c77>0?3:($c77>-0.1?2:($c77>-0.2?1:0)))));
            $e78=($c78>0.4?5:($c78>0.2?4:($c78>0?3:($c78>-0.2?2:($c78>-0.4?1:0)))));
            $e79=($c79>0.4?5:($c79>0.2?4:($c79>0?3:($c79>-0.2?2:($c79>-0.4?1:0)))));
            $e80=($c80>3?5:($c80>1?4:($c80>0?3:($c80>-1?2:($c80>-2?1:0)))));
            $e81=($c81>3?5:($c81>1?4:($c81>0?3:($c81>-1?2:($c81>-2?1:0)))));
            $e82=($c82>0.2?5:($c82>0.1?4:($c82>0?3:($c82>-0.1?2:($c82>-0.2?1:0)))));
            $e83=($c83>0.2?5:($c83>0.1?4:($c83>0?3:($c83>-0.1?2:($c83>-0.2?1:0)))));
            $e84=($c84>2.3?1:($c84>1.5?3:($c84>=1?5:($c84>0.7?4:($c84>0.4?2:($c84>0.2?1:0))))));
            $e85=($c85>2.3?1:($c85>1.5?3:($c85>=1?5:($c85>0.7?4:($c85>0.4?2:($c85>0.2?1:0))))));
            $e86=($c86>0.032?1:($c86>0.024?2:($c86>0.016?3:($c86>0.008?4:($c86>0?5:5)))));
            $e88=($c88>0.2?5:($c88>0?4:($c88>-0.1?3:($c88>-0.2?2:($c88>-0.3?1:0)))));
            $e89=($c89>0.7?5:($c89>0.3?4:($c89>0.1?3:0)));
            $e90='NIL';
            $e91=($c91>0.3?0:($c91>0.25?1:($c91>0.2?2:($c91>0.15?3:($c91>0.1?4:5)))));
            $e92=($c92>0.25?0:($c92>0.2?1:($c92>0.15?2:($c92>0.1?3:($c92>0.05?4:5)))));
            $e93=($c93>0.2?5:($c93>0.1?3:($c93>0.05?1:0)));
            $e94='NIL';
            $e96=($c96>0.555?5:($c96>0.5?4:($c96>0.45?3:($c96>0.4?2:($c96>0.35?1:0)))));
            $e97=($c97>0.35?1:($c97>0.3?2:($c97>0.28?3:($c97>0.25?3:($c97>0.2?5:0)))));
            $e98=($c98>1?5:($c98>0.95?4:($c98>0.9?3:($c98>0.85?2:($c98>0.8?1:0)))));
            $e99='NIL';
            $e100=($c100>0.7?0:($c100>0.6?1:($c100>0.5?2:($c100>0.4?3:($c100>0.3?4:5)))));
            $e102=($c102>0.95?5:($c102>0.9?4:($c102>0.85?3:($c102>0.8?2:($c102>=0.75?1:0)))));
            $e103=($c103>0.95?5:($c103>0.9?4:($c103>0.85?3:($c103>0.8?2:($c103>=0.75?1:0)))));
            $e104=($c104>0.95?5:($c104>0.9?4:($c104>0.85?3:($c104>0.8?2:($c104>=0.75?1:0)))));
            $e105='NIL';
            $e106=($c106>1.08?0:($c106>1.04?1:($c106>1?3:($c106>0.96?5:($c106>0.92?3:($c106>0.88?1:0))))));
            $e107=($c107>0.95?5:($c107>0.9?4:($c107>0.85?3:($c107>0.8?2:($c107>=0.75?1:0)))));
            $e108=($c108>0.9?5:($c108>0.7?4:($c108>0.5?3:($c108>0.3?2:($c108>0.1?1:0)))));
            $e109=($c109>0.2?3:($c109>0.1?5:($c109>=0?1:0)));
            $e110='NIL';
            $e111=($c111>0.05?0:($c111>0?1:($c111>-0.1?2:($c111>-0.2?3:($c111>-0.3?4:5)))));
            $e112=($c112>0.95?5:($c112>0.9?4:($c112>0.85?3:($c112>0.8?2:($c112>=0.75?1:0)))));
            $e113=($c113>0.15?5:($c113>0.1?3:($c113>0.05?1:0)));
            $e114=($c114>0.95?5:($c114>0.9?4:($c114>0.85?3:($c114>0.8?2:($c114>=0.75?1:0)))));
            $e115='NIL';
            $e117=($c117>5?0:($c117>3?3:($c117>1?4:5)));
            $e118=($c118>0.3?0:($c118>0.2?1:($c118>0.1?3:5)));
            $e119=($c119>0.6?1:($c119>0.2?3:5));
            $e120=($c120>0.15?0:($c120>0.1?1:($c120>0.05?3:5)));
            $e121=($c121>1?5:($c121>0.8?3:1));
            $e122=($c122>1?5:($c122>0.8?3:1));
            $e124=($c124>0.3?0:($c124>0.2?1:($c124>0.1?3:5)));
            $f75=round(($e76+$e77+$e78+$e79+$e80+$e81+$e82+$e83+$e84+$e85+$e86)/55*30,2);
            $f87=round(($e94+$e88+$e89+$e90+$e91+$e92+$e93)/25*20,2);
            $f95=round(($e100+$e96+$e97+$e98+$e99)/20*25,2);
            $f101=round(($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
            $f116=round(($e124+$e117+$e118+$e119+$e120+$e121+$e122)/35*10,2);
            $f74=$f75+$f87+$f95+$f101+$f116;
        }


//赋值
        $this->excel['c76']=(round($c76,4)*100)."%";
        $this->excel['c77']=(round($c77,4)*100)."%";
        $this->excel['c78']=(round($c78,4)*100)."%";
        $this->excel['c79']=(round($c79,4)*100)."%";
        $this->excel['c80']=(round($c80,4)*100)."%";
        $this->excel['c81']=(round($c81,4)*100)."%";
        $this->excel['c82']=(round($c82,4)*100)."%";
        $this->excel['c83']=(round($c83,4)*100)."%";
        $this->excel['c84']=(round($c84,4)*100)."%";
        $this->excel['c85']=(round($c85,4)*100)."%";
        $this->excel['c86']=(round($c86,4)*100)."%";
        $this->excel['c88']=(round($c88,4)*100)."%";
        $this->excel['c89']=(round($c89,4)*100)."%";
        $this->excel['c90']=round($c90,4);
        $this->excel['c91']=(round($c91,4)*100)."%";
        $this->excel['c92']=(round($c92,4)*100)."%";
        $this->excel['c93']=(round($c93,4)*100)."%";
        $this->excel['c94']=$c94;
        $this->excel['c96']=(round($c96,4)*100)."%";
        $this->excel['c97']=(round($c97,4)*100)."%";
        $this->excel['c98']=(round($c98,4)*100)."%";
        $this->excel['c99']=round($c99,4);
        $this->excel['c100']=(round($c100,4)*100)."%";
        $this->excel['c102']=(round($c102,4)*100)."%";
        $this->excel['c103']=(round($c103,4)*100)."%";
        $this->excel['c104']=(round($c104,4)*100)."%";
        $this->excel['c105']=$c105;
        $this->excel['c106']=(round($c106,4)*100)."%";
        $this->excel['c107']=(round($c107,4)*100)."%";
        $this->excel['c108']=(round($c108,4)*100)."%";
        $this->excel['c109']=(round($c109,4)*100)."%";
        $this->excel['c110']=$c110;
        $this->excel['c111']=(round($c111,4)*100)."%";
        $this->excel['c112']=(round($c112,4)*100)."%";
        $this->excel['c113']=(round($c113,4)*100)."%";
        $this->excel['c114']=(round($c114,4)*100)."%";
        $this->excel['c115']=(round($c115,4)*100)."%";
        $this->excel['c117']=(round($c117,4)*100)."%";
        $this->excel['c118']=(round($c118,4)*100)."%";
        $this->excel['c119']=(round($c119,4)*100)."%";
        $this->excel['c120']=(round($c120,4)*100)."%";
        $this->excel['c121']=(round($c121,4)*100)."%";
        $this->excel['c122']=(round($c122,4)*100)."%";
        $this->excel['c124']=(round($c124,4)*100)."%";
        $this->excel['e76']=round($e76,4);
        $this->excel['e77']=round($e77,4);
        $this->excel['e78']=round($e78,4);
        $this->excel['e79']=round($e79,4);
        $this->excel['e80']=round($e80,4);
        $this->excel['e81']=round($e81,4);
        $this->excel['e82']=round($e82,4);
        $this->excel['e83']=round($e83,4);
        $this->excel['e84']=round($e84,4);
        $this->excel['e85']=round($e85,4);
        $this->excel['e86']=round($e86,4);
        $this->excel['e88']=round($e88,4);
        $this->excel['e89']=round($e89,4);
        $this->excel['e90']=round($e90,4);
        $this->excel['e91']=round($e91,4);
        $this->excel['e92']=round($e92,4);
        $this->excel['e93']=round($e93,4);
        $this->excel['e94']=round($e94,4);
        $this->excel['e96']=round($e96,4);
        $this->excel['e97']=round($e97,4);
        $this->excel['e98']=round($e98,4);
        $this->excel['e99']=round($e99,4);
        $this->excel['e100']=round($e100,4);
        $this->excel['e102']=round($e102,4);
        $this->excel['e103']=round($e103,4);
        $this->excel['e104']=round($e104,4);
        $this->excel['e105']=round($e105,4);
        $this->excel['e106']=round($e106,4);
        $this->excel['e107']=round($e107,4);
        $this->excel['e108']=round($e108,4);
        $this->excel['e109']=round($e109,4);
        $this->excel['e110']=round($e110,4);
        $this->excel['e111']=round($e111,4);
        $this->excel['e112']=round($e112,4);
        $this->excel['e113']=round($e113,4);
        $this->excel['e114']=round($e114,4);
        $this->excel['e115']=round($e115,4);
        $this->excel['e117']=round($e117,4);
        $this->excel['e118']=round($e118,4);
        $this->excel['e119']=round($e119,4);
        $this->excel['e120']=round($e120,4);
        $this->excel['e121']=round($e121,4);
        $this->excel['e122']=round($e122,4);
        $this->excel['e124']=round($e124,4);

        $this->excel['f74']=$f74;
        $this->excel['f87']=$f87;
        $this->excel['f95']=$f95;
        $this->excel['f101']=$f101;
        $this->excel['f116']=$f116;
        $this->excel['f75']=$f75;

        if (count($rowss) > 0) {
            $hid = 0;
            foreach ($rowss as $rowa) {
                if ($hid!=$rowa['hdr_id']) {
                    $hid = $rowa['hdr_id'];
                    $this->id = $hid;
                    $this->year_no = $rowa['year_no'];
                    $this->month_no = $rowa['month_no'];
                    if(count($ros)>0){
                        $this->market = $ros[0]['market'];
                        $this->legwork = $ros[0]['legwork'];
                        $this->service = $ros[0]['service'];
                        $this->personnel = $ros[0]['personnel'];
                        $this->finance = $ros[0]['finance'];
                        $this->other = $ros[0]['other'];
                    }
                }
                $temp = array();
                $temp['id'] = $rowa['id'];
                $temp['code'] = $rowa['data_field'];
                $temp['name'] = $rowa['name'];
                $temp['datavalue'] = $rowa['data_value'];
                $temp['datavalueold'] = $rowa['data_value'];
                $temp['updtype'] = $rowa['upd_type'];
                $temp['fieldtype'] = $rowa['field_type'];
                $temp['manualinput'] = $rowa['manual_input'];
                $temp['excel_row'] = $rowa['excel_row'];
                $this->record[$rowa['data_field']] = $temp;
            }
        }
//        print_r('<pre>');
//        print_r($rows);
        return true;
    }

    public function retrieveDataa(){

    }
    public function sendDate($model){
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $market=$model['market'];
        $legwork=$model['legwork'];
        $service=$model['service'];
        $personnel=$model['personnel'];
        $finance=$model['finance'];
        $other=$model['other'];
        $index=$model['id'];
        $time=$model['year_no'].'/'.$model['month_no'];
        $user = Yii::app()->user->id;
        $id=$model['id'];
        $sqla="select * from swo_monthly_comment where hdr_id='".$id."'";
        $ros = Yii::app()->db->createCommand($sqla)->queryAll();
        if(empty($ros)){
            $in="insert into swo_monthly_comment(hdr_id,market,legwork,finance,service,personnel,other,luu,lcu) values('".$id."','".$market."','".$legwork."','".$finance."','".$service."','".$personnel."','".$other."','".$user."','".$user."')";
        }else{
        $in="UPDATE swo_monthly_comment SET market = '".$market."',legwork = '".$legwork."',service = '".$service."',personnel = '".$personnel."',finance = '".$finance."',other = '".$other."'
WHERE hdr_id = '".$model['id']."'";
        }
        $int = Yii::app()->db->createCommand($in)->execute();
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select approver_type, username from account$suffix.acc_approver where city='$city'";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $sql1 = "SELECT email FROM security$suffix.sec_user WHERE username='".$rows[0]['username']."'";
        $a1 = Yii::app()->db->createCommand($sql1)->queryAll();
        $sql2 = "SELECT email FROM security$suffix.sec_user WHERE username='".$rows[1]['username']."'";
        $a2 = Yii::app()->db->createCommand($sql2)->queryAll();
        $sql3 = "SELECT email FROM security$suffix.sec_user WHERE username='".$rows[2]['username']."'";
        $a3 = Yii::app()->db->createCommand($sql3)->queryAll();
        $sql4 = "SELECT email FROM security$suffix.sec_user WHERE username='".$rows[3]['username']."'";
        $a4 = Yii::app()->db->createCommand($sql4)->queryAll();
        $sql5 = "SELECT email FROM security$suffix.sec_user WHERE username='".$rows[4]['username']."'";
        $a5 = Yii::app()->db->createCommand($sql5)->queryAll();
        $sql6 = "SELECT email FROM security$suffix.sec_user WHERE username='".$rows[5]['username']."'";
        $a6 = Yii::app()->db->createCommand($sql6)->queryAll();
        $a=array($a1[0]['email'],$a2[0]['email'],$a3[0]['email'],$a4[0]['email'],$a5[0]['email']);
        $a=General::dedupToEmailList($a);
        $city = Yii::app()->user->city();
        $sql = "select a.year_no, a.month_no, b.id, b.hdr_id, b.data_field, b.data_value, c.name, c.upd_type, c.field_type, b.manual_input , c.excel_row  
				from swo_monthly_hdr a, swo_monthly_dtl b, swo_monthly_field c 
				where a.id=$index and a.city='$city'
				and a.id=b.hdr_id and b.data_field=c.code
				and c.status='Y'
				order by c.excel_row
			";
        $rowss = Yii::app()->db->createCommand($sql)->queryAll();
        $content=$this->getOutput($rowss);

        $sqla="INSERT INTO swo_queue (rpt_desc,req_dt,fin_dt,username,status,rpt_type,rpt_content)
                VALUES ('Monthly Report',now(),now(),'admin','C','EXCEL',:rpt_content)
                ";
        $aa = Yii::app()->db->createCommand($sqla);
        if (strpos($sqla,':rpt_content')!==false)
            $aa->bindParam(':rpt_content',$content,PDO::PARAM_LOB);
        $cnt = $aa->execute();
        $qid=Yii::app()->db->getLastInsertID();
        $sqlb="INSERT INTO swo_queue_param (queue_id,param_field,param_value) VALUES ('".$qid."','RPT_ID','monthlyrpt')";
        $aa = Yii::app()->db->createCommand($sqlb)->execute();
        $b=array($rows[0]['username'],$rows[1]['username'],$rows[2]['username'],$rows[3]['username'],$rows[4]['username'],$rows[5]['username']);
        $b=General::dedupToEmailList($b);
        //   $c=json_encode($b);
        if(count($b)==1){
            $sqlb="INSERT INTO swo_queue_user (queue_id,username) VALUES ('".$qid."','".$b[0]."')";
        }
        if(count($b)==2){
            $sqlb="INSERT INTO swo_queue_user (queue_id,username) VALUES ('".$qid."','".$b[0]."'),('".$qid."','".$b[1]."')";
        }
        if(count($b)==3){
            $sqlb="INSERT INTO swo_queue_user (queue_id,username) VALUES ('".$qid."','".$b[0]."'),('".$qid."','".$b[1]."'),('".$qid."','".$b[2]."')";
        }
        if(count($b)==4){
            $sqlb="INSERT INTO swo_queue_user (queue_id,username) VALUES ('".$qid."','".$b[0]."'),('".$qid."','".$b[1]."'),('".$qid."','".$b[2]."'),('".$qid."','".$b[3]."')";
        }
        if(count($b)==5){
            $sqlb="INSERT INTO swo_queue_user (queue_id,username) VALUES ('".$qid."','".$b[0]."'),('".$qid."','".$b[1]."'),('".$qid."','".$b[2]."'),('".$qid."','".$b[3]."'),('".$qid."','".$b[4]."')";
        }
        if(count($b)==6){
            $sqlb="INSERT INTO swo_queue_user (queue_id,username) VALUES ('".$qid."','".$b[0]."'),('".$qid."','".$b[1]."'),('".$qid."','".$b[2]."'),('".$qid."','".$b[3]."'),('".$qid."','".$b[4]."'),('".$qid."','".$b[5]."')";
        }
        $aa = Yii::app()->db->createCommand($sqlb)->execute();
        $from_addr = "it@lbsgroup.com.hk";
        $to_addr=json_encode($a);
        $subject = "月报表总汇-" .$time;
        $description = "内容分析";
        $message = "销售：<br/>" . $market . "<br/>外勤：<br/>" .$legwork ."<br/>财务：<br/>" . $service ."<br/>营运：<br/>" .$personnel ."<br/>人事：<br/>" .$finance ."<br/>其他：<br/>" .$other ;
        $url = Yii::app()->createAbsoluteUrl('queue/download',array('index'=>$qid));
        $msg_url = str_replace('{url}',$url, Yii::t('report',"Please click <a href=\"{url}\" onClick=\"return popup(this,'Daily Report');\">here</a> to download the report."));
        $message .= "<p>&nbsp;</p><p>$msg_url</p>";
        $lcu = "admin";
        $aaa = Yii::app()->db->createCommand()->insert("swo_email_queue", array(
            'request_dt' => date('Y-m-d H:i:s'),
            'from_addr' => $from_addr,
            'to_addr' => $to_addr,
            'subject' => $subject,//郵件主題
            'description' => $description,//郵件副題
            'message' => $message,//郵件內容（html）
            'status' => "P",
            'lcu' => $lcu,
            'lcd' => date('Y-m-d H:i:s'),
        ));
//        print_r('<pre/>');
//        print_r($msg_url);
    }

    public function getOutput($rowss){
        $excel = new MyExcel();
        $excel->init();
        $path = Yii::app()->basePath.'/commands/template/m_template_one.xlsx';
        $excel->readFile($path);
        foreach ($rowss as $row) {
            $excel->setCellValue('B', $row['excel_row'], $row['data_value']);
        }
        return $excel->getOutput();
    }

    /*
        public function retrieveDatas($model){
            Yii::$enableIncludePath = false;
            $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
            spl_autoload_unregister(array('YiiBase','autoload'));
            include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $objPHPExcel = new PHPExcel;
            $objReader  = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load("protected/commands/template/m_template_one.xlsx");
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            foreach ($model->record as $arr ){
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$arr['excel_row'], $arr['datavalueold']) ;
            }
            $time=time();
            $str="templates/month_".$time.".xlsx";
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");;
            header('Content-Disposition:attachment;filename="'.$str.'.xls"');
            header("Content-Transfer-Encoding:binary");
            $objWriter->save('php://output');
        }
    */

    public function retrieveDatas($model){
        Yii::$enableIncludePath = false;
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
        spl_autoload_unregister(array('YiiBase','autoload'));
        include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $objPHPExcel = new PHPExcel;
        $objReader  = PHPExcel_IOFactory::createReader('Excel2007');
        if(count($model->record)==65){
            $path = Yii::app()->basePath.'/commands/template/m_template_one.xlsx';
        }else{
            $path = Yii::app()->basePath.'/commands/template/m_template_old.xlsx';
        }

        $objPHPExcel = $objReader->load($path);
        foreach ($model->record as $arr ){
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$arr['excel_row'], $arr['datavalueold']) ;
        }
//print_r("<pre>");
//        print_r(count($model->record));
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $output = ob_get_clean();
        spl_autoload_register(array('YiiBase','autoload'));
        $time=time();
        $str="templates/month_".$time.".xlsx";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$str.'"');
        header("Content-Transfer-Encoding:binary");
        echo $output;
    }


    public function saveData()
    {
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $this->saveMonthly($connection);
            $transaction->commit();
        }
        catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
        }
    }

    protected function saveMonthly(&$connection) {
        $user = Yii::app()->user->id;
        $sql = '';
        switch ($this->scenario) {
            case 'edit':
//                $sql="insert into swo_monthly_comment(hdr_id,market,legwork,finance,service,personnel,other,luu,lcu) values(:hdr_id,:market,:legwork,:finance,:service,:personnel,:other,:luu,:lcu)
//on duplicate key update market = :market, legwork = :legwork, finance = :finance , service = :service , personnel = :personnel , other = :other , luu= :luu)";
                $sqla="select * from swo_monthly_comment where hdr_id=$this->id";
                $ros = Yii::app()->db->createCommand($sqla)->queryAll();
                if(empty($ros)){
                    $sql="insert into swo_monthly_comment(hdr_id,market,legwork,finance,service,personnel,other,luu,lcu) values(:hdr_id,:market,:legwork,:finance,:service,:personnel,:other,:luu,:lcu)";
                }else{
                    $sql="UPDATE swo_monthly_comment SET market =:market,legwork = :legwork,service = :service,personnel = :personnel,finance =:finance,other = :other,luu=:luu
WHERE hdr_id = :hdr_id";
                }
                break;
        }
        if (empty($sql)) return false;
//		$city = Yii::app()->user->city();
//		$uid = Yii::app()->user->id;
//		$select = "select code from swo_monthly_field
//					where status = 'Y'
//					order by code
//				";
//		$rows = Yii::app()->db->createCommand($select)->queryAll();
//
        $command=$connection->createCommand($sql);
//			print_r('<pre>');
//            print_r($user);
//            print_r($this);
//            exit();
        if (isset($this)) {
            if (strpos($sql,':hdr_id')!==false)
                $command->bindParam(':hdr_id',$this->id,PDO::PARAM_INT);
            if (strpos($sql,':market')!==false)
                $command->bindParam(':market',$this->market,PDO::PARAM_STR);
            if (strpos($sql,':legwork')!==false)
                $command->bindParam(':legwork',$this->legwork,PDO::PARAM_STR);
            if (strpos($sql,':finance')!==false)
                $command->bindParam(':finance',$this->finance,PDO::PARAM_STR);
            if (strpos($sql,':service')!==false)
                $command->bindParam(':service',$this->service,PDO::PARAM_STR);
            if (strpos($sql,':personnel')!==false)
                $command->bindParam(':personnel',$this->personnel,PDO::PARAM_STR);
            if (strpos($sql,':other')!==false)
                $command->bindParam(':other',$this->other,PDO::PARAM_STR);
            if (strpos($sql,':luu')!==false)
                $command->bindParam(':luu',$user,PDO::PARAM_STR);
            if (strpos($sql,':lcu')!==false)
                $command->bindParam(':lcu',$user,PDO::PARAM_STR);
            $command->execute();
        }

        return true;
    }
}
