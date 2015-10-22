<?php

/**
 * Get an MlmSystemLog
 */
class modMlmSystemLogGetProcessor extends modObjectGetProcessor {
	public $objectType = 'MlmSystemLog';
	public $classKey = 'MlmSystemLog';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		return parent::process();
	}

}

return 'modMlmSystemLogGetProcessor';