<?php

class MlmSystemOnWebPageInit extends MlmSystemPlugin
{
	public function run()
	{
		$this->MlmSystem->initialize($this->modx->context->key);

		$clientKey = $this->MlmSystem->getOption('client_key', null, 'rclient');
		$referrerKey = $this->MlmSystem->getOption('referrer_key', null, 'rhash');
		$cookieTime = $this->MlmSystem->getOption('referrer_time', null, 365);
		$defaultReferrerId = $this->MlmSystem->getOption('referrer_default_client',null, 0);


		$this->modx->log(1, print_r( $_REQUEST,1));

		if (!$this->modx->user->isAuthenticated() AND !empty($_REQUEST[$clientKey]) AND !empty($_REQUEST[$referrerKey])) {
			if ($this->MlmSystem->Tools->formatHashReferrer($_REQUEST[$clientKey]) == $_REQUEST[$referrerKey]) {
				setcookie($clientKey, $_REQUEST[$clientKey], time() + $cookieTime);
			}
		}
		elseif ($this->modx->user->isAuthenticated() AND !empty($_COOKIE[$clientKey])) {

		}
		elseif ($this->modx->user->isAuthenticated() AND empty($_COOKIE[$clientKey]) AND !empty($defaultReferrerId)) {

		}

	}

}