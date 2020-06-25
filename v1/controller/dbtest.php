<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../lib/Db.php';
require_once '../model/Response.php';
// $response = new Response();
// $response->setHttpStatusCode(200);
// $response->setSuccess(true);
// $response->addMessage('aaaaaaaa');
// $response->send();

try{
  // print 'aaaa';
  $db = new DB();

  $sql = "select * from tbltasks";
  // print $sql;
  print 'hasil:<br>';
  print_r($db->query($sql));

  // $response = new Response();
  // $response->setHttpStatusCode(200);
  // $response->setSuccess(true);
  // $response->addMessage($db->show());
  // $response->send();
  // exit;
}
catch(PDOException $ex){
  // $response = new Response();
  // $response->setHttpStatusCode(500);
  // $response->setSuccess(false);
  // $response->addMessage('Database connection Error');
  // $response->send();
  // exit;
}
?>
