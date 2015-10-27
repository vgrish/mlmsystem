<?php
/**
 * Resolve creating needed mode
 *
 * @var xPDOObject $object
 * @var array $options
 */
if ($object->xpdo) {
	/* @var modX $modx */
	$modx =& $object->xpdo;
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modelPath = $modx->getOption('mlmsystem_core_path', null, $modx->getOption('core_path') . 'components/mlmsystem/') . 'model/';
			$modx->addPackage('mlmsystem', $modelPath);
			$lang = $modx->getOption('manager_language') == 'en' ? 1 : 0;


			/*
			 * $modes = array(
			 * 		1 => 'списать',
			 * 		2 => 'пополнить',
			 * 		3 => 'изменить',
			 * )
			 *
			 */

			/* $MlmSystemModeChanges */
			$modes = array(
				'1' => array(
					'name' => !$lang ? 'Cписать' : 'Take',
					'description' => '',
				),
				'2' => array(
					'name' => !$lang ? 'Пополнить' : 'Put',
					'description' => '',
				),
				'3' => array(
					'name' => !$lang ? 'Изменить' : 'Change',
					'description' => '',
				),
			);

			foreach ($modes as $id => $properties) {
				if (!$mode = $modx->getCount('MlmSystemModeChanges', array('id' => $id))) {
					$mode = $modx->newObject('MlmSystemModeChanges', array_merge(array(
						'editable' => 0,
						'active' => 1,
						'rank' => $modx->getCount('MlmSystemModeChanges'),
						//'fixed' => 1,
					), $properties));
					$mode->set('id', $id);
					$mode->save();
				}
			}

			break;
		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;