<?php

class LookupController extends Controller
{
	public $interactive = false;
	
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('company','staff','product','companyex2','companyex','staffex','staffAndEx','staffex2','productex','template','userstaffex','reasonex'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionCompany($search) {
		$city = Yii::app()->user->city();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, concat(code,name) as value from swo_company
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'";
		$result = Yii::app()->db->createCommand($sql)->queryAll();
		$data = TbHtml::listData($result, 'id', 'value');
		echo TbHtml::listBox('lstlookup', '', $data, array('size'=>'15', 'multiple'=>true));
	}

	public function actionCompanyEx($search) {
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, code, name, cont_name, cont_phone, address from swo_company
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
//						'value'=>$record['code'].' '.$record['name'],
						'value'=>$record['code'].$record['name'],
						'contact'=>trim($record['cont_name']).'/'.trim($record['cont_phone']),
						'address'=>$record['address'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionReasonEx($search) {
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id,remark from swo_stop_remark
				where remark like '%".$searchx."%'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['remark'],
					);
			}
		}
		print json_encode($result);
	}
	
	public function actionCompanyEx2($search,$city='') {
		$city = empty($city)?Yii::app()->user->city():$city;
		$result = array();
		$hidden = '';
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, code, name, cont_name, cont_phone, address from swo_company
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
//				$result[$record['id']] = substr($record['code'].str_repeat(' ',8),0,8).$record['name'];
				$result[$record['id']] = $record['code'].$record['name'];
				$hidden .= TbHtml::hiddenField('otherfld_'.$record['id'].'_contact',trim($record['cont_name']).'/'.trim($record['cont_phone']));
				$hidden .= TbHtml::hiddenField('otherfld_'.$record['id'].'_address',$record['address']);
			}
			$list = TbHtml::radioButtonList('lstlookup','',$result);
			echo $list.$hidden;
		} else {
			echo TbHtml::label(Yii::t('dialog','No Record Found'),false);
		}
	}

	public function actionStaff($search)
	{
		$city = Yii::app()->user->city();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') 
				and (city='".$city."' or (city='ZY' and department like '%技术%'))
				and (leave_dt is null or leave_dt=0 or leave_dt > now())
			";
		$result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.id, concat(a.name, ' (', a.code, ')') as value from swo_staff_v a, hr$suffix.hr_plus_city b
				where (a.code like '%".$searchx."%' or a.name like '%".$searchx."%') 
				and b.city='".$city."'
				and (a.leave_dt is null or a.leave_dt=0 or a.leave_dt > now())
				and a.id=b.employee_id
			";
		$result3 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
		
		$result = array_merge($result1, $result3, $result2);
		$data = TbHtml::listData($result, 'id', 'value');
		echo TbHtml::listBox('lstlookup', '', $data, array('size'=>'15',));
	}

	public function actionStaffEx($search,$kaSearch=0) {
        $suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);
        $kaSql = "";
        if($kaSearch==1){
            $kaSql = " or b.name like '%KA%'";
        }

        $sql = "select a.id, concat(a.name, ' (', a.code, ')') as value from hr$suffix.hr_employee a
                LEFT JOIN hr$suffix.hr_dept b on a.position = b.id 
				where (a.code like '%".$searchx."%' or a.name like '%".$searchx."%') 
				and (a.city='".$city."' or (a.city='ZY' and b.name like '%技术%') {$kaSql})
				and a.staff_status=0 
			 ";
        $result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.id, concat(a.name, ' (', a.code, ')') as value from swo_staff_v a, hr$suffix.hr_plus_city b
				where (a.code like '%".$searchx."%' or a.name like '%".$searchx."%') 
				and b.city='".$city."'
				and (a.leave_dt is null or a.leave_dt=0 or a.leave_dt > now())
				and a.id=b.employee_id
			";
		$result3 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
		
		$records = array_merge($result1, $result3, $result2);
		if (count($records) > 0) {
            $result[] = array(
                'id'=>0,
                'value'=>'',
            );
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['value'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionStaffAndEx($search,$kaSearch=0) {
        $suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);
        $kaSql = "";
        if($kaSearch==1){
            $kaSql = " or b.name like '%KA%'";
        }

        $sql = "select a.id, concat(a.name, ' (', a.code, ')') as value from hr$suffix.hr_employee a
                LEFT JOIN hr$suffix.hr_dept b on a.position = b.id 
				where (a.code like '%".$searchx."%' or a.name like '%".$searchx."%') 
				and (a.city='".$city."' or (a.city='ZY' and b.name like '%技术%') {$kaSql})
				and ((a.staff_status=0 and a.table_type=1) or (a.staff_status=1 and a.table_type!=1)) 
			 ";
        $result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.id, concat(a.name, ' (', a.code, ')') as value from swo_staff_v a, hr$suffix.hr_plus_city b
				where (a.code like '%".$searchx."%' or a.name like '%".$searchx."%') 
				and b.city='".$city."'
				and (a.leave_dt is null or a.leave_dt=0 or a.leave_dt > now())
				and a.id=b.employee_id
			";
		$result3 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();

		$records = array_merge($result1, $result3, $result2);
		if (count($records) > 0) {
            $result[] = array(
                'id'=>0,
                'value'=>'',
            );
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['value'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionStaffEx2($search,$city='')	{
		$city = empty($city)?Yii::app()->user->city():$city;
		$result = array();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') 
				and (city='".$city."' or (city='ZY' and department like '%技术%'))
				and (leave_dt is null or leave_dt=0 or leave_dt > now()) 
			";
        $result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.id, concat(a.name, ' (', a.code, ')') as value from swo_staff_v a, hr$suffix.hr_plus_city b
				where (a.code like '%".$searchx."%' or a.name like '%".$searchx."%') 
				and b.city='".$city."'
				and (a.leave_dt is null or a.leave_dt=0 or a.leave_dt > now())
				and a.id=b.employee_id
			";
		$result3 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
		
		$records = array_merge($result1, $result3, $result2);
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[$record['id']] = $record['value'];
			}
			$list = TbHtml::radioButtonList('lstlookup','',$result);
			echo $list;
		} else {
			echo TbHtml::label(Yii::t('dialog','No Record Found'),false);
		}
	}

	public function actionUserstaffEx($search, $incity='')
	{
		$city = $incity;
		$result = array();
		$searchx = str_replace("'","\'",$search);

		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
			    union
				select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='ZY' and department like '%技术%'		
			    union
				select a.id, concat(a.name, ' (', a.code, ')') as value from swo_staff_v a, hr$suffix.hr_plus_city b
				where (a.code like '%".$searchx."%' or a.name like '%".$searchx."%') and b.city='".$city."' and a.id=b.employee_id
			";
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['value'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionProduct($search)
	{
		$city = '99999';	//Yii::app()->user->city();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, concat(left(concat(code,space(8)),8),description) as value from swo_product
				where (code like '%".$searchx."%' or description like '%".$searchx."%') and city='".$city."'";
		$result = Yii::app()->db->createCommand($sql)->queryAll();
		$data = TbHtml::listData($result, 'id', 'value');
		echo TbHtml::listBox('lstlookup', '', $data, array('size'=>'15',));
	}

	public function actionProductEx($search)
	{
		$city = '99999';	//Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, concat(left(concat(code,space(8)),8),description) as value from swo_product
				where (code like '%".$searchx."%' or description like '%".$searchx."%') and city='".$city."'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['value'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionTemplate($system) {
		$result = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select temp_id, temp_name from security$suffix.sec_template
				where system_id='$system'
			";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['temp_id'],
						'name'=>$record['temp_name'],
					);
			}
		}
		print json_encode($result);
	}

//	public function actionSystemDate()
//	{
//		echo CHtml::tag( date('Y-m-d H:i:s'));
//		Yii::app()->end();
//	}
}
