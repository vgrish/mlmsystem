<?php

/**
 * Get an MlmSystemClient
 */
class modMlmSystemClientGetProcessor extends modObjectGetProcessor
{
	public $objectType = 'MlmSystemClient';
	public $classKey = 'MlmSystemClient';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/**
	 * @return array|string
	 */
	public function cleanup()
	{
		$set = $this->object->toArray();

		return $this->success('', $set);
	}

}

return 'modMlmSystemClientGetProcessor';