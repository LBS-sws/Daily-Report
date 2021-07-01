<?php
$records = ServiceEndreasonList::getlist();
$content = "<p>原因：<select name='reason'>";
foreach ($records as $value) {
        $content .= "<option value='".$value['id']."'>".$value['reason']."</option>";
}
$content.="</select></p>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'sendemail',
    'header'=>'发送邮件',
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('submit'=>Yii::app()->createUrl('service/endsendemail'))),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>