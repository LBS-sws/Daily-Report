<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Reset-Login';
?>
<div class="login-container">
    <div class="header">
        <div class="logo">
            <?php echo '<img src="' . Yii::app()->baseUrl . '/images/login_logo.png" width="330px">';?>
        </div>
        <div class="sevice_select">
            <div class="sevice_select_bt">
                服务器：大陆
            </div>
        </div>
    </div>
    <div class="content">
        <div class="content_left">
            <div class="content_left_title">密码 <span>安全</span></div>
            <p class="content_left_size">为保证公司信息安全，首次登录请</p>
            <p class="content_left_size">重置密码</p>
            <?php echo '<img src="' . Yii::app()->baseUrl . '/images/banner.png">';?>
        </div>
        <div class="content_right">
            <?php $form=$this->beginWidget('TbActiveForm', array(
                'id'=>'reset-login-form',
                'enableClientValidation'=>true,
                'clientOptions'=>array(
                    'validateOnSubmit'=>true,
                    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
                ),
            )); ?>
            <h2>首次登录重置密码</h2>
            <?php
            echo $form->textField($model,'username',
                array('placeholder'=>Yii::t('user','User ID'),'class'=>'login_input','id'=>'input_user','readonly' => 'readonly','value' => $_GET['username'],));
            ?>
            <span>密码可由8~20位，数字、字母、符号组成（至少两两组合）</span>
            <?php
                echo $form->passwordField($model,'new_password',
                array('placeholder'=>Yii::t('misc','New Password'),'class'=>'login_input','id'=>'input_password'));
            ?>
            <?php
                echo $form->passwordField($model,'again_new_password',
                array('placeholder'=>Yii::t('misc','Again New Password'),'class'=>'login_input','id'=>'input_password'));
            ?>
            <?php
                echo TbHtml::submitButton(Yii::t('misc','Login'),
                array('class'=>'login_button',));
            ?>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>

