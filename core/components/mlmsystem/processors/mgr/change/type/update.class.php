<?php

/**
 * Update an MlmSystemTypeChanges
 */
class modMlmSystemTypeChangesUpdateProcessor extends modObjectUpdateProcessor
{
	public $objectType = 'MlmSystemTypeChanges';
	public $classKey = 'MlmSystemTypeChanges';
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
	public function beforeSave()
	{
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}

	/** {@inheritDoc} */
	public function beforeSet()
	{
		$id = (int)$this->getProperty('id');
		if (empty($id)) {
			return $this->modx->lexicon('mlmsystem_err_ns');
		}

		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$field = trim($this->getProperty('field'));
		if (empty($field)) {
			$this->modx->error->addField('field', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$class = trim($this->getProperty('class'));
		if (empty($class)) {
			$this->modx->error->addField('class', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		$mode = trim($this->getProperty('mode'));
		if (empty($mode)) {
			$this->modx->error->addField('mode', $this->modx->lexicon('mlmsystem_err_ae'));
		}

		if ($this->modx->getCount($this->classKey, array(
			'class' => $class,
			'field' => $field,
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
		$class = $this->object->get('class');

		$fields = array();
		$q = $this->modx->newQuery('MlmSystemTypeChanges', array('class' => $class, 'active' => 1));
		$q->sortby('rank', 'ASC');
		$q->select('id,field');
		if ($q->prepare() && $q->stmt->execute()) {
			while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
				$fields[$row['field']] = $row['id'];
			}
		}
		$fields = $this->modx->toJSON($fields);

		$ns = "mlmsystem";
		$key = "{$ns}_log_fields_{$class}";
		if (!$setting = $this->modx->getObject("modSystemSetting", $key)) {
			$setting = $this->modx->newObject("modSystemSetting");
			$setting->fromArray(array(
				"key" => $key,
				"xtype" => "textarea",
				"namespace" => $ns,
				"area" => "{$ns}_logs"
			), "", true, true);
			$setting->save();
		}
		$setting->set('value', $fields);
		$setting->save();

		$this->modx->cacheManager->refresh();

		return true;
	}

}

return 'modMlmSystemTypeChangesUpdateProcessor';
