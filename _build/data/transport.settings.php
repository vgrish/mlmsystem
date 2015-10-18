<?php

$settings = array();

$tmp = array(

	'show_log' => array(
		'value' => false,
		'xtype' => 'combo-boolean',
		'area' => 'mlmsystem_main',
	),
	'mail_notice' => array(
		'value' => true,
		'xtype' => 'combo-boolean',
		'area' => 'mlmsystem_main'
	),

	'handler_class_tools' => array(
		'value' => 'SystemTools',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_handler'
	),
	'handler_class_client_validator' => array(
		'value' => 'ClientValidator',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_handler'
	),
	'handler_class_email_validator' => array(
		'value' => 'EmailValidator',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_handler'
	),


	'date_format' => array(
		'xtype' => 'textfield',
		'value' => '%d.%m.%y <small>%H:%M</small>',
		'area' => 'mlmsystem_format',
	),
	'format_date' => array(
		'value' => 'd F Y, H:i',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),


	//временные

	'assets_path' => array(
		'value' => '{base_path}mlmsystem/assets/components/mlmsystem/',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_temp',
	),
	'assets_url' => array(
		'value' => '/mlmsystem/assets/components/mlmsystem/',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_temp',
	),
	'core_path' => array(
		'value' => '{base_path}mlmsystem/core/components/mlmsystem/',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_temp',
	),

	//временные
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'mlmsystem_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
