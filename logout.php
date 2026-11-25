<?php
require_once __DIR__ . '/src/helpers.php';

$_SESSION = [];
session_destroy();

session_start();
definirFlash('success', 'Você saiu da sua conta.');
redirecionar('login.php');