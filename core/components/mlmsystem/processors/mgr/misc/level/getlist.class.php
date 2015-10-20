<?php

class modMlmSystemPathLevelGetListProcessor extends modObjectGetListProcessor
{
	public $classKey = 'MlmSystemPath';
	public $defaultSortField = 'level';
	public $languageTopics = array('mlmsystem');

	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		if ($this->getProperty('combo')) {
			$c->limit(0);
			$c->groupby('level');
		}

		return $c;
	}

	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object) {
		if ($this->getProperty('combo')) {
			$array = array(
				'name' => $object->get('level'),
				'value' => $object->get('level'),
			);
		}
		else {
			$array = $object->toArray();
		}
		return $array;
	}

	/** {@inheritDoc} */
	public function outputArray(array $array, $count = false)
	{
		if ($this->getProperty('addall')) {
			$array = array_merge_recursive(array(array(
				'name' => $this->modx->lexicon('mlmsystem_all'),
				'value' => ''
			)), $array);
		}
		return parent::outputArray($array, $count);
	}

}

return 'modMlmSystemPathLevelGetListProcessor';