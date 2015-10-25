<?php

/**
 * Get a list of MlmSystemProfitGroup
 */
class modMlmSystemProfitGroupGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'MlmSystemProfitGroup';
	public $classKey = 'MlmSystemProfitGroup';
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'DESC';
	public $languageTopics = array('default', 'mlmsystem');
	public $permission = '';

	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {

		$class = $this->getProperty('class');

		if (!empty($class)) {
			$c->leftJoin($class, $class, array(
				"{$this->classKey}.group = {$class}.id"
			));

			$c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
			$c->select(array(
				'name' => "{$class}.name",
			));
		}

		if ($class) {
			$c->where(array('class' => $class));
		}

		$identifier = $this->getProperty('identifier');
		if ($identifier) {
			$c->where(array('identifier' => $identifier));
		}

		$query = trim($this->getProperty('query'));
		if ($query) {
			$c->where(array(
				'name:LIKE' => "%{$query}%",
				'OR:description:LIKE' => "%{$query}%",
				'OR:event:LIKE' => "%{$query}%",
			));
		}

		return $c;
	}

	/** {@inheritDoc} */
	public function outputArray(array $array, $count = false)
	{
		if ($this->getProperty('addall')) {
			$array = array_merge_recursive(array(array(
				'id' => 0,
				'name' => $this->modx->lexicon('mlmsystem_all')
			)), $array);
		}
		return parent::outputArray($array, $count);
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$icon = 'fa';
		$array = $object->toArray();
		$array['actions'] = array();

		if (!$array['type']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-exchange",
				'title' => $this->modx->lexicon('mlmsystem_action_change_type'),
				'action' => 'setIn',
				'button' => true,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-exchange",
				'title' => $this->modx->lexicon('mlmsystem_action_change_type'),
				'action' => 'setOut',
				'button' => true,
				'menu' => true,
			);
		}

		// Remove
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-trash-o red",
			'title' => $this->modx->lexicon('mlmsystem_action_remove'),
			'action' => 'remove',
			'button' => true,
			'menu' => true,
		);

		return $array;
	}

}

return 'modMlmSystemProfitGroupGetListProcessor';