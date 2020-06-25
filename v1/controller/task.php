<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once './db.php';
require_once '../model/Response.php';
require_once '../model/Task.php';

try{

  $writeDb = DB::connectWriteDB();

}
catch(PDOEXCEPTIOn $ex){
  error_log('Connection Error - '.$ex,0);
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage('Database Connection Error');
  $response->send();
  exit();
}


if(array_key_exists('taskid',$_GET)){
  $taskid= $_GET['taskid'];

  if($taskid == ''|| !is_numeric($taskid)){
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage('Task ID cannot be blank or must be numeric');
    $response->send();
    exit();
  }
}

if($_SERVER['REQUEST_METHOD'] === 'GET'){

  try{
    $query = $writeDb->prepare("select id,title,description,DATE_FORMAT(deadline,\"%d/%m/%Y %H:%i\") as deadline,completed from tbltasks where id  = :taskid");

    $query->bindParam(':taskid',$taskid,PDO::PARAM_INT);
    $query->execute();

    $rowCount = $query->rowCount();

    if($rowCount===0){
      $response = new Response();
      $response->setHttpStatusCode(404);
      $response->setSuccess(false);
      $response->addMessage('Task NOt Found');
      $response->send();
      exit();
    }else{
      while($row = $query->fetch(PDO::FETCH_ASSOC)){
        $task = new Task(
                    $row['id'],
                    $row['title'],
                    $row['description'],
                    $row['deadline'],
                    $row['completed']);

        $taskArray[]= $task->returnTaskAsArray();
      }
      $returnData = array();
      $returnData['rows_returned'] = $rowCount;
      $returnData['tasks'] = $taskArray;

      $response = new Response();
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->toCache(true);
      $response->setData($returnData);
      $response->send();
      exit();

    }

  }
  catch(TaskException $taskEx){
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage($taskex->getMessage());
    $response->send();
    exit();
  }
  catch(PDOEXCEPTION $ex){
    error_log('Database Query Error - '.$ex,0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage('Failed to get task');
    $response->send();
    exit();
  }

}else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){

}else if($_SERVER['REQUEST_METHOD'] === 'PATCH'){

}else{
  $response = new Response();
  $response->setHttpStatusCode(405);
  $response->setSuccess(false);
  $response->addMessage('Request Method not allowed');
  $response->send();
  exit();
}


 ?>
