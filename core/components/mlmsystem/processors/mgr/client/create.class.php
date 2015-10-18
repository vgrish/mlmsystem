<?php

/**
 * Create an PaymentSystemClient
 */
class modPaymentSystemClientCreateProcessor extends modProcessor
{
	public $classKey = 'PaymentSystemClient';
	public $languageTopics = array('paymentsystem');
	public $permission = '';

	public $PaymentSystem;

	public function initialize()
	{
		/** @var paymentsystem $paymentsystem */
		$this->PaymentSystem = $this->modx->getService('paymentsystem');
		$this->PaymentSystem->initialize($this->getProperty('context', $this->modx->context->key));

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

return 'modPaymentSystemClientCreateProcessor';