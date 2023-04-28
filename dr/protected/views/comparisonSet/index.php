<?php
$this->pageTitle=Yii::app()->name . ' - ComparisonSet';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'comparisonSet-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>
<style>
    #tblData th{ vertical-align: middle;text-align: center;border: 1px solid #f4f4f4}
    #tblData td{ text-align: right;border: 1px solid #f4f4f4}
    .border-box{ border: 1px solid #f4f4f4;}
    .border-top{ text-align: center;font-weight: bold;padding-top: 7px;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Comparison Set'); ?></strong>
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
	<?php
    $className = get_class($model);
    $search_add_html= TbHtml::dropDownList($className.'[comparison_year]',$model->comparison_year,SummarySetList::getSelectYear(),
        array("class"=>"form-control submitBtn"));
    $search_add_html.= TbHtml::dropDownList($className.'[month_type]',$model->month_type,SummarySetList::getSummaryMonthList(),
        array("class"=>"form-control submitBtn"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('code','City List'),
        'model'=>$model,
        'viewhdr'=>'//comparisonSet/_listhdr',
        'viewdtl'=>'//comparisonSet/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'name'
        ),
    ));
	//table-bordered
	?>
</section>
<?php
    echo TbHtml::hiddenField("cover_bool",1,array("id"=>"cover_bool"));
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php $this->renderPartial('./_update',array('model'=>$model)); ?>
<?php
TbHtml::button('test',array('submit'=>Yii::app()->createUrl('test/test')));
$ajaxUrl = Yii::app()->createUrl('comparisonSet/ajaxSave');
$js = "
$('.submitBtn').change(function(){
    $('form:first').submit();
});
function textToNumber(text){
    if(!isNaN(text)){
        return parseFloat(text);
    }else{
        return '';
    }
}
$('.update-row').click(function(){
    var city_code = $(this).data('code');
    var city_name = $(this).children('.city_name').text();
    $('#one_gross').val(textToNumber($(this).children('.one_gross').text()));
    $('#one_net').val(textToNumber($(this).children('.one_net').text()));
    $('#two_gross').val(textToNumber($(this).children('.two_gross').text()));
    $('#two_net').val(textToNumber($(this).children('.two_net').text()));
    $('#three_gross').val(textToNumber($(this).children('.three_gross').text()));
    $('#three_net').val(textToNumber($(this).children('.three_net').text()));
    $('#btnSave').data('city',city_code);
    $('#comparisonModal span[data-id=\"city_name\"]').text(city_name);
    $('#comparisonModal').modal('show');
});
function saveData(){
    var formData = {};
    formData['comparison_year'] = '{$model->comparison_year}';
    formData['month_type'] = '{$model->month_type}';
    formData['city'] = $('#btnSave').data('city');
    formData['one_gross'] = $('#one_gross').val();
    formData['one_net'] = $('#one_net').val();
    formData['two_gross'] = $('#two_gross').val();
    formData['two_net'] = $('#two_net').val();
    formData['three_gross'] = $('#three_gross').val();
    formData['three_net'] = $('#three_net').val();
    formData['cover_bool'] = $('#cover_bool').val();
    $.ajax({
        type:'post',
        url:'{$ajaxUrl}',
        data:formData,
        dataType:'json',
        success:function(data){
            if(data.status==1){
                var tr = $('.update-row[data-code=\"'+formData['city']+'\"]');
                tr.children('.one_gross').text(formData['one_gross']);
                tr.children('.one_net').text(formData['one_net']);
                tr.children('.two_gross').text(formData['two_gross']);
                tr.children('.two_net').text(formData['two_net']);
                tr.children('.three_gross').text(formData['three_gross']);
                tr.children('.three_net').text(formData['three_net']);
                $('#comparisonModal').modal('hide');
                $('#configModal').modal('hide');
            }else{
                $('#errorModal .modal-body').eq(0).html(data['message']);
                $('#configModal').modal('hide');
                $('#errorModal').modal('show');
            }
        }
    });
}
$('#coverNo').click(function(){
    $('#cover_bool').val(0);
    saveData();
});
$('#coverYes').click(function(){
    $('#cover_bool').val(1);
    saveData();
});
$('#btnSave').click(function(){
    if($('#reminderTitle').text()!=''){
        $('#configModal').modal('show');
    }else{
        $('#cover_bool').val(0);
        saveData();
    }
});
";
if(Yii::app()->user->validRWFunction('G06')){
    Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
}
?>