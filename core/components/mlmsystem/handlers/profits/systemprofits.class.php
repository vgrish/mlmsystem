<?php


interface MlmSystemProfitsInterface
{

	public function getProfit(MlmSystemProfit $instance, $cost = 0);

	public function getProfitIds($event = '', $active = 1);

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
	public function getProfit(MlmSystemProfit $instance, $cost = 0)
	{

		$profit = $instance->get('profit');
		$addProfit = $instance->get('add_profit');
		if (preg_match('/%$/', $addProfit)) {
			$addProfit = str_replace('%', '', $addProfit);
			$addProfit = $profit / 100 * $addProfit;
		}
		$profit += $addProfit;

		return $profit;
	}

	/** @inheritdoc} */
	public function getInitiator(array $scriptProperties = array())
	{
		$initiator = null;
		if ($user = $this->MlmSystem->getOption('user', $scriptProperties, $this->modx->user)) {
			$initiator = $user->getOne('MlmSystemClient');
		}
		return $initiator;
	}

	/** @inheritdoc} */
	public function getProfitIds($event = '', $active = 1)
	{
		$ids = array();
		$q = $this->modx->newQuery('MlmSystemProfit', array('event' => $event, 'active' => $active));
		$q->sortby('rank', 'ASC');
		$q->select('id');
		if ($q->prepare() && $q->stmt->execute()) {
			$ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
		}
		return $ids;
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