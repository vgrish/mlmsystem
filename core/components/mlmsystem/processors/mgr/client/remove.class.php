<?php

/**
 * Remove a MlmSystemClient
 */
class modMlmSystemClientRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'MlmSystemClient';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

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
		//$this->failure($this->modx->lexicon('paymentsystem_err_lock'));
		return parent::beforeRemove();
	}

//	public function afterRemove()
//	{
//		$this->MlmSystem->Paths->GeneratePaths($this->object->get('id'));
//		return true;
//	}

}

return 'modMlmSystemClientRemoveProcessor';