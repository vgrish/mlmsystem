<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/user/create.class.php';

class modPaymentSystemUserCreateProcessor extends modUserCreateProcessor
{

	public $classKey = 'modUser';
	public $languageTopics = array('core:default', 'core:user');
	public $permission = '';
	public $beforeSaveEvent = 'OnBeforeUserFormSave';
	public $afterSaveEvent = 'OnUserFormSave';

	public function initialize()
	{
		return parent::initialize();
	}

	/**
	 * Override in your derivative class to do functionality before the fields are set on the object
	 * @return boolean
	 */
	public function beforeSet()
	{
		$q = $this->modx->newQuery('modUserProfile', array('email' => $this->getProperty('email')));
		if ($this->modx->getCount('modUserProfile', $q)) {
			$this->addFieldError('email',$this->modx->lexicon('user_err_not_specified_email'));
		}

		$this->setProperty('passwordnotifymethod', $this->getProperty('passwordnotifymethod', 's'));
		return parent::beforeSet();
	}

	public function afterSave()
	{
		if ($client = $this->modx->getObject('PaymentSystemClient', $this->object->get('id'))) {
			/* смена стутуса  */
			$status = $this->getProperty('status');

			$this->modx->log(1, print_r($this->getProperties() ,1));

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
		$groups .= ',' . $this->modx->getOption('paymentsystem_user_groups', null, '');
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

	/**
	 * Send the password notification email, if specified
	 * @return void
	 */
	public function sendNotificationEmail()
	{
		/* @var modContext $context */
		if ($context = $this->modx->getObject('modContext', array('key' => $this->getProperty('context', null)))) {
			$context->prepare(true);
			$lang = $context->getOption('cultureKey');
			$this->modx->setOption('cultureKey', $lang);
			$this->modx->lexicon->load($lang . ':paymentsystem:default', $lang . ':core:default', $lang . ':core:user');
		}

		if ($this->getProperty('passwordnotifymethod') == 'e') {
			$message = $this->modx->getOption('signupemail_message');
			$pls = array(
				'uid' => $this->object->get('username'),
				'pwd' => $this->newPassword,
				'ufn' => $this->profile->get('fullname'),
				'sname' => $this->modx->getOption('site_name'),
				'saddr' => $this->modx->getOption('emailsender'),
				'semail' => $this->modx->getOption('emailsender'),
				'surl' => $this->modx->getOption('url_scheme') . $this->modx->getOption('http_host') . $this->modx->getOption('manager_url'),
			);

			foreach ($pls as $k => $v) {
				$message = str_replace('[[+'.$k.']]',$v,$message);
			}
			$this->object->sendEmail($message);
		}
	}

}

return 'modPaymentSystemUserCreateProcessor';