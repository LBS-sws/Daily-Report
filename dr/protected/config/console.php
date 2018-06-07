<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Daily Report System - UAT',
	'timeZone'=>'Asia/Hong_Kong',
	'sourceLanguage'=> 'en',
	'language'=>'zh_cn',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'ext.YiiMailer.YiiMailer',
	),

	// application components
	'components'=>array(
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=swoper_w',
			'emulatePrepare' => true,
			'username' => 'swuser',
			'password' => 'swisher168',
			'charset' => 'utf8',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'systemId'=>'drs',
		'envSuffix'=>'dev',
		'feedbackCcBoss'=>array('boss'),
		'systemEmail'=>'it@lbsgroup.com.hk',
		'webroot'=>'http://192.168.0.162/swoper-web',
	),
);
