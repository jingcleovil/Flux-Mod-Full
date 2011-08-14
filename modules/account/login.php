<?php
if (!defined('FLUX_ROOT')) exit;

if (Flux::config('UseLoginCaptcha') && Flux::config('EnableReCaptcha')) {
	require_once 'recaptcha/recaptchalib.php';
	$recaptcha = recaptcha_get_html(Flux::config('ReCaptchaPublicKey'));
}

$title = Flux::message('LoginTitle');
$loginLogTable = Flux::config('FluxTables.LoginLogTable');

$serverNames = $this->getServerNames();
?>