<?php
/**
 * Logout action that clears the session and returns the user home.
 */
require_once '../includes/functions.php';
session_destroy();
redirect('pages/login.php');
?>