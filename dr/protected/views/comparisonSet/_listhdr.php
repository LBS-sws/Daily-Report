<tr>
    <td colspan="8"></td>
</tr>
<tr>
    <th width="10%" rowspan="2" class="text-center" style="vertical-align: middle">
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('comparisonSet-list','b.name'))
        ;
        ?>
    </th>
    <th  colspan="2" class="upside">
        <?php echo $this->model->comparison_year.Yii::t('summary',"Annual target ").Yii::t('summary',"(upside case)");?>
    </th>
    <th  colspan="2" class="base">
        <?php echo $this->model->comparison_year.Yii::t('summary',"Annual target ").Yii::t('summary',"(base case)");?>
    </th>
    <th  colspan="2" class="minimum">
        <?php echo $this->model->comparison_year.Yii::t('summary',"Annual target ").Yii::t('summary',"(minimum case)");?>
    </th>
</tr>
<tr>
    <th width="15%" class="upside">
        <?php echo Yii::t('summary',"Gross");?>
    </th>
    <th width="15%" class="upside">
        <?php echo Yii::t('summary',"Net");?>
    </th>
    <th width="15%" class="base">
        <?php echo Yii::t('summary',"Gross");?>
    </th>
    <th width="15%" class="base">
        <?php echo Yii::t('summary',"Net");?>
    </th>
    <th width="15%" class="minimum">
        <?php echo Yii::t('summary',"Gross");?>
    </th>
    <th width="15%" class="minimum">
        <?php echo Yii::t('summary',"Net");?>
    </th>
</tr>
