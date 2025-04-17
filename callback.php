<?php

use myPHPnotes\Microsoft\Auth;
use myPHPnotes\Microsoft\Handlers\Session;
use myPHPnotes\Microsoft\Models\User;

session_start();

if (!isset($_SESSION['oauth_state']) || $_SESSION['oauth_state'] !== $_REQUEST['state']) {
    throw new Exception('State parameter does not match.');
}

require 'vendor/autoload.php';
if (!Session::get('tenant_id') ||
     !Session::get('client_id') ||
     !Session::get('client_secret') ||
     !Session::get('redirect_uri') ||
     !Session::get('scopes')) {
     header("Location: index.php");
}

$auth = new Auth(
    Session::get('tenant_id'),
    Session::get('client_id'),
    Session::get('client_secret'),
    Session::get('redirect_uri'),
    Session::get('scopes')
);


$tokens = $auth->getToken($_REQUEST['code'], $_REQUEST['state']);
$accessToken = $tokens->access_token;

$_SESSION['access_token'] = $accessToken;
$auth->setAccessToken($accessToken);

$user = new User();
if ($user != null) {
    $_SESSION["name"] = $user->data->getDisplayName();
    $_SESSION["email"] = $user->data->getUserPrincipalName();
    $_SESSION["is_logged_in"] = true;
}

header("Location: signup.php");
exit();

?>
