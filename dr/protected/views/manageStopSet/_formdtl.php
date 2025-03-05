<tr>
    <td>
        <?php echo TbHtml::dropDownList($this->getFieldName('operator'),  $this->record['operator'], array('LT'=>'<=','GT'=>'>'),
            array('disabled'=>$this->model->isReadOnly())
        ); ?>
    </td>
	<td>
		<?php  
			echo TbHtml::numberField($this->getFieldName('stopRate'), $this->record['stopRate'],
							array('max'=>100,'min'=>0,
							'readonly'=>($this->model->isReadOnly()),
							'append'=>'<span>%</span>',
							)
						);
		?>
	</td>
	<td>
		<?php
			echo TbHtml::numberField($this->getFieldName('coefficient'), $this->record['coefficient'],
							array('max'=>100,'min'=>0,
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
		<?php echo CHtml::hiddenField($this->getFieldName('hdrId'),$this->record['hdrId']); ?>
	</td>
</tr>
