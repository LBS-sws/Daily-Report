<?php

class WorklistController extends Controller
{
    public $function_id = 'WO01';

    public function filters()
    {
        return array(
            'enforceRegisteredStation',
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('staff', 'index', 'new', 'edit', 'delete', 'save', 'area', 'jobList', 'StaffInfo', 'export', 'test', 'evaluation', 'evaluationExport'),
                'expression' => array('WorklistController', 'allowReadWrite'),
            ),
            array('allow',
                'actions' => array('staff', 'index', 'new', 'edit', 'delete', 'save', 'area', 'jobList', 'StaffInfo', 'export', 'test', 'evaluation', 'evaluationExport'),
                'expression' => array('WorklistController', 'allowReadOnly'),
            ),
//            array('deny',  // deny all users
//                'users' => array('*'),
//            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('WO01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('WO01');
    }

    protected function json($data = [], $msg = "ok", $code = 1)
    {
        //设置格式
        header('Content-type: application/json');

        //输出json格式的内容
        exit(json_encode([
            "code" => $code,
            "msg" => $msg,
            "data" => $data,
            "request_id" => uniqid()
        ]));
        //结束
//        return Yii::app()->end();exit();
    }

    public function actionTest()
    {
        $session = Yii::app()->session;
        $model = new City();
        $res =  $model->getDescendantList('HXHB');
        var_dump($res);
    }

    public function actionStaffEx($search, $incity = '')
    {
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = $suffix == 'dev' ? '_w' : $suffix;
        $city = empty($incity) ? Yii::app()->user->city() : $incity;
        $result = array();
        $searchx = str_replace("'", "\'", $search);

        $sql = "select id, concat(name, ' (', code, ')') as value from service$suffix.staff
				where (code like '%$searchx%' or name like '%$searchx%') and city='$city'
				and (leave_dt is null or leave_dt=0 or leave_dt > now()) ";
        $result1 = Yii::app()->db->createCommand($sql)->queryAll();

        $sql = "select id, concat(name, ' (', code, ')',' " . Yii::t('app', '(Resign)') . "') as value from service$suffix.staff
				where (code like '%$searchx%' or name like '%$searchx%') and city='$city'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
        $result2 = Yii::app()->db->createCommand($sql)->queryAll();

        $records = array_merge($result1, $result2);
        if (count($records) > 0) {
            foreach ($records as $k => $record) {
                $result[] = array(
                    'id' => $record['id'],
                    'value' => $record['value'],
                );
            }
        }
        print json_encode($result);
    }


    public function actionIndex($pageNum = 0)
    {
        $model = new WorkOrder();
        $model->end_at = '';
        $model->start_at = '';// date("Y", strtotime($model->end_dt)).'/'.date("m", strtotime($model->end_dt)).'/01';
        $this->render('index', array('model' => $model));
    }

    /**
     * 获取区域
     * @return json
     * */
    public function actionArea()
    {
        $session = Yii::app()->session;
        $model = new WorkOrder();
        $res = $model->retrieveData($session['city_allow']);
        if ($res) {
            $this->json($res);
        }
        $this->json([], 'error', 0);
    }

    /**
     * 获取员工信息
     * @return json
     * */
    public function actionStaff()
    {
        $city = isset($_GET['city']) ? $_GET['city'] : 0;
        $model = new WorkOrder();
        $cityModel = new City();
        $citys =  $cityModel->getDescendantList($city);
        $city_ret = !empty($citys)?$citys:$city;
        $res = $model->getStaff($city_ret);
        if ($res && $city) {
            $this->json($res);
        }
        $this->json([], 'error', 0);
    }

    public function HourMinuteToDecimal($hour_minute)
    {
        $t = explode(':', $hour_minute);
        return ($t[0] * 60 + $t[1]) * 60;
    }


    /**
     * 工作报表
     * */
    public function actionjobList()
    {
        //接收参数
        $data['start_date'] = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d H:h:s', '-1 day');
        $data['end_date'] = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d H:h:s');
        $data['staff'] = isset($_GET['staff']) ? $_GET['staff'] : '';
        $data['city'] = isset($_GET['city']) ? $_GET['city'] : '';
        $time_point_start = isset($_GET['time_point_start']) ? $_GET['time_point_start'] : '';
        $time_point_end = isset($_GET['time_point_end']) ? $_GET['time_point_end'] : '';
        $data['service_type'] = isset($_GET['service_type']) ? $_GET['service_type'] : 1;
        $data['is_mark'] = isset($_GET['is_mark']) ? $_GET['is_mark'] : 1;
//        var_dump($time_point_start);exit();

        if ($time_point_start == '' || $time_point_start == 'null') {
            $data['time_point_start'] = 0;
        } else {
            $data['time_point_start'] = $this->HourMinuteToDecimal($time_point_start);
        }


        if ($time_point_end == '' || $time_point_end == 'null') {
            $data['time_point_end'] = 0;
        } else {
            $data['time_point_end'] = $this->HourMinuteToDecimal($time_point_end);
        }
        //特殊处理三个时间
//        $data['time_point_start'] = $time_point_start!=''?$this->HourMinuteToDecimal($time_point_start):0;
//        $data['time_point_end'] = $this->HourMinuteToDecimal($time_point_end);

        $model = new WorkOrder();
        try {
            $res = $model->getJob($data);
            if ($res['data'] && $res['count']) {
                $this->json($res);
            }
        } catch (Exception $exception) {
            $this->json([], $exception->getMessage(), 0);
        }
        $this->json([], '无数据', 0);

    }


    public function actionStaffInfo()
    {
        $data['start_date'] = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d H:h:s', '-1 day');
        $data['end_date'] = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d H:h:s');
        $data['staff_id'] = isset($_GET['staff_id']) ? $_GET['staff_id'] : '';

        $data['service_type'] = isset($_GET['service_type']) ? $_GET['service_type'] : '';
        $data['city'] = isset($_GET['city']) ? $_GET['city'] : '';
        $data['is_mark'] = isset($_GET['is_mark']) ? $_GET['is_mark'] : '';
//        $data['time'] = $this->HourMinuteToDecimal($data['time_point']);
        $time_point_start = isset($_GET['time_point_start']) ? $_GET['time_point_start'] : '';
        $time_point_end = isset($_GET['time_point_end']) ? $_GET['time_point_end'] : '';

        if ($time_point_start == '' || $time_point_start == 'null') {
            $data['time_point_start'] = 0;
        } else {
            $data['time_point_start'] = $this->HourMinuteToDecimal($time_point_start);
        }
        if ($time_point_end == '' || $time_point_end == 'null') {
            $data['time_point_end'] = 0;
        } else {
            $data['time_point_end'] = $this->HourMinuteToDecimal($time_point_end);
        }
        if (!isset($data['staff_id']) && empty($data['staff_id'])) {
            $this->json([], 'error', 0);
        }
        $model = new WorkOrder();
        $res = $model->getStaffInfo($data);
        if ($res) {
            $this->json($res);
        }
        $this->json([], 'error', 0);
    }

    public function actionExport()
    {
        if (empty($_GET)) {
            $this->json([], '参数错误', 0);
        }
        try {
            $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
            include_once($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $objectPHPExcel = new PHPExcel();
            $objectPHPExcel->setActiveSheetIndex(0);
            $data['start_date'] = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d H:h:s', '-1 day');
            $data['end_date'] = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d H:h:s');
//        $data['staff_id'] = isset($_GET['staff_id']) ? $_GET['staff_id'] : '';
//            $data['time_point'] = isset($_GET['time_point']) ? $_GET['time_point'] : '';
            $data['service_type'] = isset($_GET['service_type']) ? $_GET['service_type'] : '';
            $data['city'] = isset($_GET['city']) ? $_GET['city'] : '';
            $data['is_mark'] = isset($_GET['is_mark']) ? $_GET['is_mark'] : '';
//            $data['time'] = $this->HourMinuteToDecimal($data['time_point']);
            $time_point_start = isset($_GET['time_point_start']) ? $_GET['time_point_start'] : '';
            $time_point_end = isset($_GET['time_point_end']) ? $_GET['time_point_end'] : '';

            if ($time_point_start == '' || $time_point_start == 'null') {
                $data['time_point_start'] = 0;
            } else {
                $data['time_point_start'] = $this->HourMinuteToDecimal($time_point_start);
            }
            if ($time_point_end == '' || $time_point_end == 'null') {
                $data['time_point_end'] = 0;
            } else {
                $data['time_point_end'] = $this->HourMinuteToDecimal($time_point_end);
            }

            if (isset($data['staff_id']) && !empty($data['staff_id'])) {
                $this->json([], 'error', 0);
            }
            $model = new WorkOrder();
            $page_size = 50000;
            $result = $model->getExport($data);
            if (isset($result['data']['count']['row_count']) && $result['data']['count']['row_count'] >= $page_size) {
                $this->json([], '筛选的时间段过大', 0);
            }
            if ($result['data']['count']['row_count'] <= 0) {
                $this->json([], '导出错误', 0);
            }

//        $count = 10000;
            //总页数的算出 [暂不使用]
            $current_page = 0;
            $n = 0;
//            var_dump($result['data']['data'] );exit();
            foreach ($result['data']['data'] as $k => $product) {
                if ($n % $page_size === 0) {
                    $current_page = $current_page + 1;
                    //报表头的输出
                    $objectPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                    $objectPHPExcel->getActiveSheet()->setCellValue('A1', '区域明细');

                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '区域明细');
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '区域明细');
                    $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getFont()->setSize(24);
                    $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '日期：' . date("Y年m月j日"));
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '范围：' . $result['range'][0] . '--' . $result['range'][1]);
                    $objectPHPExcel->setActiveSheetIndex(0)->getStyle('I2')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                    //表格头的输出
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', '日期');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', '客户名称');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', '员工姓名');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', '城市');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', '开始时间');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', '结束时间');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', '服务类型');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', '工作时长');
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

//                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('J3','second');
//                $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

                    $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', '状态');
//                $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

                    //设置居中
                    $objectPHPExcel->getActiveSheet()->getStyle('A3:I3')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    //设置边框
                    $objectPHPExcel->getActiveSheet()->getStyle('A3:I3')
                        ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle('A3:I3')
                        ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle('A3:I3')
                        ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle('A3:I3')
                        ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle('A3:I3')
                        ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    //设置颜色
                    $objectPHPExcel->getActiveSheet()->getStyle('A3:I3')->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');
                }
                //明细的输出
                $objectPHPExcel->getActiveSheet()->setCellValue('A' . ($n + 4), $product['job_date']);
                $objectPHPExcel->getActiveSheet()->setCellValue('B' . ($n + 4), $product['customer_name']);
                $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($n + 4), $product['staff_name']);
                $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($n + 4), $product['city_name']);
                $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($n + 4), $product['start_time']);
                $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($n + 4), $product['end_time']);

                $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($n + 4), $product['service_type']);
                $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($n + 4), $product['job_time']);
//            $objectPHPExcel->getActiveSheet()->setCellValue('J'.($n+4) ,$product['second']);
                $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($n + 4), $product['status']);
                if ($product['flag'] == 0) {
                    $objectPHPExcel->getActiveSheet()->getStyle('I' . ($k + 4) . ':I' . ($k + 4) . '')->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CC88');
                } else {
                    $objectPHPExcel->getActiveSheet()->getStyle('I' . ($k + 4) . ':I' . ($k + 4) . '')->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00FF6699');
                }

                //设置边框
                $currentRowNum = $n + 4;
                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':K'.$currentRowNum )
//                ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $n = $n + 1;
            }

            //设置分页显示
            //$objectPHPExcel->getActiveSheet()->setBreak( 'I55' , PHPExcel_Worksheet::BREAK_ROW );
            //$objectPHPExcel->getActiveSheet()->setBreak( 'I10' , PHPExcel_Worksheet::BREAK_COLUMN );
//            $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
//            $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);


            @ob_end_clean();
            @ob_start();
            header('Content-Type: application/vnd.ms-excel');
//            判断PHP版本 以兼容下载
            if (phpversion() >= 7) {
                header('Content-Disposition: attachment;filename="' . '【' . $product['city_name'] . '】' . date('Y-m-d', strtotime($result['range'][0])) . '--' . date('Y-m-d', strtotime($result['range'][1])) . '.xlsx"');
                $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel2007');
            } else {
                header('Content-Disposition: attachment;filename="' . '【' . $product['city_name'] . '】' . date('Y-m-d', strtotime($result['range'][0])) . '--' . date('Y-m-d', strtotime($result['range'][1])) . '.xls"');
                $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel5');
            }
        } catch (Exception $exception) {
            $this->json([], $exception->getMessage(), 0);
        }
        $objWriter->save('php://output');
    }

    /**
     * 点评统计
     * @return void
     */
    public function actionEvaluation(){
        $this->function_id = 'WO02';
        Yii::app()->session['active_func'] = $this->function_id;

        $this->render('evaluation');
    }

    /**
     * 点评统计导出
     * @return void
     */
    public function actionEvaluationExport(){
        if (empty($_GET)) {
            $this->json([], '参数错误', 0);
        }
        
        try {
            //引入phpexcel
            $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
            include_once($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $objectPHPExcel = new PHPExcel();
            $objectPHPExcel->setActiveSheetIndex(0);

            //读取参数
            $data['start_date'] = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d H:h:s', '-1 day');
            $data['end_date'] = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d H:h:s');
            $data['staff_id'] = isset($_GET['staff_id']) ? $_GET['staff_id'] : '';
            $data['city'] = isset($_GET['city']) ? $_GET['city'] : '';
            $data['is_mark'] = isset($_GET['is_mark']) ? $_GET['is_mark'] : '';

            if (isset($data['staff_id']) && empty($data['staff_id'])) {
                $this->json([], 'staff data error', 0);
            }

            /* 查询 */
            $model = new WorkOrder();
            $page_size = 50000;
            $result = $model->getEvaluationExport($data);

            /* 判断数据 */
            if (isset($result['count']['row_count']) && $result['count']['row_count'] >= $page_size) {
                $this->json([], '筛选的时间段过大', 0);
            }
            if ($result['count']['row_count'] <= 0) {
                $this->json([], '无数据', 0);
            }

            //技术员
            $suffix = Yii::app()->params['envSuffix'];
            $staffData = Yii::app()->db->createCommand("SELECT StaffName FROM service{$suffix}.staff WHERE StaffID = {$data['staff_id']}")->queryRow();// 计算总条数

            /* 导出为excel */
            $current_page = 0;
            $n = 0;
            $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
            $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
            foreach ($result['data'] as $k =>  $item) {
                // 设置表头
                if ($n % $page_size === 0) {
                    $current_page = $current_page + 1;

                    //技术员
                    $objectPHPExcel->getActiveSheet()->setCellValue('A1', '技术员名称：');
                    $objectPHPExcel->getActiveSheet()->setCellValue('C1', $staffData['StaffName']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('A2', '工作日期：');
                    $objectPHPExcel->getActiveSheet()->setCellValue('C2', $data['start_date'].'至'.$data['end_date']);

                    //设置表头
                    $objectPHPExcel->getActiveSheet()->setCellValue('A4', '工作日期');
                    $objectPHPExcel->getActiveSheet()->setCellValue('B4', '工作单类型');
                    $objectPHPExcel->getActiveSheet()->setCellValue('C4', '客户名称');
                    $objectPHPExcel->getActiveSheet()->setCellValue('D4', '服务类型');
                    $objectPHPExcel->getActiveSheet()->setCellValue('E4', '反馈一：史伟莎技术人员着装是否整齐');
                    $objectPHPExcel->getActiveSheet()->setCellValue('F4', '反馈二：服务前史伟莎技术人员是否主动了解门店');
                    $objectPHPExcel->getActiveSheet()->setCellValue('G4', '反馈三：服务完成后史伟莎技术人员是否汇报本次服务情况');

                    //设置居中
                    $objectPHPExcel->getActiveSheet()->getStyle('A4:D4')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objectPHPExcel->getActiveSheet()->getStyle('B:G')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    //垂直居中
                    $objectPHPExcel->getActiveSheet()->getstyle('A4:G4')
                        ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    //自动换行
                    $objectPHPExcel->getActiveSheet()->getStyle('A4:G4')
                        ->getAlignment()->setWrapText(TRUE);

                    //加粗 设置行高
                    $objectPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(34);
                    $objectPHPExcel->setActiveSheetIndex(0)->getStyle("A4:G4")->getFont()->setBold(true);
                    //宽度
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                    $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                }

                //明细的输出
                $objectPHPExcel->getActiveSheet()->setCellValue('A' . ($n + 5), $item['job_date']);
                $objectPHPExcel->getActiveSheet()->setCellValue('B' . ($n + 5), isset($item['FirstJob'])? ($item['FirstJob']?'首次服务':'常规服务') : '跟进服务' );
                $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($n + 5), $item['customer_name']);
                $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($n + 5), $item['service_type']);
                $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($n + 5), $item['question']?($item['question'][0]['answer']?'是':'否') : '');
                $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($n + 5), $item['question']?($item['question'][1]['answer']?'是':'否') : '');
                $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($n + 5), $item['question']?($item['question'][2]['answer']?'是':'否') : '');
                if ($item['question'] && $item['question'][0]['answer'] == 0) {
                    $objectPHPExcel->getActiveSheet()->getStyle('E' . ($k + 5) . ':E' . ($k + 5) . '')
                        ->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
                }
                if ($item['question'] && $item['question'][1]['answer'] == 0) {
                    $objectPHPExcel->getActiveSheet()->getStyle('F' . ($k + 5) . ':F' . ($k + 5) . '')
                        ->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
                }
                if ($item['question'] && $item['question'][2]['answer'] == 0) {
                    $objectPHPExcel->getActiveSheet()->getStyle('G' . ($k + 5) . ':G' . ($k + 5) . '')
                        ->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
                }

                $n++;
            }

            //设置分页显示
//            $objectPHPExcel->getActiveSheet()->setBreak( 'I55' , PHPExcel_Worksheet::BREAK_ROW );
//            $objectPHPExcel->getActiveSheet()->setBreak( 'I10' , PHPExcel_Worksheet::BREAK_COLUMN );
//            $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
//            $objectPHPExcel->ge?tActiveSheet()->getPageSetup()->setVerticalCentered(false);


            @ob_end_clean();
            @ob_start();
            header('Content-Type: application/vnd.ms-excel');
//            判断PHP版本 以兼容下载
            if (phpversion() >= 7) {
                header('Content-Disposition: attachment;filename="' . '【' . $staffData['StaffName'] . '】' . $data['start_date'] . '--' . $data['end_date'] . '.xlsx"');
                $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel2007');
            } else {
                header('Content-Disposition: attachment;filename="' . '【' . $staffData['StaffName'] . '】' . $data['start_date'] . '--' . $data['end_date'] . '.xls"');
                $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel5');
            }
        } catch (Exception $exception) {
            $this->json([], $exception->getMessage(), 0);
        }
        $objWriter->save('php://output');
    }
}
