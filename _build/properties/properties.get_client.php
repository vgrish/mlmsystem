<?php

$properties = array();

$tmp = array(
	'id' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'tplRow' => array(
		'type' => 'textfield',
		'value' => 'tpl.mlmClient.row',
	),
	'tplOuter' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'tplEmpty' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'getUser' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'getUserProfile' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'getPaymentClient' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'getStatus' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'showOnlyClient' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'formatField' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'toPlaceholder' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
);


foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;
