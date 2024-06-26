<?php
$this->pageTitle=Yii::app()->name . ' - Supplier Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'supplier-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('supplier','Supplier Form'); ?></strong>
    </h1>
    <!--
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Layout</a></li>
            <li class="active">Top Navigation</li>
        </ol>
    -->
</section>

<section class="content">
    <div class="box"><div class="box-body">
            <div class="btn-group" role="group">
                <?php
                if ($model->scenario!='new' && $model->scenario!='view') {
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
                        'submit'=>Yii::app()->createUrl('supplier/new')));
                }
                ?>
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('supplier/index')));
                ?>
                <?php if ($model->scenario!='view'): ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                        'submit'=>Yii::app()->createUrl('supplier/save')));
                    ?>
                <?php endif ?>
                <?php if ($model->scenario=='edit'): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                    ?>
                <?php endif ?>
            </div>
        </div></div>

    <div class="btn-group">
        <button id="hide" type="button" class="btn btn-default" style="width: 555px;font-size: 16px"><?php echo Yii::t('supplier','Company Details');?></button>
        <button id="show" type="button" class="btn btn-default" style="width: 555px;font-size: 16px""><?php echo Yii::t('supplier','Transaction Record');?></button>
    </div>

    <div class="box box-info" id="p">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'code',
                        array('size'=>8,'maxlength'=>20,'readonly'=>($model->scenario!='new'),));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textField($model, 'name',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'full_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textField($model, 'full_name',
                        array('size'=>40,'maxlength'=>500,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'tax_reg_no',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'tax_reg_no',
                        array('maxlength'=>100,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'cont_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textField($model, 'cont_name',
                        array('size'=>40,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'cont_phone',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'cont_phone',
                        array('size'=>15,'maxlength'=>30,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'address',
                        array('rows'=>2,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'bank',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textField($model, 'bank',
                        array('maxlength'=>255,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'acct_no',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textField($model, 'acct_no',
                        array('maxlength'=>100,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>


            <legend><?php echo Yii::t("supplier","JD System Curl");?></legend>
            <?php
            $html = "";
            $className = get_class($model);
            foreach (SupplierForm::$jd_set_list as $num=>$item){
                $field_value = key_exists($item["field_id"],$model->jd_set)?$model->jd_set[$item["field_id"]]:null;
                if($num%2==0){
                    $html.='<div class="form-group">';
                }
                $html.=TbHtml::label(Yii::t("supplier",$item["field_name"]),'',array('class'=>"col-sm-2 control-label"));
                $html.='<div class="col-lg-3">';
                $html.=TbHtml::textField("{$className}[jd_set][{$item["field_id"]}]",$field_value,array('readonly'=>($model->scenario=='view')));
                $html.="</div>";
                if($num%2==1){
                    $html.='</div>';
                }
            }
            if(count(SupplierForm::$jd_set_list)%2==0){
                $html.='</div>';
            }
            echo $html;
            ?>
        </div>
    </div>

    <div  id="s" class="box" style="display: none">
        <?php
        $search = array(
            'req_dt',
            'ref_no',
            'trans_type_desc',
            'bank',
            'amount',
            'item_desc',
        );

        $this->widget('ext.layout.ListPageWidget', array(
            'title'=>Yii::t('supplier','Supplier List'),
            'model'=>$model,
            'viewhdr'=>'//supplier/_savehdr',
            'viewdtl'=>'//supplier/_savedtl',
            'gridsize'=>'24',
            'height'=>'600',
            'search'=>$search,
            'searchlinkparam'=>array('index'=>$model->id),
        ));
        ?>

    </div>


</section>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>

<?php
$js = <<<EOF
$(document).ready(function(){
  $("#hide").click(function(){
 document.getElementById('p').style.display = 'block';
 document.getElementById('s').style.display = 'none';
  });
  $("#show").click(function(){
 document.getElementById('p').style.display = 'none';
 document.getElementById('s').style.display = 'block';
  });
});

EOF;
?>
<?php $this->renderPartial('//site/removedialog'); ?>

<?php
Yii::app()->clientScript->registerScript('calculate',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('supplier/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

