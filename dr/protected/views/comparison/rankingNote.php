<style>
    .ranking-note-body>dl>dd{ padding-left: 15px;}
    .ranking-note-span{ position: relative;display: inline-block;padding-left: 25px;}
    .ranking-note-span>.fa{ position: absolute; width: 0px;height: 0px;overflow: visible;left: 6px;top: 3px;}
    .ranking-note{
        position: fixed;
        background: #fff;
        top:17%;
        z-index: 10;
        right: 15px;
        width: 35%;
        border-radius: 3px;
        border: 1px solid #d2d6de;
        box-shadow: 0 2px 7px rgba(0,0,0,0.1);
    }
    .ranking-note-body{
        float: left;
        width: 100%;
        padding: 5px 10px 0px 25px;
        max-height: 400px;
        overflow-y:auto;
    }
    .note-click{ position: absolute;top:0px;left:0px;display:table;width: 20px;height:100%;text-align: center;}

    .note-click:before{ content: " ";display: table-cell;vertical-align: middle;width: 0px;}
    .middle-span{ display: table-cell;vertical-align: middle;background: #f4f4f4;border-right: 1px solid #d2d6de}

    .ranking-note.active{ width: 20px;height:50px;overflow: hidden;}
    .note-click.active>.fa-angle-double-right:before{ content: "\f100"}
</style>
<div class="ranking-note" style="">
    <a class="note-click" href="javascript:void(0);">
        <span class="middle-span fa fa-angle-double-right"></span>
    </a>
    <div class="ranking-note-body">
        <div class="col-lg-12">
            <div class="row">
                <?php if (CountSearch::getSystem()===1): ?>
                    <p><?php echo Yii::t("summary","comparison_remark_tw_1");?></p>
                    <p><?php echo Yii::t("summary","comparison_remark_tw_2");?></p>
                    <p><?php echo Yii::t("summary","comparison_remark_tw_3");?></p>
                    <p><?php echo Yii::t("summary","comparison_remark_tw_4");?></p>
                <?php else:?>
                    <p>
                        <span><?php echo Yii::t("summary","comparison_remark_1");?></span><br>
                        <span class="ranking-note-span"><span class="fa fa-circle"></span><?php echo Yii::t("summary","comparison_remark_1_1");?></span><br>
                        <span class="ranking-note-span"><span class="fa fa-circle"></span><?php echo Yii::t("summary","comparison_remark_1_2");?></span>
                    </p>
                    <p><?php echo Yii::t("summary","comparison_remark_2");?></p>
                    <p><?php echo Yii::t("summary","comparison_remark_3");?></p>
                    <p><?php echo Yii::t("summary","comparison_remark_4");?></p>
                <?php endif ?>
                <p><?php echo Yii::t("summary","comparison_remark_5");?></p>
                <p>
                    <span><?php echo Yii::t("summary","comparison_remark_6");?></span><br>
                    <span class="ranking-note-span"><span class="fa fa-circle"></span><?php echo Yii::t("summary","comparison_remark_6_1");?></span>
                </p>
                <p><?php echo Yii::t("summary","comparison_remark_7");?></p>
                <p><?php echo Yii::t("summary","comparison_remark_8");?></p>
                <p><?php echo Yii::t("summary","comparison_remark_9_{$model->search_type}");?></p>
                <p><?php echo Yii::t("summary","comparison_remark_10_{$model->search_type}");?></p>
                <p><?php echo Yii::t("summary","comparison_remark_11");?></p>
                <p><?php echo Yii::t("summary","comparison_remark_12");?></p>
                <p><?php echo Yii::t("summary","comparison_remark_13_new");?></p>
                <p><?php echo Yii::t("summary","comparison_remark_14_new");?></p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(".note-click").click(function () {
            if($(this).hasClass("active")){
                $(this).removeClass("active");
                $(this).parent('.ranking-note').removeClass("active");
                localStorage.setItem("rankingNote",0);
            }else{
                $(this).addClass("active");
                $(this).parent('.ranking-note').addClass("active");
                localStorage.setItem("rankingNote",1);
            }
        });
        if(localStorage.getItem("rankingNote")==1){
            $(".note-click").trigger("click");
        }
    })
</script>