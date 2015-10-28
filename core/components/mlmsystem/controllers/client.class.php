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

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/change/type/type.window.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/change/type/type.grid.js');

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/log/log.window.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/log/log.grid.js');

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/client/client.window.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/client/client.grid.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/client/client.panel.js');

		$script = 'Ext.onReady(function() {
			MODx.load({ xtype: "mlmsystem-page-client"});
		});';
		$this->addHtml("<script type='text/javascript'>{$script}</script>");

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
