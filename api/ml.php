<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');


extract($_POST);
if (!isset($from) || !isset($message) || !isset($key) || ($key !== 'f>6Ea@/N7e')) {
    echo json_encode(['status' => 'failed', 'error' => 'Missing from or message or wrong key']);
    exit();
}

mail('ngxtri@gmail.com', "Mail from api " . $_SERVER['REMOTE_ADDR'] . " " . $_SERVER['HTTP_HOST'], $message);