<?php

/**
 * Create an MlmSystemProfitGroup
 */
class modMlmSystemProfitGroupCreateProcessor extends modObjectCreateProcessor
{
	public $objectType = 'MlmSystemProfitGroup';
	public $classKey = 'MlmSystemProfitGroup';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** @var MlmSystem $MlmSystem */
	public $MlmSystem;

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
		$identifier = trim($this->getProperty('identifier'));
		if (empty($identifier)) {
			$this->modx->error->addField('identifier', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$class = trim($this->getProperty('class'));
		if (empty($class)) {
			$this->modx->error->addField('class', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$group = trim($this->getProperty('group'));
		if (empty($group)) {
			$this->modx->error->addField('group', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		if ($this->modx->getCount($this->classKey, array(
			'identifier' => $identifier,
			'class' => $class,
			'group' => $group,
		))
		) {
			$this->modx->error->addField('identifier', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'modMlmSystemProfitGroupCreateProcessor';