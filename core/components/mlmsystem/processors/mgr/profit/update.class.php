<?php

/**
 * Update an MlmSystemProfit
 */
class modMlmSystemProfitUpdateProcessor extends modObjectUpdateProcessor
{
	public $objectType = 'MlmSystemProfit';
	public $classKey = 'MlmSystemProfit';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** @var MlmSystem $MlmSystem */
	public $MlmSystem;
	protected $event;

	/** {@inheritDoc} */
	public function initialize()
	{
		/** @var mlmsystem $mlmsystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeSet()
	{
		foreach (array('event') as $v) {
			$this->$v = $this->object->get($v);
		}

		$id = (int)$this->getProperty('id');
		if (empty($id)) {
			return $this->modx->lexicon('mlmsystem_err_ns');
		}

		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$event = trim($this->getProperty('event'));
		if (empty($event)) {
			$this->modx->error->addField('event', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		if ($this->modx->getCount($this->classKey, array(
			'name' => $event,
			'id:!=' => $id
		))
		) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		return parent::beforeSet();
	}

	/** {@inheritDoc} */
	public function afterSave()
	{
		/* удаляем старый event */
		if (
			$this->object->get('event') != $this->event AND
			!$this->modx->getCount($this->classKey, array('event' => $this->event))
		) {
			$this->MlmSystem->setPluginEvent($this->event, 'remove');
		}

		if ($this->object->get('active')) {
			$this->MlmSystem->setPluginEvent($this->object->get('event'), 'create');
		} elseif (!$this->modx->getCount($this->classKey, array('event' => $this->object->get('event'), 'active' => 1))) {
			$this->MlmSystem->setPluginEvent($this->object->get('event'), 'remove');
		}

		return true;
	}

}

return 'modMlmSystemProfitUpdateProcessor';
