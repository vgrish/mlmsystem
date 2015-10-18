<?php


interface MlmSystemToolsInterface
{

	public function getClientFields();

	public function getMenuActions();

	public function failure($message = '', $data = array(), $placeholders = array());

	public function success($message = '', $data = array(), $placeholders = array());

	public function printLog($message = '', $show = false);

}

class SystemTools implements MlmSystemToolsInterface
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
	 * @return array Client fields
	 */
	public function getClientFields()
	{
		$gridFields = array_map('trim', explode(',', $this->MlmSystem->getOption('client_grid_fields', null,
			'id,username,balance,status,createdon,updatedon,disabled', true)));
		$gridFields = array_values(array_unique(array_merge($gridFields, array(
			'id', 'username', 'disabled', 'deleted', 'properties', 'actions'))));
		return $gridFields;
	}

	/**
	 * @return array Menu Actions fields
	 */
	public function getMenuActions()
	{
		$key = $this->MlmSystem->namespace;
		$options = array(
			'cache_key' => $key . '/menu_actions',
			'cacheTime' => 0,
		);
		if (!$actions = $this->MlmSystem->getCache($options)) {
			$q = $this->modx->newQuery('modMenu', array('parent' => $key));
			$q->select('text,action');
			if ($q->prepare() && $q->stmt->execute()) {
				$arr = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($arr as $i) {
					$i['text'] = str_replace($key . '_', '', $i['text']);
					$actions[$i['text']] = $i['action'];
				}
			}
			$this->MlmSystem->setCache($actions, $options);
		}
		return (array)$actions;
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