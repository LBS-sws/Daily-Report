<?php
$thisDate = date("Y/m/01");
$status_dt = date("Y/m/d",strtotime($this->record['back_date']));
$trBool = $status_dt<$thisDate;
?>

<tr>
    <td>
        <?php
        echo TbHtml::textField($this->getFieldName('back_date'), $this->record['back_date'],
            array('readonly'=>($this->model->isReadOnly()||$trBool),
                'prepend'=>'<span class="fa fa-calendar"></span>',
                'class'=>'deadline changeOutMonth',
                'autocomplete'=>'off'
            )
        );
        ?>
    </td>
    <td>
        <?php
        echo TbHtml::numberField($this->getFieldName('back_money'), $this->record['back_money'],
            array('size'=>10,'min'=>0,
                'readonly'=>($this->model->isReadOnly()||$trBool),
                'prepend'=>'<span class="fa '.$this->model->sign.'"></span>',
                'class'=>'changeOutMoney'
            )
        );
        ?>
    </td>
    <td>
        <?php
        echo TbHtml::numberField($this->getFieldName('put_month'), $this->record['put_month'],
            array('size'=>10,'min'=>0,
                'readonly'=>($this->model->isReadOnly()||$trBool),
                'class'=>'changeOutMonth'
            )
        );
        ?>
    </td>
    <td>
        <?php
        echo TbHtml::numberField($this->getFieldName('out_month'), $this->record['out_month'],
            array('size'=>10,'min'=>0,
                'readonly'=>(true),
            )
        );
        ?>
    </td>
    <td>
        <?php
        echo TbHtml::dropDownList($this->getFieldName('back_ratio'), $this->record['back_ratio'],array(50=>"50%",100=>"100%"),
            array(
                'readonly'=>($this->model->isReadOnly()||$trBool),
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
