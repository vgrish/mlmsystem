<?php

$corePath = $modx->getOption('mlmsystem_core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mlmsystem/');
$MlmSystem = $modx->getService('mlmsystem', 'MlmSystem', $corePath . 'model/mlmsystem/', array('core_path' => $corePath));

$className = 'MlmSystem' . $modx->event->name;
$modx->loadClass('MlmSystemPlugin', $MlmSystem->getOption('modelPath') . 'mlmsystem/events/', true, true);
$modx->loadClass($className, $MlmSystem->getOption('modelPath') . 'mlmsystem/events/', true, true);
if (class_exists($className)) {
	/** @var $MlmSystem $handler */
	$handler = new $className($modx, $scriptProperties);
	$handler->run();
}
return;
