<?php

/**
 * Update an MlmSystemProfitGroup
 */
class modMlmSystemProfitGroupUpdateProcessor extends modObjectUpdateProcessor
{
	public $objectType = 'MlmSystemProfitGroup';
	public $classKey = 'MlmSystemProfitGroup';
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

		$profit = $this->MlmSystem->Tools->percentFormat($this->getProperty('profit', 0));
		$this->setProperty('profit', $profit);

		return parent::beforeSet();
	}

	/** {@inheritDoc} */
	public function afterSave()
	{

		return true;
	}

}

return 'modMlmSystemProfitGroupUpdateProcessor';
