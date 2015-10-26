<?php

/*
 *
 * for test Profits
 *
 */
class MlmSystemEventOnWebPageInit extends MlmSystemEventPlugin
{
	public function run()
	{
		$this->MlmSystem->initialize($this->modx->context->key);
		$ids = $this->MlmSystem->Profits->getProfitIds($this->modx->event->name);

		$this->MlmSystem->Profits->printLog($ids, 1);


		foreach ($ids as $id) {
			if ($profitObject = $this->modx->getObject('MlmSystemProfit', $id)) {
				$this->MlmSystem->loadProfits($profitObject->get('class'));
				if (!$this->MlmSystem->Profits) {
					$this->MlmSystem->loadProfits();
				}

				$initiator = $this->MlmSystem->Profits->getInitiator($this->scriptProperties);

				if ($initiator) {
					$this->MlmSystem->Profits->printLog($initiator->toArray(), 1);


					$groups = $this->MlmSystem->Profits->getProfitUserGroups(26);

					print_r($groups);

				}


				print_r(

					$this->MlmSystem->Profits->getProfitResourceGroups(3)
				);



				/* work */


			}
		}

	}

}
