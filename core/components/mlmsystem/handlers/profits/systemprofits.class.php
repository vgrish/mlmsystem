<?php


interface MlmSystemProfitsInterface
{


	public function runProcessor($action = '', $data = array(), $json = false);

	public function failure($message = '', $data = array(), $placeholders = array());

	public function success($message = '', $data = array(), $placeholders = array());

	public function printLog($message = '', $show = false);

}

class SystemProfits implements MlmSystemProfitsInterface
{

	/** @var modX $modx */
	protected $modx;
	/** @var MlmSystem $MlmSystem */
	protected $MlmSystem;
	/** @var array $config */
	protected $config = array();


	public function __construct($MlmSystem, $config)
	{
		$this->MlmSystem = &$MlmSystem;
		$this->modx = &$MlmSystem->modx;
		$this->config =& $config;
	}

	/**
	 * @param $n
	 * @param array $p
	 */
	public function __call($n, array$p)
	{
		echo __METHOD__ . ' says: ' . $n;
	}




	/** @inheritdoc} */
	public function runProcessor($action = '', $data = array(), $json = false)
	{
		return $this->MlmSystem->runProcessor($action, $data, $json);
	}

	/** @inheritdoc} */
	public function failure($message = '', $data = array(), $placeholders = array())
	{
		return $this->MlmSystem->failure($message, $data, $placeholders);
	}

	/** @inheritdoc} */
	public function success($message = '', $data = array(), $placeholders = array())
	{
		return $this->MlmSystem->success($message, $data, $placeholders);
	}

	/** @inheritdoc} */
	public function printLog($message = '', $show = false)
	{
		return $this->MlmSystem->printLog($message, $show);
	}

}