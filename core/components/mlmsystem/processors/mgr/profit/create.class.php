<?php

/**
 * Create an MlmSystemProfit
 */
class modMlmSystemProfitCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'MlmSystemProfit';
	public $classKey = 'MlmSystemProfit';
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

return 'modMlmSystemProfitCreateProcessor';