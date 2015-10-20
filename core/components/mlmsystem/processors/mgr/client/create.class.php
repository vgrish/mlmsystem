<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/user/create.class.php';

class modMlmSystemClientCreateProcessor extends modUserCreateProcessor
{
	public $classKey = 'modUser';
	public $languageTopics = array('core:default', 'core:user');
	public $permission = '';
	public $beforeSaveEvent = 'OnBeforeUserFormSave';
	public $afterSaveEvent = 'OnUserFormSave';

	public $MlmSystem;

	/** {@inheritDoc} */
	public function initialize()
	{
		/** @var mlmsystem $mlmsystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));

		$this->setProperties(array(
			'passwordnotifymethod' => 's',
			'blocked' => false,
			'active' => true,
		));

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeSet()
	{
		$email = $this->getProperty('email');
		if ($this->modx->getCount('modUserProfile', array('email' => $email))) {
			$this->addFieldError('email',$this->modx->lexicon('user_err_not_specified_email'));
		}

		$username = $this->getProperty('username');
		if (empty($username)) {
			$this->setProperty('username', $email);
		}

		return parent::beforeSet();
	}

	public function afterSave()
	{
		if ($client = $this->modx->getObject('MlmSystemClient', array('id' => $this->object->get('id')))) {
			$clientStatus = $this->getProperty('status', $client->getStatusCreate());
			$this->MlmSystem->Tools->changeClientStatus($client, $clientStatus);

			$parent = $this->getProperty('parent', $this->MlmSystem->getOption('referrer_default_client', null, 0));
			if (!empty($parent)) {
				$this->MlmSystem->Tools->changeClientParent($client, $parent);
			}
		}

		return parent::afterSave();
	}

	/**
	 * Add User Group memberships to the User
	 * @return array
	 */
	public function setUserGroups()
	{
		$memberships = array();
		$groups = $this->getProperty('groups', '');
		$groups .= ',' . $this->modx->getOption('mlmsystem_user_groups', null, '');
		$groups = explode(',', $groups);
		if (count($groups) > 0) {
			$groupsAdded = array();
			$idx = 0;
			foreach ($groups as $tmp) {
				@list($group, $role) = explode(':', $tmp);
				if (in_array($group, $groupsAdded)) {
					continue;
				}
				if (empty($role)) {
					$role = 1;
				}
				if ($tmp = $this->modx->getObject('modUserGroup', array('name' => $group))) {
					$gid = $tmp->get('id');
					/** @var modUserGroupMember $membership */
					$membership = $this->modx->newObject('modUserGroupMember');
					$membership->set('user_group', $gid);
					$membership->set('role', $role);
					$membership->set('member', $this->object->get('id'));
					$membership->set('rank', $idx);
					$membership->save();
					$memberships[] = $membership;
					$groupsAdded[] = $group;
					$idx++;
				}
			}
		}
		return $memberships;
	}

}

return 'modMlmSystemClientCreateProcessor';