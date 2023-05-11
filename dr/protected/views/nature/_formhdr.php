<tr>
    <th>
        <?php echo TbHtml::label($this->getLabelName('name'), false); ?>
    </th>
    <th>
        <?php echo TbHtml::label($this->getLabelName('rpt_u'), false); ?>
    </th>
    <th>
        <?php echo TbHtml::label($this->getLabelName('score_bool'), false); ?>
    </th>

	<th>
<!--		--><?php echo // Yii::app()->user->validRWFunction('XS03') ?
				TbHtml::Button('+',array('id'=>'btnAddRow','title'=>Yii::t('misc','Add'),'size'=>TbHtml::BUTTON_SIZE_SMALL));
//				: '&nbsp;';
		?>
	</th>
</tr>
