<?php
class MlmSystemProfit extends xPDOSimpleObject {

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
			if ($profitClass = $this->getOption('mlmsystem_handler_class_profit_validator', null, '')) {
				if ($profitClass = $this->xpdo->loadClass($profitClass, $this->getOption('mlmsystem_core_path', null, MODX_CORE_PATH . 'components/mlmsystem/') . 'handlers/validations/', false, true)) {
					$validatorClass = $profitClass;
				}
			}
			if ($validatorClass) {
				$this->_validator = new $validatorClass($this);
			}
		}
		return $this->_validator;
	}

}