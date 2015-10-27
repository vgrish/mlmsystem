<?php

/**
 * Get a list of MlmSystemClient
 */
class modMlmSystemClientGetListProcessor extends modObjectGetListProcessor
{
	public $classKey = 'MlmSystemClient';
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'DESC';
	public $languageTopics = array('default', 'mlmsystem');
	public $permission = '';

	public $statusBlocked;

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		$clientObject = $this->modx->newObject('MlmSystemClient');
		$this->statusBlocked = $clientObject->getStatusBlocked();

		return parent::initialize();
	}
    
	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		if (!$this->getProperty('combo')) {

			$c->groupby('MlmSystemClient.id');

			$c->leftJoin('modUser', 'modUser', 'modUser.id = MlmSystemClient.id');
			$c->leftJoin('modUserProfile', 'modUserProfile', 'modUserProfile.internalKey = MlmSystemClient.id');
			$c->leftJoin('modUser', 'modUserParent', 'modUserParent.id = MlmSystemClient.parent');
			$c->leftJoin('modUserProfile', 'modUserProfileParent', 'modUserProfileParent.internalKey = MlmSystemClient.parent');
			$c->leftJoin('MlmSystemStatus', 'MlmSystemStatus', 'MlmSystemStatus.id = MlmSystemClient.status');
			$c->leftJoin('MlmSystemPath', 'MlmSystemPath', 'MlmSystemPath.id = MlmSystemClient.id');
			$c->leftJoin('MlmSystemClient', 'MlmSystemClientParent', 'MlmSystemClientParent.parent = MlmSystemClient.id');
		
			$c->select($this->modx->getSelectColumns('MlmSystemClient', 'MlmSystemClient'));
			$c->select($this->modx->getSelectColumns('modUserProfile', 'modUserProfile', 'profile_', array('id', 'internalKey'), true));
			$c->select(array(
				'username' => 'modUser.username',
				'fullname' => 'modUserProfile.fullname',
				'email' => 'modUserProfile.email',

				'parent_username' => 'modUserParent.username',
				'parent_fullname' => 'modUserProfileParent.fullname',
				'parent_email' => 'modUserProfileParent.email',

				'status_name' => 'MlmSystemStatus.name',
				'status_color' => 'MlmSystemStatus.color',
				'level' => 'MlmSystemPath.level',
				'children' =>'COUNT(MlmSystemClientParent.parent)'
			));
		}
		else {

			$c->leftJoin('modUser', 'modUser', 'modUser.id = MlmSystemClient.id');
			$c->leftJoin('modUserProfile', 'modUserProfile', 'modUserProfile.internalKey = MlmSystemClient.id');

			$c->select($this->modx->getSelectColumns('MlmSystemClient', 'MlmSystemClient'));
			$c->select($this->modx->getSelectColumns('modUserProfile', 'modUserProfile', 'profile_', array('id', 'internalKey'), true));

			$c->select(array(
				'username' => 'modUser.username',
				'fullname' => 'modUserProfile.fullname',
			));

			$c->where(array('status:NOT IN' => array(4)));

			$client = $this->getProperty('client', 0);
			if (!empty($client)) {
				$c->where(array('id:!=' => $client));

			}

		}

		$status = $this->getProperty('status');
		if (!empty($status)) {
			$c->where(array('status' => $status));
		}

		$level = $this->getProperty('level');
		if (!empty($level)) {
			$c->where(array('MlmSystemPath.level' => $level));
		}

		$leader = $this->getProperty('leader');
		if ($leader != '') {
			$c->where(array('leader' => $leader));
		}

		// query
		if ($query = $this->getProperty('query')) {
			$c->where(array(
				'modUser.username:LIKE' => '%' . $query . '%',
				'OR:modUserProfile.fullname:LIKE' => '%' . $query . '%',
				'OR:modUserProfile.email:LIKE' => '%' . $query . '%',
			));
		}

		return $c;
	}

	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object)
	{
		$icon = 'fa';
		$array = $object->toArray();
		$array['actions'] = array();
		// Menu
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-cog actions-menu",
			'menu' => false,
			'button' => true,
			'action' => 'showMenu',
			'type' => 'menu',
		);
		// Edit
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-edit green",
			'title' => $this->modx->lexicon('mlmsystem_action_edit'),
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

		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-street-view",
			'title' => $this->modx->lexicon('mlmsystem_action_change_parent'),
			'action' => 'changeParent',
			'button' => false,
			'menu' => true,
		);
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-money ",
			'title' => $this->modx->lexicon('mlmsystem_action_change_balance'),
			'action' => 'changeBalance',
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

		//Blocked status
		if ($array['status'] != $this->statusBlocked) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-unlock-alt red",
				'title' => $this->modx->lexicon('mlmsystem_action_active_blocked'),
				'action' => 'activeBlocked',
				'button' => false,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-unlock green",
				'title' => $this->modx->lexicon('mlmsystem_action_inactive_blocked'),
				'action' => 'inactiveBlocked',
				'button' => false,
				'menu' => true,
			);
		}


		if (!$array['leader']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-shield green",
				'title' => $this->modx->lexicon('mlmsystem_action_active_leader'),
				'action' => 'activeLeader',
				'button' => false,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-shield red",
				'title' => $this->modx->lexicon('mlmsystem_action_inactive_leader'),
				'action' => 'inactiveLeader',
				'button' => false,
				'menu' => true,
			);
		}



		// Remove
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-trash-o red",
			'title' => $this->modx->lexicon('mlmsystem_action_remove'),
			'action' => 'remove',
			'button' => false,
			'menu' => true,
		);

		return $array;
	}

	/** {@inheritDoc} */
	public function outputArray(array $array, $count = false)
	{
		if ($this->getProperty('addno')) {
			$array = array_merge_recursive(array(array(
				'id' => 0,
				'username' => $this->modx->lexicon('mlmsystem_no')
			)), $array);
		}
		return parent::outputArray($array, $count);
	}

}

return 'modMlmSystemClientGetListProcessor';