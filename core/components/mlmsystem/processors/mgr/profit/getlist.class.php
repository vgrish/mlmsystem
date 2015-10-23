<?php

/**
 * Get a list of MlmSystemProfit
 */
class modMlmSystemProfitGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'MlmSystemProfit';
	public $classKey = 'MlmSystemProfit';
	public $defaultSortField = 'rank';
	public $defaultSortDirection = 'ASC';
	public $languageTopics = array('default', 'mlmsystem');
	public $permission = '';

	/** {@inheritDoc} */
	public function beforeQuery() {
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}

	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {

		$class = $this->getProperty('class');
		if ($class) {
			$c->where(array('class' => $class));
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

		// Edit
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-edit green",
			'title' => $this->modx->lexicon('mlmsystem_action_update'),
			'action' => 'update',
			'button' => true,
			'menu' => true,
		);

		if (!$array['active']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-toggle-off red",
				'title' => $this->modx->lexicon('mlmsystem_action_active'),
				'action' => 'active',
				'button' => true,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-toggle-on green",
				'title' => $this->modx->lexicon('mlmsystem_action_inactive'),
				'action' => 'inactive',
				'button' => true,
				'menu' => true,
			);
		}

		if ($array['editable']) {
			// Remove
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-trash-o red",
				'title' => $this->modx->lexicon('mlmsystem_action_remove'),
				'action' => 'remove',
				'button' => true,
				'menu' => true,
			);
		}

		return $array;
	}

}

return 'modMlmSystemProfitGetListProcessor';