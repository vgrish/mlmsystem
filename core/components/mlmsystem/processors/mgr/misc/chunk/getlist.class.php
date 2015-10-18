<?php

class modChunkGetListProcessor extends modObjectGetListProcessor
{
	public $classKey = 'modChunk';
	public $languageTopics = array('chunk');

	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		if ($this->getProperty('combo')) {
			$c->limit(0);
			$c->select('id,name');
		}

		$query = $this->getProperty('query');
		if (!empty($query)) {
			$c->where(array(
				'name:LIKE' => '%' . $query . '%',
				'OR:description:LIKE' => '%' . $query . '%',
				));
		}
		return $c;
	}

	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object) {
		if ($this->getProperty('combo')) {
			$array = array(
				'id' => $object->get('id'),
				'name' => $object->get('name'),
				'description' => $object->get('description'),
			);
		}
		else {
			$array = $object->toArray();
		}
		return $array;
	}
}

return 'modChunkGetListProcessor';