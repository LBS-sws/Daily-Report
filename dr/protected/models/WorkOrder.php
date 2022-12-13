<?php

class WorkOrder extends CListPageModel
{
    // For manually adjust the label content
    protected $rpt_labels = array(
        'city' => array('label' => 'City', 'width' => 20, 'align' => 'C'),
        'start_at' => array('label' => 'Start Date', 'width' => 25, 'align' => 'C'),
        'end_dt' => array('label' => 'End Date', 'width' => 25, 'align' => 'C'),
//        'week_1'=>array('label'=>'Week 1','width'=>25,'align'=>'C'),
//        'week_2'=>array('label'=>'Week 2','width'=>25,'align'=>'C'),
//        'week_3'=>array('label'=>'Week 3','width'=>25,'align'=>'C'),
//        'week_4'=>array('label'=>'Week 4','width'=>25,'align'=>'C'),
    );
    protected $ret = null;
    public $id;
    public $name;
    public $start_at;
    public $end_at;
    public $format;
    public $uid;
    public $city;
    public $target_at;
    public $fields;
    public $email;
    public $emailcc;
    public $touser;
    public $ccuser;
    public $year;
    public $follow_staff;
    public $type;
    public $form;
    public $staffs_desc;
    public $suffix = '';

    public function __construct()
    {

//        $this->se_suffix = Yii::app()->params['envSuffix'];
        $this->suffix = Yii::app()->params['envSuffix'];
//        $this->tab_suffix = Yii::app()->params['table_envSuffix'];
    }

    public function attributeLabels()
    {
        return array(
            'start_at' => Yii::t('statement', 'Start Date'),
            'end_at' => Yii::t('statement', 'End Date'),
            'city' => Yii::t('statement', 'City'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
//            array('id'),
            array('start_at, end_at, target_at', 'date', 'allowEmpty' => true,
                'format' => array('yyyy/MM/dd', 'yyyy-MM-dd', 'yyyy/M/d', 'yyyy-M-d',),
            ),
//            array('email','validateEmail'),
//            array('emailcc','validateEmailList'),
        );
    }


    public function fields()
    {
        return $this->rpt_labels;
    }

    public function getSelectString()
    {
        return Yii::t('returneport', 'Date') . ': ' . $this->criteria->year . '/' . $this->criteria->month;
    }

    public function retrieveData()
    {
        $year = empty($this->criteria->year) ? date('Y') : $this->criteria->year;
        $month = empty($this->criteria->month) ? date('m') : $this->criteria->month;
        $first_date = $year . '-' . $month . '-01';
//        $last_date = date('Y-m-d',strtotime(date('Y-m-d',strtotime($first_date.' +1 month')).' -1 day'));
//        $first_date2 = date('Y-m-d',strtotime($first_date.' -1 day'));
        $sql = "select * from service{$this->suffix}.officecity a left join service{$this->suffix}.enums b on b.EnumID=a.City AND b.EnumType = 1";
        $ret = Yii::app()->db->createCommand($sql)->queryAll();
        return $ret;
    }


    public function getStaff($city = '')
    {
        $sql = "select * from service{$this->suffix}.staff a left join service{$this->suffix}.enums b on b.EnumID=a.City AND b.EnumType = 1 where City = {$city} ";
        $ret = Yii::app()->db->createCommand($sql)->queryAll();
        return $ret;
    }

    public function getJob($data)
    {
//        $start_date = date('Y-m-d',strtotime($data['start_date']));
//        $end_date = date('Y-m-d',strtotime($data['end_date']));
        $staff_sql = "";
        if (isset($data['staff']) && !empty($data['staff'])) {
            $staff_sql = "and a.Staff01 = '" . $data['staff'] . "'";
        }
        switch ($data['service_type']) {
            case '1':
                $table = "service{$this->suffix}.joborder";
                $rangDate = 'FinishDate';
                break;
            case '2':
                $table = "service{$this->suffix}.followuporder";
                $rangDate = 'jobDate';
                break;
            default:
                $table = "service{$this->suffix}.joborder";
                $rangDate = 'FinishDate';
        }
        Yii::app()->db->createCommand("set session sql_mode = 'NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES'")->execute();
        $start_date = date('Y-m-d', strtotime($data['start_date'])) . " 00:00:00";
        $end_date = date('Y-m-d', strtotime($data['end_date'])) . " 23:59:59";
//        is_mark == 1 表示大于开启
        if ((isset($data['time_point_start']) && $data['time_point_start'] >= 0) && $data['time_point_end'] == 0) {
            $is_mark = '>=';
            $condition = "COUNT(IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']}, TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']}, NULL
    )) AS unusual,
    COUNT(IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_start']}, TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_start']}, NULL
    )) AS normal,";

            $condition_count = "IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']},'0','1') as 'scene',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']},'异常单','正常单') as 'name'";

        }
        if ((isset($data['time_point_end']) && $data['time_point_end'] >= 0) && $data['time_point_start'] == 0) {
            $is_mark = '<=';
            $condition = "COUNT(IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']}, TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']}, NULL
    )) AS unusual,
    COUNT(IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_end']}, TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_end']}, NULL
    )) AS normal,";

            $condition_count = "IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']},'0','1') as 'scene',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']},'异常单','正常单') as 'name'";

        }

        if ((isset($data['time_point_start']) && $data['time_point_start'] > 0) && (isset($data['time_point_end']) && $data['time_point_end'] > 0)) {

            $condition = "COUNT(IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']}, TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']}, NULL
    )) AS unusual,
    COUNT(1) - COUNT(IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']}, TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']}, NULL
    )) AS normal,";
            $condition_count = "IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']},'0','1') as 'scene',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']},'异常单','正常单') as 'name'";

        }


        $sql = "SELECT 
    COUNT(1) AS total,
    {$condition}
	b.Text AS city_name,
	IFNULL(c.StaffName ,'')  AS staff_name, 
	IFNULL(c.StaffId ,'')  AS staff_id, 
	{$rangDate} 
FROM
	{$table} a
	LEFT JOIN service{$this->suffix}.enums b ON b.EnumID = a.City
	 JOIN service{$this->suffix}.staff c ON c.StaffID = a.Staff01
WHERE
	JobDate BETWEEN '{$start_date}' AND '{$end_date}'" . $staff_sql . "
	AND b.EnumID = '{$data['city']}' AND b.EnumType = 1 AND a.Staff01 !='' AND a.`Status` = 3
GROUP BY staff_id ORDER BY {$rangDate} DESC";


        $ret['data'] = Yii::app()->db->createCommand($sql)->queryAll();
        $sql_count = "SELECT COUNT(1) AS value , FinishTime - StartTime AS service_time,
{$condition_count}
	, b.Text AS city_name, c.StaffName AS staff_name, {$rangDate} AS {$rangDate},CustomerName AS CustomerName
FROM {$table} a
	LEFT JOIN service{$this->suffix}.enums b ON b.EnumID = a.City
	 JOIN service{$this->suffix}.staff c ON c.StaffID = a.Staff01
WHERE JobDate BETWEEN '{$start_date}'  AND b.EnumType = 1 AND '{$end_date}'" . $staff_sql . "
	AND b.EnumID = '{$data['city']}' AND a.Staff01 !='' AND a.`Status` = 3
GROUP BY scene ";
//        var_dump($sql_count);exit();
        $ret['count'] = Yii::app()->db->createCommand($sql_count)->queryAll();
        return $ret;
    }

    public function getStaffInfo($data)
    {
//        var_dump($data);exit;
//        $staff_id = '',$start_date, $end_date,$time_point,$service_type = 1,$city
//        if (empty()) {
//            return false;
//        }
        switch ($data['service_type']) {
            case '1':
                $table = "service{$this->suffix}.joborder";
                $rangDate = 'FinishDate';
                $stype = 'ServiceType';
                break;
            case '2':
                $table = "service{$this->suffix}.followuporder";
                $rangDate = 'jobDate';
                $stype = 'SType';
                break;
            default:
                $table = "service{$this->suffix}.joborder";
                $rangDate = 'FinishDate';
                $stype = 'ServiceType';
        }
        $start_date = date('Y-m-d', strtotime($data['start_date'])) . " 00:00:00";
        $end_date = date('Y-m-d', strtotime($data['end_date'])) . " 23:59:59";
        if ((isset($data['time_point_start']) && $data['time_point_start'] >= 0) && $data['time_point_end'] == 0) {
            $is_mark = '>=';
            $condition_x = "TIMEDIFF(a.FinishTime,a.StartTime) AS job_time,IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']},'1','0') as 'flag',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']},'异常单','正常单') as 'status'";

        }
        if ((isset($data['time_point_end']) && $data['time_point_end'] >= 0) && $data['time_point_start'] == 0) {
            $is_mark = '<=';
            $condition_x = "TIMEDIFF(a.FinishTime,a.StartTime) AS job_time,IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']},'1','0') as 'flag',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']},'异常单','正常单') as 'status'";

        }

        if ((isset($data['time_point_start']) && $data['time_point_start'] > 0) && (isset($data['time_point_end']) && $data['time_point_end'] > 0)) {

            $condition_x = "TIMEDIFF(a.FinishTime,a.StartTime) AS job_time,IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']},'1','0') as 'flag',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']},'异常单','正常单') as 'status'";

        }


        $sql = "SELECT 
	a.CustomerName AS customer_name,
	d.StaffName AS staff_name,
	b.ServiceName AS service_type,
	c.Text AS city_name,
	a.StartTime AS start_time,
	a.FinishTime AS end_time,
	a.JobDate AS job_date,
	{$condition_x}
FROM
	{$table} a
	LEFT JOIN service{$this->suffix}.service b ON b.ServiceType = a.{$stype}
	 JOIN service{$this->suffix}.enums c ON c.EnumID = a.City 
	 JOIN service{$this->suffix}.staff d ON d.StaffID = a.Staff01
WHERE
	a.Staff01 = '" . $data['staff_id'] . "'  AND b.EnumType = 1 AND JobDate BETWEEN '{$start_date}' AND '{$end_date}'
	AND a.`Status` = 3 AND a.City = '{$data['city']}' ORDER BY  a.JobDate DESC";
//        var_dump($sql);exit;

        $ret = Yii::app()->db->createCommand($sql)->queryAll();
        return $ret;
    }


    public function getExport($data)
    {
//        var_dump($data);exit;
//        $staff_id = '',$start_date, $end_date,$time_point,$service_type = 1,$city
//        if (empty()) {
//            return false;
//        }
        switch ($data['service_type']) {
            case '1':
                $table = "service{$this->suffix}.joborder";
                $rangDate = 'FinishDate';
                $stype = 'ServiceType';
                break;
            case '2':
                $table = "service{$this->suffix}.followuporder";
                $rangDate = 'jobDate';
                $stype = 'SType';
                break;
            default:
                $table = "service{$this->suffix}.joborder";
                $rangDate = 'FinishDate';
                $stype = 'ServiceType';
        }

        $start_date = date('Y-m-d', strtotime($data['start_date'])) . " 00:00:00";
        $end_date = date('Y-m-d', strtotime($data['end_date'])) . " 23:59:59";
        if ((isset($data['time_point_start']) && $data['time_point_start'] >= 0) && $data['time_point_end'] == 0) {
            $is_mark = '>=';
            $condition_x = "TIMEDIFF(a.FinishTime,a.StartTime) AS job_time,IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']},'1','0') as 'flag',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_start']},'异常单','正常单') as 'status'";

        }
        if ((isset($data['time_point_end']) && $data['time_point_end'] >= 0) && $data['time_point_start'] == 0) {
            $is_mark = '<=';
            $condition_x = "TIMEDIFF(a.FinishTime,a.StartTime) AS job_time,IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']},'1','0') as 'flag',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime)){$is_mark}{$data['time_point_end']},'异常单','正常单') as 'status'";

        }

        if ((isset($data['time_point_start']) && $data['time_point_start'] > 0) && (isset($data['time_point_end']) && $data['time_point_end'] > 0)) {

            $condition_x = "TIMEDIFF(a.FinishTime,a.StartTime) AS job_time,IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']},'1','0') as 'flag',
  IF(TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))>{$data['time_point_start']} OR TIME_TO_SEC(TIMEDIFF(a.FinishTime,a.StartTime))<{$data['time_point_end']},'异常单','正常单') as 'status'";

        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
	a.CustomerName AS customer_name,
	d.StaffName AS staff_name,
    b.ServiceName AS service_type,
	c.Text AS city_name,
	a.StartTime AS start_time,
	a.FinishTime AS end_time,
	a.JobDate AS job_date,
	{$condition_x}
FROM
	{$table} a
    LEFT JOIN service{$this->suffix}.service b ON b.ServiceType = a.{$stype}
	 JOIN service{$this->suffix}.enums c ON c.EnumID = a.City  
	 JOIN service{$this->suffix}.staff d ON d.StaffID = a.Staff01

WHERE
    JobDate BETWEEN '{$start_date}' AND '{$end_date}'  AND c.EnumType = 1
	AND a.`Status` = 3 AND a.City = '{$data['city']}'  ORDER BY  a.JobDate DESC";
        $ret['data'] = Yii::app()->db->createCommand($sql)->queryAll();
//        $this->findBySql("SELECT FOUND_ROWS() as row_count;");
        $ret['count'] = Yii::app()->db->createCommand("SELECT FOUND_ROWS() as row_count;")->queryRow();
        return ['data' => $ret, 'range' => [$start_date, $end_date]];

    }

    public function showField($name)
    {
        $a = explode(',', $this->fields);
        return empty($this->fields) || in_array($name, $a);
    }
}

?>