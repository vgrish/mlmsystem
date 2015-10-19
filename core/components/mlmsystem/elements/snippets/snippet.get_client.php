<?php
/** @var array $scriptProperties */
/** @var MlmSystem $MlmSystem */

if (!isset($scriptProperties['snippetName']) AND $snippetName = $this->get('name')) {
	$scriptProperties['snippetName'] = $snippetName;
}

$class = 'MlmSystemClient';
$scriptProperties['context'] = $modx->getOption('context', $scriptProperties, $modx->context->key, true);
$scriptProperties['addPls'] = $modx->fromJSON($modx->getOption('addPls', $scriptProperties, '{}', true));
$scriptProperties['id'] = $modx->getOption('id', $scriptProperties, $modx->user->id, true);

$MlmSystem = $modx->getService('mlmsystem');
$MlmSystem->initialize($scriptProperties['context'], $scriptProperties);
$MlmSystem->loadCustomJsCss($scriptProperties['snippetName']);

$instance = null;
$data = array(
	'form_key' => $MlmSystem->config['form_key'],
);

$view = (int)$scriptProperties['showOnlyClient'];
if ($view AND $modx->user->id != $scriptProperties['id']) {
	$view = false;
}
else {
	$view = true;
}

$output = '';
$instance = $modx->getObject($class, (int)$scriptProperties['id']);
if (!$instance OR !$view) {
	return !empty($tplEmpty) ? $MlmSystem->pdoTools->getChunk($tplEmpty, $data) : '';
}

if ($view AND $instance) {
	$data = array_merge($data, $MlmSystem->Tools->processObject($instance, true, $scriptProperties['formatField']));
	if ($scriptProperties['getUser'] AND $userObject = $instance->getOne('User')) {
		$data = array_merge($data, $MlmSystem->Tools->processObject($userObject, true, $scriptProperties['formatField'], 'user_'));
	}
	if ($scriptProperties['getUserProfile'] AND $userProfileObject = $instance->getOne('UserProfile')) {
		$data = array_merge($data, $MlmSystem->Tools->processObject($userProfileObject, true, $scriptProperties['formatField'], 'user_profile_'));
	}
	if ($scriptProperties['getPaymentClient'] AND $paymentClientObject = $instance->getOne('PaymentClient')) {
		$data = array_merge($data, $MlmSystem->Tools->processObject($paymentClientObject, true, $scriptProperties['formatField'], 'payment_client_'));
	}
	if ($scriptProperties['getStatus'] AND $statusObject = $instance->getOne('Status')) {
		$data = array_merge($data, $MlmSystem->Tools->processObject($statusObject, true, $scriptProperties['formatField'], 'status_'));
	}
	$output = empty($tplRow)
		? $MlmSystem->pdoTools->getChunk('', $data)
		: $MlmSystem->pdoTools->getChunk($tplRow, $data, $MlmSystem->pdoTools->config['fastMode']);
}
elseif (!empty($tplEmpty)) {
	$output = empty($tplEmpty)
		? $MlmSystem->pdoTools->getChunk('', $data)
		: $MlmSystem->pdoTools->getChunk($tplEmpty, $data, $MlmSystem->pdoTools->config['fastMode']);
}
if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
	$output = $MlmSystem->pdoTools->getChunk($tplWrapper, array('output' => $output), $MlmSystem->pdoTools->config['fastMode']);
}
if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}