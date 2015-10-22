<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

class ControllersClientManagerController extends MlmSystemMainController
{

	public static function getDefaultController()
	{
		return 'client';
	}

}

class MlmSystemClientManagerController extends MlmSystemMainController
{

	public function getPageTitle()
	{
		return $this->modx->lexicon('mlmsystem') . ' :: ' . $this->modx->lexicon('mlmsystem_client');
	}

	public function getLanguageTopics()
	{
		return array('mlmsystem:default');
	}

	public function loadCustomCssJs()
	{
		$this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/misc/mlmsystem.utils.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/misc/mlmsystem.combo.js');

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/status/status.window.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/status/status.grid.js');

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/client/client.window.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/client/client.grid.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/client/client.panel.js');

		$gridFields = $this->MlmSystem->Tools->getClientFields();
		$windowUpdateTabs = $this->MlmSystem->Tools->getClientWindowUpdateTabs();

		$this->addHtml(str_replace('			', '', '
			<script type="text/javascript">
				Ext.onReady(function() {
					mlmsystem.config.client_grid_fields = ' . $this->modx->toJSON($gridFields) . ';
					mlmsystem.config.client_window_update_tabs = ' . $this->modx->toJSON($windowUpdateTabs) . ';
					MODx.load({ xtype: "mlmsystem-page-client"});
				});
			</script>'
		));
		$this->modx->invokeEvent('MlmSystemOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'client'));
	}

	public function getTemplateFile()
	{
		return $this->MlmSystem->config['templatesPath'] . 'client.tpl';
	}

}

// MODX 2.3
class ControllersMgrClientManagerController extends ControllersClientManagerController
{

	public static function getDefaultController()
	{
		return 'client';
	}

}

class MlmSystemMgrClientManagerController extends MlmSystemClientManagerController
{

}
