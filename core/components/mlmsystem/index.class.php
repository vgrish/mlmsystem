<?php

/**
 * Class mlmsystemMainController
 */
abstract class MlmSystemMainController extends modExtraManagerController
{
	/** @var MlmSystem $MlmSystem */
	public $MlmSystem;


	/**
	 * @return void
	 */
	public function initialize()
	{
		$corePath = $this->modx->getOption('mlmsystem_core_path', null, $this->modx->getOption('core_path') . 'components/mlmsystem/');
		require_once $corePath . 'model/mlmsystem/mlmsystem.class.php';

		$this->MlmSystem = new MlmSystem($this->modx);
		$this->MlmSystem->initialize($this->modx->context->key);

		$this->addCss($this->MlmSystem->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->MlmSystem->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addCss($this->MlmSystem->config['assetsUrl'] . 'vendor/fontawesome/css/font-awesome.min.css');

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/mlmsystem.js');

		$config = $this->MlmSystem->config;
		$config['connector_url'] = $this->MlmSystem->config['connectorUrl'];
		$config['menu_actions'] = $this->MlmSystem->Tools->getMenuActions();
		$config['client_status'] = $this->MlmSystem->Tools->getClientStatus();

		$config['client_grid_fields'] = $this->MlmSystem->Tools->getClientFields();
		$config['client_window_update_tabs'] = $this->MlmSystem->Tools->getClientWindowUpdateTabs();

		$config['profit_grid_fields'] = $this->MlmSystem->Tools->getProfitFields();
		$config['profit_window_update_tabs'] = $this->MlmSystem->Tools->getProfitWindowUpdateTabs();

		$config['operation_grid_fields'] = $this->MlmSystem->Tools->getOperationFields();

		$config['log_grid_fields'] = $this->MlmSystem->Tools->getLogFields();
		$config['log_window_view_tabs'] = $this->MlmSystem->Tools->getLogWindowViewTabs();

		$this->addHtml("<script type='text/javascript'>mlmsystem.config={$this->modx->toJSON($config)}</script>");

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics()
	{
		return array('mlmsystem:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions()
	{
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends MlmSystemMainController
{

	/**
	 * @return string
	 */
	public static function getDefaultController()
	{
		return 'client';
	}
}