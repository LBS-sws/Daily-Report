<?php
$this->pageTitle=Yii::app()->name . ' - Supplier';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'supplier-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('supplier','Supplier'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>


<section class="content" >
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if (Yii::app()->user->validRWFunction('A10'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Record'), array(
					'submit'=>Yii::app()->createUrl('supplier/new'), 
				)); 
		?>
	</div>
	</div>

    </div>
<!--    <div class="btn-group">-->
<!--        <button id="hide" type="button" class="btn btn-default" style="width: 555px;font-size: 16px">基本资料</button>-->
<!--        <button id="show" type="button" class="btn btn-default" style="width: 555px;font-size: 16px"">付款记录</button>-->
<!--    </div>-->

<div  id="p">
	<?php 
		$search = array(
						'code',
						'name',
						'cont_name',
						'cont_phone',
					);
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('supplier','Supplier List'),
			'model'=>$model,
				'viewhdr'=>'//supplier/_listhdr',
				'viewdtl'=>'//supplier/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>$search,
		));
	?>

</div>

</section>

<!--<div class="content" id="s" style="z-index: 20;margin-top:-137px;display: none">-->
<!--    --><?php
//    $search = array(
//        'code',
//        'name',
//        'cont_name',
//        'cont_phone',
//    );
//    $this->widget('ext.layout.ListPageWidget', array(
//        'title'=>Yii::t('supplier','Supplier Pay'),
//        'model'=>$models,
//        'viewhdr'=>'//supplier/_payhdr',
//        'viewdtl'=>'//supplier/_paydtl',
//        'gridsize'=>'24',
//        'height'=>'600',
//        'search'=>$search,
//    ));
//    ?>
<!--</div>-->

<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>
<?php
//$js = <<<EOF
//$(document).ready(function(){
//  $("#hide").click(function(){
// document.getElementById('p').style.display = 'block';
// document.getElementById('s').style.display = 'none';
//  });
//  $("#show").click(function(){
//
// document.getElementById('p').style.display = 'none';
// document.getElementById('s').style.display = 'block';
//  });
//});
//
//EOF;
//?>
<?php

	$js = Script::genTableRowClick();

	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

