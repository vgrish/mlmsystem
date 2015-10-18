<?php

/**
 * Get an PaymentSystemClient
 */
class modPaymentSystemClientGetProcessor extends modObjectGetProcessor
{
	public $objectType = 'PaymentSystemClient';
	public $classKey = 'PaymentSystemClient';
	public $languageTopics = array('paymentsystem');
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

return 'modPaymentSystemClientGetProcessor';