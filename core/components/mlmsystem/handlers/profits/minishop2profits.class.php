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
			$resource = $this->modx->getObject('msProduct', array('id' => $id)) AND
			!$groups = $this->MlmSystem->getCache($options)
		) {
			$ids = $this->modx->getParentIds($id, 10, array('context' => $resource->get('context_key')));
			$ids[] = $id;
			$ids = array_unique($ids);

			$q = $this->modx->newQuery('msCategoryMember', array('product_id' => $id));
			$q->select('category_id');
			$tstart = microtime(true);
			if ($q->prepare() && $q->stmt->execute()) {
				$this->modx->queryTime += microtime(true) - $tstart;
				$this->modx->executedQueries++;
				if ($tmp = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
					$ids = array_merge($ids, $tmp);
				}
			}
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

}