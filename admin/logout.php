<?php
require_once '../includes/config.php';
require_once '../includes/Utils.php';

Utils::adminLogout();
header('Location: login.php');
exit;
?>