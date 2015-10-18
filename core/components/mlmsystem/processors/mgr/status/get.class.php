<?php

/**
 * Get an MlmSystemStatus
 */
class modMlmSystemStatusGetProcessor extends modObjectGetProcessor {
	public $objectType = 'MlmSystemStatus';
	public $classKey = 'MlmSystemStatus';
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

return 'modMlmSystemStatusGetProcessor';