<?php
class MonthList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
	    if(empty($this->city)){
            $city = Yii::app()->user->city();
        }
        else{
            $city =$this->city;
        }
		$sql1 = "select *,b.name as cityname
				from swo_monthly_hdr a,security$suffix.sec_city b			
				where a.city='$city' and a.city=b.code
			";
		$sql2 = "select count(id)
				from swo_monthly_hdr
				where city='$city'
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'year_no':
					$clause .= General::getSqlConditionClause('year_no', $svalue);
					break;
				case 'month_no':
					$clause .= General::getSqlConditionClause('month_no', $svalue);
					break;
			}
		}

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by year_no desc, month_no desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $sql="select b.month_no, c.excel_row, a.data_value, c.field_type ,c.name
				from 
					swo_monthly_dtl a, swo_monthly_hdr b, swo_monthly_field c  
				where 
					a.hdr_id = b.id and 
					a.data_field = c.code and 
					b.city = '".$record['city']."' and 
					b.year_no = '".$record['year_no']."' and 
					b.month_no = '".$record['month_no']."' and
					c.status = 'Y'
				order by b.month_no, c.excel_row ";
                $rows = Yii::app()->db->createCommand($sql)->queryAll();
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
                }elseif (!empty($rows[67])){
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
                    $b73=intval($rows[65]['data_value']);
                    $b74=intval($rows[66]['data_value']);
                    $b75=intval($rows[67]['data_value']);
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
                    $c107=$b51/($b34==0?1:$b34);
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
                    //利润的
                    $bc102=$b74/($b4==0?1:$b4);
                    $bc103=($b74-$b73)/($b73==0?1:$b73);
                    $bc104=($b74-$b75)/($b75==0?1:$b75);
                    $be102=($bc102>0.2?5:($bc102>0.15?4:($bc102>0.1?3:($bc102>0.05?2:1))));
                    $be103=($bc103>=0.03?5:($bc103>=0.025?4:($bc103>=0.02?3:($bc103>=0.015?2:($bc103>=0.01?1:0)))));
                    $be104=($bc104>0.34?5:($bc104>0.25?4:($bc104>0.16?3:($bc104>0.08?2:($bc104>0?1:0)))));

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
                    $f95=round(($e100+$e96+$e97+$e98+$e99+$be102+$be103+$be104)/20*25,2);
                    $f101=round(($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
                    $f116=round(($e124+$e117+$e118+$e119+$e120+$e121+$e122)/35*10,2);
                    $f74=$f75+$f87+$f95+$f101+$f116;
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
                    $b34=intval($rows[30]['data_value']);
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
                    $b50=intval($rows[44]['data_value']);
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
                    $c94=0;
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
                    $c107=$b51/($b34==0?1:$b34);
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
                    $f87=round(($e88+$e89+$e90+$e91+$e92+$e93+$e94)/25*20,2);
                    $f95=round(($e100+$e96+$e97+$e98+$e99)/20*25,2);
                    $f101=round(($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
                    $f116=round(($e122+$e117+$e118+$e119+$e120+$e121+$e124)/35*10,2);
                    $f74=$f75+$f87+$f95+$f101+$f116;
                }


                $this->attr[] = array(
						'id'=>$record['id'],
						'year_no'=>$record['year_no'],
						'month_no'=>$record['month_no'],
                        'city'=>$record['cityname'],
                        'cityname'=>$record['city'],
                        'f74'=>$f75,
                        'f86'=>$f87,
                        'f94'=>$f95,
                        'f100'=>$f101,
                        'f115'=>$f116,
                        'f73'=>$f74
					);
			}
		}

		$session = Yii::app()->session;
		$session['criteria_a09'] = $this->getCriteria();
		return true;
	}

}
