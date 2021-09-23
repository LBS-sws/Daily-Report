<?php

class CustomertypeIDForm extends CFormModel
{
    /* User Fields */
    public $id;
    public $description;
    public $rpt_cat;
    public $single;
    public $index_num=1;
    public $cust_type_id;
    public $cust_type_name;
    public $detail = array(
        array('id'=>0,
            'cust_type_name'=>'',
            'conditions'=>'',
            'single'=>0,//是否是一次性服务 0：非一次性  1：一次性
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
            'conditions'=>Yii::t('code','Condition'),
            'fraction'=>Yii::t('code','Fractiony'),
            'toplimit'=>Yii::t('code','Toplimit'),
            'index_num'=>Yii::t('code','index num'),

        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id,description,rpt_cat,single,detail,cust_type_name,index_num','safe'),
            array('id','validateID','on'=>array('edit')),
        );
    }
    public function validateID($attribute, $params) {
        if($this->index_num==1){
            $row = Yii::app()->db->createCommand()->select()->from("swo_customer_type_id")
                ->where("id=:id",array(":id"=>$this->id))->queryRow();
        }else{
            $row = Yii::app()->db->createCommand()->select()->from("swo_customer_type_info")
                ->where("id=:id",array(":id"=>$this->id))->queryRow();
        }
        if(!$row){
            $this->addError($attribute,"服务id不存在，请刷新重试");
        }
    }

    public function retrieveData($index,$type=0)
    {
        $index = is_numeric($index)?$index:0;
        $this->index_num = 1;
        if (empty($type)){ //一級欄位
            $sql = "select * from swo_customer_type_id where id=$index";
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            if($row){
                $this->id = $row['id'];
                $this->description = $row['description'];
                $this->rpt_cat = $row['rpt_cat'];
                $this->single = $row['single'];
                $this->index_num = 1;
            }
        }else{//n級欄位
            $sql = "select * from swo_customer_type_info where id=$index";
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            if($row){
                $this->id = $row['id'];
                $this->cust_type_id = $row['cust_type_id'];
                $this->cust_type_name = $row['cust_type_name'];
                $this->index_num = $row['index_num'];
            }
        }
        $index_num = $this->index_num+1;
        $sql = "select * from swo_customer_type_info where cust_type_id=$index and index_num=$index_num";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $this->detail = array();
            foreach ($rows as $row) {
                $temp = array();
                $temp['id'] = $row['id'];
                $temp['cust_type_name'] = $row['cust_type_name'];
                $temp['conditions'] = $row['conditions'];
                $temp['single'] = $row['single'];
                $temp['fraction'] = $row['fraction'];
                $temp['toplimit'] = $row['toplimit'];
                $temp['index_num'] = $row['index_num'];
                $temp['uflag'] = 'N';
                $this->detail[] = $temp;
            }
        }
        if(empty($this->id)){
            return false;
        }
        return true;
    }

    public static function getLineTitleHtml($index,$num=2){
        $html = "";
        $lists = self::foreachParents($index,$num);
        if(!empty($lists)){
            $html = '<ol class="breadcrumb">';
            for ($i = count($lists)-1;$i>=0;$i--){
                if($lists[$i]['id'] == $index){
                    $html.="<li class='active'>".$lists[$i]['name']."</li>";
                }else{
                    $html.="<li><a href='{$lists[$i]['url']}'>".$lists[$i]['name']."</a></li>";
                }
            }
            $html .= '</ol>';
        }
        return $html;
    }

    //获取ID服务的一级栏位
    public static function getCustTypeRow(){
        $list=array();
        $rows = Yii::app()->db->createCommand()->select("id,description")->from("swo_customer_type_id")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["description"];
            }
        }
        return $list;
    }

    //
    public static function getCustTypeInfoNameForId($id){
        $row = Yii::app()->db->createCommand()->select("cust_type_name")->from("swo_customer_type_info")
            ->where("id=:id",array(":id"=>$id))
            ->queryRow();
        if($row){
            return $row["cust_type_name"];
        }
        return $id;
    }

    //获取ID服务的栏位Json
    public static function getCustTypeJson(){
        $all = Yii::app()->db->createCommand()->select("id,cust_type_id,cust_type_name as name,index_num")->from("swo_customer_type_info")
            ->order("index_num asc")->queryAll();
        $list = $all?$all:array();
        return json_encode($list);
    }

    public function foreachParents($index,$num=2){
        $list = array();
        if($num>1){
            $num--;
            $row = Yii::app()->db->createCommand()->select("cust_type_name,cust_type_id")->from("swo_customer_type_info")
                ->where("id=:id",array(":id"=>$index))->queryRow();
            $url = Yii::app()->createUrl('customertypeID/edit',array("index"=>$index,"type"=>1));
            $list[]=array("id"=>$index,"name"=>$row["cust_type_name"],"url"=>$url);
            if($row["cust_type_id"] != $index){
                $list = array_merge($list,self::foreachParents($row["cust_type_id"],$num));
            }
        }else{
            $url = Yii::app()->createUrl('customertypeID/edit',array("index"=>$index));
            $row = Yii::app()->db->createCommand()->select("description,id")->from("swo_customer_type_id")
                ->where("id=:id",array(":id"=>$index))->queryRow();
            $list[]=array("id"=>$index,"name"=>$row["description"],"url"=>$url);
        }
        return $list;
    }

    public function saveData()
    {
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            if($this->index_num==1){
                $this->saveUser($connection);
            }
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
                $sql = "delete from swo_customer_type_id where id = :id";
                break;
            case 'new':
                $sql = "insert into swo_customer_type_id(
						description, rpt_cat,single, luu, lcu) values (
						:description, :rpt_cat,:single, :luu, :lcu)";
                break;
            case 'edit':
                $sql = "update swo_customer_type_id set 
					description = :description, 
					rpt_cat = :rpt_cat,
					single = :single,
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


    protected function deleteFearchInfo($id,$index){
        $index++;
        Yii::app()->db->createCommand()->delete("swo_customer_type_info","id=:id",array(":id"=>$id));
        $rows = Yii::app()->db->createCommand()->select("id")->from("swo_customer_type_info")
            ->where("cust_type_id=:id and index_num=:index_num",
                array(":id"=>$id,":index_num"=>$index)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->deleteFearchInfo($row["id"],$index);
            }
        }
    }

    protected function saveCustomertypeDtl(&$connection)
    {
        $uid = Yii::app()->user->id;
        if($this->getScenario()=="delete"){
            $this->deleteFearchInfo($this->id,$this->index_num);
        }
        foreach ($this->detail as $row) {
            $sql = '';
            switch ($this->scenario) {
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into swo_customer_type_info(
									cust_type_id,index_num, cust_type_name, single, fraction, toplimit, conditions,
									 luu, lcu
								) values (
									:cust_type_id,:index_num, :cust_type_name, :single, :fraction, :toplimit, :conditions,
									 :luu, :lcu
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $this->deleteFearchInfo($row['id'],$this->index_num);
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into swo_customer_type_info(
										cust_type_id,index_num, cust_type_name, single, fraction, toplimit, conditions,
										 luu, lcu
									) values (
										:cust_type_id,:index_num, :cust_type_name, :single, :fraction, :toplimit,:conditions,
										:luu, :lcu
									)
									"
                                :
                                "update swo_customer_type_info set
										cust_type_id = :cust_type_id,
										cust_type_name = :cust_type_name, 
										single = :single,
										conditions = :conditions,
										fraction = :fraction,									
										toplimit = :toplimit,
										luu = :luu 
									where id = :id and index_num=:index_num
									";
                            break;
                    }
                    break;
            }

            if ($sql != '') {
                $command=$connection->createCommand($sql);
                if (strpos($sql,':id')!==false)
                    $command->bindParam(':id',$row['id'],PDO::PARAM_INT);
                if (strpos($sql,':index_num')!==false){
                    $index_num = $this->index_num+1;
                    $command->bindParam(':index_num',$index_num,PDO::PARAM_INT);
                }
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
        if($this->index_num==1){
            $sql = "select a.id from swo_serviceid a where a.cust_type=".$index." limit 1";
        }else{
            $sql = "select a.id from swo_serviceid a where a.cust_type_name=".$index." limit 1";
        }
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
