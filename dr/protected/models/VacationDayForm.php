<?php


class VacationDayForm
{
    public $employee_id;
    public $vacation_id;
    public $employee_list;
    public $vacation_list;//後期修改，不循環查詢（2019-10-28）
    public $time;
    public $city;//員工所在城市
    public $vaca_type;
    public $diffMonth=0;
    public $remain_bool=false;//该假期类型是否有规则
    public $yearLeaveType=0;//年假的計算方式（2020-07-07）0：正常  1:新加坡  2:吉隆坡）

    protected $vacation_sum=0;//剩餘天數
    protected $sumDay=0;//累計天數（不包含額外添加的年假）
    protected $useDay=0;//已使用天數
    protected $extraDay=0;//累计年假的天數（人事系統的累計年假）
    protected $vacation_id_list=array();
    protected $start_time;
    protected $end_time;

    protected $year_type = "E";//年假

    protected $error_bool = false;

    public function __construct($employee_id='',$vacation_id='',$time='',$vaca_type='E')
    {
        $time = empty($time)?date("Y/m/d"):$time;
        $this->employee_id = $employee_id;
        $this->vacation_id = $vacation_id;
        $this->vaca_type = empty($vacation_id)?$vaca_type:"";
        $this->time = $time;
        $this->init();
    }

    public function init(){
        if(!empty($this->vacation_id)){
            $this->setVacationId($this->vacation_id);
        }
        if(!empty($this->employee_id)){
            $this->setEmployeeList($this->employee_id);
        }
    }

    public function setYearLeaveType($int=-1){
        if(in_array($int,array(0,1,2))){
            $this->yearLeaveType = $int;
        }else{
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("set_value")->from("hr$suffix.hr_setting")
                ->where('set_name="yearLeaveType" and set_city=:city',array(":city"=>$this->city))->queryRow();
            if($row){
                if(in_array($row["set_value"],array(0,1,2))){
                    $this->yearLeaveType = $row["set_value"];
                }
            }
        }
    }

    public function setEmployeeList($employee_id){
        $this->employee_id = $employee_id;
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("year_day,city,entry_time")->from("hr$suffix.hr_employee")
            ->where('id=:id',array(':id'=>$employee_id))->queryRow();
        if($rows){
            $this->employee_list = $rows;
            $this->diffMonth = -1;
            $this->remain_bool = false;
            $this->error_bool = false;
            $this->sumDay = 0;
            $this->useDay = 0;
            if($this->vaca_type == $this->year_type&&$this->city != $rows["city"]){
                $this->city = $rows["city"];
                $this->setYearLeaveType();
                $this->setVacationType();
            }else{
                $this->city = $rows["city"];
            }
        }else{
            $this->error_bool = true;
        }
    }

    public function getErrorBool(){
        return $this->error_bool;
    }

    private function setVacationType(){
        $this->vacation_list = '';
        $this->vacation_id_list = array();
        if ($this->vaca_type == $this->year_type){ //特別處理年假
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("*")->from("hr$suffix.hr_vacation")
                ->where("vaca_type=:vaca_type and (city=:city OR only='default') ",
                    array(":vaca_type"=>$this->year_type,":city"=>$this->city))->queryRow();
            if($row){
                $this->vaca_type = $row["vaca_type"];
                $this->vacation_id = $row["id"];
                $this->vacation_list = $row;
                if($row['ass_bool'] == 1){ //有關聯假期規則
                    $this->vacation_id_list = explode(",",$row['ass_id']);
                }
                $this->vacation_id_list[] = $row["id"];
            }else{
                $this->error_bool = true;
            }
        }
        $this->vacation_sum = 0;
        $this->remain_bool = false;
        $this->error_bool = false;
        $this->sumDay = 0;
        $this->useDay = 0;
    }

    private function setVacationId($vacation_id){
        $this->vacation_list = '';
        $this->vacation_id_list = array();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("hr$suffix.hr_vacation")
            ->where("id=:id",array(":id"=>$vacation_id))->queryRow();
        if($row){
            $this->vaca_type = $row["vaca_type"];
            $this->vacation_id = $row["id"];
            $this->vacation_list = $row;
            if($row['ass_bool'] == 1){ //有關聯假期規則
                $this->vacation_id_list = explode(",",$row['ass_id']);
            }
            $this->vacation_id_list[] = $row["id"];
        }else{
            $this->error_bool = true;
        }
        $this->vacation_sum = 0;
        $this->remain_bool = false;
        $this->error_bool = false;
        $this->sumDay = 0;
        $this->useDay = 0;
    }

    //計算相隔多少月份
    private function diffBetweenToMonth(){
        if($this->employee_list){
            $entry_time = strtotime($this->employee_list["entry_time"]);
            $time = strtotime($this->time);
            if($entry_time<$time){
                $year = date("Y",$time);
                $diffYear = date("Y",$time)-date("Y",$entry_time);
                $diffMonth = date("m",$time)-date("m",$entry_time);
                $diffDay = date("d",$time)-date("d",$entry_time);
                if($diffYear>0){
                    $diffMonth +=($diffYear*12);
                }
                if($diffDay<0){
                    $diffMonth--;
                }
                $this->diffMonth = $diffMonth;

                if($this->yearLeaveType == 0){
                    //大陸版的一年：員工月份為起點
                    if(date("m-d",$time)>=date("m-d",$entry_time)){
                        $this->start_time = $year.date("/m/d",$entry_time);
                        $this->end_time = (intval($year)+1).date("/m/d",$entry_time);
                    }else{
                        $this->start_time = (intval($year)-1).date("/m/d",$entry_time);
                        $this->end_time = $year.date("/m/d",$entry_time);
                    }
                }else{
                    $this->start_time = $year."/01/01";
                    $this->end_time = $year."/12/31";
                }
            }else{
                $this->diffMonth = 0;
                $this->start_time = '';
                $this->end_time = '';
            }
        }
    }

    public function getEndTime(){
        return $this->end_time;
    }

    public function getVacationSum($lcd=''){
        $this->diffBetweenToMonth();//計算時間段
        $this->foreachVacationSum($this->vacation_id);//計算總假期天數
        $this->foreachVacationUse($lcd);//減去已申請的假期

        return $this->vacation_sum;
    }

    //計算已申請多少假期
    private function foreachVacationUse($lcd=''){
        if(!$this->employee_list){
            return false;
        }
        if(empty($lcd)){
            $statusSql = " and status IN (1,2,4)";
        }else{
            $lcd = date("Y/m/d H:i:s",strtotime($lcd));
            $statusSql = " and status =  4 and date_format(lcd,'%Y/%m/%d %H:%i:%s')<='$lcd'";
        }
        if(!empty($this->vacation_id_list[0])){
            $vacation_id_list = implode(",",$this->vacation_id_list);
            $statusSql.=" and vacation_id in ($vacation_id_list)";
        }else{
            $statusSql.=" and vacation_id in ('".$this->vacation_id."')";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("sum(log_time)")->from("hr$suffix.hr_employee_leave")
            ->where("employee_id=:employee_id $statusSql and date_format(start_time,'%Y/%m/%d')>=:start_time and date_format(start_time,'%Y/%m/%d')<:end_time",
                array(":employee_id"=>$this->employee_id,":start_time"=>$this->start_time,":end_time"=>$this->end_time))->queryScalar();

        //bcsub(); 函数不可以，沒安裝php-bcmath扩展
        $this->vacation_sum = strval($this->vacation_sum);
        $this->vacation_sum-=$sum;
        $this->useDay = $sum;
    }

    public function getSumDay(){
        return $this->sumDay;
    }

    public function getUseDay(){
        return $this->useDay;
    }

    public function getExtraDay(){
        return $this->extraDay;
    }


    //計算在假期規則裡是多少天假期
    private function foreachVacationSum($vacation_id){
        if ($this->error_bool){
            return false;
        }
        $suffix = Yii::app()->params['envSuffix'];
        if(empty($this->vacation_list)){
            $row = Yii::app()->db->createCommand()->select("*")->from("hr$suffix.hr_vacation")
                ->where('id=:id and (city=:city OR only="default")',array(':id'=>$vacation_id,':city'=>$this->city))->queryRow();
            if($row['ass_bool'] == 1){ //有關聯假期規則
                $this->vacation_id_list = explode(",",$row['ass_id']);
            }
            $this->vaca_type = $row["vaca_type"];
            $this->vacation_id_list[] = $row["id"];
            $this->vacation_list = $row;
        }

        if($this->vaca_type==$this->year_type){ //年假類型
            $this->remain_bool = true;
            switch ($this->yearLeaveType){
                case 0://正常（大陸版、台灣版）
                    $this->addEmployeeNum();//年假根據員工信息計算
                    break;
                case 1://1：新加坡
                    $this->addEmployeeNumToOne();
                    break;
                case 2://  2：吉隆坡
                    $this->addEmployeeNumToTwo();
                    break;
            }
            $this->addYearLeaveNum();//根據假期種類，分別對待
        }else{
            $this->addRulesNum($this->vacation_list);//假期規則添加天數
            $this->sumDay=$this->vacation_sum;
        }
    }

    //根據員工信息添加年假
    private function addEmployeeNum(){
        if($this->employee_list){
            $this->sumDay=$this->employee_list["year_day"];
            if($this->diffMonth>=12){
                $this->vacation_sum=$this->employee_list["year_day"];
            }else{
                $this->vacation_sum=0;
            }
        }
    }

    //递归计算累计的年假
    private function foreachYearAddSum($foreachYear,$sumDay=0){
        if(!empty($this->time)){
            $thisYear = intval(date("Y",strtotime($this->time)));
            $thisMonth = intval(date("m",strtotime($this->time)));
            $thisDay = intval(date("d",strtotime($this->time)));
        }else{
            $thisYear = intval(date("Y"));
            $thisMonth = intval(date("m"));
            $thisDay = intval(date("d"));
        }
        $staffYear = intval(date("Y",strtotime($this->employee_list["entry_time"])));
        $staffMonth = intval(date("m",strtotime($this->employee_list["entry_time"])));
        $staffDay = intval(date("d",strtotime($this->employee_list["entry_time"])));
        if($foreachYear>$thisYear){
            if(intval($sumDay)!=floatval($sumDay)){
                return round(floatval($sumDay),1);
            }else{
                return floatval($sumDay);
            }
        }else{
            if($foreachYear==$thisYear){ //循環的最後一次
                if($thisYear == $staffYear){//只循環一次(入職的第一年)
                    $diffMonth =$staffDay>$thisDay?($thisMonth-$staffMonth-1):$thisMonth-$staffMonth;
                    $diffDay = strtotime("$thisYear/$thisMonth/$thisDay")-strtotime($this->employee_list["entry_time"]);
                    $diffDay = $diffDay/(60*60*24);
                }else{
                    $diffMonth =$thisMonth;
                    $diffDay = strtotime("$thisYear/$thisMonth/$thisDay")-strtotime("$thisYear/01/01");
                    $diffDay = $diffDay/(60*60*24);
                }
            }elseif($staffYear==$foreachYear){//循環開始的第一年
                $diffMonth = 12-$staffMonth;//間隔月份
                $diffDay = strtotime("$staffYear/12/31")-strtotime($this->employee_list["entry_time"]);//間隔天數
                $diffDay = $diffDay/(60*60*24);
            }else{//循環的中間年
                $diffMonth = 12;//間隔月份
                $diffDay = 365;//間隔天數
            }
            switch ($this->yearLeaveType){
                case 1://新加坡
                    $sumDay = $sumDay>10?10:$sumDay;//累計的年假不允許超過10天
                    $sumDay+=floatval($this->employee_list["year_day"])/12*$diffMonth;
                    break;
                case 2://吉隆坡
                    $num = floatval($this->employee_list["year_day"])/2;
                    $sumDay = $sumDay>$num?$num:$sumDay;//累計年假不能超過自身年假的一半
                    $sumDay+=floatval($this->employee_list["year_day"])/365*$diffDay;
                    break;
            }
            if($foreachYear!=$thisYear){ //如果不是最後一次循環
                $this->setSumDayToForeachYear($sumDay,$foreachYear);
            }

            $foreachYear++;
            return $this->foreachYearAddSum($foreachYear,$sumDay);
        }
    }
    private function setSumDayToForeachYear(&$sumDay,$foreachYear){
        //系統手動添加的累計年假
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("sum(add_num)")->from("hr$suffix.hr_staff_year")
            ->where("employee_id=:employee_id and year=:year",array(":employee_id"=>$this->employee_id,":year"=>$foreachYear))->queryScalar();
        $sumDay+=$sum;
        //用掉的年假
        $sum = Yii::app()->db->createCommand()->select("sum(log_time)")->from("hr$suffix.hr_employee_leave")
            ->where("employee_id=:employee_id and status IN (1,2,4) and date_format(start_time,'%Y')=:year",
                array(":employee_id"=>$this->employee_id,":year"=>$foreachYear))->queryScalar();
        $sumDay-=$sum;
    }

    //根據員工信息添加年假（新加坡）
    private function addEmployeeNumToOne(){
        if($this->employee_list){
            $this->sumDay=$this->employee_list["year_day"];
            if($this->diffMonth>=3){
                $foreachYear = date("Y",strtotime($this->employee_list["entry_time"]));
                $foreachYear = intval($foreachYear);
                $this->vacation_sum=$this->foreachYearAddSum($foreachYear);
            }else{
                $this->vacation_sum=0;
            }
        }
    }

    //根據員工信息添加年假（吉隆坡）
    private function addEmployeeNumToTwo(){
        if($this->employee_list){
            $this->sumDay=$this->employee_list["year_day"];
            if($this->diffMonth>=3){
                $foreachYear = date("Y",strtotime($this->employee_list["entry_time"]));
                $foreachYear = intval($foreachYear);
                $this->vacation_sum=$this->foreachYearAddSum($foreachYear);
            }else{
                $this->vacation_sum=0;
            }
        }
    }

    private function addRulesNum($row){
        if($row['log_bool'] == 1){//有假期規則
            $this->remain_bool = true;
            $max_log = json_decode($row['max_log'],true);
            foreach ($max_log as $list){
                if ($this->diffMonth<$list["monthLong"]){
                    if($this->vacation_sum<$list["dayNum"]){
                        $this->vacation_sum=$list["dayNum"];
                    }
                    break;
                }elseif($list["monthLong"]==="other"){
                    if($this->vacation_sum<$list["dayNum"]){
                        $this->vacation_sum=$list["dayNum"];
                    }
                    break;
                }
            }
        }
    }

    //累計年假
    private function addYearLeaveNum($vacation_list=''){
        switch ($this->vaca_type){
            case $this->year_type://年假（需要添加累計年假的天數）
                $year = date("Y",strtotime($this->start_time));
                $suffix = Yii::app()->params['envSuffix'];
                $sum = Yii::app()->db->createCommand()->select("sum(add_num)")->from("hr$suffix.hr_staff_year")
                    ->where("employee_id=:employee_id and year=:year",array(":employee_id"=>$this->employee_id,":year"=>$year))->queryScalar();
                //var_dump($sum);
                $this->vacation_sum+=$sum;
                //$this->sumDay+=$sum;
                $this->extraDay = $sum;
                break;
        }
    }

    public function setTime($time){
        $this->time = $time;
    }

    public function getEmployeeList(){
        return $this->employee_list;
    }

    private function LeaveTime($entry_time,$time){
        $year = date("Y",strtotime($time));
        $month = date("m/d",strtotime($time));
        $entry_time = date("m/d",strtotime($entry_time));
        if($entry_time>$month){
            $year--;
        }

        return array(
            "minDay"=>$year."/".$entry_time,
            "maxDay"=>($year+1)."/".$entry_time,
        );
    }
}
