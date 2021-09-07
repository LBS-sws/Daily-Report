<tr>
    <th>
        <?php echo TbHtml::label($this->getLabelName('back_date'), false); ?>
    </th>
    <th>
        <?php echo TbHtml::label($this->getLabelName('back_money')." <small class='text-danger'>".Yii::t("service","Note: Deposit is not included")."</small>", false); ?>
    </th>
    <th>
        <?php echo TbHtml::label($this->getLabelName('put_month'), false); ?>
    </th>
	<th>
		<?php echo TbHtml::label($this->getLabelName('out_month'), false); ?>
	</th>

	<th>
<!--		--><?php echo // Yii::app()->user->validRWFunction('XS03') ?
				TbHtml::Button('+',array('id'=>'btnAddRow','title'=>Yii::t('misc','Add'),'size'=>TbHtml::BUTTON_SIZE_SMALL));
//				: '&nbsp;';
		?>
	</th>
</tr>
