<?php
class MlmSystemLog extends xPDOSimpleObject {

	/**
	 * @param bool $cacheFlag
	 * @return bool
	 */
	public function save($cacheFlag = false)
	{
		$isNew = $this->isNew();

		if (!$isNew) {
			return false;
		}
		$this->set('timestamp', time());

		if ($this->xpdo instanceof modX) {

			if (empty($this->xpdo->request)) {
				$this->xpdo->getRequest();
			}

			$this->set('user', $this->xpdo->user->id);
			$this->set('ip', $this->xpdo->request->getClientIp());
		}

		if ($this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnLogBeforeSave', array(
				'mode' => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
				'log' => &$this,
				'cacheFlag' => $cacheFlag,
			));
		}

		$saved = parent:: save($cacheFlag);

		if ($saved && $this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnLogSave', array(
				'mode' => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
				'log' => &$this,
				'cacheFlag' => $cacheFlag,
			));
		}
		return $saved;
	}

	/**
	 * @param array $ancestors
	 * @return bool
	 */
	public function remove(array $ancestors = array())
	{
		if ($this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnLogBeforeRemove', array(
				'log' => &$this,
				'ancestors' => $ancestors,
			));
		}

		$removed = parent:: remove($ancestors);

		if ($this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnLogRemove', array(
				'log' => &$this,
				'ancestors' => $ancestors,
			));
		}

		return $removed;
	}
	
}