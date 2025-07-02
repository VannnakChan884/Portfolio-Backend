<?php
session_start();
require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setClientId('73933804377-jd44frkago7qqrp40qlgno7u788lr4rf.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/portfolio-backend/google-callback.php'); // ✅ Only 1 URI registered
$client->addScope('email');
$client->addScope('profile');

// Redirect user to Google
$authUrl = $client->createAuthUrl();
header("Location: $authUrl");
exit;
