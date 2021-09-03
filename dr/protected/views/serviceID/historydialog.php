<?php
$ftrbtn = array();
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'historydialog',
    'header'=>Yii::t('service','Service List'),
    'footer'=>$ftrbtn,
    'show'=>false,
    'size'=>" modal-lg",
));
?>

<div class="box" id="flow-list" style="max-height: 300px; overflow-y: auto;">
    <table id="tblFlow" class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th><?php echo Yii::t('service','service no'); ?></th>
            <th><?php echo Yii::t('service','Customer'); ?></th>
            <th><?php echo Yii::t('service','Customer Type'); ?></th>
            <th><?php echo Yii::t('service','Customer type end'); ?></th>
            <th><?php echo Yii::t('service','Record Type'); ?></th>
            <th><?php echo Yii::t('service','Record Date'); ?></th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $historyList = ServiceIDForm::getServiceIDHistory($model->service_new_id);
        if($historyList){
            foreach ($historyList as $list){
                if($list["id"]==$model->id){
                    echo "<tr class='success'>";
                }else{
                    echo "<tr>";
                }
                echo "<td>".$list["service_no"]."</td>";
                echo "<td>".$list["company_name"]."</td>";
                echo "<td>".$list["description"]."</td>";
                echo "<td>".$list["cust_type_name"]."</td>";
                echo "<td>".General::getStatusDesc($list["status"])."</td>";
                echo "<td>".General::toDate($list["status_dt"])."</td>";
                echo "<td>";
                echo "<a target='_blank' href='".Yii::app()->createUrl('serviceID/edit',array('index'=>$list['id']))."'><span class='glyphicon glyphicon-eye-open'></span></a>";
                echo "</td></tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>

<?php
$this->endWidget();
?>
