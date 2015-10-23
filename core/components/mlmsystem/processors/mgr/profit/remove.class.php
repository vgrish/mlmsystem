<?php

/**
 * Remove a MlmSystemProfit
 */
class modMlmSystemProfitRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'MlmSystemProfit';
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
		if (!$this->object->get('editable')) {
			$this->failure($this->modx->lexicon('mlmsystem_err_lock'));
		}

		return parent::beforeRemove();
	}

	/** {@inheritDoc} */
	public function afterRemove()
	{
		$this->MlmSystem->setPluginEvent($this->object->get('event'), 'remove');

		return true;
	}

}

return 'modMlmSystemProfitRemoveProcessor';