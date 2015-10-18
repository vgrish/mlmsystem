<?php

class modMlmSystemEmailSendProcessor extends modProcessor
{
	public $classKey = 'MlmSystemEmail';

	public $MlmSystem;

	protected $processed = array(
		'success' => 0,
		'failure' => 0,
		'total' => 0,
	);

	public function process()
	{
		$this->setDefaultProperties(array(
			'listUser' => '',
			'listEmail' => '',
			'subjectEmail' => '',
			'bodyEmail' => '',
			'queueEmail' => false,
			'getUser' => false,
			'formatField' => false,
			'context' => 'web',
			'addPls' => array(),
			'fastMode' => true
		));

		/** @var mlmsystem $mlmsystem */
		$this->MlmSystem = $this->modx->getService('mlmsystem');
		$this->MlmSystem->initialize($this->getProperty('context', $this->modx->context->key));
		$this->MlmSystem->Tools->prepareContext($this->getProperty('context'));

		$emailSend = array();
		$listUser = $this->getProperty('listUser');
		if (!empty($listUser)) {
			$listUser = array_map('trim', (array)explode(',', $listUser));
			$q = $this->modx->newQuery('modUserProfile', array('internalKey:IN' => $listUser));
			$q->select('email');
			if ($q->prepare() && $q->stmt->execute()) {
				$emailSend = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
			}
		}

		$listEmail = $this->getProperty('listEmail');
		$listEmail = array_map('trim', explode(',', $listEmail));
		$emailSend = array_values(array_unique(array_merge($emailSend, $listEmail)));

		$getUser = $this->getProperty('getUser');
		$fastMode = $this->getProperty('fastMode', $this->MlmSystem->pdoTools->config['fastMode']);

		$level = $this->modx->getLogLevel();
		$this->modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);

		foreach ($emailSend as $email) {

			if (empty($email)) {
				continue;
			}
			$pls = array();
			$email = strtolower($email);
			if (!empty($getUser)) {
				$q = $this->modx->newQuery('modUserProfile', array('email' => $email));
				if ($profileObject = $this->modx->getObject('modUserProfile', $q)) {
					$pls = $this->MlmSystem->Tools->processObject($profileObject, $this->getProperty('formatField'));
				}
			}

			$pls = array_merge($pls, $this->getProperty('addPls'));

			$subjectEmail = $this->getProperty('subjectEmail');
			$subject = $this->MlmSystem->pdoTools->getChunk($subjectEmail, $pls, $fastMode);
			$subject = $this->MlmSystem->Tools->processTags($subject);

			$bodyEmail = $this->getProperty('bodyEmail');
			$body = $this->MlmSystem->pdoTools->getChunk($bodyEmail, $pls, $fastMode);
			$body = $this->MlmSystem->Tools->processTags($body);

			if ($response = $this->modx->runProcessor('create',
				array(
					'uid' => 0,
					'subject' => $subject,
					'body' => $body,
					'email' => $email,
					'queueEmail' => $this->getProperty('queueEmail')
				),
				array('processors_path' => dirname(__FILE__) . '/')
			)
			) {
				if ($response->isError()) {
					++$this->processed['failure'];
					$this->modx->error->reset();
				}
				else {
					++$this->processed['success'];
				}
			}
			++$this->processed['total'];
		}

		$this->modx->setLogLevel($level);

		return $this->success('', $this->processed);
	}
}

return 'modMlmSystemEmailSendProcessor';