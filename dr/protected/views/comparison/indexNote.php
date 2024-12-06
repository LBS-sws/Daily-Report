<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2 text-danger">
        <p><?php echo Yii::t("summary","index_note_1")?></p>
        <p><?php echo Yii::t("summary","index_note_2")?></p>
    </div>
</div>
<?php
$js="
    $('#search_month').change(function(){
        var search_month = $('#search_month').val();
        $('#search_month_end').val(search_month);
        $('#search_month_end>option').each(function(){
            var month_num = $(this).attr('value');
            if(month_num<search_month){
                $(this).prop('disabled',true).addClass('disabled');
            }else{
                $(this).prop('disabled',false).removeClass('disabled');
            }
        });
    });
";
Yii::app()->clientScript->registerScript('searchMonthFun',$js,CClientScript::POS_READY);
?>