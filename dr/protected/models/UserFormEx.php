<?php

class UserFormEx 
{
	public static function fieldsOnlib() {
		return array(
			'onlibrole'=>'json', 
			'onlibuser'=>'string',
		);
	}
	
	public static function roleListOnlib() {
		return array(
			'Finance_Admin'=>Yii::t('external','Finance').' '.Yii::t('external','Admin'),
			'Finance_Team'=>Yii::t('external','Finance').' '.Yii::t('external','Member'),
			'IT_Group_Admin'=>Yii::t('external','IT').' '.Yii::t('external','Admin'),
			'IT_Team'=>Yii::t('external','IT').' '.Yii::t('external','Member'),
			'Full_Admin'=>Yii::t('external','Admin').' ('.Yii::t('external','All').')',
		);
	}
	
	public static function saveOnlib(&$connection, &$model) {
		$sys_id = 'onlib';
		$action = '';
		$right = isset($model->rights[5]['XX01']) ? $model->rights[5]['XX01'] : 'NA';
		$old_param = array(
				'role'=>isset($model->oriextfields['onlibrole']['value'])
						? (is_array($model->oriextfields['onlibrole']['value']) 
							? $model->oriextfields['onlibrole']['value']
							: (empty($model->oriextfields['onlibrole']['value']) ? array() : json_decode($model->oriextfields['onlibrole']['value']))
						)
						: array(),
				'user'=>isset($model->oriextfields['onlibuser']['value']) ? $model->oriextfields['onlibuser']['value'] : '',
				'right'=>isset($model->oriextrights['onlib']['XX01']) ? $model->oriextrights['onlib']['XX01'] : '',
				'disp_name'=>$model->disp_name,
				'email'=>$model->email,
			);
		$new_param = array(
				'role'=>isset($model->extfields['onlibrole']['value'])
						? (is_array($model->extfields['onlibrole']['value']) 
							? $model->extfields['onlibrole']['value']
							: $model->extfields['onlibrole']['value']
						)
						: array(),
				'user'=>isset($model->extfields['onlibuser']['value']) ? $model->extfields['onlibuser']['value'] : '',
				'right'=>$right,
				'disp_name'=>$model->disp_name,
				'email'=>$model->email,
			);
		$model->scenario=='delete' && $action='delete';
		$model->scenario=='new' && $right=='CN' && $action='new';
		$model->scenario=='edit' && $old_param!=$new_param && $action='edit';
		
		if (!empty($action)) {
			$onlibuser = isset($model->extfields['onlibuser']['value']) ? $model->extfields['onlibuser']['value'] : '';
			if ($right=='CN' && empty($onlibuser)) {
				$onlibuser = strtolower($model->username).'.'.strtolower($model->city);
				$model->extfields['onlibuser']['value'] = $onlibuser;
				$new_param['user'] = $onlibuser;
			}
			
			$suffix = Yii::app()->params['envSuffix'];
			$sql = "insert into security$suffix.sec_extsys_queue(sys_id, action, req_dt, username, old_param, new_param, status)
					value(:sys_id, :action, now(), :username, :old_param, :new_param, 'P')
				";
			$command=$connection->createCommand($sql);
			if (strpos($sql,':sys_id')!==false)
				$command->bindParam(':sys_id',$sys_id,PDO::PARAM_STR);
			if (strpos($sql,':action')!==false)
				$command->bindParam(':action',$action,PDO::PARAM_STR);
			if (strpos($sql,':username')!==false)
				$command->bindParam(':username',$onlibuser,PDO::PARAM_STR);
			if (strpos($sql,':old_param')!==false) {
				$oparam = json_encode($old_param);
				$command->bindParam(':old_param',$oparam,PDO::PARAM_STR);
			}
			if (strpos($sql,':new_param')!==false) {
				$nparam = json_encode($new_param);
				$command->bindParam(':new_param',$nparam,PDO::PARAM_STR);
			}
			$command->execute();
		}
		return true;
	}
}