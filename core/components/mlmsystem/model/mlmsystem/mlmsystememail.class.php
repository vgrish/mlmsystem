<?php

class MlmSystemEmail extends xPDOSimpleObject
{

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
			if ($queueClass = $this->getOption('mlmsystem_handler_class_email_validator', null, '')) {
				if ($queueClass = $this->xpdo->loadClass($queueClass, $this->getOption('mlmsystem_core_path', null, MODX_CORE_PATH . 'components/mlmsystem/') . 'handlers/validations/', false, true)) {
					$validatorClass = $queueClass;
				}
			}
			if ($validatorClass) {
				$this->_validator = new $validatorClass($this);
			}
		}
		return $this->_validator;
	}

	/**
	 * @return bool|string
	 */
	public function Send()
	{
		/* @var modPHPMailer $mail */
		$mail = $this->xpdo->getService('mail', 'mail.modPHPMailer');
		$mail->setHTML(true);
		$mail->set(modMail::MAIL_SUBJECT, $this->subject);
		$mail->set(modMail::MAIL_BODY, $this->body);
		$mail->set(modMail::MAIL_SENDER, $this->xpdo->getOption('mlmsystem_mail_from', null, $this->xpdo->getOption('emailsender'), true));
		$mail->set(modMail::MAIL_FROM, $this->xpdo->getOption('mlmsystem_mail_from', null, $this->xpdo->getOption('emailsender'), true));
		$mail->set(modMail::MAIL_FROM_NAME, $this->xpdo->getOption('mlmsystem_mail_from_name', null, $this->xpdo->getOption('site_name'), true));
		if ($user = $this->getOne('User')) {
			$profile = $user->getOne('Profile');
			if ($profile->get('blocked')) {
				return 'This user is blocked.';
			}
			$email = $profile->get('email');
		} else {
			$email = $this->get('email');
		}
		if (empty($email)) {
			return 'Could not get email.';
		}
		$mail->address('to', $email);
		if (!$mail->send()) {
			$this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo);
			$mail->reset();
			return false;
		}
		$mail->reset();
		return true;
	}

}