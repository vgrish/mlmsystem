<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

class ControllersProfitManagerController extends MlmSystemMainController
{

	public static function getDefaultController()
	{
		return 'profit';
	}

}

class MlmSystemProfitManagerController extends MlmSystemMainController
{

	public function getPageTitle()
	{
		return $this->modx->lexicon('mlmsystem') . ' :: ' . $this->modx->lexicon('mlmsystem_profit');
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
		
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/profit/profit.window.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/profit/profit.grid.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/profit/profit.panel.js');

		$gridFields = $this->MlmSystem->Tools->getProfitFields();

		$this->addHtml(str_replace('			', '', '
			<script type="text/javascript">
				Ext.onReady(function() {
					mlmsystem.config.profit_grid_fields = ' . $this->modx->toJSON($gridFields) . ';
					MODx.load({ xtype: "mlmsystem-page-profit"});
				});
			</script>'
		));
		$this->modx->invokeEvent('MlmSystemOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'profit'));
	}

	public function getTemplateFile()
	{
		return $this->MlmSystem->config['templatesPath'] . 'profit.tpl';
	}

}

// MODX 2.3
class ControllersMgrProfitManagerController extends ControllersProfitManagerController
{

	public static function getDefaultController()
	{
		return 'profit';
	}

}

class MlmSystemMgrProfitManagerController extends MlmSystemProfitManagerController
{

}
