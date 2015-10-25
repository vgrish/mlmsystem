<?php

class MlmSystemEventmsOnChangeOrderStatus extends MlmSystemEventPlugin
{
	public function run()
	{
		$this->MlmSystem->initialize($this->modx->context->key);


		$this->modx->log(1, print_r('MlmSystemmsOnChangeOrderStatus', 1));

	}

}