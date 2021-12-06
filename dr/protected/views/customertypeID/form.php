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
		<strong><?php echo Yii::t('app','Customer Type ID'); ?></strong>
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
            switch ($model->index_num){
                case 1:
                    echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                        'submit'=>Yii::app()->createUrl('customertypeID/index')));
                    break;
                default:
                    echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                        'submit'=>Yii::app()->createUrl('customertypeID/edit',array("index"=>$model->cust_type_id,"type"=>$model->index_num==2?0:1))));
                    break;
            }
		?>
        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('customertypeID/save')));
            ?>
        <?php endif ?>
	</div>
            <?php if ($model->scenario=='edit'): ?>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            </div>
            <?php endif ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'index_num'); ?>

            <?php if ($model->index_num==1): ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'description',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-5">
					<?php echo $form->textField($model, 'description', 
						array('size'=>50,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'rpt_cat',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'rpt_cat', 
						array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'single',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
                    <?php echo $form->dropDownList($model,'single', array('0'=>'非一次性服务','1'=>'一次性服务'),
                        array('disabled'=>$model->scenario=='view')
                    ); ?>
				</div>
			</div>
            <?php else: ?>
                <div class="box">
                    <div class="box-body">
                        <?php echo CustomertypeIDForm::getLineTitleHtml($model->id,$model->index_num); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'cust_type_name',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-5">
                        <?php echo $form->textField($model, 'cust_type_name',
                            array('size'=>50,'maxlength'=>100,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'index_num',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'index_num',
                            array('size'=>50,'maxlength'=>100,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="box">
                <div class="col-sm-10 col-lg-offset-2">
                    <p class="form-control-static text-danger">第二栏的“服务类型”为“非一次性服务”时将在人事系统的老总年度考核统计ID服务总金额</p>
                </div>
                <div class="box-body table-responsive">
                    <?php
                    $this->widget('ext.layout.TableView2Widget', array(
                        'model'=>$model,
                        'attribute'=>'detail',
                        'viewhdr'=>'//customertypeID/_formhdr',
                        'viewdtl'=>'//customertypeID/_formdtl',
                    ));
                    ?>
                </div>
            </div>
        </div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>
<?php
$js = "
$('table').on('change','[id^=\"CustomertypeIDForm\"]',function() {
	var n=$(this).attr('id').split('_');
	$('#CustomertypeIDForm_'+n[1]+'_'+n[2]+'_uflag').val('Y');
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
	if($('#tblDetail tr').eq(-1).find('input:first').val()==''){
		$('#tblDetail tr').eq(-1).children('td:first').html('');
	}
	$('#dtltemplate').attr('value',ct);
});

$('#btnAddRow').on('click',function() {
	var r = $('#tblDetail tr').length;
	if (r>0) {
		var nid = '';
		var ct = $('#dtltemplate').val();
		$('#tblDetail tbody:last').append('<tr>'+ct+'</tr>');
		$('#tblDetail tr').eq(-1).children('td:first').html('');
		$('#tblDetail tr').eq(-1).find('[id*=\"CustomertypeIDForm_\"]').each(function(index) {
			var id = $(this).attr('id');
			var name = $(this).attr('name');

			var oi = 0;
			var ni = r;
			id = id.replace('_'+oi.toString()+'_', '_'+ni.toString()+'_');
			$(this).attr('id',id);
			name = name.replace('['+oi.toString()+']', '['+ni.toString()+']');
			$(this).attr('name',name);

		
			if (id.indexOf('_cust_type_name') != -1) $(this).attr('value','');
			if (id.indexOf('_fraction') != -1) $(this).attr('value','');
			if (id.indexOf('_toplimit') != -1) $(this).attr('value','');
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
        'CustomertypeIDForm__start_dt',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>
<?php
$js = Script::genDeleteData(Yii::app()->createUrl('customertypeID/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

?>

<?php $this->endWidget(); ?>
