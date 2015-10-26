<?php

/**
 * Update an MlmSystemClient
 */
class modMlmSystemClientUpdateProcessor extends modObjectUpdateProcessor
{
	/** @var MlmSystemClient $object */
	public $object;
	public $classKey = 'MlmSystemClient';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	/** @var MlmSystem $MlmSystem */
	public $MlmSystem;
	public $prop = array();
	public $successMessage = '';
	protected $status;
	protected $parent;


	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		/** @var mlmsystem $mlmsystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeSet()
	{
		foreach (array('status', 'parent') as $v) {
			$this->$v = $this->object->get($v);
		}
		//$valid = $this->object->validate();

		$this->modx->log(1, print_r('beforeSet' ,1));

		return parent::beforeSet();
	}

	public function beforeSave()
	{
		/* проверка на кружева */
		if (
			$this->parent != $this->getProperty('parent', $this->object->get('parent'))
		) {
			if (!$this->MlmSystem->Tools->checkClientParent($this->object, $this->getProperty('parent'))) {
				$this->addFieldError('parent',$this->MlmSystem->lexicon('err_parent'));
			}
		}

		return parent::beforeSave();
	}

	public function afterSave()
	{
		/* выполняем уведомления */
		if (
			$this->object->get('status') != $this->status
		) {
			$this->MlmSystem->Tools->sendNotice($this->object);
		}

		/* генерируем пути */
		if (
			$this->object->get('parent') != $this->parent
		) {
			$this->MlmSystem->Paths->removePathItem($this->object->get('id'));
			$this->MlmSystem->Paths->GeneratePaths($this->object->get('id'));
		}

		return parent::afterSave();
	}

	/**
	 * Return the success message
	 * @return array
	 */
	public function cleanup()
	{
		return $this->success($this->successMessage, $this->object);
	}

}

return 'modMlmSystemClientUpdateProcessor';