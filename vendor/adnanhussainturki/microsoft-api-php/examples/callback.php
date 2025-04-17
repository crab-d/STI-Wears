<?php

use myPHPnotes\Microsoft\Auth;
use myPHPnotes\Microsoft\Handlers\Session;
use myPHPnotes\Microsoft\Models\User;

session_start();

require 'vendor/autoload.php';

// Sign-out function to clear session and revoke token
function signOut() {
    global $auth;

    // Revoke the access token (optional but recommended for security)
    $auth->revokeToken();

    // Clear all session data
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header('Location: index.php'); // Redirect to login page or homepage
    exit();
}

// If the user clicks the "logout" link, sign out
if (isset($_GET['logout'])) {
    signOut();
}

// Initialize the Auth object
$auth = new Auth(
    Session::get('tenant_id'),
    Session::get('client_id'),
    Session::get('client_secret'),
    Session::get('redirect_uri'),
    Session::get('scopes')
);

// Get the access token after authorization
$tokens = $auth->getToken($_REQUEST['code'], $_REQUEST['state']);
$accessToken = $tokens->access_token;

$auth->setAccessToken($accessToken);

// Fetch the user's information
$user = new User();
echo 'Name: ' . $user->data->getDisplayName() . '<br>';
echo 'Email: ' . $user->data->getUserPrincipalName() . '<br>';
echo "test";

?>

<!-- Sign-out button (link) -->
<a href="?logout=true">Sign Out</a>