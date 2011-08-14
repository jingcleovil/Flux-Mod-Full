<?php
if (!defined('FLUX_ROOT')) exit;

if (Flux::config('UseCaptcha') && Flux::config('EnableReCaptcha')) {
	require_once 'recaptcha/recaptchalib.php';
	$recaptcha = recaptcha_get_html(Flux::config('ReCaptchaPublicKey'));
}

$title = Flux::message('AccountCreateTitle');

$serverNames = $this->getServerNames();
?>