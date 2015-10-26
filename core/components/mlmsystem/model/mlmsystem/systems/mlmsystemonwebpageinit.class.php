<?php

class MlmSystemOnWebPageInit extends MlmSystemPlugin
{
	public function run()
	{
		$this->MlmSystem->initialize($this->modx->context->key);

		$userId = $this->modx->user->id;
		$clientKey = $this->MlmSystem->getOption('client_key', null, 'rclient');
		$referrerKey = $this->MlmSystem->getOption('referrer_key', null, 'rhash');
		$cookieTime = $this->MlmSystem->getOption('referrer_time', null, 365);
		$defaultReferrerId = $this->MlmSystem->getOption('referrer_default_client', null, 0);

		switch (true) {
			case (!$this->modx->user->isAuthenticated() AND !empty($_REQUEST[$clientKey]) AND !empty($_REQUEST[$referrerKey])):
				if ($this->MlmSystem->Tools->formatHashReferrer($_REQUEST[$clientKey]) == $_REQUEST[$referrerKey]) {
					setcookie($clientKey, $_REQUEST[$clientKey], time() + $cookieTime);
				}
				break;
			case ($this->modx->user->isAuthenticated() AND !empty($_COOKIE[$clientKey])):
				if ($this->modx->getCount('MlmSystemClient', array('id' => (int)$_COOKIE[$clientKey]))) {
					$defaultReferrerId = (int)$_COOKIE[$clientKey];
				}
				if ($client = $this->modx->user->getOne('MlmSystemClient')) {
					if (!$client->get('parent') AND $defaultReferrerId != $userId) {
						$this->MlmSystem->Tools->changeClientParent($client, $defaultReferrerId);
					}
				}
				setcookie($clientKey, '', time() - $cookieTime);
				break;
			case ($this->modx->user->isAuthenticated() AND !empty($_REQUEST[$referrerKey]) AND !empty($_REQUEST[$referrerKey])):
				if (
					$this->MlmSystem->Tools->formatHashReferrer($_REQUEST[$clientKey]) == $_REQUEST[$referrerKey] AND
					$this->modx->getCount('MlmSystemClient', array('id' => (int)$_REQUEST[$clientKey]))
				) {
					$defaultReferrerId = (int)$_REQUEST[$clientKey];
				}
				if ($client = $this->modx->user->getOne('MlmSystemClient')) {
					if (!$client->get('parent') AND $defaultReferrerId != $userId) {
						$this->MlmSystem->Tools->changeClientParent($client, $defaultReferrerId);
					}
				}
				break;
			case ($this->modx->user->isAuthenticated() AND !empty($defaultReferrerId)):
				if ($client = $this->modx->user->getOne('MlmSystemClient')) {
					if (!$client->get('parent') AND $defaultReferrerId != $userId) {
						$this->MlmSystem->Tools->changeClientParent($client, $defaultReferrerId);
					}
				}
				break;
			default:
				break;
		}
	}

}
