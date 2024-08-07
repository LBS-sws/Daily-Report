<?php
$this->pageTitle=Yii::app()->name . ' - City Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'code-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('code','City Form'); ?></strong>
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
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('city/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('city/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('city/save'))); 
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

			<div class="form-group">
				<?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
				<?php echo $form->textField($model, 'code', 
					array('size'=>15,'maxlength'=>15,'readonly'=>($model->scenario!='new'),)); 
				?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-5">
				<?php echo $form->textField($model, 'name', 
					array('size'=>50,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'ka_bool',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
                    echo $form->dropDownList($model, 'ka_bool', CityList::getCityTypeList(),
                        array('disabled'=>($model->scenario=='view'))
                    );
                    ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'region',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
						$item = General::getCityList();
						$item = array_merge(array(''=>Yii::t('misc','-- None --')),$item);
						echo $form->dropDownList($model, 'region', $item,
							array('disabled'=>($model->scenario=='view'))
						);
					?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'incharge',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php 
						$item = $model->getCityInChargeList();
						$item = array_merge(array(''=>Yii::t('misc','-- None --')),$item);
						echo $form->dropDownList($model, 'incharge', $item,
							array('disabled'=>($model->scenario=='view'))
						); 
					?>
				</div>
			</div>

            <?php
                foreach ($model->getDynamicFields() as $key=>$fileList){
                    echo '<div class="form-group">';
                    echo $form->labelEx($model,$key,array('class'=>"col-sm-2 control-label"));
                    echo '<div class="col-sm-3">';
                    switch ($fileList["type"]){
                        case "list":
                            $item = call_user_func_array($fileList["func"], $fileList["param"]);
                            echo $form->dropDownList($model, $key, $item,
                                array('disabled'=>($model->scenario=='view'))
                            );
                            break;
                        case "text":
                            echo $form->textField($model,$key,
                                array('readonly'=>($model->scenario=='view'))
                            );
                            break;
                        case "number":
                            echo $form->numberField($model,$key,
                                array('readonly'=>($model->scenario=='view'))
                            );
                            break;
                    }
                    echo '</div>';
                    echo '</div>';
                }
            ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('city/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


