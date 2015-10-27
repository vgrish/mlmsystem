<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

class ControllersOperationManagerController extends MlmSystemMainController
{

	public static function getDefaultController()
	{
		return 'operation';
	}

}

class MlmSystemOperationManagerController extends MlmSystemMainController
{

	public function getPageTitle()
	{
		return $this->modx->lexicon('mlmsystem') . ' :: ' . $this->modx->lexicon('mlmsystem_operations');
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

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/type/type.window.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/type/type.grid.js');

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/operation/operation.grid.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/operation/operation.panel.js');

		$gridFields = $this->MlmSystem->Tools->getOperationFields();

		$this->addHtml(str_replace('			', '', '
			<script type="text/javascript">
				Ext.onReady(function() {
					mlmsystem.config.operation_grid_fields = ' . $this->modx->toJSON($gridFields) . ';
					MODx.load({ xtype: "mlmsystem-page-operation"});
				});
			</script>'
		));
		$this->modx->invokeEvent('MlmSystemOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'operation'));
	}

	public function getTemplateFile()
	{
		return $this->MlmSystem->config['templatesPath'] . 'operation.tpl';
	}

}

// MODX 2.3
class ControllersMgrOperationManagerController extends ControllersOperationManagerController
{

	public static function getDefaultController()
	{
		return 'operation';
	}

}

class MlmSystemMgrOperationManagerController extends MlmSystemOperationManagerController
{

}
