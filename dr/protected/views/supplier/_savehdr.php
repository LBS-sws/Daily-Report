<tr>
    <th></th>
    <?php if (!Yii::app()->user->isSingleCity()) : ?>
<!--        <th>-->
<!--            --><?php //echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('supplier-list','city_name'))
//            ;
//            ?>
<!--        </th>-->
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('req_dt').$this->drawOrderArrow('req_dt'),'#',$this->createOrderLink('supplier-form','req_dt'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('ref_no').$this->drawOrderArrow('ref_no'),'#',$this->createOrderLink('supplier-form','ref_no'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('trans_type_desc').$this->drawOrderArrow('trans_type_desc'),'#',$this->createOrderLink('supplier-form','trans_type_desc'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('bank').$this->drawOrderArrow('bank'),'#',$this->createOrderLink('supplier-form','bank'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('amount').$this->drawOrderArrow('amount'),'#',$this->createOrderLink('supplier-form','amount'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('item_desc').$this->drawOrderArrow('item_desc'),'#',$this->createOrderLink('supplier-form','item_desc'))
        ;
        ?>
    </th>
</tr> 
