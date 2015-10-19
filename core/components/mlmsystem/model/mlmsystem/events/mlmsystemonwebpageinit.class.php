<?php

class MlmSystemOnWebPageInit extends MlmSystemPlugin
{
	public function run()
	{
		$this->MlmSystem->initialize($this->modx->context->key);

		$user = $this->modx->getOption('user', $this->scriptProperties, 0);
		$userId = $user->get('id');

		$clientKey = $this->MlmSystem->getOption('client_key', null, 'rclient');
		$referrerKey = $this->MlmSystem->getOption('referrer_key', null, 'rhash');
		$cookieTime = $this->MlmSystem->getOption('referrer_time', null, 365);
		$defaultReferrerId = $this->MlmSystem->getOption('referrer_default_client', null, 0);

		if (!$this->modx->user->isAuthenticated() AND !empty($_REQUEST[$clientKey]) AND !empty($_REQUEST[$referrerKey])) {
			if ($this->MlmSystem->Tools->formatHashReferrer($_REQUEST[$clientKey]) == $_REQUEST[$referrerKey]) {
				setcookie($clientKey, $_REQUEST[$clientKey], time() + $cookieTime);
			}
		} elseif ($this->modx->user->isAuthenticated() AND !empty($_COOKIE[$clientKey])) {
			if ($parent = $this->modx->getObject('MlmSystemClient', (int)$_REQUEST[$clientKey])) {
				$defaultReferrerId = $parent->get('id');
			}
			if ($client = $this->modx->getObject('MlmSystemClient', $userId)) {
				if (!$client->get('parent') AND $defaultReferrerId != $userId) {
					$this->MlmSystem->Tools->changeClientParent($client, $defaultReferrerId);
				}
			}
			setcookie($clientKey, '', time() - $cookieTime);
		} elseif ($this->modx->user->isAuthenticated() AND empty($_COOKIE[$clientKey]) AND !empty($defaultReferrerId)) {
			if ($client = $this->modx->getObject('MlmSystemClient', $userId)) {
				if (!$client->get('parent') AND $defaultReferrerId != $userId) {
					$this->MlmSystem->Tools->changeClientParent($client, $defaultReferrerId);
				}
			}
		}
	}

}
