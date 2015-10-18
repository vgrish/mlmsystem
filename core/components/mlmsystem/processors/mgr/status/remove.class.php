<?php

/**
 * Remove a MlmSystemStatus
 */
class modMlmSystemStatusRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'MlmSystemStatus';
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
		if (!$this->object->get('editable')) {
			$this->failure($this->modx->lexicon('mlmsystem_err_lock'));
		}
		return parent::beforeRemove();
	}
}

return 'modMlmSystemStatusRemoveProcessor';