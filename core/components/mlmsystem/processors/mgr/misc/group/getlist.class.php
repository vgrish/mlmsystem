<?php

/**
 * Get a list of MlmSystemProfitGroup
 */
class modMlmSystemProfitGroupGetListProcessor extends modObjectGetListProcessor {

	public $objectType = '';
	public $classKey = '';
	public $defaultSortField = 'name';
	public $defaultSortDirection = 'DESC';
	public $languageTopics = array('default', 'mlmsystem');
	public $permission = '';

	public $profitType = 'MlmSystemProfitGroup';

	/** {@inheritDoc} */
	public function initialize() {
		switch ($this->getProperty('class')) {
			case 'modUserGroup':
				$this->objectType = $this->classKey = 'modUserGroup';
				break;
			case 'modResourceGroup':
				$this->objectType = $this->classKey = 'modResourceGroup';
				break;
		}
		if (empty($this->classKey)) {
			return $this->modx->lexicon('mlmsystem_err_class_ns');
		}
		return parent::initialize();
	}

	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {


		$c->leftJoin($this->profitType, $this->profitType, array(
			"{$this->profitType}.group = {$this->classKey}.id",
			"{$this->profitType}.identifier = {$this->getProperty('identifier')}",
			"{$this->profitType}.class" => "{$this->classKey}",
		));

		$c->select($this->modx->getSelectColumns($this->profitType, $this->profitType));
		$c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

		$c->where(array("{$this->profitType}.profit" => null));

		return $c;
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();

		return $array;
	}

}

return 'modMlmSystemProfitGroupGetListProcessor';