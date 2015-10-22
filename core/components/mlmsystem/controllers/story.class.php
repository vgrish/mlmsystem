<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

class ControllersStoryManagerController extends MlmSystemMainController
{

	public static function getDefaultController()
	{
		return 'story';
	}

}

class MlmSystemStoryManagerController extends MlmSystemMainController
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

		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/story/story.grid.js');
		$this->addJavascript($this->MlmSystem->config['jsUrl'] . 'mgr/story/story.panel.js');

		$gridFields = $this->MlmSystem->Tools->getStoryFields();

		$this->addHtml(str_replace('			', '', '
			<script type="text/javascript">
				Ext.onReady(function() {
					mlmsystem.config.story_grid_fields = ' . $this->modx->toJSON($gridFields) . ';
					MODx.load({ xtype: "mlmsystem-page-story"});
				});
			</script>'
		));
		$this->modx->invokeEvent('MlmSystemOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'story'));
	}

	public function getTemplateFile()
	{
		return $this->MlmSystem->config['templatesPath'] . 'story.tpl';
	}

}

// MODX 2.3
class ControllersMgrStoryManagerController extends ControllersStoryManagerController
{

	public static function getDefaultController()
	{
		return 'story';
	}

}

class MlmSystemMgrStoryManagerController extends MlmSystemStoryManagerController
{

}
