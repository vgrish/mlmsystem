<?php

/**
 * Class mlmsystemMainController
 */
abstract class MlmSystemMainController extends modExtraManagerController {
	/** @var MlmSystem $MlmSystem */
	public $MlmSystem;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('mlmsystem_core_path', null, $this->modx->getOption('core_path') . 'components/mlmsystem/');
		require_once $corePath . 'model/mlmsystem/mlmsystem.class.php';

		$this->MlmSystem = new MlmSystem($this->modx);
		$this->MlmSystem->initialize($this->modx->context->key);

		$menuActions = $this->MlmSystem->Tools->getMenuActions();

		$this->addCss($this->MlmSystem->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/mlmsystem.js');
		$this->addHtml('
		<script type="text/javascript">
			mlmsystem.config = ' . $this->modx->toJSON($this->MlmSystem->config) . ';
			mlmsystem.config.connector_url = "' . $this->MlmSystem->config['connectorUrl'] . '";
			mlmsystem.config.menu_actions = ' . $this->modx->toJSON($menuActions) . ';
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('mlmsystem:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends MlmSystemMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'client';
	}
}