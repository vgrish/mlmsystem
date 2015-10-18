<?php

/**
 * Create an MlmSystemStatus
 */
class modMlmSystemStatusCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'MlmSystemStatus';
	public $classKey = 'MlmSystemStatus';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function beforeSet() {
		$name = trim($this->getProperty('name'));
		$class = trim($this->getProperty('class'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}
		if (empty($class)) {
			$this->modx->error->addField('class', $this->modx->lexicon('mlmsystem_err_ae'));
		}
		if ($this->modx->getCount($this->classKey, array(
			'name' => $name,
			'class' => $class,
		))) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		return parent::beforeSet();
	}

	/** {@inheritDoc} */
	public function beforeSave() {
		$this->object->fromArray(array(
			'rank' => $this->modx->getCount($this->classKey),
			'editable' => true
		));
		return parent::beforeSave();
	}

}

return 'modMlmSystemStatusCreateProcessor';