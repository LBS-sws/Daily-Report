<?php
$this->pageTitle=Yii::app()->name . ' - Customer Enquiry';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'customer-enq',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
//'layout'=>TbHtml::FORM_LAYOUT_INLINE,
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    #CustomerEnqList_noOfItem{ width: 30%;display: inline-block;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','LBS Customer Enquiry'); ?></strong>
	</h1>
</section>


<section class="content">
	<div class="box"><div class="box-body">
		<div class="form-group">
			<?php echo $form->labelEx($model,'company_code',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-2">
				<?php echo $form->textField($model, 'company_code', array('maxlength'=>20)); ?>
			</div>

			<?php echo $form->labelEx($model,'company_name',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-5">
				<?php echo $form->textField($model, 'company_name', array('maxlength'=>250)); ?>
			</div>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model,'company_status',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-2">
				<?php 
					$list = array(''=>Yii::t('customer','All'), 'A'=>Yii::t('customer','Active'), 'T'=>Yii::t('customer','Terminated'), 'U'=>Yii::t('customer','Unknown'));
					echo $form->dropDownList($model, 'company_status', $list); 
				?>
			</div>

			<?php echo $form->labelEx($model,'city_list',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-5">
				<?php
						$list = General::getCityList();
						echo $form->dropDownList($model, 'city_list', $list,
								array('class'=>'select2','multiple'=>'multiple')
							); 
				?>
			</div>
		</div>

		<div class="form-group">
            <?php echo $form->labelEx($model,'chain_num',array('class'=>"col-sm-2 control-label")); ?>
            <div class="col-sm-2">
                <?php echo $form->numberField($model, 'chain_num', array('min'=>2)); ?>
            </div>
            <div class="col-sm-7">
                <p class="form-control-static text-warning"><?php echo Yii::t('customer','note 1:');?></p>
            </div>
		</div>
		<div class="btn-group" role="group">
			<?php 
				echo TbHtml::button('dummyButton', array('style'=>'display:none','disabled'=>true,'submit'=>'#',));
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Search'), array(
						'id'=>'btnSubmit', 
					)); 
			?>
		</div>
	</div></div>

	<?php 
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('customer','Customer List'),
			'model'=>$model,
				'viewhdr'=>'//customerenq/_listhdr',
				'viewdtl'=>'//customerenq/_listdtl',
				'hasSearchBar'=>false,
				'item_bool'=>true,
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
switch(Yii::app()->language) {
	case 'zh_cn': $lang = 'zh-CN'; break;
	case 'zh_tw': $lang = 'zh-TW'; break;
	default: $lang = Yii::app()->language;
}
//$disabled = (!$model->isReadOnly()) ? 'false' : 'true';
	$js = <<<EOF
$('#CustomerEnqList_city_list').select2({
	tags: false,
	multiple: true,
	maximumInputLength: 0,
	maximumSelectionLength: 200,
	allowClear: true,
	language: '$lang',
	disabled: false
});

$('#CustomerEnqList_city_list').on('select2:opening select2:closing', function( event ) {
    var searchfield = $(this).parent().find('.select2-search__field');
    searchfield.prop('disabled', true);
});
EOF;
Yii::app()->clientScript->registerScript('select2',$js,CClientScript::POS_READY);

$js = <<<EOF
function showdetail(id) {
	var icon = $('#btn_'+id).attr('class');
	if (icon.indexOf('plus') >= 0) {
		$('.detail_'+id).show();
		$('#btn_'+id).attr('class', 'fa fa-minus-square');
	} else {
		$('.detail_'+id).hide();
		$('#btn_'+id).attr('class', 'fa fa-plus-square');
	}
}
EOF;
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_HEAD);

$url = Yii::app()->createUrl('customerenq/index', array('pageNum'=>1));
$js = <<<EOF
$('#btnSubmit').on('click', function() {
	Loading.show();
	jQuery.yii.submitForm(this,'$url',{});
});
EOF;
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);

//$js = Script::genTableRowClick();
//Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

