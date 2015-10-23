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
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$event = trim($this->getProperty('event'));
		if (empty($event)) {
			$this->modx->error->addField('event', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$class = trim($this->getProperty('class'));
		if (empty($class)) {
			$this->modx->error->addField('class', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		if ($this->modx->getCount($this->classKey, array(
			'name' => $name,
			'event' => $event,
		))) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		if ($this->modx->getCount($this->classKey, array(
			'class' => $class,
			'event' => $event,
		))) {
			$this->modx->error->addField('event', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		if ($this->getProperty('tree_active')) {
			$treeProfit = $this->modx->fromJSON(trim($this->getProperty('tree_profit', '{}')));
			if (empty($treeProfit)) {
				$this->modx->error->addField('tree_profit', $this->modx->lexicon('mlmsystem_err_ns'));
			}
			else {
				$this->setProperty('tree_profit', $this->modx->toJSON($treeProfit));
			}
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