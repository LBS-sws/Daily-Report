<?php
$this->pageTitle=Yii::app()->name . ' - CurlNotes';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'curlNotes-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Curl Notes'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="pull-left">
                <p style="margin: 7px 0px;">未进行：P，完成：C，错误：E。</p>
            </div>
        </div>
    </div>
	<?php
    $search_add_html="";
    $modelName = get_class($model);
    $typeList=CurlNotesList::getInfoTypeList();
    if(!empty($typeList)){
        $typeList = array_merge(array(""=>"-- 全部 --"),$typeList);
        $search_add_html .= TbHtml::dropDownList($modelName.'[info_type]',$model->info_type,$typeList,
            array("class"=>"form-control submitBtn"));
    }

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('curl','CurlNotes List'),
        'model'=>$model,
        'viewhdr'=>'//curlNotes/_listhdr',
        'viewdtl'=>'//curlNotes/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'id',
            'data_content',
            'out_content',
            'message',
            'status_type',
        ),
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
<div class="modal fade" tabindex="-1" role="dialog" id="textModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">内容详情</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <textarea class="form-control" rows="7" id="textInput"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
echo TbHtml::button("",array("submit"=>"#","class"=>"hide"));
	$js = "
	    $('.text-break').click(function(){
	        var text = $(this).children('pre').text();
	        if(typeof text=='object'){
	            text = JSON.stringify(text);
	        }
	        text = text.replace(/\\u(\w{4})/gi, function(match, group1) {
                return String.fromCharCode(parseInt(group1, 16));
            });
            text=text.replaceAll('\\\\', '');
	        $('#textInput').val(text);
	        $('#textModal').modal('show');
	    });
	    
$('.submitBtn').change(function(){
    $('form:first').submit();
});
	";
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
