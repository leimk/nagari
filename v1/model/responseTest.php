<?php
require_once './Response.php';

$response = new Response();

$response->setSuccess(true);
$response->setHttpStatusCode(200);
$response->addMessage('Test MEssage 1');
$response->addMessage('TEst message 2');

$response->send();

?>
