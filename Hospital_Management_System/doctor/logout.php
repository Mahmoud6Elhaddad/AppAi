<?php
require_once __DIR__ . '/../config.php';

// Clear all session data
$_SESSION = [];

// Destroy session cookie if present
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params['path'],
      $params['domain'],
      $params['secure'],
      $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect to doctor login page in the same folder
header('Location: login.php');
exit;