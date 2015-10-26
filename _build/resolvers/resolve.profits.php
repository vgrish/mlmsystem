<?php
/**
 * Resolve creating needed statuses
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

			/** @var MlmSystem $MlmSystem */
			$MlmSystem = $modx->getService('mlmsystem');
			$MlmSystem->initialize();

			/* MlmSystemProfit */
			$profits = array(
				'1' => array(
					'name' => !$lang ? 'Покупка' : 'Purchase',
					'description' => 'Покупка товара MiniShop2',
					'event' => 'msOnChangeOrderStatus',
					'class' => 'MiniShop2Profits',
					'profit' => 10
				),
			);

			foreach ($profits as $id => $properties) {
				if (!$profit = $modx->getCount('MlmSystemProfit', array('id' => $id))) {
					$profit = $modx->newObject('MlmSystemProfit', array_merge(array(
						'editable' => 1,
						'active' => 1,
						'rank' => $id - 1,
					), $properties));
					$profit->set('id', $id);
					if ($profit->save()) {
						$MlmSystem->setPluginEvent($profit->get('event'), 'create');
					}
				}
			}

			break;
		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;