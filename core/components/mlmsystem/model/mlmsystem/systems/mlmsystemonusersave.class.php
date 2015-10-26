<?php

class MlmSystemOnUserSave extends MlmSystemPlugin
{
	public function run()
	{
		if ($this->modx->context->key == 'mgr') {
			return '';
		}

		$mode = $this->modx->getOption('mode', $this->scriptProperties, 0);
		$user = $this->modx->getOption('user', $this->scriptProperties, 0);

		if (
			$mode != modSystemEvent::MODE_NEW OR
			!$client = $user->getOne('MlmSystemClient')
		) {
			return '';
		}

		$clientKey = $this->MlmSystem->getOption('client_key', null, 'rclient');
		$defaultReferrerId = $this->MlmSystem->getOption($clientKey, $_COOKIE, $this->MlmSystem->getOption('referrer_default_client', null, 0));

		if (
			$client->get('parent') OR
			!$this->modx->getCount('MlmSystemClient', array('id' => $defaultReferrerId))
		) {
			return '';
		}

		$this->MlmSystem->initialize($this->modx->context->key);
		$this->MlmSystem->Tools->changeClientParent($client, $defaultReferrerId);

		return '';
	}

}
