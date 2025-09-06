<?php
// logout.php
require_once __DIR__ . '/core/Auth.php';
$auth = new Auth();
$auth->logout();
