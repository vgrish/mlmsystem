<?php

/**
 * Get an MlmSystemTypeChanges
 */
class modMlmSystemTypeChangesGetProcessor extends modObjectGetProcessor {
	public $objectType = 'MlmSystemTypeChanges';
	public $classKey = 'MlmSystemTypeChanges';
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

return 'modMlmSystemTypeChangesGetProcessor';