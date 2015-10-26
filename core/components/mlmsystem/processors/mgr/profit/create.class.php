<?php

/**
 * Create an MlmSystemProfit
 */
class modMlmSystemProfitCreateProcessor extends modObjectCreateProcessor
{
	public $objectType = 'MlmSystemProfit';
	public $classKey = 'MlmSystemProfit';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** @var MlmSystem $MlmSystem */
	public $MlmSystem;

	/** {@inheritDoc} */
	public function initialize()
	{
		/** @var mlmsystem $mlmsystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeSet()
	{
		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$event = trim($this->getProperty('event'));
		if (empty($event)) {
			$this->modx->error->addField('event', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		if ($this->modx->getCount($this->classKey, array(
			'name' => $event,
		))
		) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		return parent::beforeSet();
	}

	/** {@inheritDoc} */
	public function beforeSave()
	{
		$this->object->fromArray(array(
			'rank' => $this->modx->getCount($this->classKey),
			'editable' => true
		));

		return parent::beforeSave();
	}

	/** {@inheritDoc} */
	public function afterSave()
	{
		$this->MlmSystem->setPluginEvent($this->object->get('event'), 'create');

		return true;
	}

}

return 'modMlmSystemProfitCreateProcessor';