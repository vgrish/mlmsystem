<?php

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modelPath = $modx->getOption('mlmsystem_core_path', null, $modx->getOption('core_path') . 'components/mlmsystem/') . 'model/';
			$modx->addPackage('mlmsystem', $modelPath);

			$manager = $modx->getManager();

			$objects = array(
				'MlmSystemClient',

				'MlmSystemStatus',
				'MlmSystemEmail',
				'MlmSystemLog',

				'MlmSystemProfit',
				'MlmSystemPath'
			);

			foreach ($objects as $tmp) {
				$manager->createObjectContainer($tmp);
			}

			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;
