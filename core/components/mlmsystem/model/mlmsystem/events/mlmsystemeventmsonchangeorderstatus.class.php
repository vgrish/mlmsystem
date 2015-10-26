<?php

class MlmSystemEventmsOnChangeOrderStatus extends MlmSystemEventPlugin
{
	public function run()
	{

		$status = $this->modx->getOption('status', $this->scriptProperties, 0);
//		if ($status != 2) {
//			return '';
//		}

		$this->MlmSystem->initialize($this->modx->context->key);
		$ids = $this->MlmSystem->Profits->getProfitIds($this->modx->event->name);

		$this->modx->log(1, print_r('MlmSystemmsOnChangeOrderStatus', 1));
		$this->modx->log(1, print_r($ids, 1));

		foreach ($ids as $id) {
			if (!$profitObject = $this->modx->getObject('MlmSystemProfit', $id)) {
				continue;
			}

			$this->MlmSystem->loadProfits($profitObject->get('class'));
			if (!$this->MlmSystem->Profits) {
				$this->MlmSystem->loadProfits();
			}

			if (!$initiator = $this->MlmSystem->Profits->getInitiator($this->scriptProperties)) {
				continue;
			}

			$profit = $this->MlmSystem->Profits->getProfit($profitObject);
			if (empty($profit)) {
				continue;
			}



		}

		$this->modx->log(1, print_r('$initiator', 1));
		$this->modx->log(1, print_r($initiator->toArray(), 1));

	}

}