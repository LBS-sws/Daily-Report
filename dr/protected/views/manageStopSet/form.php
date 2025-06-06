<?php
$this->pageTitle=Yii::app()->name . ' - Customer Type Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'code-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Management Stop Setting'); ?></strong>
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
        <?php echo CHtml::hiddenField('dtltemplate'); ?>
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('manageStopSet/new')));
				echo TbHtml::button('<span class="fa fa-copy"></span> '.Yii::t('misc','Copy'), array(
					'submit'=>Yii::app()->createUrl('manageStopSet/new',array("index"=>$model->id))));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('manageStopSet/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('manageStopSet/save'))); 
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

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'start_date',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'start_date',
						array('id'=>'start_date','readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'set_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-5">
					<?php echo $form->textField($model, 'set_name',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>

            <div class="form-group">
                <div class="col-lg-6 col-lg-offset-3">
                    <div class="box">
                        <div class="box-body table-responsive">
                            <?php
                            $this->widget('ext.layout.TableView2Widget', array(
                                'model'=>$model,
                                'attribute'=>'detail',
                                'viewhdr'=>'//manageStopSet/_formhdr',
                                'viewdtl'=>'//manageStopSet/_formdtl',
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>
<?php
$js = "
$('table').on('change','[id^=\"ManageStopSetForm\"]',function() {
	var n=$(this).attr('id').split('_');
	$('#ManageStopSetForm_'+n[1]+'_'+n[2]+'_uflag').val('Y');
});
";
Yii::app()->clientScript->registerScript('setFlag',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = <<<EOF
$('table').on('click','#btnDelRow', function() {
	$(this).closest('tr').find('[id*=\"_uflag\"]').val('D');
	$(this).closest('tr').hide();
});
EOF;
    Yii::app()->clientScript->registerScript('removeRow',$js,CClientScript::POS_READY);

    $js = <<<EOF
$(document).ready(function(){
	var ct = $('#tblDetail tr').eq(1).html();
	$('#dtltemplate').attr('value',ct);
});

$('#btnAddRow').on('click',function() {
	var r = $('#tblDetail tr').length;
	if (r>0) {
		var nid = '';
		var ct = $('#dtltemplate').val();
		$('#tblDetail tbody:last').append('<tr>'+ct+'</tr>');
		$('#tblDetail tr').eq(-1).find('[id*=\"ManageStopSetForm_\"]').each(function(index) {
			var id = $(this).attr('id');
			var name = $(this).attr('name');

			var oi = 0;
			var ni = r;
			id = id.replace('_'+oi.toString()+'_', '_'+ni.toString()+'_');
			$(this).attr('id',id);
			name = name.replace('['+oi.toString()+']', '['+ni.toString()+']');
			$(this).attr('name',name);

		
			if (id.indexOf('_operator') != -1) $(this).attr('value','');
			if (id.indexOf('_stopRate') != -1) $(this).attr('value','');
			if (id.indexOf('_coefficient') != -1) $(this).attr('value','');
			if (id.indexOf('_id') != -1) $(this).attr('value',0);
		});
		if (nid != '') {
			var topos = $('#'+nid).position().top;
			$('#tbl_detail').scrollTop(topos);
		}
	}
});
EOF;
    Yii::app()->clientScript->registerScript('addRow',$js,CClientScript::POS_READY);

    $js = Script::genDatePicker(array(
        'start_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>
<?php
$js = Script::genDeleteData(Yii::app()->createUrl('manageStopSet/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

?>

<?php $this->endWidget(); ?>
