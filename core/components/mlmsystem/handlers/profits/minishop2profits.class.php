<?php

if (!class_exists('MlmSystemProfitsInterface')) {
	require_once dirname(__FILE__) . '/systemprofits.class.php';
}


class MiniShop2Profits extends SystemProfits implements MlmSystemProfitsInterface
{

	/** @var modX $modx */
	protected $modx;
	/** @var MlmSystem $MlmSystem */
	protected $MlmSystem;
	/** @var MiniShop2 $MiniShop2 */
	protected $MiniShop2;
	/** @var array $config */
	protected $config = array();


	public function __construct($MlmSystem, $config)
	{
		$this->MlmSystem = &$MlmSystem;
		$this->modx = &$MlmSystem->modx;
		$this->config =& $config;

		if ($this->MiniShop2 = $this->modx->getService('miniShop2')) {
			$this->MiniShop2->initialize($this->modx->context->key);
		} else {
			$this->MlmSystem->printLog('MiniShop2Profits requires installed miniShop2.', 1);
		}
	}

	/** @inheritdoc} */
	public function getInitiator(array $scriptProperties = array())
	{
		$initiator = null;
		if ($order = $this->MlmSystem->getOption('order', $scriptProperties, null)) {
			$initiator = $order->getOne('User')->getOne('MlmSystemClient');
		}
		return $initiator;
	}

}