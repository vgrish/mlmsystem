<?php

/**
 * Update an PaymentSystemClient
 */
class modPaymentSystemClientUpdateProcessor extends modObjectUpdateProcessor
{
	public $classKey = 'PaymentSystemClient';
	public $languageTopics = array('paymentsystem');
	public $permission = '';

	public $PaymentSystem;
	public $prop = array();
	public $successMessage = '';
	protected $status;

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		/** @var paymentsystem $paymentsystem */
		$this->PaymentSystem = $this->modx->getService('paymentsystem');
		$this->PaymentSystem->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeSet()
	{
		foreach (array('status') as $v) {
			$this->$v = $this->object->get($v);
		}

		$valid = $this->object->validate();
//		if (!$valid) {
//			$this->modx->log(1, print_r('NOT VALID', 1));
//		} else {
//			$this->modx->log(1, print_r('VALID', 1));
//		}

		return parent::beforeSet();
	}

	public function afterSave()
	{

		/* выполняем уведомления */
		if (
			$this->object->get('status') != $this->status)
		{
			$this->PaymentSystem->Tools->sendNotice($this->object);
		}

		/* выполняем обработчики */
		if (
			$this->object->get('status') != $this->status
		) {
			$this->PaymentSystem->Tools->makeHandlers($this->object);
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

return 'modPaymentSystemClientUpdateProcessor';