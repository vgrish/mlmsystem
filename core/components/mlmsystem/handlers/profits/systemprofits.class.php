<?php


interface MlmSystemProfitsInterface
{
	public function getInitiator(array $scriptProperties = array());

	public function getProfit(MlmSystemProfit $instance, $cost = 0);

	public function setProfit(MlmSystemClient $client, $profit = 0);

	public function setDeposit(MlmSystemClient $client, $deposit = 0);

	public function getProfitIds($event = '', $active = 1);

	/** @inheritdoc} */
	public function getProfitUserGroups($id = 0);

	/** @inheritdoc} */
	public function getProfitResourceGroups($id = 0);

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
	public function getInitiator(array $scriptProperties = array())
	{
		$initiator = null;
		if ($user = $this->MlmSystem->getOption('user', $scriptProperties, $this->modx->user)) {
			$initiator = $user->getOne('MlmSystemClient');
		}
		return $initiator;
	}

	/** @inheritdoc} */
	public function getProfit(MlmSystemProfit $instance, $cost = 0)
	{
		$profit = $instance->get('profit');
		return $profit;
	}

	/** @inheritdoc} */
	public function setProfit(MlmSystemClient $client, $profit = 0)
	{
		if (
			empty($profit) OR
			in_array($client->getOne('status'), array($client->getStatusBlocked(), $client->getStatusRemoved()))
		) {
			return false;
		}
		$client->profitSum($profit);
		return $client->save();
	}

	/** @inheritdoc} */
	public function setDeposit(MlmSystemClient $client, $deposit = 0)
	{
		if (
			empty($deposit) OR
			in_array($client->getOne('status'), array($client->getStatusRemoved()))
		) {
			return false;
		}
		$client->depositSum($deposit);
		return $client->save();
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
	public function getProfitUserGroups($id = 0)
	{
		$groups = array();
		$key = $this->MlmSystem->namespace;
		$options = array(
			'cache_key' => $key . '/profit/group/' . __CLASS__ . '/user/' . $id,
			'cacheTime' => 0,
		);
		if (
			$this->modx->getObject('MlmSystemClient', array('id' => $id)) AND
			!$groups = $this->MlmSystem->getCache($options)
		) {
			$q = $this->modx->newQuery('modUserGroupMember', array('member' => $id));
			$q->leftJoin('MlmSystemProfitGroup', 'MlmSystemProfitGroup', 'MlmSystemProfitGroup.group = modUserGroupMember.user_group');
			$q->where(array('MlmSystemProfitGroup.class' => 'modUserGroup'));
			$q->select('user_group,profit');
			$q->sortby('profit');
			$tstart = microtime(true);
			if ($q->prepare() && $q->stmt->execute()) {
				$this->modx->queryTime += microtime(true) - $tstart;
				$this->modx->executedQueries++;
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					$groups[$row['user_group']] = $row['profit'];
				}
				$this->MlmSystem->setCache($groups, $options);
			}
		}
		return $groups;
	}

	/** @inheritdoc} */
	public function getProfitResourceGroups($id = 0)
	{
		$groups = array();
		$key = $this->MlmSystem->namespace;
		$options = array(
			'cache_key' => $key . '/profit/group/' . __CLASS__ . '/resource/' . $id,
			'cacheTime' => 0,
		);
		if (
			$resource = $this->modx->getObject('modResource', array('id' => $id)) AND
			!$groups = $this->MlmSystem->getCache($options)
		) {
			$ids = $this->modx->getParentIds($id, 10, array('context' => $resource->get('context_key')));
			$ids[] = $id;
			$ids = array_unique($ids);

			$q = $this->modx->newQuery('modResourceGroupResource', array('document:IN' => $ids));
			$q->leftJoin('MlmSystemProfitGroup', 'MlmSystemProfitGroup', 'MlmSystemProfitGroup.group = modResourceGroupResource.document_group');
			$q->where(array('MlmSystemProfitGroup.class' => 'modResourceGroup'));
			$q->select('document_group,profit');
			$q->sortby('profit');
			$q->groupby('MlmSystemProfitGroup.group');
			$tstart = microtime(true);
			if ($q->prepare() && $q->stmt->execute()) {
				$this->modx->queryTime += microtime(true) - $tstart;
				$this->modx->executedQueries++;
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					$groups[$row['document_group']] = $row['profit'];
				}
			}
			$this->MlmSystem->setCache($groups, $options);
		}
		return $groups;
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