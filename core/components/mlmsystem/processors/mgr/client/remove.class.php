<?php

/**
 * Remove a PaymentSystemClient
 */
class modPaymentSystemClientRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'PaymentSystemClient';
	public $languageTopics = array('paymentsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeRemove()
	{
/*		if (!$this->object->get('editable')) {
			$this->failure($this->modx->lexicon('paymentsystem_err_lock'));
		}*/
		return parent::beforeRemove();
	}
}

return 'modPaymentSystemClientRemoveProcessor';