<?php

class ManageStopSetForm extends CFormModel
{
    /* User Fields */
    public $id;
    public $start_date;
    public $set_name;
    public $detail = array(
        array('id'=>0,
            'hdrId'=>'',
            'operator'=>'',
            'stopRate'=>0,//
            'coefficient'=>0,//
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
            'start_date'=>Yii::t('summary','Effective date'),
            'set_name'=>Yii::t('summary','setting name'),
            'operator'=>Yii::t('summary','operator'),
            'stopRate'=>Yii::t('summary','Composite Stop Rate'),
            'coefficient'=>Yii::t('summary','Commission coefficient'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('start_date,set_name','required'),
            array('id,start_date,set_name,detail','safe'),
        );
    }

    public function retrieveData($index)
    {
        $sql = "select * from swo_manage_stop_hdr where id=".$index."";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row){
            $this->id = $row['id'];
            $this->start_date = General::toDate($row['start_date']);
            $this->set_name = $row['set_name'];
            $sql = "select * from swo_manage_stop_hdl where hdr_id=$index ";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            if (count($rows) > 0) {
                $this->detail = array();
                foreach ($rows as $row) {
                    $temp = array();
                    $temp['id'] = $row['id'];
                    $temp['hdrId'] = $row['hdr_id'];
                    $temp['operator'] = $row['operator'];
                    $temp['stopRate'] = floatval($row['stop_rate']);
                    $temp['coefficient'] = floatval($row['coefficient']);
                    $temp['uflag'] = 'N';
                    $this->detail[] = $temp;
                }
            }
            return true;
        }
        return false;
    }

    public function saveData()
    {
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $this->saveUser($connection);
            $this->saveManageStopSetDtl($connection);
            $transaction->commit();
        }
        catch(Exception $e) {
            var_dump($e);
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update.');
        }
    }

    protected function saveUser(&$connection)
    {
        $sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from swo_manage_stop_hdr where id = :id";
                break;
            case 'new':
                $sql = "insert into swo_manage_stop_hdr(
						start_date, set_name, luu, lcu) values (
						:start_date, :set_name, :luu, :lcu)";
                break;
            case 'edit':
                $sql = "update swo_manage_stop_hdr set 
					start_date = :start_date, 
					set_name = :set_name,
					luu = :luu
					where id = :id";
                break;
        }

        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':start_date')!==false)
            $command->bindParam(':start_date',$this->start_date,PDO::PARAM_STR);
        if (strpos($sql,':set_name')!==false)
            $command->bindParam(':set_name',$this->set_name,PDO::PARAM_INT);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();
        return true;
    }

    protected function saveManageStopSetDtl(&$connection)
    {
        $uid = Yii::app()->user->id;
        foreach ($_POST['ManageStopSetForm']['detail'] as $row) {
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from swo_manage_stop_hdl where hdr_id = :hdr_id ";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into swo_manage_stop_hdl(
									hdr_id, operator, stop_rate, coefficient,
									 luu, lcu
								) values (
									:hdr_id, :operator, :stop_rate, :coefficient,
									 :luu, :lcu
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from swo_manage_stop_hdl where id = :id ";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into swo_manage_stop_hdl(
										hdr_id, operator, stop_rate, coefficient,
										 luu, lcu
									) values (
										:hdr_id, :operator, :stop_rate, :coefficient,
										:luu, :lcu
									)
									"
                                :
                                "update swo_manage_stop_hdl set
										operator = :operator, 
										stop_rate = :stop_rate,
										coefficient = :coefficient,
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
                if (strpos($sql,':hdr_id')!==false)
                    $command->bindParam(':hdr_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':operator')!==false)
                    $command->bindParam(':operator',$row['operator'],PDO::PARAM_INT);
                if (strpos($sql,':stop_rate')!==false){
                    $row['stop_rate'] = empty($row['stopRate'])?0:$row['stopRate'];
                    $command->bindParam(':stop_rate',$row['stop_rate'],PDO::PARAM_INT);
                }
                if (strpos($sql,':coefficient')!==false){
                    $row['coefficient'] = empty($row['coefficient'])?0:$row['coefficient'];
                    $command->bindParam(':coefficient',$row['coefficient'],PDO::PARAM_INT);
                }
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
        return true;
    }

    public function isReadOnly() {
        return ($this->scenario=='view');
    }
}
