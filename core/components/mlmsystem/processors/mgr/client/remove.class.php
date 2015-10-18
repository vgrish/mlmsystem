<?php

/**
 * Remove a MlmSystemClient
 */
class modMlmSystemClientRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'MlmSystemClient';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeRemove()
	{
/*		if (!$this->object->get('editable')) {
			$this->failure($this->modx->lexicon('paymentsystem_err_lock'));
		}*/
		return parent::beforeRemove();
	}
}

return 'modMlmSystemClientRemoveProcessor';