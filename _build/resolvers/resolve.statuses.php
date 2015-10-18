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

			$statuses = array(

				/* MlmSystemClient */
				'1' => array(
					'name' => !$lang ? 'Создан' : 'Created',
					'color' => '000000',
					'email_user' => 0,
					'email_manager' => 0,
					'tpl_user' => 0,
					'tpl_manager' => 0,
					'final' => 0,
					'fixed' => 1,
					'class' => 'MlmSystemClient'
				),
				'2' => array(
					'name' => !$lang ? 'Новый' : 'New',
					'color' => '993300',
					'email_user' => 1,
					'email_manager' => 0,
					'tpl_user' => 0,
					'tpl_manager' => 0,
					'final' => 0,
					'fixed' => 0,
					'class' => 'MlmSystemClient'
				),
				'3' => array(
					'name' => !$lang ? 'Блокирован' : 'Blocked',
					'color' => '008000',
					'email_user' => 1,
					'email_manager' => 0,
					'tpl_user' => 0,
					'tpl_manager' => 0,
					'final' => 0,
					'fixed' => 0,
					'class' => 'MlmSystemClient'
				),
				'4' => array(
					'name' => !$lang ? 'Удален' : 'Removed',
					'color' => '008000',
					'email_user' => 1,
					'email_manager' => 0,
					'tpl_user' => 0,
					'tpl_manager' => 0,
					'final' => 1,
					'fixed' => 1,
					'class' => 'MlmSystemClient'
				),
				
			);

			foreach ($statuses as $id => $properties) {
				if (!$status = $modx->getCount('MlmSystemStatus', array('id' => $id))) {
					$status = $modx->newObject('MlmSystemStatus', array_merge(array(
						'editable' => 0,
						'active' => 1,
						'rank' => $modx->getCount('MlmSystemStatus'),
						//'fixed' => 1,
					), $properties));
					$status->set('id', $id);
					/* @var modChunk $chunk */
					if (!empty($properties['tpl_user'])) {
						if ($chunk = $modx->getObject('modChunk', array('name' => $properties['tpl_user']))) {
							$status->set('tpl_user', $chunk->get('id'));
						}
					}
					if (!empty($properties['tpl_manager'])) {
						if ($chunk = $modx->getObject('modChunk', array('name' => $properties['tpl_manager']))) {
							$status->set('tpl_manager', $chunk->get('id'));
						}
					}
					$status->save();
				}
			}

			break;
		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;