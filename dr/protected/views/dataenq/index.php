<?php
$this->pageTitle=Yii::app()->name . ' - Data Analysis';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'report-enq',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
//'layout'=>TbHtml::FORM_LAYOUT_INLINE,
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Data Analysis'); ?></strong>
	</h1>
</section>


<section class="content">
	<div class="box"><div class="box-body">
		<div class="form-group">
			<?php echo $form->labelEx($model,'data_type',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-2">
				<?php 
					$list = $model->getDataTypeList();
					echo $form->dropDownList($model, 'data_type', $list); 
				?>
			</div>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model,'year_from',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-2">
				<?php 
					$item = array();
					for ($i=2017;$i<=2027;$i++) {$item[$i] = $i; }
					echo $form->dropDownList($model, 'year_from', $item); 
				?>
			</div>

			<?php echo $form->labelEx($model,'month_from',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-3">
				<?php 
					$item = array();
					for ($i=1;$i<=12;$i++) {$item[$i] = $i; }
					echo $form->dropDownList($model, 'month_from', $item); 
				?>
			</div>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model,'year_to',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-2">
				<?php 
					$item = array();
					for ($i=2017;$i<=2027;$i++) {$item[$i] = $i; }
					echo $form->dropDownList($model, 'year_to', $item); 
				?>
			</div>

			<?php echo $form->labelEx($model,'month_to',array('class'=>"col-sm-2 control-label")); ?>
			<div class="col-sm-2">
				<?php 
					$item = array();
					for ($i=1;$i<=12;$i++) {$item[$i] = $i; }
					echo $form->dropDownList($model, 'month_to', $item); 
				?>
			</div>
		</div>

		<div class="form-group">
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
	if ($model->show!=0) {
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('report','Result List'),
			'model'=>$model,
				'viewhdr'=>'//dataenq/_listhdr',
				'viewdtl'=>'//dataenq/_listdtl',
				'hasSearchBar'=>false,
				'hasNavBar'=>false,
				'hasPageBar'=>false,
		));
	}
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
	echo $form->hiddenField($model,'show');
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

$url = Yii::app()->createUrl('dataenq/index');
$js = <<<EOF
$('#btnSubmit').on('click', function() {
	Loading.show();
	jQuery.yii.submitForm(this,'$url',{});
});
EOF;
Yii::app()->clientScript->registerScript('searchRec',$js,CClientScript::POS_READY);

//$js = Script::genTableRowClick();
//Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

