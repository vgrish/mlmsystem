<?php


interface MlmSystemToolsInterface
{

	public function processObject(xPDOObject $instance, $format = false, $replace = true, $keyPrefix = '');

	public function processTags($html, $maxIterations = 10);

	public function sendNotice(xPDOObject $instance, $sendStatus = 0);

	public function changeClientStatus(MlmSystemClient $client, $status = 0);

	public function getClientFields();

	public function getMenuActions();

	public function getClientStatus();

	public function getPropertiesKey(array $properties = array());

	public function saveProperties(array $properties = array());

	public function getProperties($key);

	public function runProcessor($action = '', $data = array(), $json = false);

	public function failure($message = '', $data = array(), $placeholders = array());

	public function success($message = '', $data = array(), $placeholders = array());

	public function printLog($message = '', $show = false);

	public function formatHashReferrer($id = 0);

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

	/* подготовка Обьекта... */
	public function processObject(xPDOObject $instance, $format = false, $replace = true, $keyPrefix = '')
	{
		$pls = $instance->toArray();

		switch (true) {
			case $instance instanceof MlmSystemClient:
				$pls['date_createdon'] = $pls['createdon'];
				$pls['date_updatedon'] = $pls['updatedon'];
				$pls['hash_referrer'] = $pls['id'];
				break;
			case $instance instanceof MlmSystemStatus:
				break;
			case $instance instanceof MlmSystemEmail:
				break;
			case $instance instanceof MlmSystemLog:
				break;
			case $instance instanceof modUser:
				unset(
					$pls['cachepwd'],
					$pls['class_key'],
					$pls['remote_key'],
					$pls['remote_data'],
					$pls['hash_class'],
					$pls['password'],
					$pls['salt']
				);
				break;

			default:
				break;
		}

		/* TODO подготовка полей */

		if ($format) {
			while (list($key, $val) = each($pls)) {
				$keyMethod = 'format' . ucfirst(str_replace('_', '', $key));
				if (!method_exists($this, $keyMethod)) {
					continue;
				}
				if ($replace) {
					$pls[$key] = $this->$keyMethod($val);
				} else {
					$pls['format_' . $key] = $this->$keyMethod($val);
				}
			}
		}

		if (!empty($keyPrefix)) {
			$plsPrefix = array();
			foreach ($pls as $key => $value) {
				$plsPrefix[$keyPrefix . $key] = $value;
			}
			$pls = $plsPrefix;
		}

		return $pls;
	}

	/**
	 * Collects and processes any set of tags
	 *
	 * @param mixed $html Source code for parse
	 * @param integer $maxIterations
	 *
	 * @return mixed $html Parsed html
	 */
	public function processTags($html, $maxIterations = 10)
	{
		if (strpos($html, '[[') !== false) {
			$this->modx->getParser()->processElementTags('', $html, false, false, '[[', ']]', array(), $maxIterations);
			$this->modx->getParser()->processElementTags('', $html, true, true, '[[', ']]', array(), $maxIterations);
		}
		return $html;
	}

	/** @inheritdoc} */
	public function sendNotice(xPDOObject $instance, $status = 0)
	{

		$this->modx->log(1, print_r('sendNotice sendNotice', 1));

		if (!$status) {
			$status = $instance->getOne('Status');
		} else {
			$status = $this->modx->getObject('MlmSystemStatus', $status);
		}
		if (!$status OR !$this->MlmSystem->getOption('mail_notice', null, false)) {
			return false;
		}

		/* get context */
		if (!$context = $instance->get('context')) {
			$context = !$this->modx->context->key || $this->modx->context->key == 'mgr' ? 'web' : $this->modx->context->key;
		}

		/* get users */
		$user = array();
		switch (true) {
			case $instance instanceof MlmSystemClient:
				$user[] = $instance->get('id');
				break;
			default:
				break;
		}

		$pls = array(
			'listUser' => '',
			'listEmail' => '',
			'subjectEmail' => '',
			'bodyEmail' => '',
			'queueEmail' => false,
			'getUser' => false,
			'formatField' => true,
			'fastMode' => true,
			'context' => $context,
			'addPls' => array()
		);

		if ($status->get('email_user')) {
			if ($chunk = $this->modx->getObject('modChunk', $status->get('tpl_user'))) {

				$plsWork = $pls;
				$plsWork['listUser'] = implode(',', $user);

				if ($properties = $chunk->getProperties()) {
					foreach ($properties as $k => $v) {
						if (!isset($plsWork[$k])) {
							$plsWork[$k] = $v;
						} elseif (is_string($plsWork[$k]) AND !empty($plsWork[$k])) {
							$plsWork[$k] .= ',' . $v;
						} elseif (is_string($plsWork[$k]) AND empty($plsWork[$k])) {
							$plsWork[$k] = $v;
						} elseif (is_array($plsWork[$k])) {
							$plsWork[$k] = array_merge($this->modx->fromJSON($v), $plsWork[$k]);
						} elseif (is_bool($plsWork[$k])) {
							$plsWork[$k] = $v;
						}
					}
				}

				$plsWork['addPls'] = array_merge($plsWork['addPls'], $this->processObject($instance, (int)$plsWork['formatField']));
				$this->runProcessor('mgr/email/send', $plsWork);
			}
		}

		if ($status->get('email_manager')) {
			if ($chunk = $this->modx->getObject('modChunk', $status->get('tpl_manager'))) {

				$plsWork = $pls;
				$plsWork['listEmail'] = $this->MlmSystem->getOption('email_manager', null, $this->modx->getOption('emailsender'));

				if ($properties = $chunk->getProperties()) {
					foreach ($properties as $k => $v) {
						if (!isset($plsWork[$k])) {
							$plsWork[$k] = $v;
						} elseif (is_string($plsWork[$k]) AND !empty($plsWork[$k])) {
							$plsWork[$k] .= ',' . $v;
						} elseif (is_string($plsWork[$k]) AND empty($plsWork[$k])) {
							$plsWork[$k] = $v;
						} elseif (is_array($plsWork[$k])) {
							$plsWork[$k] = array_merge($this->modx->fromJSON($v), $plsWork[$k]);
						} elseif (is_bool($plsWork[$k])) {
							$plsWork[$k] = $v;
						}
					}
				}

				$plsWork['addPls'] = array_merge($plsWork['addPls'], $this->processObject($instance, (int)$plsWork['formatField']));
				$this->runProcessor('mgr/email/send', $plsWork);
			}
		}

		return true;
	}

	/** @inheritdoc} */
	public function changeClientStatus(MlmSystemClient $client, $status = 0)
	{
		$data = array(
			'id' => $client->get('id'),
			'field_name' => 'status',
			'field_value' => $status,
		);

		$response = $this->runProcessor('mgr/client/setproperty', $data, $json = false);
		if (empty($response['success'])) {
			return $this->MlmSystem->lexicon('err_change_status');
		}

		return !empty($response['success']);
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
	public function getClientStatus()
	{
		$statuses = array();
		$q = $this->modx->newQuery('MlmSystemStatus', array('class' => 'MlmSystemClient', 'active' => 1));
		$q->sortby('rank', 'ASC');
		$q->select('id');
		if ($q->prepare() && $q->stmt->execute()) {
			$statuses = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
		}
		return $statuses;
	}

	
	

	/** @inheritdoc} */
	public function getPropertiesKey(array $properties = array())
	{
		$fields = array('snippetName', 'context', 'class', 'frontendMainCss', 'frontendMainJs', 'frontendCss', 'frontendJs');

		$hashValues = array();
		foreach ($fields as $field) {
			$hashValues[] = (isset($properties[$field])) ? $properties[$field] : '';
		}
		return md5(implode('#', $hashValues));
	}

	/** @inheritdoc} */
	public function saveProperties(array $properties = array())
	{
		if (!isset($properties['form_key'])) {
			$properties['form_key'] = $this->getPropertiesKey($properties);
		}
		$this->MlmSystem->config['form_key'] = $properties['form_key'];
		$_SESSION[$this->MlmSystem->namespace][$properties['form_key']] = $properties;
	}

	/** @inheritdoc} */
	public function getProperties($key)
	{
		$properties = array();
		if (isset($_SESSION[$this->MlmSystem->namespace][$key])) {
			$properties = $_SESSION[$this->MlmSystem->namespace][$key];
		} else {
			$this->printLog('Could not get properties for key: ' . $key, 1);
		}
		return $properties;
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




	/*
	 * FORMAT
	 */

	/** @inheritdoc} */
	public function formatHashReferrer($id = 0)
	{
		$hashValues[] = $id;
		$hashValues[] = $this->MlmSystem->getOption('referrer_salt', null, '');

		return md5(implode('#', $hashValues));
	}
}