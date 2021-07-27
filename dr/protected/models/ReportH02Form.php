<?php
/* Reimbursement Form */

class ReportH02Form extends CReportForm
{
	public $staffs;
	public $staffs_desc;
	
	protected function labelsEx() {
		return array(
				'staffs'=>Yii::t('report','Staffs'),
                'city'=>Yii::t('code','City'),
                 'from'=>Yii::t('code','From'),
                 'to'=>Yii::t('code','To'),
			);
	}
	
	protected function rulesEx() {
        return array(
            array('staffs, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
			);
	}
	
	public function init() {
		$this->id = 'RptFive';
		$this->name = Yii::t('app','Five Steps');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'start_dt,end_dt,staffs,staffs_desc';
		$this->start_dt = date("Y/m/d");
        $this->end_dt = date("Y/m/d");
		$this->five = array();
		$this->date="";
        $this->staffs = '';
        $this->month="";
        $this->year="";
        $this->excel=array();
		$this->staffs_desc = Yii::t('misc','All');
	}

    public function retrieveDatas($model){
        $start_date = '2017-01-01'; // 自动为00:00:00 时分秒
        $end_date = date("Y-m-d");
        $start_arr = explode("-", $start_date);
        $end_arr = explode("-", $end_date);
        $start_year = intval($start_arr[0]);
        $start_month = intval($start_arr[1]);
        $end_year = intval($end_arr[0]);
        $end_month = intval($end_arr[1]);
        $diff_year = $end_year-$start_year;
        $year_arr=[];
        for($year=$end_year;$year>=$start_year;$year--){
            $year_arr[] = $year;
        }
        $this->date=$year_arr;
    }

    public function retrieveXiaZai($model){
        Yii::$enableIncludePath = false;
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
        spl_autoload_unregister(array('YiiBase','autoload'));
        include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $objPHPExcel = new PHPExcel;
        $objReader  = PHPExcel_IOFactory::createReader('Excel2007');
        if(count($model->five[0])==68){
            $path = Yii::app()->basePath.'/commands/template/month_more_lirun.xlsx';
            $objPHPExcel = $objReader->load($path);
            $excel_m=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
//        foreach ($model->record as $arr ){
//            $objPHPExcel->getActiveSheet()->setCellValue('B'.$arr['excel_row'], $arr['datavalueold']) ;
//        }
            //  $num_rows = $objPHPExcel->getActiveSheet()->getHighestColumn();
            if($model->ccuser!=1){
                $objPHPExcel->getActiveSheet()->insertNewColumnBefore($excel_m[1],$model->ccuser-1);
            }

            for($i=0;$i<$model->ccuser;$i++){
                foreach ($model->five[$i] as $arr ){
                    if($arr['excel_row']>59){
                        $arr['excel_row']=$arr['excel_row']-2;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$arr['excel_row'], $arr['data_value']) ;
                }
                for($a=80;$a<132;$a++){
//                    if($a!=87&&$a!=95&&$a!=101&&$a!=116&&$a!=123){
//                        $value=$model->excel[$i]['c'.$a]."(".$model->excel[$i]['e'.$a].")";
//                        $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$a, $value) ;
//                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'80',$model->excel[$i]['c76']."(".$model->excel[$i]['e76'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'81',$model->excel[$i]['c77']."(".$model->excel[$i]['e77'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'82',$model->excel[$i]['c78']."(".$model->excel[$i]['e78'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'83',$model->excel[$i]['c79']."(".$model->excel[$i]['e79'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'84',$model->excel[$i]['c80']."(".$model->excel[$i]['e80'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'85',$model->excel[$i]['c81']."(".$model->excel[$i]['e81'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'86',$model->excel[$i]['c82']."(".$model->excel[$i]['e82'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'87',$model->excel[$i]['c83']."(".$model->excel[$i]['e83'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'88',$model->excel[$i]['c84']."(".$model->excel[$i]['e84'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'89',$model->excel[$i]['c85']."(".$model->excel[$i]['e85'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'90',$model->excel[$i]['c86']."(".$model->excel[$i]['e86'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'92',$model->excel[$i]['c88']."(".$model->excel[$i]['e88'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'93',$model->excel[$i]['c89']."(".$model->excel[$i]['e89'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'94',$model->excel[$i]['c90']."(".$model->excel[$i]['e90'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'95',$model->excel[$i]['c91']."(".$model->excel[$i]['e91'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'96',$model->excel[$i]['c92']."(".$model->excel[$i]['e92'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'97',$model->excel[$i]['c93']."(".$model->excel[$i]['e93'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'98',$model->excel[$i]['c94']."(".$model->excel[$i]['e94'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'100',$model->excel[$i]['c96']."(".$model->excel[$i]['e96'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'101',$model->excel[$i]['c97']."(".$model->excel[$i]['e97'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'105',$model->excel[$i]['c98']."(".$model->excel[$i]['e98'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'106',$model->excel[$i]['c99']."(".$model->excel[$i]['e99'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'107',$model->excel[$i]['c100']."(".$model->excel[$i]['e100'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'109',$model->excel[$i]['c102']."(".$model->excel[$i]['e102'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'110',$model->excel[$i]['c103']."(".$model->excel[$i]['e103'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'111',$model->excel[$i]['c104']."(".$model->excel[$i]['e104'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'112',$model->excel[$i]['c105']."(".$model->excel[$i]['e105'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'113',$model->excel[$i]['c106']."(".$model->excel[$i]['e106'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'114',$model->excel[$i]['c107']."(".$model->excel[$i]['e107'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'115',$model->excel[$i]['c108']."(".$model->excel[$i]['e108'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'116',$model->excel[$i]['c109']."(".$model->excel[$i]['e109'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'117',$model->excel[$i]['c110']."(".$model->excel[$i]['e110'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'118',$model->excel[$i]['c111']."(".$model->excel[$i]['e111'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'119',$model->excel[$i]['c112']."(".$model->excel[$i]['e112'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'120',$model->excel[$i]['c113']."(".$model->excel[$i]['e113'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'121',$model->excel[$i]['c114']."(".$model->excel[$i]['e114'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'122',$model->excel[$i]['c115']."(".$model->excel[$i]['e115'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'124',$model->excel[$i]['c117']."(".$model->excel[$i]['e117'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'125',$model->excel[$i]['c118']."(".$model->excel[$i]['e118'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'126',$model->excel[$i]['c119']."(".$model->excel[$i]['e119'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'127',$model->excel[$i]['c120']."(".$model->excel[$i]['e120'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'128',$model->excel[$i]['c121']."(".$model->excel[$i]['e121'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'129',$model->excel[$i]['c122']."(".$model->excel[$i]['e122'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'131',$model->excel[$i]['c124']."(".$model->excel[$i]['e124'].")") ;
                //利润
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'102',$model->excel[$i]['bc102']."(".$model->excel[$i]['be102'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'103',$model->excel[$i]['bc103']."(".$model->excel[$i]['be103'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'104',$model->excel[$i]['bc104']."(".$model->excel[$i]['be104'].")") ;

                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'2', $model->year[$i].'/'.$model->month[$i]) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'78', $model->excel[$i]['f74']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'91', $model->excel[$i]['f87']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'99', $model->excel[$i]['f95']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'108', $model->excel[$i]['f101']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'123', $model->excel[$i]['f116']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'79', $model->excel[$i]['f75']) ;
                }
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel_m[$i])->setWidth(17);
            }
        }elseif(count($model->five[0])==71){
            $path = Yii::app()->basePath.'/commands/template/month_more_iaib.xlsx';
            $objPHPExcel = $objReader->load($path);
            $excel_m=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
//        foreach ($model->record as $arr ){
//            $objPHPExcel->getActiveSheet()->setCellValue('B'.$arr['excel_row'], $arr['datavalueold']) ;
//        }
            //  $num_rows = $objPHPExcel->getActiveSheet()->getHighestColumn();
            if($model->ccuser!=1){
                $objPHPExcel->getActiveSheet()->insertNewColumnBefore($excel_m[1],$model->ccuser-1);
            }

            for($i=0;$i<$model->ccuser;$i++){
                foreach ($model->five[$i] as $arr ){
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$arr['excel_row'], $arr['data_value']) ;
                }
                for($a=82;$a<134;$a++){
//                    if($a!=87&&$a!=95&&$a!=101&&$a!=116&&$a!=123){
//                        $value=$model->excel[$i]['c'.$a]."(".$model->excel[$i]['e'.$a].")";
//                        $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$a, $value) ;
//                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'83',$model->excel[$i]['c76']."(".$model->excel[$i]['e76'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'84',$model->excel[$i]['c77']."(".$model->excel[$i]['e77'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'85',$model->excel[$i]['c78']."(".$model->excel[$i]['e78'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'86',$model->excel[$i]['c79']."(".$model->excel[$i]['e79'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'87',$model->excel[$i]['c80']."(".$model->excel[$i]['e80'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'88',$model->excel[$i]['c81']."(".$model->excel[$i]['e81'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'89',$model->excel[$i]['c82']."(".$model->excel[$i]['e82'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'90',$model->excel[$i]['c83']."(".$model->excel[$i]['e83'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'91',$model->excel[$i]['c84']."(".$model->excel[$i]['e84'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'92',$model->excel[$i]['c85']."(".$model->excel[$i]['e85'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'93',$model->excel[$i]['c86']."(".$model->excel[$i]['e86'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'95',$model->excel[$i]['c88']."(".$model->excel[$i]['e88'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'96',$model->excel[$i]['c89']."(".$model->excel[$i]['e89'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'97',$model->excel[$i]['c90']."(".$model->excel[$i]['e90'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'98',$model->excel[$i]['c91']."(".$model->excel[$i]['e91'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'99',$model->excel[$i]['c92']."(".$model->excel[$i]['e92'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'100',$model->excel[$i]['c93']."(".$model->excel[$i]['e93'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'101',$model->excel[$i]['c94']."(".$model->excel[$i]['e94'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'103',$model->excel[$i]['c96']."(".$model->excel[$i]['e96'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'104',$model->excel[$i]['c97']."(".$model->excel[$i]['e97'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'108',$model->excel[$i]['c98']."(".$model->excel[$i]['e98'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'109',$model->excel[$i]['c99']."(".$model->excel[$i]['e99'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'110',$model->excel[$i]['c100']."(".$model->excel[$i]['e100'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'112',$model->excel[$i]['c102']."(".$model->excel[$i]['e102'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'113',$model->excel[$i]['c103']."(".$model->excel[$i]['e103'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'114',$model->excel[$i]['c104']."(".$model->excel[$i]['e104'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'115',$model->excel[$i]['c105']."(".$model->excel[$i]['e105'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'116',$model->excel[$i]['c106']."(".$model->excel[$i]['e106'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'117',$model->excel[$i]['c107']."(".$model->excel[$i]['e107'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'118',$model->excel[$i]['c108']."(".$model->excel[$i]['e108'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'119',$model->excel[$i]['c109']."(".$model->excel[$i]['e109'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'120',$model->excel[$i]['c110']."(".$model->excel[$i]['e110'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'121',$model->excel[$i]['c111']."(".$model->excel[$i]['e111'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'122',$model->excel[$i]['c112']."(".$model->excel[$i]['e112'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'123',$model->excel[$i]['c113']."(".$model->excel[$i]['e113'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'124',$model->excel[$i]['c114']."(".$model->excel[$i]['e114'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'125',$model->excel[$i]['c115']."(".$model->excel[$i]['e115'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'127',$model->excel[$i]['c117']."(".$model->excel[$i]['e117'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'128',$model->excel[$i]['c118']."(".$model->excel[$i]['e118'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'129',$model->excel[$i]['c119']."(".$model->excel[$i]['e119'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'130',$model->excel[$i]['c120']."(".$model->excel[$i]['e120'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'131',$model->excel[$i]['c121']."(".$model->excel[$i]['e121'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'132',$model->excel[$i]['c122']."(".$model->excel[$i]['e122'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'134',$model->excel[$i]['c124']."(".$model->excel[$i]['e124'].")") ;
                    //利润
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'105',$model->excel[$i]['bc102']."(".$model->excel[$i]['be102'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'106',$model->excel[$i]['bc103']."(".$model->excel[$i]['be103'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'107',$model->excel[$i]['bc104']."(".$model->excel[$i]['be104'].")") ;

                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'2', $model->year[$i].'/'.$model->month[$i]) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'81', $model->excel[$i]['f74']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'94', $model->excel[$i]['f87']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'102', $model->excel[$i]['f95']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'111', $model->excel[$i]['f101']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'126', $model->excel[$i]['f116']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'82', $model->excel[$i]['f75']) ;
                }
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel_m[$i])->setWidth(17);
            }
        }elseif(count($model->five[0])==70){
            $path = Yii::app()->basePath.'/commands/template/month_more_xidi.xlsx';
            $objPHPExcel = $objReader->load($path);
            $excel_m=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
//        foreach ($model->record as $arr ){
//            $objPHPExcel->getActiveSheet()->setCellValue('B'.$arr['excel_row'], $arr['datavalueold']) ;
//        }
            //  $num_rows = $objPHPExcel->getActiveSheet()->getHighestColumn();
            if($model->ccuser!=1){
                $objPHPExcel->getActiveSheet()->insertNewColumnBefore($excel_m[1],$model->ccuser-1);
            }

            for($i=0;$i<$model->ccuser;$i++){
                foreach ($model->five[$i] as $arr ){
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$arr['excel_row'], $arr['data_value']) ;
                }
                for($a=82;$a<134;$a++){
//                    if($a!=87&&$a!=95&&$a!=101&&$a!=116&&$a!=123){
//                        $value=$model->excel[$i]['c'.$a]."(".$model->excel[$i]['e'.$a].")";
//                        $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$a, $value) ;
//                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'82',$model->excel[$i]['c76']."(".$model->excel[$i]['e76'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'83',$model->excel[$i]['c77']."(".$model->excel[$i]['e77'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'84',$model->excel[$i]['c78']."(".$model->excel[$i]['e78'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'85',$model->excel[$i]['c79']."(".$model->excel[$i]['e79'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'86',$model->excel[$i]['c80']."(".$model->excel[$i]['e80'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'87',$model->excel[$i]['c81']."(".$model->excel[$i]['e81'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'88',$model->excel[$i]['c82']."(".$model->excel[$i]['e82'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'89',$model->excel[$i]['c83']."(".$model->excel[$i]['e83'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'90',$model->excel[$i]['c84']."(".$model->excel[$i]['e84'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'91',$model->excel[$i]['c85']."(".$model->excel[$i]['e85'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'92',$model->excel[$i]['c86']."(".$model->excel[$i]['e86'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'94',$model->excel[$i]['c88']."(".$model->excel[$i]['e88'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'95',$model->excel[$i]['c89']."(".$model->excel[$i]['e89'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'96',$model->excel[$i]['c90']."(".$model->excel[$i]['e90'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'97',$model->excel[$i]['c91']."(".$model->excel[$i]['e91'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'98',$model->excel[$i]['c92']."(".$model->excel[$i]['e92'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'99',$model->excel[$i]['c93']."(".$model->excel[$i]['e93'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'100',$model->excel[$i]['c94']."(".$model->excel[$i]['e94'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'102',$model->excel[$i]['c96']."(".$model->excel[$i]['e96'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'103',$model->excel[$i]['c97']."(".$model->excel[$i]['e97'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'107',$model->excel[$i]['c98']."(".$model->excel[$i]['e98'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'108',$model->excel[$i]['c99']."(".$model->excel[$i]['e99'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'109',$model->excel[$i]['c100']."(".$model->excel[$i]['e100'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'111',$model->excel[$i]['c102']."(".$model->excel[$i]['e102'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'112',$model->excel[$i]['c103']."(".$model->excel[$i]['e103'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'113',$model->excel[$i]['c104']."(".$model->excel[$i]['e104'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'114',$model->excel[$i]['c105']."(".$model->excel[$i]['e105'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'115',$model->excel[$i]['c106']."(".$model->excel[$i]['e106'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'116',$model->excel[$i]['c107']."(".$model->excel[$i]['e107'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'117',$model->excel[$i]['c108']."(".$model->excel[$i]['e108'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'118',$model->excel[$i]['c109']."(".$model->excel[$i]['e109'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'119',$model->excel[$i]['c110']."(".$model->excel[$i]['e110'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'120',$model->excel[$i]['c111']."(".$model->excel[$i]['e111'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'121',$model->excel[$i]['c112']."(".$model->excel[$i]['e112'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'122',$model->excel[$i]['c113']."(".$model->excel[$i]['e113'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'123',$model->excel[$i]['c114']."(".$model->excel[$i]['e114'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'124',$model->excel[$i]['c115']."(".$model->excel[$i]['e115'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'126',$model->excel[$i]['c117']."(".$model->excel[$i]['e117'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'127',$model->excel[$i]['c118']."(".$model->excel[$i]['e118'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'128',$model->excel[$i]['c119']."(".$model->excel[$i]['e119'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'129',$model->excel[$i]['c120']."(".$model->excel[$i]['e120'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'130',$model->excel[$i]['c121']."(".$model->excel[$i]['e121'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'131',$model->excel[$i]['c122']."(".$model->excel[$i]['e122'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'133',$model->excel[$i]['c124']."(".$model->excel[$i]['e124'].")") ;
                    //利润
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'104',$model->excel[$i]['bc102']."(".$model->excel[$i]['be102'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'105',$model->excel[$i]['bc103']."(".$model->excel[$i]['be103'].")") ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'106',$model->excel[$i]['bc104']."(".$model->excel[$i]['be104'].")") ;

                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'2', $model->year[$i].'/'.$model->month[$i]) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'80', $model->excel[$i]['f74']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'93', $model->excel[$i]['f87']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'101', $model->excel[$i]['f95']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'110', $model->excel[$i]['f101']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'125', $model->excel[$i]['f116']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'81', $model->excel[$i]['f75']) ;
                }
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel_m[$i])->setWidth(17);
            }
        }elseif(count($model->five[0])==65){
            $path = Yii::app()->basePath.'/commands/template/month_more_ones.xlsx';
            $objPHPExcel = $objReader->load($path);
            $excel_m=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
//        foreach ($model->record as $arr ){
//            $objPHPExcel->getActiveSheet()->setCellValue('B'.$arr['excel_row'], $arr['datavalueold']) ;
//        }
            //  $num_rows = $objPHPExcel->getActiveSheet()->getHighestColumn();

            if($model->ccuser!=1){
                $objPHPExcel->getActiveSheet()->insertNewColumnBefore($excel_m[1],$model->ccuser-1);
            }

            for($i=0;$i<$model->ccuser;$i++){
                foreach ($model->five[$i] as $arr ){
                    if($arr['excel_row']>59){
                        $arr['excel_row']=$arr['excel_row']-2;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$arr['excel_row'], $arr['data_value']) ;
                }
                for($a=76;$a<125;$a++){
                    if($a!=87&&$a!=95&&$a!=101&&$a!=116&&$a!=123){
                        $value=$model->excel[$i]['c'.$a]."(".$model->excel[$i]['e'.$a].")";
                        $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].$a, $value) ;
                    }
//                    print_r('<pre>');
//                    print_r( $model->excel);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'2', $model->year[$i].'/'.$model->month[$i]) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'74', $model->excel[$i]['f74']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'87', $model->excel[$i]['f87']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'95', $model->excel[$i]['f95']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'101', $model->excel[$i]['f101']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'116', $model->excel[$i]['f116']) ;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel_m[$i].'75', $model->excel[$i]['f75']) ;
                }
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel_m[$i])->setWidth(17);
            }
        }

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


	public function retrieveData($model){
        //获取月份
        $start_date = $model['_scenario']['start_dt']."-".$model['_scenario']['start_dt1']."-1"; // 自动为00:00:00 时分秒
        $end_date = $model['_scenario']['end_dt']."-".$model['_scenario']['end_dt1']."-1";
        $start_arr = explode("-", $start_date);
        $end_arr = explode("-", $end_date);
        $start_year = intval($start_arr[0]);
        $start_month = intval($start_arr[1]);
        $end_year = intval($end_arr[0]);
        $end_month = intval($end_arr[1]);
        $diff_year = $end_year-$start_year;
        $month_arr = [];
        $year_arr=[];
        if($diff_year == 0){
            for($month = $start_month;$month<=$end_month;$month++){
                $month_arr[] = $month;
                $year_arr[] = $start_year;
            }
        } else {
            for($year =$start_year;$year<=$end_year;$year++){
                if($year == $start_year){
                    for($month = $start_month;$month<=12;$month++){
                        $month_arr[] = $month;
                        $year_arr[] = $year;
                    }
                }elseif($year==$end_year){
                    for($month = 1;$month<=$end_month;$month++){
                        $month_arr[] = $month;
                        $year_arr[] = $year;
                    }
                }else{
                    for($month = 1;$month<=12;$month++){
                        $month_arr[] = $month;
                        $year_arr[] = $year;
                    }
                }
            }
        }
        $i=0;
        $city=$model['_scenario']['city'];//获得城市
        $sum=count($month_arr);//获得个数
        $arr=array();
        for($i=0;$i<count($month_arr);$i++){
            $sql= "select b.month_no, c.excel_row, a.data_value, c.field_type,c.name
				from
					swo_monthly_dtl a, swo_monthly_hdr b, swo_monthly_field c
				where
					a.hdr_id = b.id and
					a.data_field = c.code and
					b.city = '$city' and
					b.year_no = '".$year_arr[$i]."' and
					b.month_no = '".$month_arr[$i]."' and
					c.status = 'Y'
				order by b.month_no, c.excel_row
			";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            if(empty($rows)){
              $arr="";
            }

            if($year_arr[$i]==2019&&($month_arr[$i]==1||$month_arr[$i]==2||$month_arr[$i]==3)){
                $rows[65]['data_value']=0;
                $rows[65]['name']='上月利润额';
                $rows[65]['excel_row']='73';
                $rows[66]['data_value']=0;
                $rows[66]['name']='今月利润额';
                $rows[66]['excel_row']='74';
                $rows[67]['data_value']=0;
                $rows[67]['name']='去年今月利润额';
                $rows[67]['excel_row']='75';
            }

//            print_r('<pre>');
//            print_r($rows);
            if(!empty($rows)){
                $arr[]=$rows;
                if(count($rows)==64){
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
                elseif(count($rows)==68){
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
                    if($b51==0&&$b34==0){
                        $c107=1;
                    }
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
                    $bc103=($b74-$b73)/abs(($b73==0?1:$b73));
                    $bc104=($b74-$b75)/abs(($b75==0?1:$b75));
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
                    if($year_arr[$i]==2019&&($month_arr[$i]==1||$month_arr[$i]==2||$month_arr[$i]==3)){
                        $f95=round(($e100+$e96+$e97+$e98+$e99)/20*25,2);
                    }else{
                        $f95=round(($e100+$e96+$e97+$e98+$e99+$be102+$be103+$be104)/35*25,2);
                    }

                    $f101=round(($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
                    $f116=round(($e124+$e117+$e118+$e119+$e120+$e121+$e122)/35*10,2);
                    $f74=$f75+$f87+$f95+$f101+$f116;
                }elseif(count($rows)==70){
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
                    $b61=intval($rows[54]['data_value']);
                    $b62=intval($rows[55]['data_value']);
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
                    $b76=intval($rows[68]['data_value']);
                    $b77=intval($rows[69]['data_value']);
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
                    $c93=$b37/($b68==0?1:$b68);
                    $c94=$b38.":".$b39;
                    $c96=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:($b5+$b6));
                    $c97=$b28/($b4==0?1:$b4);
                    $c98=$b23/($b3==0?1:$b3);
                    $c99=$b29;
                    $c100=$b22/($b4==0?1:$b4);
                    $c102=$b52/($b32==0?1:$b32);
                    $c103=$b57/($b56==0?1:$b56);
                    $c104=($b59+$b61)/(($b58+$b60)==0?1:($b58+$b60));
                    $c105=0;
                    $c106=$b62/100;
                    $c107=$b51/($b34==0?1:$b34);
                    if($b51==0&&$b34==0){
                        $c107=1;
                    }
                    $c108=$b48/(($b18==0?1:$b18)/(1500*12));
                    $c109=$b49/($b48==0?1:$b48);
                    $c110=$b50;
                    $c111=($b42-$b41)/abs($b41==0?1:$b41);
                    $c112=$b44/($b42==0?1:$b42);
                    $c113=$b46/($b42==0?1:$b42);
                    $c114=$b45/($b42==0?1:$b42);
                    $c115=$b47;
                    $c117=$b64;
                    $c118=$b65/($b71==0?1:$b71);
                    $c119=$b74/($b73==0?1:$b73);
                    $c120=$b66/($b68==0?1:$b68);
                    $c121=$b69/(($b68==0?1:$b68)/6);
                    $c122=$b70/(($b68==0?1:$b68)/30);
                    $c123=0;
                    $c124=$b67/($b72==0?1:$b72);
                    //利润的
                    $bc102=$b76/($b4==0?1:$b4);
                    $bc103=($b76-$b75)/abs(($b75==0?1:$b75));
                    $bc104=($b76-$b77)/abs(($b77==0?1:$b77));
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
                    if($year_arr[$i]==2019&&($month_arr[$i]==1||$month_arr[$i]==2||$month_arr[$i]==3)){
                        $f95=round(($e100+$e96+$e97+$e98+$e99)/20*25,2);
                    }else{
                        $f95=round(($e100+$e96+$e97+$e98+$e99+$be102+$be103+$be104)/35*25,2);
                    }

                    $f101=round(($e115+$e102+$e103+$e104+$e105+$e106+$e107+$e108+$e109+$e110+$e111+$e112+$e113+$e114)/55*15,2);
                    $f116=round(($e124+$e117+$e118+$e119+$e120+$e121+$e122)/35*10,2);
                    $f74=$f75+$f87+$f95+$f101+$f116;
                }elseif(count($rows)==71){
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
                    $b61=intval($rows[54]['data_value']);
                    $b62=intval($rows[55]['data_value']);
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
                    $b76=intval($rows[68]['data_value']);
                    $b77=intval($rows[69]['data_value']);
                    $b78=intval($rows[70]['data_value']);
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
                    $c93=$b37/($b68==0?1:$b68);
                    $c94=$b38.":".$b39;
                    $c96=($b5+$b6-$b25-$b26-$b27)/(($b5+$b6)==0?1:($b5+$b6));
                    $c97=$b28/($b4==0?1:$b4);
                    $c98=$b23/($b3==0?1:$b3);
                    $c99=$b29;
                    $c100=$b22/($b4==0?1:$b4);
                    $c102=$b52/($b32==0?1:$b32);
                    $c103=$b57/($b56==0?1:$b56);
                    $c104=($b59+$b61)/(($b58+$b60)==0?1:($b58+$b60));
                    $c105=0;
                    $c106=$b62/100;
                    $c107=$b51/($b34==0?1:$b34);
                    if($b51==0&&$b34==0){
                        $c107=1;
                    }
                    $c108=$b48/(($b18==0?1:$b18)/(1500*12));
                    $c109=$b49/($b48==0?1:$b48);
                    $c109s=$b48/($b78==0?1:$b78);
                    $c110=$b50;
                    $c111=($b42-$b41)/abs($b41==0?1:$b41);
                    $c112=$b44/($b42==0?1:$b42);
                    $c113=$b46/($b42==0?1:$b42);
                    $c114=$b45/($b42==0?1:$b42);
                    $c115=$b47;
                    $c117=$b64;
                    $c118=$b65/($b71==0?1:$b71);
                    $c119=$b74/($b73==0?1:$b73);
                    $c120=$b66/($b68==0?1:$b68);
                    $c121=$b69/(($b68==0?1:$b68)/6);
                    $c122=$b70/(($b68==0?1:$b68)/30);
                    $c123=0;
                    $c124=$b67/($b72==0?1:$b72);
                    //利润的
                    $bc102=$b76/($b4==0?1:$b4);
                    $bc103=($b76-$b75)/abs(($b75==0?1:$b75));
                    $bc104=($b76-$b77)/abs(($b77==0?1:$b77));
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
//                    if($c109s<0.1||empty($b78)){
//                        $e109=($c109>0.2?3:($c109>0.1?5:($c109>=0?1:0)));
//                    }else{
//                        $e109=($c109>0.2?1:($c109>0.1?3:($c109>=0?5:0)));
//                    }
                    //=IF(B48/IF(B78=0,10000,B78)<0.1,IF(C119>0.2,3,IF(C119>0.1,5,IF(C119>=0,1,5))),IF(C119>0.2,1,IF(C119>0.1,3,IF(C119>=0,5,1))))
                    $e109= ($b48/($b78==0?10000:$b78)) >= 0.1 ? ( ($c109)>0.2?1:($c109>0.1?3:($c109>=0?5:0)) ) :( ($c109)>0.2?3:($c109>0.1?5:($c109>=0?1:0)));
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
                    if($year_arr[$i]==2019&&($month_arr[$i]==1||$month_arr[$i]==2||$month_arr[$i]==3)){
                        $f95=round(($e100+$e96+$e97+$e98+$e99)/20*25,2);
                    }else{
                        $f95=round(($e100+$e96+$e97+$e98+$e99+$be102+$be103+$be104)/35*25,2);
                    }

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
                    if($b51==0&&$b34==0){
                        $c107=1;
                    }
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
                $excel['c76']=(round($c76,4)*100)."%";
                $excel['c77']=(round($c77,4)*100)."%";
                $excel['c78']=(round($c78,4)*100)."%";
                $excel['c79']=(round($c79,4)*100)."%";
                $excel['c80']=(round($c80,4)*100)."%";
                $excel['c81']=(round($c81,4)*100)."%";
                $excel['c82']=(round($c82,4)*100)."%";
                $excel['c83']=(round($c83,4)*100)."%";
                $excel['c84']=(round($c84,4)*100)."%";
                $excel['c85']=(round($c85,4)*100)."%";
                $excel['c86']=(round($c86,4)*100)."%";
                $excel['c88']=(round($c88,4)*100)."%";
                $excel['c89']=(round($c89,4)*100)."%";
                $excel['c90']=round($c90,4);
                $excel['c91']=(round($c91,4)*100)."%";
                $excel['c92']=(round($c92,4)*100)."%";
                $excel['c93']=(round($c93,4)*100)."%";
                $excel['c94']=round($c94,4);
                $excel['c96']=(round($c96,4)*100)."%";
                $excel['c97']=(round($c97,4)*100)."%";
                $excel['c98']=(round($c98,4)*100)."%";
                $excel['c99']=round($c99,4);
                $excel['c100']=(round($c100,4)*100)."%";
                $excel['c102']=(round($c102,4)*100)."%";
                $excel['c103']=(round($c103,4)*100)."%";
                $excel['c104']=(round($c104,4)*100)."%";
                $excel['c105']=$c105;
                $excel['c106']=(round($c106,4)*100)."%";
                $excel['c107']=(round($c107,4)*100)."%";
                $excel['c108']=(round($c108,4)*100)."%";
                $excel['c109']=(round($c109,4)*100)."%";
                $excel['c110']=$c110;
                $excel['c111']=(round($c111,4)*100)."%";
                $excel['c112']=(round($c112,4)*100)."%";
                $excel['c113']=(round($c113,4)*100)."%";
                $excel['c114']=(round($c114,4)*100)."%";
                $excel['c115']=(round($c115,4)*100)."%";
                $excel['c117']=(round($c117,4)*100)."%";
                $excel['c118']=(round($c118,4)*100)."%";
                $excel['c119']=(round($c119,4)*100)."%";
                $excel['c120']=(round($c120,4)*100)."%";
                $excel['c121']=(round($c121,4)*100)."%";
                $excel['c122']=(round($c122,4)*100)."%";
                $excel['c124']=(round($c124,4)*100)."%";

                $excel['e76']=round($e76,4);
                $excel['e77']=round($e77,4);
                $excel['e78']=round($e78,4);
                $excel['e79']=round($e79,4);
                $excel['e80']=round($e80,4);
                $excel['e81']=round($e81,4);
                $excel['e82']=round($e82,4);
                $excel['e83']=round($e83,4);
                $excel['e84']=round($e84,4);
                $excel['e85']=round($e85,4);
                $excel['e86']=round($e86,4);
                $excel['e88']=round($e88,4);
                $excel['e89']=round($e89,4);
                $excel['e90']=round($e90,4);
                $excel['e91']=round($e91,4);
                $excel['e92']=round($e92,4);
                $excel['e93']=round($e93,4);
                $excel['e94']=round($e94,4);
                $excel['e96']=round($e96,4);
                $excel['e97']=round($e97,4);
                $excel['e98']=round($e98,4);
                $excel['e99']=round($e99,4);
                $excel['e100']=round($e100,4);
                $excel['e102']=round($e102,4);
                $excel['e103']=round($e103,4);
                $excel['e104']=round($e104,4);
                $excel['e105']=round($e105,4);
                $excel['e106']=round($e106,4);
                $excel['e107']=round($e107,4);
                $excel['e108']=round($e108,4);
                $excel['e109']=round($e109,4);
                $excel['e110']=round($e110,4);
                $excel['e111']=round($e111,4);
                $excel['e112']=round($e112,4);
                $excel['e113']=round($e113,4);
                $excel['e114']=round($e114,4);
                $excel['e115']=round($e115,4);
                $excel['e117']=round($e117,4);
                $excel['e118']=round($e118,4);
                $excel['e119']=round($e119,4);
                $excel['e120']=round($e120,4);
                $excel['e121']=round($e121,4);
                $excel['e122']=round($e122,4);
                $excel['e124']=round($e124,4);
                if(!empty($rows[67])){
                    $excel['b67']=round($rows[67],4);
                    $excel['bc102']=(round($bc102,4)*100)."%";
                    $excel['bc103']=(round($bc103,4)*100)."%";
                    $excel['bc104']=(round($bc104,4)*100)."%";
                    $excel['be102']=round($be102,4);
                    $excel['be103']=round($be103,4);
                    $excel['be104']=round($be104,4);
                }

                $excel['f74']=$f74;
                $excel['f87']=$f87;
                $excel['f95']=$f95;
                $excel['f101']=$f101;
                $excel['f116']=$f116;
                $excel['f75']=$f75;
                $this->excel[]=$excel;
            }
        }

        $this->five=$arr;
        $this->year=$year_arr;
        $this->month=$month_arr;
        $this->ccuser=$sum;
//        print_r('<pre>');
//        print_r($model);
    }


}
