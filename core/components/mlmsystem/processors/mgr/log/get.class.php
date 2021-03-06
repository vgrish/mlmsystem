<?php

/**
 * Get an MlmSystemLog
 */
class modMlmSystemLogGetProcessor extends modObjectGetProcessor {
	public $objectType = 'MlmSystemLog';
	public $classKey = 'MlmSystemLog';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	public $MlmSystem;

	/** {@inheritDoc} */
	public function initialize()
	{
		/** @var mlmsystem $mlmsystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	/**
	 * @return array|string
	 */
	public function cleanup()
	{
		$set = $this->object->toArray();

		$process = $this->getProperty('process', false);
		if ($process) {
			$set = array_merge($this->MlmSystem->Tools->processObject($this->object, true, true, '', true), $set);
		}

		$aliases = $this->modx->fromJSON($this->getProperty('aliases', ''));
		if (!empty($aliases)) {
			foreach ($aliases as $alias) {
				$keyPrefix = '';
				if (in_array($alias, array('ParentUser', 'ParentUserProfile'))) {
					$keyPrefix = 'parent_';
				}
				if ($o = $this->object->getOne($alias)) {
					$set = array_merge($this->MlmSystem->Tools->processObject($o, true, true, $keyPrefix, true), $set);
				}
			}
		}

		return $this->success('', $set);
	}

}

return 'modMlmSystemLogGetProcessor';