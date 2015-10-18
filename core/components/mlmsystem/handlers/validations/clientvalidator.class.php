<?php

/**
 * Custom validation class for operation
 *
 * @package modx
 * @subpackage validation
 */
class ClientValidator extends xPDOValidator
{
	public $xpdo = null;
	public $object = null;
	public $results = array();
	public $messages = array();

	public $config = array();
	public $customMessages = array();
	public $Tools = null;

	public function __construct(& $object)
	{
		$this->object = &$object;
		$this->xpdo = &$object->xpdo;
		$this->object->_loadValidation(true);

		$corePath = $this->xpdo->getOption('mlmsystem_core_path', null, MODX_CORE_PATH . 'components/mlmsystem/');

		$this->config = array(
			'corePath' => $corePath,
			'handlersPath' => $corePath . 'handlers/',
		);
	}

	/**
	 * Validate a xPDOObject by the parameters specified
	 *
	 * @access public
	 * @param array $parameters An associative array of config parameters.
	 * @return boolean Either true or false indicating valid or invalid.
	 */
	public function validate(array $parameters = array())
	{
		$isNew = $this->object->isNew();

		if ($isNew) {
			$validated = parent:: validate($parameters);
		}
		else {
			$validated = (parent:: validate($parameters) AND $this->_validateObject());
		}

		if (!empty($this->messages)) {
			reset($this->messages);
			while (list ($k, $v) = each($this->messages)) {
				if (array_key_exists('message', $this->messages[$k])) {
					$this->messages[$k]['message'] = $this->xpdo->lexicon($this->messages[$k]['message']);
				}
			}
		}
		return $validated;
	}

	public function _validateObject()
	{
		$validated = true;
		foreach ($this->object->_fields as $field => $value) {
			$field = $this->object->getField($field, true);
			if ($field === false) {
				continue;
			}
			if ($this->_validateField($field, $value) === false) {
				$message = (isset($this->customMessages[$field])) ? $this->customMessages[$field] : 'mlmsystem_err_' . $field;
				$this->addMessage($field, __CLASS__, $message);
				$validated = false;
			}
		}
		return $validated;
	}

	/** @inheritdoc} */
	public function _validateField($field, $value)
	{
		$oldValue = $value;
		if (!$oldObject = $this->xpdo->getObject($this->object->_class, $this->object->id)) {
			$oldObject = $this->xpdo->newObject($this->object->_class);
		}

		$this->xpdo->invokeEvent('MlmSystemOnBeforeValidateValue', array(
			'key' => $field,
			'value' => &$value,
			'old_value' => &$oldValue,
			'object' => &$this->object,
			'old_object' => &$oldObject,
			'object_class' => $this->object->_class,
			'class' => __CLASS__
		));

		switch ($field) {
			case 'balance':
			case 'incoming':
			case 'outcoming':
				$value = ($value >= 0) ? $value : false;
				break;
			case 'status':
				$q = $this->xpdo->newQuery('MlmSystemStatus', array('id' => $value, 'class' => $this->object->_class, 'active' => 1));
				$value = $this->xpdo->getCount('MlmSystemStatus', $q) ? $value : false;
				if ($value) {
					$isFinal = ($oldObject->getOne('Status')) ? (bool)$oldObject->getOne('Status')->get('final') : false;
					$isFixed = ($oldObject->getOne('Status')) ? (bool)$oldObject->getOne('Status')->get('fixed') : false;
					if ($isFinal) {
						$this->addCustomMessages($field, 'mlmsystem_err_status_final');
						$value = false;
					}
					if ($isFixed) {
						if (
							$oldStatusObject = $oldObject->getOne('Status') AND
							$newStatusObject = $this->xpdo->getObject('MlmSystemStatus', $value)
						) {
							if ($newStatusObject->get('rank') < $oldStatusObject->get('rank')) {
								$this->addCustomMessages($field, 'mlmsystem_err_status_fixed');
								$value = false;
							}
						}
					}
				}
				break;
			case 'createdon':
			case 'updatedon':
				break;
			default:
				break;
		}

		$this->xpdo->invokeEvent('MlmSystemOnValidateValue', array(
			'key' => $field,
			'value' => &$value,
			'old_value' => &$oldValue,
			'object' => &$this->object,
			'old_object' => &$oldObject,
			'object_class' => $this->object->_class,
			'class' => __CLASS__
		));

		return $value;
	}

	public function addCustomMessages($field, $message = null)
	{
		if ($message) {
			$this->customMessages[$field] = $message;
		}
	}

}