<tr>
    <td>
        <?php
        echo TbHtml::textField($this->getFieldName('name'), $this->record['name'],
            array('size'=>10,'min'=>0,
                'readonly'=>($this->model->isReadOnly()),
            )
        );
        ?>
    </td>
    <td>
        <?php
        echo TbHtml::numberField($this->getFieldName('rpt_u'), $this->record['rpt_u'],
            array('min'=>0,'readonly'=>($this->model->isReadOnly()))
        );
        ?>
    </td>
    <td>
        <?php echo TbHtml::dropDownList($this->getFieldName('score_bool'),  $this->record['score_bool'], array('0'=>'否','1'=>'是'),
            array('disabled'=>$this->model->isReadOnly())
        ); ?>
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
