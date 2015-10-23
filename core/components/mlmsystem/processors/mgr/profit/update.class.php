<?php

/**
 * Update an MlmSystemProfit
 */
class modMlmSystemProfitUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'MlmSystemProfit';
	public $classKey = 'MlmSystemProfit';
	public $languageTopics = array('mlmsystem');
	public $permission = '';


	/** {@inheritDoc} */
	public function beforeSave() {
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}

	/** {@inheritDoc} */
	public function beforeSet() {
		$id = (int)$this->getProperty('id');
		$name = trim($this->getProperty('name'));
		$class = trim($this->getProperty('class'));

		if (empty($id)) {
			return $this->modx->lexicon('mlmsystem_err_ns');
		}

		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}
		if (empty($class)) {
			$this->modx->error->addField('class', $this->modx->lexicon('mlmsystem_err_ae'));
		}
		if ($this->modx->getCount($this->classKey, array(
			'name' => $name,
			'class' => $class,
			'id:!=' => $id
		))) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		return parent::beforeSet();
	}
}

return 'modMlmSystemProfitUpdateProcessor';
