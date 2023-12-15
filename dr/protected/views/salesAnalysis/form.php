<?php
$this->pageTitle=Yii::app()->name . ' - SalesAnalysis Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'SalesAnalysis-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .click-th,.click-tr{ cursor: pointer;}
    .click-tr>.fa:before{ content: "\f062";}
    .click-tr.show-tr>.fa:before{ content: "\f063";}
    .table-fixed{ table-layout: fixed;}
    .form-group{ margin-bottom: 0px;}
    .table-fixed>thead>tr>th,.table-fixed>tfoot>tr>td,.table-fixed>tbody>tr>td{ text-align: center;vertical-align: middle;font-size: 12px;border-color: #333;}
    .table-fixed>tfoot>tr>td,.table-fixed>tbody>tr>td{ text-align: right;}
    .table-fixed>thead>tr>th.header-width{ height: 0px;padding: 0px;overflow: hidden;border-width: 0px;line-height: 0px;}

    tr.searchTr{ background:#b8e9fb;}
</style>

<section class="content-header">
	<h1>
        <strong><?php echo Yii::t('app','Sales Analysis'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('salesAnalysis/index')));
		?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('summary','Download Not Sales'), array(
                    'submit'=>Yii::app()->createUrl('salesAnalysis/downExcelNot')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('dialog','Download'), array(
                    'submit'=>Yii::app()->createUrl('salesAnalysis/downExcel')));
                ?>
            </div>
	</div></div>

    <div class="box">
        <div id="yw0" class="tabbable">
            <div class="box-info" >
                <div class="box-body" >
                    <div class="col-lg-6" style="padding-bottom: 15px;">
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'search_date',array('class'=>"col-sm-4 control-label")); ?>
                            <div class="col-sm-5">
                                <?php echo $form->textField($model, 'search_date',
                                    array('readonly'=>true,'prepend'=>"<span class='fa fa-calendar'></span>")
                                ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" style="padding-bottom: 15px;">
                        <div class="form-group">
                            <?php
                            echo TbHtml::label("页内搜索",'',array('class'=>"col-sm-4 control-label"));
                            ?>
                            <div class="col-sm-5">
                                <?php
                                echo  TbHtml::dropDownList("searchEx",'',array(""=>"-- 全部 --"),array('id'=>'searchEx'));
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $contentHead='<div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">{:head:}<small>('.date("Y/m/01/",strtotime($model->end_date)) ." ~ ".$model->end_date.')</small>
                                </h3>
                            </div>
                            <!-- Table -->
                            <div class="table-responsive">';

                    $contentEnd='</div></div></div>';
                    $tabs =array();
                    //在职销售人员产能
                    $contentTable = str_replace("{:head:}",Yii::t("summary","Capacity Staff"),$contentHead);
                    $contentTable.=$model->salesAnalysisHtml();
                    $contentTable.=$contentEnd;
                    $contentTable.=TbHtml::hiddenField("excel[one]",$model->downJsonText);
                    $tabs[] = array(
                        'label'=>Yii::t("summary","Capacity Staff"),
                        'content'=>$contentTable,
                        'active'=>true,
                    );
                    //在职销售产能统计
                    $areaModel = new SalesAnalysisAreaForm();
                    $areaModel->search_date = $model->search_date;
                    $areaModel->setAttrAll($model);
                    $areaModel->data = $model->twoDate;
                    $contentTable = str_replace("{:head:}",Yii::t("summary","Capacity Area"),$contentHead);
                    $contentTable.=$areaModel->salesAnalysisHtml();
                    $contentTable.=$contentEnd;
                    $contentTable.=TbHtml::hiddenField("excel[two]",$areaModel->downJsonText);
                    $tabs[] = array(
                        'label'=>Yii::t("summary","Capacity Area"),
                        'content'=>$contentTable,
                        'active'=>false,
                    );
                    //在职销售达标统计
                    $cityModel = new SalesAnalysisCityForm();
                    $cityModel->search_date = $model->search_date;
                    $cityModel->setAttrAll($model);
                    $cityModel->data = $model->threeDate;
                    $contentTable = str_replace("{:head:}",Yii::t("summary","Capacity City"),$contentHead);
                    $contentTable.=$cityModel->salesAnalysisHtml();
                    $contentTable.=$contentEnd;
                    $contentTable.=TbHtml::hiddenField("excel[three]",$cityModel->downJsonText);
                    $tabs[] = array(
                        'label'=>Yii::t("summary","Capacity City"),
                        'content'=>$contentTable,
                        'active'=>false,
                    );
                    //在职销售达标统计
                    $cityModel = new SalesAnalysisFTEForm();
                    $cityModel->search_date = $model->search_date;
                    $cityModel->setAttrAll($model);
                    $cityModel->data = $model->fourDate;
                    $contentTable = str_replace("{:head:}",Yii::t("summary","Capacity FTE"),$contentHead);
                    $contentTable.=$cityModel->salesAnalysisHtml();
                    $contentTable.=$contentEnd;
                    $contentTable.=TbHtml::hiddenField("excel[four]",$cityModel->downJsonText);
                    $tabs[] = array(
                        'label'=>Yii::t("summary","Capacity FTE"),
                        'content'=>$contentTable,
                        'active'=>false,
                    );
                    echo TbHtml::tabbableTabs($tabs);
                    ?>
                </div>
            </div>

        </div>

    </div>

</section>

<?php
$js="
    $('.click-th').click(function(){
        var contNum = 4;
        var startNum=contNum;
        var endNum = $(this).attr('colspan');
        $(this).prevAll('.click-th').each(function(){
            var colspan = $(this).attr('colspan');
            startNum += parseInt(colspan,10);
        });
        endNum = parseInt(endNum,10)+startNum;
        if($(this).hasClass('active')){
            $(this).children('span').text($(this).data('text'));
            $(this).removeClass('active');
            $('#salesAnalysis>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = $(this).data('width')+'px';
                $(this).width(width);
            });
            $('#salesAnalysis>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).children('span').text($(this).data('text'));
            });
            $('#salesAnalysis>tbody>tr').each(function(){
                $(this).children().slice(startNum,endNum).each(function(){
                    $(this).children('span').text($(this).data('text'));
                });
            });
        }else{
            $(this).data('text',$(this).text());
            $(this).children('span').text('.');
            $(this).addClass('active');
            $('#salesAnalysis>thead>tr').eq(0).children().slice(startNum,endNum).each(function(){
                var width = '15px';
                $(this).width(width);
            });
            $('#salesAnalysis>thead>tr').eq(2).children().slice(startNum-contNum,endNum-contNum).each(function(){
                $(this).data('text',$(this).text());
                $(this).children('span').text('');
            });
            $('#salesAnalysis>tbody>tr').each(function(){
                $(this).children().slice(startNum,endNum).each(function(){
                    $(this).data('text',$(this).text());
                    $(this).children('span').text('');
                });
            });
        }
    });
    
    $('.click-tr').click(function(){
        var show = $(this).hasClass('show-tr');
        if(show){
            $(this).removeClass('show-tr');
        }else{
            $(this).addClass('show-tr');
        }
        $(this).prevAll('tr').each(function(){
            if($(this).hasClass('tr-end')||$(this).children('td:first').hasClass('click-tr')){
                return false;
            }else{
                if(show){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            }
        });
    });
    
    var arr={};
    $('#salesAnalysis').find('tbody>tr').not('.tr-end').each(function(){
        var dept_name = $(this).children('td').eq(1).text();
        dept_name = dept_name.trim();
        if(dept_name!=''){
            arr[dept_name]=0;
        }
    });
    $.each(arr,function(key,val){
        $('#searchEx').append('<option value=\"'+key+'\">'+key+'</option>');
    });
    
    $('#searchEx').change(function(){
        var searchText = $(this).val();
        if(searchText==''){
            $('#salesAnalysis').find('tbody>tr').show();
            $('#salesAnalysis').find('.searchTr').remove();
        }else{
            $('#salesAnalysis .click-tr').each(function(){
                var trClick=$(this);
                var list=[];
                trClick.prevAll('tr').each(function(){
                    if($(this).hasClass('tr-end')){
                        return false;
                    }else{
                        var dept_name=$(this).find('td').eq(1).text();
                        dept_name=dept_name.trim();
                        if(dept_name==searchText){
                            $(this).show();
                            list.push($(this));
                        }else{
                            $(this).hide();
                        }
                    }
                });
                $(this).next('.searchTr').remove();
                if(list.length!=0){
                    var countList=[];
                    var html ='<tr class=\"searchTr\">';
                    html+='<td colspan=\"3\">搜索合计</td><td>-</td><td>-</td><td>-</td>';
                    $.each(list,function(i,objTr){
                        objTr.find('td').slice(6).each(function(key,objTd){
                            objTdText=$(objTd).text();
                            if(countList[key]==undefined){
                                countList[key]=0;
                            }
                            countList[key]+=objTdText==''||isNaN(objTdText)?0:parseFloat(objTdText);
                        });
                    });
                    $.each(countList,function(j,value){
                        html+='<td>'+value+'</td>';
                    });
                    html+='</tr>';
                    $(this).after(html);
                }
            });
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);


$language = Yii::app()->language;
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


