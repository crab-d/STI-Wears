<?php

session_start();

require 'vendor/autoload.php';

use myPHPnotes\Microsoft\Auth;

$tenant = 'cubao.sti.edu.ph';
$client_id =  '';
$client_secret = '';
$callback = 'https://stiwears.free.nf/callback.php';
$scopes = ['User.Read'];

$state = bin2hex(random_bytes(16)); 
$_SESSION['oauth_state'] = $state; 


$microsoft = new Auth($tenant, $client_id, $client_secret, $callback, $scopes);
$microsoft->setState($state); 

header('location: ' . $microsoft->getAuthUrl());
