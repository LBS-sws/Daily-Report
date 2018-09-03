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
		$city = Yii::app()->user->city();
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

                $c75=($b8-$b7)/abs($b7==0?1:$b7);
                $c76=($b8-$b9)/abs($b9==0?1:$b9);
                $c77=($b32-$b31)/abs($b31==0?1:$b31);
                $c78=($b32-$b34)/abs($b34==0?1:$b34);
                $c79=($b11-$b10)/abs($b10==0?1:$b10);
                $c80=($b11-$b12)/abs($b12==0?1:$b12);
                $c81=($b16-$b15)/abs($b15==0?1:$b15);
                $c82=($b16-$b17)/abs($b17==0?1:$b17);
                $c83=$b13/($b14==0?1:$b14);
                $c84=$b5/($b6==0?1:$b6);
                $c85=$b19/($b4==0?1:$b4);
                $c87=($b20-3000)/3000;
                $c88=($b21-3000)/3000;
                $c89=$b21;
                $c90=$b25/($b5==0?1:$b5);
                $c91=$b26/($b6==0?1:$b6);
                $c92=$b36/($b65==0?1:$b65);
                $c95=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:$b5+$b6);
                $c96=$b28/($b4==0?1:$b4);
                $c97=$b23/($b3==0?1:$b3);
                $c98=$b29;
                $c99=$b22/($b4==0?1:$b4);
                $c101=$b51/($b32==0?1:$b32);
                $c102=$b56/($b55==0?1:$b55);
                $c103=$b58/($b57==0?1:$b57);
                $c105=$b59/100;
                $c106=$b50/($b33==0?1:$b33);
                $c107=$b47/(($b18==0?1:$b18)/(1500*12));
                $c108=$b48/($b47==0?1:$b47);
                $c109=$b49;
                $c110=($b41-$b40)/abs($b40==0?1:$b40);
                $c111=$b43/($b41==0?1:$b41);
                $c112=$b45/($b41==0?1:$b41);
                $c113=$b44/($b41==0?1:$b41);
                $c114=$b46;
                $c116=$b61;
                $c117=$b62/($b68==0?1:$b68);
                $c118=$b71/($b70==0?1:$b70);
                $c119=$b63/($b65==0?1:$b65);
                $c120=$b66/(($b65==0?1:$b65)/6);
                $c121=$b67/(($b65==0?1:$b65)/30);
                $c122=0;
                $c123=$b64/($b69==0?1:$b69);

                $e75=($c75>0.2?5:($c75>0.1?4:($c75>0?3:($c75>-0.1?2:($c75>-0.2?1:0)))));
                $e76=($c76>0.2?5:($c76>0.1?4:($c76>0?3:($c76>-0.1?2:($c76>-0.2?1:0)))));
                $e77=($c77>0.4?5:($c77>0.2?4:($c77>0?3:($c77>-0.2?2:($c77>-0.4?1:0)))));
                $e78=($c78>0.4?5:($c78>0.2?4:($c78>0?3:($c78>-0.2?2:($c78>-0.4?1:0)))));
                $e79=($c79>3?5:($c79>1?4:($c79>0?3:($c79>-1?2:($c79>-2?1:0)))));
                $e80=($c80>3?5:($c80>1?4:($c80>0?3:($c80>-1?2:($c80>-2?1:0)))));
                $e81=($c81>0.2?5:($c81>0.1?4:($c81>0?3:($c81>-0.1?2:($c81>-0.2?1:0)))));
                $e82=($c82>0.2?5:($c82>0.1?4:($c82>0?3:($c82>-0.1?2:($c82>-0.2?1:0)))));
                $e83=($c83>2.3?1:($c83>1.5?3:($c83>=1?5:($c83>0.7?4:($c83>0.4?2:($c83>0.2?1:0))))));
                $e84=($c84>2.3?1:($c84>1.5?3:($c84>=1?5:($c84>0.7?4:($c84>0.4?2:($c84>0.2?1:0))))));
                $e85=($c85>0.032?1:($c85>0.024?2:($c85>0.016?3:($c85>0.008?4:($c85>0?5:5)))));
                $e87=($c87>0.2?5:($c87>0?4:($c87>-0.1?3:($c87>-0.2?2:($c87>-0.3?1:0)))));
                $e88=($c88>0.7?5:($c88>0.3?4:($c88>0.1?3:0)));
                $e89='NIL';
                $e90=($c90>0.3?0:($c90>0.25?1:($c90>0.2?2:($c90>0.15?3:($c90>0.1?4:5)))));
                $e91=($c91>0.25?0:($c91>0.2?1:($c91>0.15?2:($c91>0.1?3:($c91>0.05?4:5)))));
                $e92=($c92>0.2?5:($c92>0.1?3:($c92>0.05?1:0)));
                $e93='NIL';
                $e95=($c95>0.555?5:($c95>0.5?4:($c95>0.45?3:($c95>0.4?2:($c95>0.35?1:0)))));
                $e96=($c96>0.35?1:($c96>0.3?2:($c96>0.28?3:($c96>0.25?3:($c96>0.2?5:0)))));
                $e97=($c97>1?5:($c97>0.95?4:($c97>0.9?3:($c97>0.85?2:($c97>0.8?1:0)))));
                $e98='NIL';
                $e99=($c99>0.7?0:($c99>0.6?1:($c99>0.5?2:($c99>0.4?3:($c99>0.3?4:5)))));
                $e101=($c101>0.95?5:($c101>0.9?4:($c101>0.85?3:($c101>0.8?2:($c101>=0.75?1:0)))));
                $e102=($c102>0.95?5:($c102>0.9?4:($c102>0.85?3:($c102>0.8?2:($c102>=0.75?1:0)))));
                $e103=($c103>0.95?5:($c103>0.9?4:($c103>0.85?3:($c103>0.8?2:($c103>=0.75?1:0)))));
                $e104='NIL';
                $e105=($c105>1.08?0:($c105>1.04?1:($c105>1?3:($c105>0.96?5:($c105>0.92?3:($c105>0.88?1:0))))));
                $e106=($c106>0.95?5:($c106>0.9?4:($c106>0.85?3:($c106>0.8?2:($c106>=0.75?1:0)))));
                $e107=($c107>0.9?5:($c107>0.7?4:($c107>0.5?3:($c107>0.3?2:($c107>0.1?1:0)))));
                $e108=($c108>0.2?3:($c108>0.1?5:($c108>=0?1:0)));
                $e109='NIL';
                $e110=($c110>0.05?0:($c110>0?1:($c110>-0.1?2:($c110>-0.2?3:($c110>-0.3?4:5)))));
                $e111=($c111>0.95?5:($c111>0.9?4:($c111>0.85?3:($c111>0.8?2:($c111>=0.75?1:0)))));
                $e112=($c112>0.15?5:($c112>0.1?3:($c112>0.05?1:0)));
                $e113=($c113>0.95?5:($c113>0.9?4:($c113>0.85?3:($c113>0.8?2:($c113>=0.75?1:0)))));
                $e114='NIL';
                $e116=($c116>5?0:($c116>3?3:($c116>1?4:5)));
                $e117=($c117>0.3?0:($c117>0.2?1:($c117>0.1?3:5)));
                $e118=($c118>0.6?1:($c118>0.2?3:5));
                $e119=($c119>0.15?0:($c119>0.1?1:($c119>0.05?3:5)));
                $e120=($c120>1?5:($c120>0.8?3:1));
                $e121=($c121>1?5:($c121>0.8?3:1));
                $e123=($c123>0.3?0:($c123>0.2?1:($c123>0.1?3:5)));

                $f74=round(($e75+$e76+$e77+$e78+$e79+$e80+$e81+$e82+$e83+$e84+$e85)/55*30,2);
                $f86=round(($e87+$e88+$e89+$e90+$e91+$e92+$e93)/25*20,2);
                $f94=round(($e95+$e96+$e97+$e98+$e99)/20*25,2);
                $f100=round(($e101+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
                $f115=round(($e116+$e117+$e118+$e119+$e120+$e121+$e123)/35*10,2);
                $f73=$f74+$f86+$f94+$f100+$f115;
                $this->attr[] = array(
						'id'=>$record['id'],
						'year_no'=>$record['year_no'],
						'month_no'=>$record['month_no'],
                        'city'=>$record['cityname'],
                        'f74'=>$f74,
                        'f86'=>$f86,
                        'f94'=>$f94,
                        'f100'=>$f100,
                        'f115'=>$f115,
                        'f73'=>$f73
					);
			}
		}

		$session = Yii::app()->session;
		$session['criteria_a09'] = $this->getCriteria();
		return true;
	}

}
