<?php
require '../backend/auth.php';

$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
?>
