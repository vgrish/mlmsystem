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


	'referrer_salt' => array(
		'value' => 'ZsBE8HC8y*fV',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_referrer'
	),
	'client_key' => array(
		'value' => 'rclient',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_referrer'
	),
	'referrer_key' => array(
		'value' => 'rhash',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_referrer'
	),
	'referrer_page' => array(
		'value' => '',
		'xtype' => 'numberfield',
		'area' => 'mlmsystem_referrer'
	),
	'referrer_time' => array(
		'value' => 365,
		'xtype' => 'numberfield',
		'area' => 'mlmsystem_referrer'
	),
	'referrer_default_client' => array(
		'value' => 0,
		'xtype' => 'numberfield',
		'area' => 'mlmsystem_referrer'
	),


	'handler_class_tools' => array(
		'value' => 'SystemTools',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_handler'
	),
	'handler_class_paths' => array(
		'value' => 'SystemPaths',
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
	'format_date_now' => array(
		'value' => '10',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),
	'format_date_day' => array(
		'value' => 'day H:i',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),
	'format_date_minutes' => array(
		'value' => '59',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),
	'format_date_hours' => array(
		'value' => '10',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),
	'format_balance' => array(
		'value' => '[2, ".", " "]',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),
	'format_balance_no_zeros' => array(
		'value' => true,
		'xtype' => 'combo-boolean',
		'area' => 'mlmsystem_format'
	),
	'format_incoming' => array(
		'value' => '[2, ".", " "]',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),
	'format_incoming_no_zeros' => array(
		'value' => true,
		'xtype' => 'combo-boolean',
		'area' => 'mlmsystem_format'
	),
	'format_outcoming' => array(
		'value' => '[2, ".", " "]',
		'xtype' => 'textfield',
		'area' => 'mlmsystem_format'
	),
	'format_outcoming_no_zeros' => array(
		'value' => true,
		'xtype' => 'combo-boolean',
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
