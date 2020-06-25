<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// $dsn = "mysql:host=localhost;dbname=taskdb";
// $user = "leimk";
// $passwd = "Yindi!@#";
class DB {

  private static $writeDBconnection;
  private $dsn = "mysql:host=localhost1;dbname=taskdb";
  private $user = "leimk";
  private $passwd = "Yindi!@#";
  private static $readDBconnection;
  public function __construct(){
    $dsn = "mysql:host=localhost;dbname=taskdb";
    $user = "leimk";
    $passwd = "Yindi!@#";
    $this->db = new PDO($dsn, $user,$passwd);
  }

  public function show(){
    $query = $this->db->prepare("select * from tbltasks");
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
  }

  $db = new DB();
  // print_r($db->show());s
  // public static function connectWriteDB(){
  //   // print self::$writeDBconnection;
  //   // if(self::$writeDBconnection === null){
  //   //.
  //   self::$writeDBconnection = new PDO($this->dsn, $this->$user, $this->passwd);
  //   self::$writeDBconnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  //   self::$writeDBconnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  //   // try{
  //   //   self::$writeDBconnection = new PDO('mysql:host=localhost;dbname=taskdb;','leimk','Yindi!@#');
  //   //   self::$writeDBconnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  //   //   self::$writeDBconnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  //   // }
  //   // catch(PDOEXCEPTION $ex){
  //   //   echo $e->getMessage();
  //   //   exit();
  //   //
  //   // }
  //     // var_dump(self::$writeDBconnection);
  //   // }
  //
  //   // $pdo = new PDO('mysql:host=localhost;dbname=taskdb;','leimk','Yindi!@#');
  // }

}
 ?>
