<?php
class MlmSystemClient extends xPDOObject {


	const STATUS_CREATE = 1;
	const STATUS_NEW = 2;
	const STATUS_BLOCKED = 3;
	const STATUS_REMOVED = 4;

	protected $logFields = array(
		'parent', 'leader', 'status',
	);

	protected $logFieldsToo = array(
		'put',	'take', 'profit', 'deposit'
	);

	/**
	 * Get the xPDOValidator class configured for this instance.
	 *
	 * @return string|boolean The xPDOValidator instance or false if it could
	 * not be loaded.
	 */
	public function getValidator()
	{
		if (!is_object($this->_validator)) {
			$validatorClass = $this->xpdo->loadClass('validation.xPDOValidator', XPDO_CORE_PATH, true, true);
			if ($derivedClass = $this->getOption(xPDO::OPT_VALIDATOR_CLASS, null, '')) {
				if ($derivedClass = $this->xpdo->loadClass($derivedClass, '', false, true)) {
					$validatorClass = $derivedClass;
				}
			}
			if ($clientClass = $this->getOption('mlmsystem_handler_class_client_validator', null, '')) {
				if ($clientClass = $this->xpdo->loadClass($clientClass, $this->getOption('mlmsystem_core_path', null, MODX_CORE_PATH . 'components/mlmsystem/') . 'handlers/validations/', false, true)) {
					$validatorClass = $clientClass;
				}
			}
			if ($validatorClass) {
				$this->_validator = new $validatorClass($this);
			}
		}
		return $this->_validator;
	}

	public function set($k, $v = null, $vType = '')
	{
		switch ($this->_getPHPType($k)) {
			case 'int':
			case 'integer':
			case 'boolean':
				$v = (integer)$v;
				break;
			case 'float':
				$v = (float)$v;
				break;
			case 'date':
			case 'datetime':
			case 'timestamp':
				if (preg_match('/int/i', $v)) {
					$v = (integer)$v;
				}
				break;
			default:
				break;
		}

		return parent:: set($k, $v, $vType);
	}

	/**
	 * @param xPDO $xpdo
	 * @param string $className
	 * @param mixed $criteria
	 * @param bool $cacheFlag
	 *
	 * @return MlmSystemClient
	 */
	public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag = true)
	{
		/* @var $instance MlmSystemClient */
		$instance = parent::load($xpdo, 'MlmSystemClient', $criteria, $cacheFlag);
		if (!is_object($instance) || !($instance instanceof $className)) {
			if (is_numeric($criteria) || (is_array($criteria) && !empty($criteria['id']))) {
				$id = is_numeric($criteria) ? $criteria : $criteria['id'];
				if ($xpdo->getCount('modUser', array('id' => $id))) {
					$instance = $xpdo->newObject('MlmSystemClient');
					$instance->set('id', $id);
					if ($instance->save()) {

					}
				}
			}
		}
		return $instance;
	}

	/**
	 * @param bool $cacheFlag
	 *
	 * @return bool
	 */
	public function save($cacheFlag= null)
	{
		$isNew = $this->isNew();

		if ($isNew) {
			$this->set('createdon', time());
		} else {
			$this->set('updatedon', time());
		}
		if ($this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnClientBeforeSave', array(
				'mode' => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
				'client' => &$this,
				'cacheFlag' => $cacheFlag,
			));
		}

		/* log fields too */
		foreach ($this->logFieldsToo as $field) {
			if ($v = $this->get($field)) {
				$this->log($field, $v);
			}
		}

		/* log fields */
		foreach ($this->logFields as $field) {
			if (!array_key_exists($field, $this->_fieldMeta)) {
				continue;
			}
			if ($this->isDirty($field)) {
				$this->log($field, $this->get($field));
			}
		}

		$saved = parent:: save($cacheFlag);

		if ($saved && $this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnClientSave', array(
				'mode' => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
				'client' => &$this,
				'cacheFlag' => $cacheFlag,
			));
		}
		return $saved;
	}

	/**
	 * @param array $ancestors
	 *
	 * @return bool
	 */
	public function remove(array $ancestors = array())
	{
		if ($this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnClientBeforeRemove', array(
				'client' => &$this,
				'ancestors' => $ancestors,
			));
		}

		$removed = parent:: remove($ancestors);

		if ($this->xpdo instanceof modX) {
			$this->xpdo->invokeEvent('MlmSystemOnClientRemove', array(
				'client' => &$this,
				'ancestors' => $ancestors,
			));
		}

		return $removed;
	}

	/** @inheritdoc} */
	public function getStatusCreate()
	{
		return self::STATUS_CREATE;
	}

	/** @inheritdoc} */
	public function getStatusNew()
	{
		return self::STATUS_NEW;
	}

	/** @inheritdoc} */
	public function getStatusBlocked()
	{
		return self::STATUS_BLOCKED;
	}

	/** @inheritdoc} */
	public function getStatusRemoved()
	{
		return self::STATUS_REMOVED;
	}

	/**
	 * Set the leader field explicitly
	 *
	 * @param boolean $$leader
	 * @return bool
	 */
	public function setLeader($leader) {
		$this->_fields['leader'] = (boolean)$leader;
		$this->setDirty('leader');
		return true;
	}

	/** @inheritdoc} */
	public function putSum($sum = 0)
	{
		$sum = abs($sum);
		$balance = $this->get('balance');
		$incoming = abs($this->get('incoming'));
		$balance += $sum;
		$incoming += $sum;
		$this->set('put', $sum);
		$this->set('incoming', $incoming);
		return $this->set('balance', $balance);
	}

	/** @inheritdoc} */
	public function takeSum($sum = 0)
	{
		$sum = abs($sum);
		$balance = $this->get('balance');
		$outcoming = abs($this->get('outcoming'));
		$balance -= $sum;
		$outcoming += $sum;
		$this->set('take', $sum);
		$this->set('outcoming', $outcoming);
		return $this->set('balance', $balance);
	}

	/** @inheritdoc} */
	public function profitSum($sum = 0)
	{
		$sum = abs($sum);
		$balance = $this->get('balance');
		$incoming = abs($this->get('incoming'));
		$balance += $sum;
		$incoming += $sum;
		$this->set('profit', $sum);
		$this->set('incoming', $incoming);
		return $this->set('balance', $balance);
	}

	/** @inheritdoc} */
	public function depositSum($sum = 0)
	{
		$sum = abs($sum);
		$balance = $this->get('balance');
		$incoming = abs($this->get('incoming'));
		$balance += $sum;
		$incoming += $sum;
		$this->set('deposit', $sum);
		$this->set('incoming', $incoming);
		return $this->set('balance', $balance);
	}

	/** @inheritdoc} */
	public function leaderSum($sum = 0)
	{
		$sum = abs($sum);
		$balance = $this->get('balance');
		$incoming = abs($this->get('incoming'));
		$balance += $sum;
		$incoming += $sum;
		//$this->set('leader', $sum);
		$this->set('incoming', $incoming);
		return $this->set('balance', $balance);
	}

	/** @inheritdoc} */
	public function log($target = '', $value = '')
	{
		$this->Log = $this->xpdo->newObject('MlmSystemLog', array(
			'target' => $target,
			'value' => $value,
			'class' => __CLASS__,
		));
		return true;
	}

	/**
	 * Adds a lock on the MlmSystemClient
	 *
	 * @access public
	 * @param array $options An array of options for the lock.
	 * @return boolean True if the lock was successful.
	 */
	public function addLock(array $options = array()) {
		$locked = false;
		if ($this->xpdo instanceof modX) {
			$lockedBy = $this->getLock();
			if (empty($lockedBy)) {
				$this->xpdo->registry->locks->subscribe('/mlmsystemclient/');
				$this->xpdo->registry->locks->send('/mlmsystemclient/', array(md5($this->get('id')) => 1), array('ttl' => $this->xpdo->getOption('lock_ttl', $options, 360)));
				$locked = true;
			}
		}
		return $locked;
	}

	/**
	 * Gets the lock on the MlmSystemClient.
	 *
	 * @access public
	 * @return int
	 */
	public function getLock() {
		$lock = 0;
		if ($this->xpdo instanceof modX) {
			if ($this->xpdo->getService('registry', 'registry.modRegistry')) {
				$this->xpdo->registry->addRegister('locks', 'registry.modDbRegister', array('directory' => 'locks'));
				$this->xpdo->registry->locks->connect();
				$this->xpdo->registry->locks->subscribe('/mlmsystemclient/' . md5($this->get('id')));
				if ($msgs = $this->xpdo->registry->locks->read(array('remove_read' => false, 'poll_limit' => 1))) {
					$msg = reset($msgs);
					$lock = intval($msg);
				}
			}
		}
		return $lock;
	}

	/**
	 * Removes all locks on a MlmSystemClient.
	 *
	 * @access public
	 * @return boolean True if locks were removed.
	 */
	public function removeLock() {
		$removed = false;
		if ($this->xpdo instanceof modX) {
			//$lockedBy = $this->getLock();
			if ($this->xpdo->getService('registry', 'registry.modRegistry')) {
				$this->xpdo->registry->addRegister('locks', 'registry.modDbRegister', array('directory' => 'locks'));
				$this->xpdo->registry->locks->connect();
				$this->xpdo->registry->locks->subscribe('/mlmsystemclient/' . md5($this->get('id')));
				$this->xpdo->registry->locks->read(array('remove_read' => true, 'poll_limit' => 1));
				$removed = true;
			}
		}
		return $removed;
	}
	
}