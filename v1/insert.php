<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-type: application/json');
require_once './lib/function.php';

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    http_response_code(403);
    $arr = array(
        'statusCode' => '403',
        'errMsg' => 'Method Salah'
    );
    print json_encode($arr);
}else{
    require_once './lib/Db.php';
    $db =new Db();
    // $tmstmp =  date("Y-m-d H:i:s");
    $data = implode(",",$_POST);
    
    // VALIDASI ---------------------------------------------------------------------------------------------------------------
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    $pk = $_POST['no_pk'];
    //Cek required par --------------------------------------------------------------------------------------

    $nama = $_POST['nama'];
    $ktp = $_POST['ktp'];
    $tglLahir = $_POST['bdate'];
    $pekerjaan = $_POST['pekerjaan'];
    $jkelamin = $_POST['jnsKelamin'];
    $usia = $_POST['usia'];
    $jenis = $_POST['jenis'];
    if(!isset($nama) || !isset($ktp) || !isset($tglLahir) || !isset($pekerjaan) || !isset($kelamin) || !isset($usia) || !isset($jenis)){
        $arr = array(
            'statusCode'    =>  '400',
            'errMsg'        => 'Periksa kembali parameter wajib'
        );
        http_response_code(400);
        print json_encode($arr);
    }

    //CEK FORMAT TANGGAL-------------------------------------------------------------------------------------
    if(isset($sdate) && isset($edate)){
        if(!check_date($sdate) && !check_date($edate)){
            // header('HTTP/1.1 204: Penulisan Tanggal Salah');
            // $http_code = ;?
            $errMsg = 'Penulisan Tanggal Salah';
            $arr = array(
                'statusCode' => '400',
                'errMsg' => $errMsg
            );
            $arr = json_encode($arr);
            
            // print $arr;
    
            return http_response_code(400);
            return $arr;
        
        }
    }
    

    // $pk = $_POST['no_pk'];
    // $nama = $_POST['nama'];
    // $ktp = $_POST['ktp'];
    // $tglLahir = $_POST['bdate'];
    // $pekerjaan = $_POST['pekerjaan'];
    // $jkelamin = $_POST['jnsKelamin'];
    // $usia = $_POST['usia'];
    // $jenis = $_POST['jenis'];


    // $sqlLog = "insert into incomingLog(data)values('$data')";

    // // print $sql;/
    // if($_SERVER['REQUEST_METHOD']=='POST'){
    //     if(count($_POST)>0){
    // //        print "<pre>";
    //         print_r($_POST);  
    //         $data= $_POST;
    //         switch(strtolower($data['jenis'])){
    //             case 'baru' : break;
    //             case 'covid':break;
    //             case 'komersil':break;
    //             case 'meninggalsaja': break;
    //             default: return http_response_code(204);
    //         }          
    //         $msg = $db->exe($sqlLog) or die($db->error());    
    //     }else{
    //         http_response_code(204);
    //     }
    // }


}
// var_dump($_POST);




?>