<?php

class MlmSystemOnUserRemove extends MlmSystemPlugin
{
	public function run()
	{
		$this->MlmSystem->initialize($this->modx->context->key);

		$user = $this->modx->getOption('user', $this->scriptProperties, 0);
		if ($client = $user->getOne('MlmSystemClient')) {
			$this->MlmSystem->Tools->changeClientStatus($client, $client->getStatusRemoved());
		}
	}

}