<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once './Task.php';

try{
  $task = new Task(1,'Title Here','Description Here','01/01/2019 12:00','N');
  header('Content-Type: Application/json;charset=UTF-8');
  echo json_encode($task->returnTaskAsArray());

}catch(TaskException $ex){
  echo "Error:".$ex->getMessage();
}

 ?>
