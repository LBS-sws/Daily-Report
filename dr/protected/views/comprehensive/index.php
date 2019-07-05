<?php
$this->pageTitle=Yii::app()->name . ' - Report';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'report-form',
'action'=>Yii::app()->createUrl('comprehensive/view'),
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('report','Comprehensive data comparative analysis'); ?></strong>
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
		<?php echo TbHtml::button(Yii::t('misc','Submit'), array(
				'submit'=>Yii::app()->createUrl('comprehensive/view')));
		?>
	</div>
	</div></div>
	<div class="box box-info">
		<div class="box-body">
<!--			--><?php //echo $form->hiddenField($model, 'id'); ?>
<!--			--><?php //echo $form->hiddenField($model, 'name'); ?>
<!--			--><?php //echo $form->hiddenField($model, 'fields'); ?>
<!--			--><?php //echo $form->hiddenField($model, 'form'); ?>

<!--		--><?php //if ($model->showField('city') && !Yii::app()->user->isSingleCity()): ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->dropDownList($model, 'city',$model->city,
						array('disabled'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
<!--		--><?php //else: ?>
<!--			--><?php //echo $form->hiddenField($model, 'city'); ?>
<!--		--><?php //endif ?>


		<?php if ($model->showField('end_dt')): ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'起止日期',array('class'=>"col-sm-2 control-label")); ?>
				<div >
                    <select id="select1"  name="ReportG02Form[start_dt]" style="width:80px;height: 35px">
                        <?php foreach ($model->date as $v){?>
                            <option value="<?php echo $v;?>"><?php echo $v;?>年</option>
                        <?php }?>
                    </select>
                                <select id="select2"  name="ReportG02Form[start_dt1]"  style="width:50px;height: 35px">
                                    <option value="1" class="a">1月</option>
                                    <option value="2" class="a">2月</option>
                                    <option value="3" class="a">3月</option>
                                    <option value="4" class="a">4月</option>
                                    <option value="5" class="a">5月</option>
                                    <option value="6" class="a">6月</option>
                                    <option value="7" class="a">7月</option>
                                    <option value="8" class="a">8月</option>
                                    <option value="9" class="a">9月</option>
                                    <option value="10" class="a">10月</option>
                                    <option value="11" class="a">11月</option>
                                    <option value="12" class="a">12月</option>
                                </select> --至--
                    <select id="select3" name="ReportG02Form[end_dt]"  style="width:80px;height: 35px" >
                        <?php foreach ($model->date as $v){?>
                        <option value="<?php echo $v;?>" "><?php echo $v;?>年</option>
                        <?php }?>
                    </select>
                    <select id="select4"  name="ReportG02Form[end_dt1]" style="width:50px;height: 35px">
                        <option value="1" class="c">1月</option>
                        <option value="2" class="c">2月</option>
                        <option value="3" class="c">3月</option>
                        <option value="4" class="c">4月</option>
                        <option value="5" class="c">5月</option>
                        <option value="6" class="c">6月</option>
                        <option value="7" class="c">7月</option>
                        <option value="8" class="c">8月</option>
                        <option value="9" class="c">9月</option>
                        <option value="10" class="c">10月</option>
                        <option value="11" class="c">11月</option>
                        <option value="12" class="c">12月</option>
                    </select>

				</div>
			</div>
		<?php else: ?>
			<?php echo $form->hiddenField($model, 'end_dt'); ?>
		<?php endif ?>


		</div>
	</div>
</section>

<?php
$url=Yii::app()->createUrl('report/city');
$js = <<<EOF
$(document).ready(function(){
   txt=$(select1).find("option:selected").val();
   var myDate = new Date();
   var year=myDate.getFullYear();
   var month=myDate.getMonth()+1;
    if(txt==year){     
     $("option").remove(".a");    
     var i = 1 ; 
     for (i;i<month;i++){ 
      //循环一次 i加1                                
       $("#select2").append("<option  value='"+i+"' class='a'>"+i+"月</option>"); //为Select追加一个Option(下拉项)                                          
      }  
    }   
    txt1=$(select3).find("option:selected").val();
    if(txt1==year){     
     $("option").remove(".c");    
     var i = 1 ; 
     for (i;i<month;i++){ 
      //循环一次 i加1                                
       $("#select4").append("<option  value='"+i+"' class='c'>"+i+"月</option>"); //为Select追加一个Option(下拉项)                                          
      }  
    } 
                 
      $(document).on("change","#select1",function () {    
            txt=$(this).find("option:selected").val();
            var myDate = new Date();
        //获取当前年
            var year=myDate.getFullYear();
            var month=myDate.getMonth()+1;
            if(txt==year){     
             $("option").remove(".a");
              var i = 1 ; 
                for (i;i<month;i++){             
                     $("#select2").append("<option  value='"+i+"' class='a'>"+i+"月</option>"); //为Select追加一个Option(下拉项)                                          
                 }   
            }else{
              $("option").remove(".a");
              var i = 1 ; 
              for (i;i<13;i++){             
                     $("#select2").append("<option  value='"+i+"' class='a'>"+i+"月</option>"); //为Select追加一个Option(下拉项)                                          
                 } 
           `}       
    });

      $('#select3').on("change",function () {    
            txt=$(this).find("option:selected").val();
            var myDate = new Date();
        //获取当前年
            var year=myDate.getFullYear();
            var month=myDate.getMonth()+1;
            if(txt==year){     
             $("option").remove(".b");
              var i = 1 ; 
                for (i;i<month;i++){             
                     $("#select4").append("<option  value='"+i+"' class='b'>"+i+"月</option>"); //为Select追加一个Option(下拉项)                                          
                 }   
            }else{
              $("option").remove(".b");
              var i = 1 ; 
           
           `}         
    });  
});
$(document).ready(function(){

      $('#select3').on("change",function () {    
            txt=$(this).find("option:selected").val();
            var myDate = new Date();
            var year=myDate.getFullYear();
            var month=myDate.getMonth()+1;
             if(txt==year){     
             $("option").remove(".c");  
              var i = 1 ; 
                for (i;i<month;i++){             
                     $("#select4").append("<option  value='"+i+"' class='c'>"+i+"月</option>"); //为Select追加一个Option(下拉项)                                          
                 }                   
            }else{
             $("option").remove(".c");
              var i = 1 ; 
              for (i;i<13;i++){             
                     $("#select4").append("<option  value='"+i+"' class='c'>"+i+"月</option>"); //为Select追加一个Option(下拉项)                                          
                 }            
            }    
 
    });  
});
EOF;
?>
<?php
Yii::app()->clientScript->registerScript('changestyle',$js,CClientScript::POS_READY);
$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnStaff', 'staff', 'staffs', 'staffs_desc',
    array(),
    true
);
Yii::app()->clientScript->registerScript('lookupStaffs',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);

$js = Script::genDatePicker(array(
    'ReportVisitForm_start_dt',
    'ReportVisitForm_end_dt',
));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

