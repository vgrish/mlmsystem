<?php

$menus = array();

$tmp = array(
	'mlmsystem' => array(
		'description' => 'mlmsystem_desc',
		'action' => array(
			'controller' => 'index',
		),
	),
	'mlmsystem_clients' => array(
		'description' => 'mlmsystem_clients_desc',
		'parent' => 'mlmsystem',
		'menuindex' => 1,
		'action' => array(
			'controller' => 'controllers/client'
		)
	),
	'mlmsystem_profits' => array(
		'description' => 'mlmsystem_profits_desc',
		'parent' => 'mlmsystem',
		'menuindex' => 2,
		'action' => array(
			'controller' => 'controllers/profit'
		)
	),
//	'mlmsystem_operations' => array(
//		'description' => 'mlmsystem_operations_desc',
//		'parent' => 'mlmsystem',
//		'menuindex' => 3,
//		'action' => array(
//			'controller' => 'controllers/operation'
//		)
//	),
	'mlmsystem_logs' => array(
		'description' => 'mlmsystem_logs_desc',
		'parent' => 'mlmsystem',
		'menuindex' => 4,
		'action' => array(
			'controller' => 'controllers/log'
		)
	),

);

$i = 0;
foreach ($tmp as $k => $v) {
	$action = null;
	if (!empty($v['action'])) {
		/* @var modAction $action */
		$action = $modx->newObject('modAction');
		$action->fromArray(array_merge(array(
			'namespace' => PKG_NAME_LOWER,
			'id' => 0,
			'parent' => 0,
			'haslayout' => 1,
			'lang_topics' => PKG_NAME_LOWER . ':default',
			'assets' => '',
		), $v['action']), '', true, true);
		unset($v['action']);
	}

	/* @var modMenu $menu */
	$menu = $modx->newObject('modMenu');
	$menu->fromArray(array_merge(
		array(
			'text' => $k,
			'parent' => 'components',
			'icon' => 'images/icons/plugin.gif',
			'menuindex' => $i,
			'params' => '',
			'handler' => '',
		), $v
	), '', true, true);

	if (!empty($action) && $action instanceof modAction) {
		$menu->addOne($action);
	}

	$menus[] = $menu;
	$i++;
}

unset($action, $menu, $i);
return $menus;