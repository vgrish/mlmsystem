<?php

/*
 *
 * for test Profits
 *
 */
class MlmSystemOnWebPageInit extends MlmSystemEventPlugin
{
	public function run()
	{
		$this->MlmSystem->initialize($this->modx->context->key);


		$this->MlmSystem->Profits->printLog('MlmSystemOnWebPageInit');


	}

}
