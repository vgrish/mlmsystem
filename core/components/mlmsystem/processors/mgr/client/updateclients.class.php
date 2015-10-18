<?php

/**
 * Update Clients an PaymentSystemClient
 */
class modPaymentSystemClientsUpdateProcessor extends modProcessor
{
	public $classKey = 'PaymentSystemClient';

	public function process()
	{
		$clients = $users = array();

		$q = $this->modx->newQuery('PaymentSystemClient', array('deleted' => 0));
		$q->select('id');
		if ($q->prepare() && $q->stmt->execute()) {
			$clients = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
		}

		$q = $this->modx->newQuery('modUser', array('active' => 1));
		if (!empty($clients)) {
			$q->andCondition(array('id:NOT IN' => $clients));
		}
		$q->select('id');
		if ($q->prepare() && $q->stmt->execute()) {
			$users = $q->stmt->fetchAll(PDO::FETCH_COLUMN);

			foreach ($users as $id) {
				$client = $this->modx->getObject('PaymentSystemClient', $id);
				if ($user = $client->getOne('User') AND $groups = $this->modx->getOption('paymentsystem_user_groups', null, false)) {
					$groups = array_map('trim', explode(',', $groups));
					foreach ($groups as $group) {
						$user->joinGroup($group);
					}
				}
			}
		}
		return $this->success();
	}

}
return 'modPaymentSystemClientsUpdateProcessor';