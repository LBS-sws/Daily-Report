<?php

class AnnounceForm extends CFormModel {
	public $id;
	public $name;
	public $start_dt;
	public $end_dt;
	public $priority = 0;
	public $content;
	public $image_type;
	public $image_caption;
	public $imageA;
	public $imageA_old;

	public function attributeLabels() {
		return array(
			'name'=>Yii::t('code','Description'),
			'start_dt'=>Yii::t('code','Start Date'),
			'end_dt'=>Yii::t('code','End Date'),
			'priority'=>Yii::t('code','Priority'),
			'imageA'=>Yii::t('code','Image'),
			'image_type'=>Yii::t('code','Image Type'),
			'image_caption'=>Yii::t('code','Image Caption'),
			'content'=>Yii::t('code','Content'),
		);
	}

	public function rules() {
		return array(
			array('name, start_dt, end_dt','required'),
			array('imageA','file','types'=>'jpg, png, gif','allowEmpty'=>true),
			array('id,priority,imageA_old,image_type,image_caption,content','safe'), 
		);
	}

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$sql = "select * from announcement$suffix.ann_announce where id=".$index." ";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->name = $row['name'];
			$this->start_dt = General::toDate($row['start_dt']);
			$this->end_dt = General::toDate($row['end_dt']);
			$this->priority = $row['priority'];
			$this->imageA = $row['image'];
			$this->imageA_old = $row['image'];
			$this->image_type = $row['image_type'];
			$this->image_caption = $row['image_caption'];
			$this->content = $row['content'];
		}
		return true;
	}
	
	public function saveData() {
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->save($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function save(&$connection) {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from announcement$suffix.ann_announce where id = :id";
				break;
			case 'new':
				$sql = "insert into announcement$suffix.ann_announce(
						name, start_dt, end_dt, priority, content, image_caption, image_type, image, lcu, luu) values (
						:name, :start_dt, :end_dt, :priority, :content, :image_caption, :image_type, :image, :lcu, :luu)";
				break;
			case 'edit':
				$sql = "update announcement$suffix.ann_announce set 
					name = :name, 
					start_dt = :start_dt,
					end_dt = :end_dt,
					priority = :priority,
					content = :content,
					image_caption = :image_caption,
					image_type = :image_type,
					image = :image,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':start_dt')!==false) {
			$sdate = General::toMyDate($this->start_dt);
			$command->bindParam(':start_dt',$sdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':end_dt')!==false) {
			$edate = General::toMyDate($this->end_dt);
			$command->bindParam(':end_dt',$edate,PDO::PARAM_STR);
		}
		if (strpos($sql,':priority')!==false)
			$command->bindParam(':priority',$this->priority,PDO::PARAM_INT);
		if (strpos($sql,':image')!==false)
			$command->bindParam(':image',$this->imageA,PDO::PARAM_STR);
		if (strpos($sql,':image_type')!==false)
			$command->bindParam(':image_type',$this->image_type,PDO::PARAM_STR);
		if (strpos($sql,':image_caption')!==false)
			$command->bindParam(':image_caption',$this->image_caption,PDO::PARAM_STR);
		if (strpos($sql,':content')!==false)
			$command->bindParam(':content',$this->content,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		return true;
	}

	public function getImageString() {
		$rtn = '';
		if (!empty($this->imageA)) {
			$type = ($this->image_type=='jpg') ? 'jpeg' : $this->image_type;
			$rtn = "data:image/$type;base64,".$this->imageA;
		}
		return $rtn;
	}

}
