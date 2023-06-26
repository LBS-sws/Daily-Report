<?php
$this->pageTitle=Yii::app()->name . ' - ServiceCount Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ServiceCount-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
        <strong><?php echo Yii::t('app','Customer Service Count'); ?></strong>
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
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('serviceCount/index')));
		?>
	</div>
	</div></div>

    <div class="box box-info">
        <div class="box-body">
            <div class="form-group">
                <?php echo $form->labelEx($model,'status',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'status',ServiceCountForm::getStatusList(),
                        array('class'=>'form-control','readonly'=>true,));
                    ?>
                </div>
                <?php echo $form->labelEx($model,'cust_type',array('class'=>"col-sm-1 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'cust_type',ServiceCountForm::getServiceTypeList(),
                        array('class'=>'form-control','readonly'=>true));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'search_year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'search_year',ServiceCountForm::getYearList(),
                        array('class'=>'form-control','readonly'=>true,));
                    ?>
                </div>
                <?php echo $form->labelEx($model,'city_allow',array('class'=>"col-sm-1 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'city_allow',ServiceCountForm::getCityList(),
                        array('class'=>'form-control','readonly'=>true));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'company_name',
                        array('class'=>'form-control','readonly'=>true,));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-5 col-sm-offset-2">
                    <?php
                    echo $model->printHtml();
                    ?>
                </div>
            </div>
        </div>
    </div>

</section>
<!--詳情彈窗-->
<div class="modal fade" tabindex="-1" role="dialog" id="detailDialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <p>加载中....</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$ajaxUrl = Yii::app()->createUrl('serviceCount/ajaxDetail');
$js = "
$('.td_detail').on('click',function(){
    var tdOne = $(this).parent('tr').children('td').eq(0).text();
    var titleStatus=$('#ServiceCountForm_status>option:selected').text();
    $('#detailDialog').find('.modal-title').text(titleStatus+' - '+tdOne);
    $('#detailDialog').find('.modal-body').html('<p>加载中....</p>');
    $('#detailDialog').modal('show');
    $.ajax({
        type: 'GET',
        url: '{$ajaxUrl}',
        data: {
            'city':$(this).data('city'),
            'status':$('#ServiceCountForm_status').val(),
            'search_year':$('#ServiceCountForm_search_year').val(),
            'city_allow':$('#ServiceCountForm_city_allow').val(),
            'company_name':$('#ServiceCountForm_company_name').val(),
            'cust_type':$('#ServiceCountForm_cust_type').val()
        },
        dataType: 'json',
        success: function(data) {
            $('#detailDialog').find('.modal-body').html(data['html']);
        },
        error: function(data) { // if error occured
            alert('Error occured.please try again');
        }
    });
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


