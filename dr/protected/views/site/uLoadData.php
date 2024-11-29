<?php
$load_length = 0;
$u_load_length = 0;
if (isset($model->u_load_data)&&is_array($model->u_load_data)){
    $load_length = key_exists("load_end",$model->u_load_data)?$model->u_load_data["load_end"]:0;
    $load_length-= key_exists("load_start",$model->u_load_data)?$model->u_load_data["load_start"]:0;
    $u_load_length = key_exists("u_load_end",$model->u_load_data)?$model->u_load_data["u_load_end"]:0;
    $u_load_length-= key_exists("u_load_start",$model->u_load_data)?$model->u_load_data["u_load_start"]:0;
}
?>

<?php if ($load_length>=100): ?>
    <small>
        <span>查询总时长：</span>
        <span><?php echo $load_length;?></span>
        <span>(秒)；</span>
        <span>派单系统查询时长：</span>
        <span><?php echo $u_load_length;?></span>
        <span>(秒)</span>
    </small>
<?php endif ?>