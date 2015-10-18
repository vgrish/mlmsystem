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

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		$c->leftJoin('modUser', 'modUser', 'modUser.id = MlmSystemClient.id');
		$c->leftJoin('modUserProfile', 'modUserProfile', 'modUserProfile.internalKey = MlmSystemClient.id');
		$c->leftJoin('MlmSystemStatus', 'MlmSystemStatus', 'MlmSystemStatus.id = MlmSystemClient.status');

		$c->select($this->modx->getSelectColumns('MlmSystemClient', 'MlmSystemClient'));
		$c->select($this->modx->getSelectColumns('modUserProfile', 'modUserProfile', 'profile_', array('id', 'internalKey'), true));
		$c->select(array(
			'username' => 'modUser.username',
			'fullname' => 'modUserProfile.fullname',
			'email' => 'modUserProfile.email',
			'status_name' => 'MlmSystemStatus.name',
			'status_color' => 'MlmSystemStatus.color',
		));

		$status = $this->getProperty('status');
		if (!empty($status)) {
			$c->where(array('status' => $status));
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
//		$array['actions'][] = array(
//			'cls' => '',
//			'icon' => "$icon $icon-edit green",
//			'title' => $this->modx->lexicon('mlmsystem_action_edit'),
//			'action' => 'editClient',
//			'button' => true,
//			'menu' => true,
//		);
		// On
//		if (empty($array['disabled'])) {
//			$array['actions'][] = array(
//				'cls' => '',
//				'icon' => "$icon $icon-toggle-on green",
//				'title' => $this->modx->lexicon('mlmsystem_action_inactive'),
//				'action' => 'activeDisabled',
//				'button' => true,
//				'menu' => true,
//			);
//		}
//		// Off
//		if (!empty($array['disabled'])) {
//			$array['actions'][] = array(
//				'cls' => '',
//				'icon' => "$icon $icon-toggle-off red",
//				'title' => $this->modx->lexicon('mlmsystem_action_active'),
//				'action' => 'inactiveDisabled',
//				'button' => true,
//				'menu' => true,
//			);
//		}

		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-balance-scale",
			'title' => $this->modx->lexicon('mlmsystem_action_correct_balance'),
			'action' => 'correctBalance',
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
			'button' => false,
			'menu' => true,
		);

		return $array;
	}

}

return 'modMlmSystemClientGetListProcessor';