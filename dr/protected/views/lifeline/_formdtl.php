<tr>
    <td>
        <?php echo TbHtml::dropDownList($this->getFieldName('office_id'),  $this->record['office_id'], LifelineForm::getOfficeList($this->model->city),
            array('disabled'=>$this->model->isReadOnly())
        ); ?>
    </td>
	<td>
		<?php  
			echo TbHtml::numberField($this->getFieldName('life_num'), $this->record['life_num'],
							array('min'=>0,
							'readonly'=>($this->model->isReadOnly()),
							)
						);
		?>
	</td>
	<td>
		<?php
			echo !$this->model->isReadOnly() 
				? TbHtml::Button('-',array('id'=>'btnDelRow','title'=>Yii::t('misc','Delete'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
        <?php echo CHtml::hiddenField($this->getFieldName('uflag'),$this->record['uflag']); ?>
		<?php echo CHtml::hiddenField($this->getFieldName('id'),$this->record['id']); ?>
	</td>
</tr>
