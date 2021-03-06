<?php
/**
 * Resolve creating needed types
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
			 * $modeods = array(
			 * 		1 => 'списать',
			 * 		2 => 'пополнить',
			 * 		3 => 'изменить',
			 * )
			 *
			 */

			$types = array(

				/* MlmSystemType */
				'1' => array(
					'name' => !$lang ? 'Пополнение' : 'Refill',
					'field' => 'incoming',
					'description' => 'Пополнение Баланса',
					'mode' => 2,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),
				'2' => array(
					'name' => !$lang ? 'Пополнение' : 'Refill',
					'field' => 'incoming',
					'description' => 'Пополнение Администратором',
					'mode' => 2,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),


				'8' => array(
					'name' => !$lang ? 'Начисление' : 'Profit',
					'field' => 'incoming',
					'description' => 'Комиммионное начисление',
					'mode' => 2,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),
				'9' => array(
					'name' => !$lang ? 'Начисление' : 'Add Profit',
					'field' => 'incoming',
					'description' => 'Дополнительное начисление',
					'mode' => 2,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),
				'10' => array(
					'name' => !$lang ? 'Начисление' : 'Add Profit',
					'field' => 'incoming',
					'description' => 'Процентное начисление',
					'mode' => 2,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),

				'11' => array(
					'name' => !$lang ? 'Списание' : 'Withdrawal',
					'field' => 'outcoming',
					'description' => 'Списание Баланса',
					'mode' => 1,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),
				'12' => array(
					'name' => !$lang ? 'Списание' : 'Withdrawal',
					'field' => 'outcoming',
					'description' => 'Списание Администратором',
					'mode' => 1,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),

				'21' => array(
					'name' => !$lang ? 'Изменение' : 'Change',
					'field' => 'parent',
					'description' => 'Изменение Родителя',
					'mode' => 3,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),
				'22' => array(
					'name' => !$lang ? 'Изменение' : 'Change',
					'field' => 'status',
					'description' => 'Изменение Статуса Клиента',
					'mode' => 3,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),
				'23' => array(
					'name' => !$lang ? 'Изменение' : 'Change',
					'field' => 'leader',
					'description' => 'Изменение Руководящего Статуса',
					'mode' => 3,
					'front' => 0,
					'class' => 'MlmSystemClient'
				),

			);

			foreach ($types as $id => $properties) {
				if (!$type = $modx->getCount('MlmSystemTypeChanges', array('id' => $id))) {
					$type = $modx->newObject('MlmSystemTypeChanges', array_merge(array(
						'editable' => 0,
						'active' => 1,
						'rank' => $modx->getCount('MlmSystemTypeChanges'),
						//'fixed' => 1,
					), $properties));
					$type->set('id', $id);
					$type->save();
				}
			}

			break;
		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;