<?php

require_once dirname(__FILE__) . '/update.class.php';

/**
 * SetProperty a MlmSystemTypeChanges
 */
class modMlmSystemTypeChangesSetPropertyProcessor extends modMlmSystemTypeChangesUpdateProcessor
{
	/** @var MlmSystemTypeChanges $object */
	public $object;
	public $objectType = 'MlmSystemTypeChanges';
	public $classKey = 'MlmSystemTypeChanges';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function beforeSet()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);

		$this->setProperties($this->object->toArray());

		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$this->setProperty($fieldName, $fieldValue);
		}

		return parent::beforeSet();
	}

}

return 'modMlmSystemTypeChangesSetPropertyProcessor';