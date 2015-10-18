<?php

/**
 * Create an MlmSystemClient
 */
class modMlmSystemClientCreateProcessor extends modProcessor
{
	public $classKey = 'MlmSystemClient';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	public $MlmSystem;

	public function initialize()
	{
		/** @var MlmSystem $MlmSystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	public function process()
	{
		$data = array();
		$data['email'] = $this->getProperty('email', null);
		$data['active'] = $this->getProperty('active', true);
		$data['status'] = $this->getProperty('status');
		$data['username'] = $this->getProperty('username');
		$data['context'] = $this->modx->context->key;
		if (empty($data['username'])) {
			$data['username'] = $data['email'];
		}

		if ($response = $this->modx->runProcessor('create',
			$data,
			array('processors_path' => dirname(dirname(__FILE__)) . '/misc/client/')
		)
		) {
			if ($response->isError()) {
				return $response->getResponse();
			}
		}

		return $this->success();
	}

}

return 'modMlmSystemClientCreateProcessor';