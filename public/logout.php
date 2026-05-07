<?php
// public/logout.php
require_once '../includes/session.php';

$_SESSION = [];
session_destroy();
header('Location: connexion.php');
exit;