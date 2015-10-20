<?php

require_once dirname(dirname(dirname(__FILE__))) . '/client/update.class.php';

/**
 * Update an MlmSystemClient
 */
class modMlmSystemClientBalanceUpdateProcessor extends modMlmSystemClientUpdateProcessor
{
	/** @var MlmSystemClient $object */
	public $object;
	public $classKey = 'MlmSystemClient';
	/** {@inheritDoc} */
	public function beforeSet()
	{
		if (!$beforeSet = parent::beforeSet()) {
			return $beforeSet;
		}

		$sum = $this->getProperty('change_balance_sum', 0);
		if (empty($sum)) {
			return $this->MlmSystem->lexicon('err_sum');
		}

		$type = $this->getProperty('change_balance_type', 0);
		if (empty($type)) {
			return $this->MlmSystem->lexicon('err_type');
		}

		switch ($type) {
			case '1':
				$this->object->takeSum($sum);
				break;
			case '2':
				$this->object->putSum($sum);
				break;
			default:
				return $this->MlmSystem->lexicon('err_correct_type');
				break;
		}

		$this->properties = array();

		$valid = $this->object->validate();
		if (!$valid) {
			$validator = $this->object->getValidator();
			if ($validator->hasMessages()) {
				foreach ($validator->getMessages() as $message) {
					if ($message['field'] == 'balance') {
						$this->addFieldError('change_balance_sum', $message['message']);
					}
					else {
						$this->addFieldError($message['field'],$this->modx->lexicon($message['message']));
					}
				}
			}
		}

		return true;
	}

}

return 'modMlmSystemClientBalanceUpdateProcessor';