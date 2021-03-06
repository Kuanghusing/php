<?php
//interface first, then class ...
include '../lib/captcha/PhraseBuilderInterface.php';
include '../lib/captcha/CaptchaBuilderInterface.php';
include '../lib/captcha/CaptchaBuilder.php';
include '../lib/captcha/PhraseBuilder.php';
use Gregwar\Captcha\CaptchaBuilder;

session_start();
$builder = new CaptchaBuilder();
$builder->build(70, 30);
$_SESSION['captcha'] = $builder->getPhrase();
$builder->output();
