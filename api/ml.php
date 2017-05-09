<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');


extract($_POST);
if (!isset($from) || !isset($message)) {
    echo json_encode(['status' => 'failed', 'error' => 'Missing from or message']);
    exit();
}

mail('ngxtri@gmail.com', "Mail from api " . $_SERVER['REMOTE_ADDR'] . " " . $_SERVER['HTTP_HOST'], $message);