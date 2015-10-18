<?php
require_once(dirname(__FILE__) . '/update.class.php');

/**
 * Update FromGrid a PaymentSystemClient
 */
class modPaymentSystemClientFromGridProcessor extends modPaymentSystemClientUpdateProcessor
{
	public $classKey = 'PaymentSystemClient';

	/** {@inheritDoc} */
	public static function getInstance(modX &$modx, $className, $properties = array())
	{
		/** @var modProcessor $processor */
		$processor = new modPaymentSystemClientFromGridProcessor($modx, $properties);
		return $processor;
	}

	/** {@inheritDoc} */
	public function initialize()
	{
		$data = $this->getProperty('data');
		if (empty($data)) {
			return $this->modx->lexicon('invalid_data');
		}
		$data = $this->modx->fromJSON($data);
		if (empty($data)) {
			return $this->modx->lexicon('invalid_data');
		}
		$this->setProperties($data);
		$this->unsetProperty('data');
		return parent::initialize();
	}
}

return 'modPaymentSystemClientFromGridProcessor';