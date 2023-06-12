<tr>
    <th width="55%">
        <?php echo TbHtml::label($this->getLabelName('office_id'), false); ?>
    </th>
    <th width="45%">
        <?php echo TbHtml::label($this->getLabelName('life_num'), false); ?>
    </th>

	<th>
<!--		--><?php echo // Yii::app()->user->validRWFunction('XS03') ?
				TbHtml::Button('+',array('id'=>'btnAddRow','title'=>Yii::t('misc','Add'),'size'=>TbHtml::BUTTON_SIZE_SMALL));
//				: '&nbsp;';
		?>
	</th>
</tr>
