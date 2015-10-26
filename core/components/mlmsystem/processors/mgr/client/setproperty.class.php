<?php

require_once dirname(__FILE__) . '/update.class.php';

/**
 * SetProperty a MlmSystemClient
 */
class modMlmSystemClientSetPropertyProcessor extends modMlmSystemClientUpdateProcessor
{
	/** @var MlmSystemClient $object */
	public $object;
	public $objectType = 'MlmSystemClient';
	public $classKey = 'MlmSystemClient';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function beforeSet()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);

		$this->properties = array();

		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$this->setProperty($fieldName, $fieldValue);
		}

		return parent::beforeSet();
	}

}

return 'modMlmSystemClientSetPropertyProcessor';