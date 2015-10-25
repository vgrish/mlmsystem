<?php

abstract class MlmSystemEventPlugin
{
	/** @var modX $modx */
	protected $modx;
	/** @var MlmSystem $MlmSystem */
	protected $MlmSystem;
	/** @var array $scriptProperties */
	protected $scriptProperties;

	public function __construct($modx, &$scriptProperties)
	{
		$this->scriptProperties =& $scriptProperties;
		$this->modx = $modx;
		$this->MlmSystem = $this->modx->MlmSystem;

		if (!is_object($this->MlmSystem)) {
			$this->MlmSystem = $this->modx->getService('mlmsystem');
		}

	}

	abstract public function run();
}