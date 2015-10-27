<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

class ControllersLogManagerController extends MlmSystemMainController
{

	public static function getDefaultController()
	{
		return 'log';
	}

}

class MlmSystemLogManagerController extends MlmSystemMainController
{

	public function getPageTitle()
	{
		return $this->modx->lexicon('mlmsystem') . ' :: ' . $this->modx->lexicon('mlmsystem_stories');
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

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/log/log.grid.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/log/log.panel.js');

		$gridFields = $this->MlmSystem->Tools->getLogFields();

		$this->addHtml(str_replace('			', '', '
			<script type="text/javascript">
				Ext.onReady(function() {
					mlmsystem.config.log_grid_fields = ' . $this->modx->toJSON($gridFields) . ';
					MODx.load({ xtype: "mlmsystem-page-log"});
				});
			</script>'
		));
		$this->modx->invokeEvent('MlmSystemOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'log'));
	}

	public function getTemplateFile()
	{
		return $this->MlmSystem->config['templatesPath'] . 'log.tpl';
	}

}

// MODX 2.3
class ControllersMgrLogManagerController extends ControllersLogManagerController
{

	public static function getDefaultController()
	{
		return 'log';
	}

}

class MlmSystemMgrLogManagerController extends MlmSystemLogManagerController
{

}
