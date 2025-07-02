<?php
require_once 'vendor/autoload.php';
// error_reporting(E_ALL ^ E_DEPRECATED);

$client = new Google_Client();
$client->setClientId('73933804377-jd44frkago7qqrp40qlgno7u788lr4rf.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/portfolio-backend/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit;