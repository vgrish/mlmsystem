<?php

/**
 * Create an MlmSystemEmail
 */
class modMlmSystemEmailCreateProcessor extends modProcessor
{
	public $objectType = 'MlmSystemEmail';
	public $classKey = 'MlmSystemEmail';
	public $languageTopics = array('mlmsystem');
	public $permission = '';

	public $email;

	public function process()
	{
		if (!$this->createEmail()) {
			return $this->modx->lexicon($this->objectType.'_err_nfs');
		}

		$this->email->fromArray($this->getProperties());

		/* run queue validation */
		if (!$this->email->validate()) {
			return $this->failure($this->modx->lexicon($this->objectType.'_err_validate'));
		}

		$queueEmail = $this->getProperty('queueEmail', false);
		if ($queueEmail) {
			$result = $this->email->save();
		} else {
			$result = $this->email->Send();
		}

		if ($result == false) {
			return $this->failure('', $this->email);
		}
		return $this->success('', $this->email);

	}

	public function createEmail()
	{
		$this->email = $this->modx->newObject('MlmSystemEmail');
		$this->email->fromArray(array(
			'uid' => 0,
			'subject' => 'subject',
			'body' => 'body',
			'email' => '',
		));
		return (!empty($this->email) AND $this->email instanceof MlmSystemEmail);
	}

}

return 'modMlmSystemEmailCreateProcessor';