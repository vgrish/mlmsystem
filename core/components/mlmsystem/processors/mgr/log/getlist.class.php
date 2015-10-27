<?php

/**
 * Get a list of MlmSystemLog
 */
class modMlmSystemLogGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'MlmSystemLog';
	public $classKey = 'MlmSystemLog';
	public $defaultSortField = 'id';
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

		if (!$this->getProperty('combo')) {
			$c->leftJoin('modUser', 'modUser', 'modUser.id = MlmSystemLog.user');
			$c->leftJoin('modUserProfile', 'modUserProfile', 'modUserProfile.internalKey = MlmSystemLog.user');
			$c->leftJoin('MlmSystemTypeChanges', 'MlmSystemTypeChanges', 'MlmSystemTypeChanges.id = MlmSystemLog.type');

			$c->select($this->modx->getSelectColumns('MlmSystemLog', 'MlmSystemLog'));
			$c->select(array(
				'username' => 'modUser.username',
				'fullname' => 'modUserProfile.fullname',
				'email' => 'modUserProfile.email',
				'mode' => 'MlmSystemTypeChanges.mode',
				'description' => 'MlmSystemTypeChanges.description',
				'name' => 'MlmSystemTypeChanges.name',
				'object' => 'MlmSystemTypeChanges.class',
				'type_field' => 'MlmSystemTypeChanges.field',
			));
		}
		else {

		}

		$class = $this->getProperty('class');
		if ($class) {
			$c->where(array('class' => $class));
		}

		$target = $this->getProperty('target');
		if ($target) {
			$c->where(array('target' => $target));
		}

		$type = $this->getProperty('type');
		if ($type) {
			$c->where(array('type' => $type));
		}

		$mode = $this->getProperty('mode');
		if ($mode) {
			$c->where(array('MlmSystemTypeChanges.mode' => $mode));
		}

		$query = trim($this->getProperty('query'));
		if ($query) {
			$c->where(array(
				'value:LIKE' => "%{$query}%",
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
			'icon' => "$icon $icon-eye green",
			'title' => $this->modx->lexicon('mlmsystem_action_update'),
			'action' => 'update',
			'button' => true,
			'menu' => true,
		);
		// sep
		$array['actions'][] = array(
			'cls' => '',
			'icon' => '',
			'title' => '',
			'action' => 'sep',
			'button' => false,
			'menu' => true,
		);
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

return 'modMlmSystemLogGetListProcessor';