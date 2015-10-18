<?php

/**
 * The base class for mlmsystem.
 */
class MlmSystem {

	/* @var modX $modx */
	public $modx;
	public $namespace = 'mlmsystem';

	/* @var array The array of config */
	public $config = array();
	/** @var array $initialized */
	public $initialized = array();

	/** @var Tools $Tools */
	public $Tools;

	/* @var pdoTools $pdoTools */
	public $pdoTools;

	public $authenticated = false;


	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('mlmsystem_core_path', $config, $this->modx->getOption('core_path') . 'components/mlmsystem/');
		$assetsUrl = $this->modx->getOption('mlmsystem_assets_url', $config, $this->modx->getOption('assets_url') . 'components/mlmsystem/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',
			'handlersPath' => $corePath . 'handlers/',

			'frontendMainCss' => $this->getOption('frontend_main_css'),
			'frontendMainJs' => $this->getOption('frontend_main_js'),
			'showLog' => $this->getOption('show_log', null, false),
			'jsonResponse' => true,
			'prepareResponse' => true,
			'nestedChunkPrefix' => 'mlmsystem_',

		), $config);

		$this->modx->addPackage('mlmsystem', $this->config['modelPath']);
		$this->modx->lexicon->load('mlmsystem:default');
		$this->namespace = $this->getOption('namespace', $config, 'mlmsystem');
		$this->authenticated = $this->modx->user->isAuthenticated($this->modx->context->get('key'));
	}

	/**
	 * @param $n
	 * @param array $p
	 */
	public function __call($n, array$p)
	{
		echo __METHOD__ . ' says: ' . $n;
	}

	/**
	 * @param $key
	 * @param array $config
	 * @param null $default
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null)
	{
		$option = $default;
		if (!empty($key) AND is_string($key)) {
			if ($config != null AND array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}_{$key}");
			}
		}
		return $option;
	}

	/**
	 * Initializes component into different contexts.
	 *
	 * @param string $ctx The context to load. Defaults to web.
	 * @param array $scriptProperties
	 *
	 * @return boolean
	 */
	public function initialize($ctx = 'web', $scriptProperties = array())
	{
		$this->config = array_merge($this->config, $scriptProperties);
		$this->config['ctx'] = $ctx;
		if (!empty($this->initialized[$ctx])) {
			return true;
		}

		if (!$this->Tools) {
			$this->loadTools();
		}

		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}
		$this->pdoTools->setConfig($this->config);

		switch ($ctx) {
			case 'mgr':
				break;
			default:
				if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {

					$this->initialized[$ctx] = true;

				}
				break;
		}

		return true;
	}

	/**
	 * Loads an instance of pdoTools
	 * @return boolean
	 */
	public function loadPdoTools()
	{
		if (!is_object($this->pdoTools) OR !($this->pdoTools instanceof pdoTools)) {
			/** @var pdoFetch $pdoFetch */
			$fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
			if ($pdoClass = $this->modx->loadClass($fqn, '', false, true)) {
				$this->pdoTools = new $pdoClass($this->modx, $this->config);
			} elseif ($pdoClass = $this->modx->loadClass($fqn, MODX_CORE_PATH . 'components/pdotools/model/', false, true)) {
				$this->pdoTools = new $pdoClass($this->modx, $this->config);
			} else {
				$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not load pdoFetch from "MODX_CORE_PATH/components/pdotools/model/".');
			}
		}
		return !empty($this->pdoTools) AND $this->pdoTools instanceof pdoTools;
	}

	/**
	 * Loads an instance of Tools
	 * @return boolean
	 */
	public function loadTools()
	{
		if (!is_object($this->Tools) OR !($this->Tools instanceof MlmSystemToolsInterface)) {
			$toolsClass = $this->modx->loadClass('tools.Tools', $this->config['handlersPath'], true, true);
			if ($derivedClass = $this->getOption('handler_class_tools', null, '')) {
				if ($derivedClass = $this->modx->loadClass('tools.' . $derivedClass, $this->config['handlersPath'], true, true)) {
					$toolsClass = $derivedClass;
				}
			}
			if ($toolsClass) {
				$this->Tools = new $toolsClass($this, $this->config);
			}
		}
		return !empty($this->Tools) AND $this->Tools instanceof MlmSystemToolsInterface;
	}
	
	/**
	 * Process and return the output from a Chunk by name.
	 *
	 * @param string $name The name of the chunk.
	 * @param array $properties An associative array of properties to process the Chunk with, treated as placeholders within the scope of the Element.
	 * @param boolean $fastMode If false, all MODX tags in chunk will be processed.
	 *
	 * @return string The processed output of the Chunk.
	 */
	public function getChunk($name, array $properties = array(), $fastMode = false)
	{
		if (!$this->modx->parser) {
			$this->modx->getParser();
		}
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}
		return $this->pdoTools->getChunk($name, $properties, $fastMode);
	}

	public function getCache($options = array())
	{
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}
		return $this->pdoTools->getCache($options);
	}

	public function setCache($data = array(), $options = array())
	{
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}
		return $this->pdoTools->setCache($data, $options);
	}
	
	public function setJsonResponse($json = true)
	{
		return ($this->config['jsonResponse'] = $json);
	}
	
	/**
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 * @return array|string
	 */
	public function failure($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => false,
			'message' => $this->lexicon($message, $placeholders),
			'data' => $data,
		);
		return $this->config['jsonResponse']
			? $this->modx->toJSON($response)
			: $response;
	}

	/**
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => true,
			'message' => $this->lexicon($message, $placeholders),
			'data' => $data,
		);
		return $this->config['jsonResponse']
			? $this->modx->toJSON($response)
			: $response;
	}

	public function printLog($message = '', $show = false)
	{
		if (($show OR !empty($this->config['showLog'])) AND !empty($message)) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, print_r('[' . $this->namespace . '] ' . (($show) ? 'show' : ''), 1));
			$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($message, 1));
		}
		return true;
	}
	
}