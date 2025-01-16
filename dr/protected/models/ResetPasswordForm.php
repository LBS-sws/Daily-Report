<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ResetPasswordForm extends CFormModel
{
	public $username;
//	public $password;
    public $new_password;
    public $again_new_password;
	public $rememberMe;
    public $errorCode;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('username,new_password,again_new_password', 'required'),
			// password needs to be authenticated
			array('new_password', 'authenticate'),
			array('rememberMe', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>Yii::t('misc','User ID'),
            'new_password'=>Yii::t('misc','New Password'),
            'again_new_password'=>Yii::t('misc','Again New Password'),
		);
	}

	/**
	 * Authenticates the password.
	 */
	public function authenticate($attribute,$params) {
        if($this->new_password != $this->again_new_password) $this->addError('password',Yii::t('dialog','The password entered twice is inconsistent'));
        return true;
	}


}
