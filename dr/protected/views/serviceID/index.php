<?php
$this->pageTitle=Yii::app()->name . ' - Service';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'serviceID-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Customer Service ID'); ?></strong>
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
			if (Yii::app()->user->validRWFunction('A11'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','New Record'), array(
					'name'=>'btnAdd','id'=>'btnAdd','data-toggle'=>'modal','data-target'=>'#addrecdialog',)
				); 
		?>
	</div>
	</div></div>
	<?php 
		$search = array(
						'service_no',
						'company_name',
						'type_desc',
						'nature_desc',
						'service',
						'cont_info',
						'status',
					);
		if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('service','Service List'),
			'model'=>$model,
			'viewhdr'=>'//serviceID/_listhdr',
			'viewdtl'=>'//serviceID/_listdtl',
			'search'=>$search,
			'hasDateButton'=>true,
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>

<?php

$js = "
$('.clickable-row').click(function() {
	window.document.location = $(this).data('href');
});
$('.btnChangeAdd').on('click',function(){
    var url = $(this).data('href');
    var city = $('#dialog_city').val();
    window.document.location = url+'&city='+city;
});
";
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);

?>

<?php $this->endWidget(); ?>

<?php
echo TbHtml::button("",array("submit"=>"","class"=>"hide"));
$buttons = array(
    TbHtml::button(Yii::t('service','New Service'),
        array(
            'name'=>'btnNew',
            'id'=>'btnNew',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"N")),
            'data-dismiss'=>'modal',
        )),
    TbHtml::button(Yii::t('service','Renew Service'),
        array(
            'name'=>'btnRenew',
            'id'=>'btnRenew',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"C")),
            'data-dismiss'=>'modal',
        )),
    TbHtml::button(Yii::t('service','Amend Service'),
        array(
            'name'=>'btnAmend',
            'id'=>'btnAmend',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"A")),
            'data-dismiss'=>'modal',
        )),
    TbHtml::button(Yii::t('service','Suspend Service'),
        array(
            'name'=>'btnSuspend',
            'id'=>'btnSuspend',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"S")),
            'data-dismiss'=>'modal',
        )),
    TbHtml::button(Yii::t('service','Resume Service'),
        array(
            'name'=>'btnResume',
            'id'=>'btnResume',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"R")),
            'data-dismiss'=>'modal',
        )),
    TbHtml::button(Yii::t('service','Terminate Service'),
        array(
            'name'=>'btnTerminate',
            'id'=>'btnTerminate',
            'class'=>'btn btn-block btnChangeAdd',
            'data-href'=>Yii::app()->createUrl('serviceID/new',array("type"=>"T")),
            'data-dismiss'=>'modal',
        )),
);

$content = '<div class="form-horizontal">';
$content.= '<div class="form-group">';
$content.= Tbhtml::label(Yii::t('misc','City'),'',array('class'=>"col-lg-4 control-label"));
$content.= '<div class="col-lg-4">';
$content.= Tbhtml::dropDownList("dialog_city", Yii::app()->user->city(),General::getCityListWithCityAllow(Yii::app()->user->city_allow()),
    array('id'=>"dialog_city",'empty'=>'')
);
$content.= '</div></div>';
$content.= '<div class="form-group">';
foreach ($buttons as $button) {
    $content .= "<div class=\"col-sm-10 col-sm-offset-1\">$button</div>";
}
$content.= '</div></div>';
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'addrecdialog',
    'header'=>Yii::t('service','Add Record'),
    'content'=>$content,
//					'footer'=>array(
//						TbHtml::button(Yii::t('dialog','OK'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
//					),
    'show'=>false,
));
?>
