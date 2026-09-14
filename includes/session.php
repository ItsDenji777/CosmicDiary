<?php
/**
 * Session start and login-state guard used by protected pages.
 */
require_once 'config.php';
require_once 'functions.php';
if (!isLoggedIn()) {
    redirect('pages/login.php');
}
?>