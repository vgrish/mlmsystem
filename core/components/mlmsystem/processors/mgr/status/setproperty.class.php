<?php

/**
 * SetProperty a MlmSystemStatus
 */
class modMlmSystemStatusSetPropertyProcessor extends modObjectUpdateProcessor
{
	/** @var MlmSystemStatus $object */
	public $object;
	public $objectType = 'MlmSystemStatus';
	public $classKey = 'MlmSystemStatus';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function beforeSet()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);

		$this->properties = array();

		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$this->setProperty('field_name', $fieldName);
			$this->setProperty('field_value', $fieldValue);
		}

		return true;
	}

	/** {@inheritDoc} */
	public function beforeSave()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);
		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$array = $this->object->toArray();
			if (isset($array[$fieldName])) {
				$this->object->fromArray(array(
					$fieldName => $fieldValue,
				));
			}
		}

		return parent::beforeSave();
	}
}

return 'modMlmSystemStatusSetPropertyProcessor';