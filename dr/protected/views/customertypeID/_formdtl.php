<tr>
    <td>
        <?php
        if ($this->model->index_num<3){
            echo TbHtml::button("<span class='fa fa-cog'></span>",array('submit'=>Yii::app()->createUrl('customertypeID/edit',array("index"=>$this->record['id'],"type"=>1))));
        }
        ?>
    </td>
    <td>
        <?php
        echo TbHtml::textField($this->getFieldName('cust_type_name'), $this->record['cust_type_name'],
            array('size'=>10,'min'=>0,
                'readonly'=>($this->model->isReadOnly()),
            )
        );
        ?>
    </td>
    <td>
        <?php echo TbHtml::dropDownList($this->getFieldName('single'),  $this->record['single'], array('0'=>'非一次性服务','1'=>'一次性服务'),
            array('disabled'=>$this->model->isReadOnly())
        ); ?>
    </td>
    <td>
        <?php echo TbHtml::dropDownList($this->getFieldName('conditions'),  $this->record['conditions'], array('1'=>'每个','2'=>'每个新客户','3'=>'每个新客户订购一包','4'=>'每个新客户每桶','5'=>'每个新客户每箱','6'=>'每月'),
            array('disabled'=>$this->model->isReadOnly())
        ); ?>
    </td>
	<td>
		<?php  
			echo TbHtml::numberField($this->getFieldName('fraction'), $this->record['fraction'],
							array('size'=>10,'min'=>0,
							'readonly'=>($this->model->isReadOnly()),
							)
						);
		?>
	</td>
	<td>
		<?php  
			echo TbHtml::numberField($this->getFieldName('toplimit'), $this->record['toplimit'],
							array('size'=>5,'min'=>0,
							'readonly'=>($this->model->isReadOnly()),
							)
						);
		?>
	</td>
<!--	<td>-->
<!--		--><?php //
//			echo TbHtml::numberField($this->getFieldName('inv_rate'), $this->record['inv_rate'],
//							array('size'=>5,'min'=>0,
//							'readonly'=>($this->model->isReadOnly()),
//							)
//						);
//		?>
<!--	</td>-->
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
