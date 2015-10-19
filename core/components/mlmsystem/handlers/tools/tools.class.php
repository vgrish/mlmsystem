<?php


interface MlmSystemToolsInterface
{

	public function processObject(xPDOObject $instance, $format = false, $replace = true, $keyPrefix = '');

	public function processTags($html, $maxIterations = 10);

	public function sendNotice(xPDOObject $instance, $sendStatus = 0);

	public function changeClientStatus(MlmSystemClient $client, $status = 0);

	public function changeClientParent(MlmSystemClient $client, $parent = 0);

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


	public function declension($count, $forms, $lang = null);

	public function dateFormat($date, $dateFormat = null);

	public function sumFormat($sum = '0', array $pf = array(), $noZeros = true);

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
				$pls['url_referrer'] = $pls['id'];
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

	/** @inheritdoc} */
	public function changeClientParent(MlmSystemClient $client, $parent = 0)
	{
		$data = array(
			'id' => $client->get('id'),
			'field_name' => 'parent',
			'field_value' => $parent,
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


	/**
	 * Declension of words
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/eca10c0186c8174b774a2125d8af3760e1c34825/engine/modules/viewer/plugs/modifier.declension.php
	 *
	 * @param int $count
	 * @param string $forms
	 * @param string $lang
	 *
	 * @return string
	 */
	public function declension($count, $forms, $lang = null)
	{
		if (empty($lang)) {
			$lang = $this->modx->getOption('cultureKey', null, 'en');
		}
		$forms = $this->modx->fromJSON($forms);
		if ($lang == 'ru') {
			$mod100 = $count % 100;
			switch ($count % 10) {
				case 1:
					if ($mod100 == 11) {
						$text = $forms[2];
					} else {
						$text = $forms[0];
					}
					break;
				case 2:
				case 3:
				case 4:
					if (($mod100 > 10) && ($mod100 < 20)) {
						$text = $forms[2];
					} else {
						$text = $forms[1];
					}
					break;
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 0:
				default:
					$text = $forms[2];
			}
		} else {
			if ($count == 1) {
				$text = $forms[0];
			} else {
				$text = $forms[1];
			}
		}
		return $text;
	}

	/**
	 * Formats date to "10 minutes ago" or "Yesterday in 22:10"
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/7a6039b21c326acf03c956772325e1398801c5fe/engine/modules/viewer/plugs/function.date_format.php
	 *
	 * @param string $date Timestamp to format
	 * @param string $dateFormat
	 *
	 * @return string
	 */
	public function dateFormat($date, $dateFormat = null)
	{

		//print_r($date);die;

		$date = preg_match('/^\d+$/', $date)
			? $date
			: strtotime($date);
		$dateFormat = !empty($dateFormat)
			? $dateFormat
			: $this->MlmSystem->getOption('format_date');
		$current = time();
		$delta = $current - $date;
		if ($this->MlmSystem->getOption('format_date_now')) {
			if ($delta < $this->MlmSystem->getOption('format_date_now')) {
				return $this->modx->lexicon('mlmsystem_date_now');
			}
		}
		if ($this->MlmSystem->getOption('format_date_minutes')) {
			$minutes = round(($delta) / 60);
			if ($minutes < $this->MlmSystem->getOption('format_date_minutes')) {
				if ($minutes > 0) {
					return $this->declension($minutes, $this->modx->lexicon('mlmsystem_date_minutes_back', array('minutes' => $minutes)));
				} else {
					return $this->modx->lexicon('mlmsystem_date_minutes_back_less');
				}
			}
		}
		if ($this->MlmSystem->getOption('format_date_hours')) { //
			$hours = round(($delta) / 3600);
			if ($hours < $this->MlmSystem->getOption('format_date_hours')) {
				if ($hours > 0) {
					return $this->declension($hours, $this->modx->lexicon('mlmsystem_date_hours_back', array('hours' => $hours)));
				} else {
					return $this->modx->lexicon('mlmsystem_date_hours_back_less');
				}
			}
		}
		if ($this->MlmSystem->getOption('format_date_day')) {
			switch (date('Y-m-d', $date)) {
				case date('Y-m-d'):
					$day = $this->modx->lexicon('mlmsystem_date_today');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))):
					$day = $this->modx->lexicon('mlmsystem_date_yesterday');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))):
					$day = $this->modx->lexicon('mlmsystem_date_tomorrow');
					break;
				default:
					$day = null;
			}
			if ($day) {
				$format = str_replace("day", preg_replace("#(\w{1})#", '\\\${1}', $day), $this->MlmSystem->getOption('format_date_day'));
				return date($format, $date);
			}
		}
		$m = date("n", $date);
		$month_arr = $this->modx->fromJSON($this->modx->lexicon('mlmsystem_date_months'));
		$month = $month_arr[$m - 1];
		$format = preg_replace("~(?<!\\\\)F~U", preg_replace('~(\w{1})~u', '\\\${1}', $month), $dateFormat);
		return date($format, $date);
	}

	/**
	 * Function for formatting sum
	 *
	 * @param string $sum
	 * @param array $pf
	 * @param bool $noZeros
	 * @return mixed|string
	 */
	public function sumFormat($sum = '0', array $pf = array(), $noZeros = true)
	{
		if (empty($pf)) {
			$pf = array(2, '.', ' ');
		}
		if (is_array($pf)) {
			$sum = number_format($sum, $pf[0], $pf[1], $pf[2]);
		}
		if ($noZeros) {
			if (preg_match('/\..*$/', $sum, $matches)) {
				$tmp = rtrim($matches[0], '.0');
				$sum = str_replace($matches[0], $tmp, $sum);
			}
		}
		return $sum;
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

	/** @inheritdoc} */
	public function formatUrlReferrer($id = 0)
	{
		$clientKey = $this->MlmSystem->getOption('client_key', null, 'rclient');
		$referrerKey = $this->MlmSystem->getOption('referrer_key', null, 'rhash');
		$contextKey = $this->MlmSystem->getOption('ctx', null, $this->MlmSystem->getOption('referrer_context'), true);
		$referrerPage = $this->MlmSystem->getOption('referrer_page', null, $this->modx->getOption('site_start'));
		if (empty($referrerPage)) {
			$referrerPage = $this->modx->getOption('site_start');
		}

		$params = array(
			$clientKey => $id,
			$referrerKey => $this->formatHashReferrer($id)
		);

		$url = $this->modx->makeUrl($referrerPage, $contextKey, $params, 'full');
		return $url;
	}
	
	/** @inheritdoc} */
	public function formatBalance($sum = '0')
	{
		$pf = $this->modx->fromJSON($this->MlmSystem->getOption('format_balance', null, '[2, ".", " "]'));
		$noZeros = $this->MlmSystem->getOption('format_balance_no_zeros', null, true);

		return $this->sumFormat($sum, $pf, $noZeros);
	}

	/** @inheritdoc} */
	public function formatIncoming($sum = '0')
	{
		$pf = $this->modx->fromJSON($this->MlmSystem->getOption('format_incoming', null, '[2, ".", " "]'));
		$noZeros = $this->MlmSystem->getOption('format_incoming_no_zeros', null, true);

		return $this->sumFormat($sum, $pf, $noZeros);
	}

	/** @inheritdoc} */
	public function formatOutcoming($sum = '0')
	{
		$pf = $this->modx->fromJSON($this->MlmSystem->getOption('format_outcoming', null, '[2, ".", " "]'));
		$noZeros = $this->MlmSystem->getOption('format_outcoming_no_zeros', null, true);

		return $this->sumFormat($sum, $pf, $noZeros);
	}

	public function formatDateCreatedon($date)
	{
		return $this->dateFormat($date);
	}

	public function formatDateUpdatedon($date)
	{
		return $this->dateFormat($date);
	}

}