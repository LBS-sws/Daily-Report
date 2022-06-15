<?php

class CustomertypeForm extends CFormModel
{
    /* User Fields */
    public $id;
    public $description;
    public $rpt_cat;
    public $single;
    public $sales_rate=0;//是否参加销售提成计算
    public $display=0;//是否显示（暂不使用）
    public $z_index=0;//层级（暂不使用）
    public $detail = array(
        array('id'=>0,
            'cust_type_name'=>'',
            'conditions'=>'',
            'single'=>0,//是否是一次性服务 0：非一次性  1：一次性
            'bring'=>0,//是否計算創新獎勵點 0：不計算 1：計算
            'fraction'=>0,
            'toplimit'=>0,
            'uflag'=>'N',
        ),
    );
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'description'=>Yii::t('code','Description'),
            'rpt_cat'=>Yii::t('code','Report Category'),
            'cust_type_name'=>Yii::t('code','Cust Type Name'),
            'single'=>Yii::t('code','service type'),
            'bring'=>Yii::t('code','bring'),
            'conditions'=>Yii::t('code','Condition'),
            'fraction'=>Yii::t('code','Fractiony'),
            'toplimit'=>Yii::t('code','Toplimit'),
            'sales_rate'=>Yii::t('code','sales rate'),

        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('description','required'),
            array('id,rpt_cat,single,sales_rate','safe'),
        );
    }

    public function retrieveData($index)
    {
        $sql = "select * from swo_customer_type where id=".$index."";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $this->id = $row['id'];
                $this->description = $row['description'];
                $this->rpt_cat = $row['rpt_cat'];
                $this->sales_rate = $row['sales_rate'];
                $this->single = $row['single'];
                break;
            }
        }
        $sql = "select * from swo_customer_type_twoname where cust_type_id=$index ";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $this->detail = array();
            foreach ($rows as $row) {
                $temp = array();
                $temp['id'] = $row['id'];
                $temp['cust_type_name'] = $row['cust_type_name'];
                $temp['conditions'] = $row['conditions'];
                $temp['single'] = $row['single'];
                $temp['bring'] = $row['bring'];
                $temp['fraction'] = $row['fraction'];
                $temp['toplimit'] = $row['toplimit'];
                $temp['uflag'] = 'N';
                $this->detail[] = $temp;
            }
        }
        return true;
    }

    public function saveData()
    {
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $this->saveUser($connection);
            $this->saveCustomertypeDtl($connection);
            $transaction->commit();
        }
        catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update.');
        }
    }

    protected function saveUser(&$connection)
    {
        $sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from swo_customer_type where id = :id";
                break;
            case 'new':
                $sql = "insert into swo_customer_type(
						description, rpt_cat,single,sales_rate, luu, lcu) values (
						:description, :rpt_cat,:single,:sales_rate, :luu, :lcu)";
                break;
            case 'edit':
                $sql = "update swo_customer_type set 
					description = :description, 
					rpt_cat = :rpt_cat,
					single = :single,
					sales_rate = :sales_rate,
					luu = :luu
					where id = :id";
                break;
        }

        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':description')!==false)
            $command->bindParam(':description',$this->description,PDO::PARAM_STR);
        if (strpos($sql,':single')!==false)
            $command->bindParam(':single',$this->single,PDO::PARAM_INT);
        if (strpos($sql,':sales_rate')!==false)
            $command->bindParam(':sales_rate',$this->sales_rate,PDO::PARAM_INT);
        if (strpos($sql,':rpt_cat')!==false)
            $command->bindParam(':rpt_cat',$this->rpt_cat,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();
        return true;
    }

    protected function saveCustomertypeDtl(&$connection)
    {
        $uid = Yii::app()->user->id;
//        print_r('<pre>');
//        print_r($this->id);
//        print_r($_POST['CustomertypeForm']['detail']);
//        exit();
        foreach ($_POST['CustomertypeForm']['detail'] as $row) {
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from swo_customer_type_twoname where cust_type_id = :cust_type_id ";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into swo_customer_type_twoname(
									cust_type_id, cust_type_name, single, fraction, toplimit, conditions,
									 luu, lcu
								) values (
									:cust_type_id, :cust_type_name, :single, :fraction, :toplimit, :conditions,
									 :luu, :lcu
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from swo_customer_type_twoname where id = :id ";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into swo_customer_type_twoname(
										cust_type_id, cust_type_name, single, bring, fraction, toplimit, conditions,
										 luu, lcu
									) values (
										:cust_type_id, :cust_type_name, :single, :bring, :fraction, :toplimit,:conditions,
										:luu, :lcu
									)
									"
                                :
                                "update swo_customer_type_twoname set
										cust_type_id = :cust_type_id,
										cust_type_name = :cust_type_name, 
										single = :single,
										conditions = :conditions,
										fraction = :fraction,									
										bring = :bring,									
										toplimit = :toplimit,
										luu = :luu 
									where id = :id 
									";
                            break;
                    }
                    break;
            }

            if ($sql != '') {
                $command=$connection->createCommand($sql);
                if (strpos($sql,':id')!==false)
                    $command->bindParam(':id',$row['id'],PDO::PARAM_INT);
                if (strpos($sql,':cust_type_id')!==false)
                    $command->bindParam(':cust_type_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':single')!==false)
                    $command->bindParam(':single',$row['single'],PDO::PARAM_INT);
                if (strpos($sql,':cust_type_name')!==false)
                    $command->bindParam(':cust_type_name',$row['cust_type_name'],PDO::PARAM_STR);
                if (strpos($sql,':conditions')!==false)
                    $command->bindParam(':conditions',$row['conditions'],PDO::PARAM_INT);
                if (strpos($sql,':fraction')!==false)
                    $command->bindParam(':fraction',$row['fraction'],PDO::PARAM_INT);
                if (strpos($sql,':toplimit')!==false)
                    $command->bindParam(':toplimit',$row['toplimit'],PDO::PARAM_INT);
                if (strpos($sql,':bring')!==false)
                    $command->bindParam(':bring',$row['bring'],PDO::PARAM_INT);
                if (strpos($sql,':luu')!==false)
                    $command->bindParam(':luu',$uid,PDO::PARAM_STR);
                if (strpos($sql,':lcu')!==false)
                    $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
                $command->execute();
            }
        }
        return true;
    }

    public function isOccupied($index) {
        $rtn = false;
        $sql = "select a.id from swo_service a where a.cust_type=".$index." limit 1";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $row) {
            $rtn = true;
            break;
        }
        return $rtn;
    }

    public function isReadOnly() {
        return ($this->scenario=='view');
    }
}
