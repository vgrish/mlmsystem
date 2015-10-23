<?php

/**
 * Get an MlmSystemProfit
 */
class modMlmSystemProfitGetProcessor extends modObjectGetProcessor {
	public $objectType = 'MlmSystemProfit';
	public $classKey = 'MlmSystemProfit';
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

return 'modMlmSystemProfitGetProcessor';