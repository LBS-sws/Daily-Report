<?php
$this->pageTitle=Yii::app()->name . ' - Feedback Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'feedback-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('feedback','Feedback Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('feedback/index'))); 
		?>
<?php if ($model->scenario!='view'&&$model->city==Yii::app()->user->city()): ?>
			<?php echo TbHtml::button('<span class="fa fa-hand-o-up"></span> '.Yii::t('misc','Temp'), array(
				'submit'=>Yii::app()->createUrl('feedback/save',array('type'=>'temp'))));
			?>
            <?php echo TbHtml::button('<span class="fa fa-paper-plane-o"></span> '.Yii::t('misc','Send'), array(
                'submit'=>Yii::app()->createUrl('feedback/save',array('type'=>'send'))));
            ?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
			<?php echo $form->hiddenField($model, 'rpt_id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php
                    echo TbHtml::textField("city",General::getCityName($model->city),
                        array('size'=>15,'maxlength'=>50,'readonly'=>true)
                    );
                    ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'request_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'request_dt',
						array('size'=>15,'maxlength'=>50,'readonly'=>true)
					); ?>
				</div>
				<div class="col-sm-2">
				</div>
				<?php echo $form->labelEx($model,'status_desc',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'status_desc',
						array('size'=>10,'maxlength'=>10,'readonly'=>true)
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'feedback_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'feedback_dt', 
						array('size'=>15,'maxlength'=>50,'readonly'=>true)
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'to',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php echo $form->textArea($model, 'to', 
						array('rows'=>4,'cols'=>30,'maxlength'=>200,'readonly'=>true)
					); ?>
				</div>
				<?php echo $form->labelEx($model,'cc',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-4">
					<?php 
						echo $form->listbox($model, 'cc', General::getEmailListboxData(), 
							array('size'=>4,'multiple'=>true,'disabled'=>($model->scenario=='view'))
						); 
					?>
				</div>
			</div>

<?php
	$cnt = 0;
	$fldnames = $model->attributeLabels();
	foreach ($model->cats as $cat=>$desc) {
		$cnt++;
		$cat_field = 'cat_'.$cnt;
		$fb_field = 'feedback_'.$cnt;
		echo '<div class="form-group">';
		echo '<div class="col-sm-2 col-md-push-1">';
		echo $form->checkBox($model,$cat_field, array('label'=>$fldnames[$cat_field],'value'=>'Y','uncheckValue'=>'N','disabled'=>($model->scenario=='view')));
		echo '</div>';
		echo '<div class="col-sm-7">';
		echo $form->textArea($model, $fb_field, 
				array('rows'=>5,'cols'=>80,'maxlength'=>5000,'readonly'=>($model->scenario=='view' || $model->$cat_field!='Y'))
			);		
		echo '</div>';
		if(!in_array($fb_field,array("feedback_7"))){ //其它没有详情
            echo '<div class="col-sm-2">';
            echo TbHtml::link(Yii::t("feedback","click detail"),"javascript:void(0);",array("class"=>"link_detail","data-type"=>$fb_field,"data-title"=>$fldnames[$cat_field]));
            echo '</div>';
        }
		echo '</div>';
	}
?>
		</div>
	</div>
</section>

<!--詳情彈窗-->
<div class="modal fade" tabindex="-1" role="dialog" id="detailDialog">
    <div class="modal-dialog" role="document" style="width: 80%;">
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
$js = "";
$cnt = 0;
foreach ($model->cats as $cat=>$desc) {
	$cnt++;
	$cfield = 'FeedbackForm_cat_'.$cnt;
	$ffield = 'FeedbackForm_feedback_'.$cnt;
	$js .= "
$('#".$cfield."').on('change',function() {
	if ($(this).is(':checked')) {
		$('#".$ffield."').removeAttr('readonly');
		$('#".$ffield."').removeClass('readonly');
	} else {
		$('#".$ffield."').prop('readonly',true);
		$('#".$ffield."').addClass('readonly');
	}
});
";
}
Yii::app()->clientScript->registerScript('feedbackReadonly',$js,CClientScript::POS_READY);

$ajaxUrl = Yii::app()->createUrl('feedback/ajaxDetail');
$js.= "
$('.link_detail').on('click',function(){
    var dateStr = $('#FeedbackForm_request_dt').val();
    $('#detailDialog').find('.modal-title').text($(this).data('title')+' ('+dateStr+')');
    $('#detailDialog').find('.modal-body').html('<p>加载中....</p>');
    $('#detailDialog').modal('show');
    $.ajax({
        type: 'GET',
        url: '{$ajaxUrl}',
        data: {
            'type':$(this).data('type'),
            'city':'{$model->city}',
            'request_dt':'{$model->request_dt}'
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

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

