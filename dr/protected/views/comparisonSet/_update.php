<div class="form-horizontal">
    <div class="modal fade " tabindex="-1" role="dialog" id="comparisonModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php
                        $monthStr = SummarySetList::getSummaryMonthList($model->month_type,true,$model->comparison_year);
                        echo Yii::t("summary","Update Annual Target")."<small>({$monthStr})</small>";
                        ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="border-box">
                        <div class="border-top">
                            <p>(<span data-id="city_name"></span>)
                                <?php echo $model->comparison_year.Yii::t('summary',"Annual target ").Yii::t('summary',"(upside case)");?>
                            </p>
                        </div>
                        <div class="form-group">
                            <?php echo TbHtml::label(Yii::t('summary',"None Gross"),"",array("class"=>"col-lg-4 control-label"));?>
                            <div class="col-lg-4">
                                <?php echo TbHtml::numberField("","",array("id"=>"one_gross"));?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo TbHtml::label(Yii::t('summary',"None Net"),"",array("class"=>"col-lg-4 control-label"));?>
                            <div class="col-lg-4">
                                <?php echo TbHtml::numberField("","",array("id"=>"one_net"));?>
                            </div>
                        </div>
                    </div>
                    <div class="border-box">
                        <div class="border-top">
                            <p>(<span data-id="city_name"></span>)
                                <?php echo $model->comparison_year.Yii::t('summary',"Annual target ").Yii::t('summary',"(base case)");?>
                            </p>
                        </div>
                        <div class="form-group">
                            <?php echo TbHtml::label(Yii::t('summary',"None Gross"),"",array("class"=>"col-lg-4 control-label"));?>
                            <div class="col-lg-4">
                                <?php echo TbHtml::numberField("","",array("id"=>"two_gross"));?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo TbHtml::label(Yii::t('summary',"None Net"),"",array("class"=>"col-lg-4 control-label"));?>
                            <div class="col-lg-4">
                                <?php echo TbHtml::numberField("","",array("id"=>"two_net"));?>
                            </div>
                        </div>
                    </div>
                    <div class="border-box">
                        <div class="border-top">
                            <p>(<span data-id="city_name"></span>)
                                <?php echo $model->comparison_year.Yii::t('summary',"Annual target ").Yii::t('summary',"(minimum case)");?>
                            </p>
                        </div>
                        <div class="form-group">
                            <?php echo TbHtml::label(Yii::t('summary',"None Gross"),"",array("class"=>"col-lg-4 control-label"));?>
                            <div class="col-lg-4">
                                <?php echo TbHtml::numberField("","",array("id"=>"three_gross"));?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo TbHtml::label(Yii::t('summary',"None Net"),"",array("class"=>"col-lg-4 control-label"));?>
                            <div class="col-lg-4">
                                <?php echo TbHtml::numberField("","",array("id"=>"three_net"));?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php echo TbHtml::button(Yii::t("dialog","Close"),array('data-dismiss'=>'modal'));?>
                    <?php echo TbHtml::button(Yii::t("dialog","Save"),array('class'=>'btn btn-primary','id'=>'btnSave'));?>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- bs-example-modal-lg/.modal -->
</div>

<?php

$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>"errorModal",
    'header'=>Yii::t('dialog',"Validation Message"),
    'content'=>"",
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>

<?php

$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>"configModal",
    'header'=>Yii::t('summary',"Reminder"),
    'content'=>SummarySetList::getReminderTitle($model->comparison_year,$model->month_type),
    'footer'=>array(
        TbHtml::button(Yii::t('summary','No'), array('color'=>TbHtml::BUTTON_COLOR_DEFAULT,'id'=>'coverNo')),
        TbHtml::button(Yii::t('summary','Yes'), array('color'=>TbHtml::BUTTON_COLOR_PRIMARY,'id'=>'coverYes')),
    ),
    'show'=>false,
));
?>