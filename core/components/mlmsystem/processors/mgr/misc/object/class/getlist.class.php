<?php

class modMlmSystemObjectClassGetListProcessor extends modObjectGetListProcessor
{
	public $classKey = 'MlmSystemLog';
	public $defaultSortField = 'class';
	public $languageTopics = array('mlmsystem');

	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		if ($this->getProperty('combo')) {
			$c->limit(0);
			$c->groupby('class');
		}

		return $c;
	}

	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object) {
		if ($this->getProperty('combo')) {
			$array = array(
				'name' => $this->modx->lexicon('mlmsystem_class_'.$object->get('class')),
				'value' => $object->get('class'),
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

return 'modMlmSystemObjectClassGetListProcessor';