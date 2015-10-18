<?php

/**
 * Update an MlmSystemClient
 */
class modMlmSystemClientUpdateProcessor extends modObjectUpdateProcessor
{
	public $classKey = 'MlmSystemClient';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	public $MlmSystem;
	public $prop = array();
	public $successMessage = '';
	protected $status;

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
	public function beforeSet()
	{
		foreach (array('status') as $v) {
			$this->$v = $this->object->get($v);
		}

		return parent::beforeSet();
	}

	public function afterSave()
	{

		/* выполняем уведомления */
		if (
			$this->object->get('status') != $this->status)
		{
			$this->MlmSystem->Tools->sendNotice($this->object);
		}

		return parent::afterSave();
	}

	/**
	 * Return the success message
	 * @return array
	 */
	public function cleanup()
	{
		return $this->success($this->successMessage, $this->object);
	}

}

return 'modMlmSystemClientUpdateProcessor';