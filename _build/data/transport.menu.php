<?php

$menus = array();

$tmp = array(
	'mlmsystem' => array(
		'description' => 'mlmsystem_desc',
		'action' => array(
			'controller' => 'index',
		),
	),
	'mlmsystem_client' => array(
		'description' => 'mlmsystem_client_desc',
		'parent' => 'mlmsystem',
		'menuindex' => 1,
		'action' => array(
			'controller' => 'controllers/client'
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