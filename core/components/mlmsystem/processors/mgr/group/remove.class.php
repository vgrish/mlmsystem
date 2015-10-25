<?php

/**
 * Remove a MlmSystemProfitGroup
 */
class modMlmSystemProfitGroupRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'MlmSystemProfitGroup';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** @var MlmSystem $MlmSystem */
	public $MlmSystem;

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}

		/** @var mlmsystem $mlmsystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeRemove()
	{

		return parent::beforeRemove();
	}

}

return 'modMlmSystemProfitGroupRemoveProcessor';