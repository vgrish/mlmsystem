<?php

/**
 * Remove a MlmSystemLog
 */
class modMlmSystemLogRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'MlmSystemLog';
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
		if (!$this->modx->getOption('mlmsystem_allow_remove_story')) {
			$this->failure($this->modx->lexicon('mlmsystem_err_lock'));
		}
		return parent::beforeRemove();
	}
}

return 'modMlmSystemLogRemoveProcessor';