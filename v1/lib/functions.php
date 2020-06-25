<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// require_once '../lib/Financial.php';
require_once 'Db.php';

function posisiCicilan($pv, $rate, $nper, $posisi)
{
	$cicilan = pmt($rate, $nper, $pv);
	$i = 1;
	$sisa = $pv;
	while ($i <= $posisi) {

		$bunga = $rate / 1200 * $sisa;
    // print "bunga $i : ". number_format($bunga,2);
		$pokok = $cicilan - $bunga;
    // print "- pokok : ". number_format($pokok,2);
		$sisa -= $pokok;
    // print "- sisa : ". number_format($sisa,2)."<br>";

		$i++;
	}
	$arr = array(
		"bunga" => $bunga,
		"pokok" => $pokok,
		"sisa" => $sisa
	);
	return $arr;
}
/**
 * PHP Version of PMT in Excel.
 *
 * @param float $apr
 *   Interest rate.
 * @param integer $term
 *   Loan length in years.
 * @param float $loan
 *   The loan amount.
 *
 * @return float
 *   The monthly mortgage amount.
 */
function pmt($apr, $term, $loan)
{
	$term = $term * 12;
	$apr = $apr / 1200;
	$amount = $apr * -$loan * pow((1 + $apr), $term) / (1 - pow((1 + $apr), $term));
	return $amount;
}
function qPolicy($pol)
{
	$conn = sqlServerConnect();
	$sql = "SELECT A.POLICYNO,A.AName,A.SDATE,A.EDATE,AI.* FROM ACCEPTANCE A
		JOIN AINFO AI ON A.ANO=AI.ANO
		WHERE POLICYNO='$pol'";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$interest = qICover($a['ANO']);
		$risk = qRCover($a['ANO']);
		$genH[1] = qGhtab($a['FLDID1']);
		$genH[2] = qGhtab($a['FLDID2']);
		$genH[3] = qGhtab($a['FLDID3']);
		$genH[4] = qGhtab($a['FLDID4']);
		$genH[5] = qGhtab($a['FLDID5']);
		$genH[6] = qGhtab($a['FLDID6']);
		$genH[7] = qGhtab($a['FLDID7']);
		$genH[8] = qGhtab($a['FLDID8']);
		$genH[9] = qGhtab($a['FLDID9']);
		$genH[10] = qGhtab($a['FLDID10']);
		$genH[11] = qGhtab($a['FLDID11']);
		$genH[12] = qGhtab($a['FLDID12']);
		$genH[13] = qGhtab($a['FLDID13']);
		$genH[14] = qGhtab($a['FLDID14']);
		$genH[15] = qGhtab($a['FLDID15']);

		$arr = array(
			"polis" => $a['POLICYNO'],
			"ttg" => $a['AName'],
			"sdate" => $a['SDATE'],
			"edate" => $a['EDATE'],
			"fldid1" => $genH[1],
			"valuedesc1" => $a['VALUEDESC1'],
			"fldid2" => $genH[2],
			"valuedesc2" => $a['VALUEDESC2'],
			"fldid3" => $genH[3],
			"valuedesc3" => $a['VALUEDESC3'],
			"fldid4" => $genH[4],
			"valuedesc4" => $a['VALUEDESC4'],
			"fldid5" => $genH[5],
			"valuedesc5" => $a['VALUEDESC5'],
			"fldid6" => $genH[6],
			"valuedesc6" => $a['VALUEDESC6'],
			"fldid7" => $genH[7],
			"valuedesc7" => $a['VALUEDESC7'],
			"fldid8" => $genH[8],
			"valuedesc8" => $a['VALUEDESC8'],
			"fldid9" => $genH[9],
			"valuedesc9" => $a['VALUEDESC9'],
			"fldid10" => $genH[10],
			"valuedesc10" => $a['VALUEDESC10'],
			"fldid11" => $genH[11],
			"valuedesc11" => $a['VALUEDESC11'],
			"fldid12" => $genH[12],
			"valuedesc12" => $a['VALUEDESC12'],
			"fldid13" => $genH[13],
			"valuedesc13" => $a['VALUEDESC13'],
			"fldid14" => $genH[14],
			"valuedesc14" => $a['VALUEDESC14'],
			"fldid15" => $genH[15],
			"valuedesc15" => $a['VALUEDESC15']
		);

	}
	$gabung = array(
		"info" => $arr,
		"risk" => $risk,
		"interest" => $interest
	);
	//debug($gabung);
	return $gabung;
}

function qGhtab($fldid)
{
	$conn = sqlServerConnect();
	$sql = "SELECT FLDTAG FROM GENHTAB WHERE FLDID='$fldid'";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	$a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);
	return $a['FLDTAG'];
}

function qICover($ano)
{
	$conn = sqlServerConnect();
	$sql = "SELECT REMARK,CURRENCY,SI FROM ICOVER WHERE ANO='$ano'";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"remark" => $a['REMARK'],
			"kurs" => $a['CURRENCY'],
			"tsi" => $a['SI']
		);
	}
	return $arr;
}

function qRCover($ano)
{
	$conn = sqlServerConnect();
	$sql = "SELECT REMARK,RATE,UNIT FROM RCOVER WHERE ANO='$ano'";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"remark" => $a['REMARK'],
			"kurs" => $a['RATE'],
			"tsi" => $a['UNIT']
		);
	}
	return $arr;
}

function achivementByMonthY($tahun)
{
	$i = 1;
	while ($i <= 4) {
		switch ($i) {
			case 1:
				$quarter = array('1', '2', '3');
				break;
			case 2:
				$quarter = array('4', '5', '6');
				break;
			case 3:
				$quarter = array('7', '8', '9');
				break;
			case 4:
				$quarter = array('10', '11', '12');
				break;
			default:
				break;
		}

		// $tahun = date('Y');
		foreach ($quarter as $val) {
			foreach (branchList() as $cbg) {
				$gp = gpPerBulan($cbg['idCabang'], $val, $tahun);
				$budget = budgetPerBulan($cbg['idCabang'], $val, $tahun);
				$persen = $gp / $budget * 100;

				switch ($i) {
					case 1:
						$qua = 'q1';
						break;
					case 2:
						$qua = 'q2';
						break;
					case 3:
						$qua = 'q3';
						break;
					case 4:
						$qua = 'q4';
						break;
				}

				switch ($val) {
					case 1:
						$bln = "JAN";
						break;
					case 2:
						$bln = "FEB";
						break;
					case 3:
						$bln = "MAR";
						break;
					case 4:
						$bln = "APR";
						break;
					case 5:
						$bln = "MAY";
						break;
					case 6:
						$bln = "JUN";
						break;
					case 7:
						$bln = "JUL";
						break;
					case 8:
						$bln = "AUG";
						break;
					case 9:
						$bln = "SEP";
						break;
					case 10:
						$bln = "OCT";
						break;
					case 11:
						$bln = "NOV";
						break;
					case 12:
						$bln = "DEC";
						break;

				}

				$arr[$qua][$cbg['namaCabang']][$bln] = array(
					"premi" => $gp,
					"budget" => $budget,
					"persen" => $persen
				);
			}
		}
		$i++;
	}

	return $arr;

}

function achivementByMonth()
{
	$i = 1;
	while ($i <= 4) {
		switch ($i) {
			case 1:
				$quarter = array('1', '2', '3');
				break;
			case 2:
				$quarter = array('4', '5', '6');
				break;
			case 3:
				$quarter = array('7', '8', '9');
				break;
			case 4:
				$quarter = array('10', '11', '12');
				break;
			default:
				break;
		}

		$tahun = date('Y');

		foreach ($quarter as $val) {
			foreach (branchList() as $cbg) {
				$gp = gpPerBulan($cbg['idCabang'], $val, $tahun);

				$budget = budgetPerBulan($cbg['idCabang'], $val, $tahun);
				$persen = $gp / $budget * 100;

				switch ($i) {
					case 1:
						$qua = 'q1';
						break;
					case 2:
						$qua = 'q2';
						break;
					case 3:
						$qua = 'q3';
						break;
					case 4:
						$qua = 'q4';
						break;
				}

				switch ($val) {
					case 1:
						$bln = "JAN";
						break;
					case 2:
						$bln = "FEB";
						break;
					case 3:
						$bln = "MAR";
						break;
					case 4:
						$bln = "APR";
						break;
					case 5:
						$bln = "MAY";
						break;
					case 6:
						$bln = "JUN";
						break;
					case 7:
						$bln = "JUL";
						break;
					case 8:
						$bln = "AUG";
						break;
					case 9:
						$bln = "SEP";
						break;
					case 10:
						$bln = "OCT";
						break;
					case 11:
						$bln = "NOV";
						break;
					case 12:
						$bln = "DEC";
						break;

				}

				$arr[$qua][$cbg['namaCabang']][$bln] = array(
					"premi" => $gp,
					"budget" => $budget,
					"persen" => $persen
				);
			}
		}
		$i++;
	}

	return $arr;

}


function addUser($arr)
{
	require_once '../lib/Db.php';
	$db = new Db();
	$sql = "insert into login (login, passwd, nama, cabang, departmentID) values ('" .
		$arr['username'] . "','" .
		md5($arr['pwd']) . "','" .
		$arr['nama'] . "','" .
		$arr['cabang'] . "','" .
		$arr['dept'] . "')";
	return $db->exe($sql);
}

function agingCurrency($branch, $cob)
{
	$conn = sqlServerConnect();
	$today = date('m/d/Y');
	if (isset($branch)) {
		if ($branch == '10') {
			$branch = "";
		} else {
			$branch = "BRANCH = '$branch' and ";
		}
	}
	$firstDayCurrent = '01/01/2013';
	$today = date('m/d/Y');
	$sql = "Select Voucher.* into #Voucher from Admlink, nVoucher Voucher, TOC, Profile
                Where Profile.ID=Admlink.ID and TOC.TOC=Admlink.Code and Voucher.Voucher=Admlink.Voucher  and Voucher.Date<='$today'
                and ((Voucher.Nominal_CC-Voucher.Diff_CC-Voucher.Payment_CC-Voucher.Payment_OC)<>0 or Voucher.PDate>='$today')
                and ((Admlink.Type in ('DI','BO') and Admlink.Subject in ('Premium')) or
                (Admlink.Type in ('') and Admlink.Subject in ('')) or (Admlink.Type in ('') and Admlink.Subject in ('')))
                and Admlink.Date Between '$firstDayCurrent' and '$today' and payment_cc=0";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));

	$sql = "SELECT DISTINCT CURRENCY FROM #VOUCHER WHERE $branch SUBSTRING(VTYPE,4,2) = '$cob'";
	$query_result = sqlsrv_query($conn, $sql);
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"kurs" => $a['CURRENCY'],
			"amount1" => 0,
			"amount2" => 0,
			"amount3" => 0,
			"amount4" => 0
		);
	}
	return $arr;
}



function agingQuery($branch, $cob, $source)
{
	$conn = sqlServerConnect();

	$firstDayCurrent = '01/01/2013';
	$today = date('m/d/Y');
//      $sql = "drop table #voucher";
//      sqlsrv_query($conn,$sql) or die(debug(sqlsrv_errors()));
	if ($source <> '') {
		$sour = " and admlink.source in (" . $source . ") ";
	} else {
		$sour = '';
		$source = '';
	}

	$sql = "Select Admlink.SOURCE as bsource,Voucher.* into #Voucher from Admlink, nVoucher Voucher, TOC, Profile
                Where Profile.ID=Admlink.ID and TOC.TOC=Admlink.Code and Voucher.Voucher=Admlink.Voucher  and Voucher.Date<='$today'
                and ((Voucher.Nominal_CC-Voucher.Diff_CC-Voucher.Payment_CC-Voucher.Payment_OC)<>0 or Voucher.PDate>='$today')
                and ((Admlink.Type in ('DI','BO') and Admlink.Subject in ('Premium')) or
                (Admlink.Type in ('') and Admlink.Subject in ('')) or (Admlink.Type in ('') and Admlink.Subject in ('')))
                and Admlink.Date Between '$firstDayCurrent' and '$today' and payment_cc=0 $sour";

	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));


	if (isset($branch)) {
		if ($branch == '10') {
			$branch = "BRANCH LIKE '%'";
		} else {
			$branch = "BRANCH = '$branch'";
		}
	}

	$sql = "select bsource,SUBSTRING(VTYPE,4,2) AS COB,DOCNO,PROACC,REMARKS,VOUCHER,DATEDIFF(D,DUEDATE,'$today') DAYDUED,CURRENCY,DEBTORF,CREDITORF,
		AMOUNTDUE FROM #VOUCHER WHERE $branch and SUBSTRING(VTYPE,4,2) = '$cob'
		ORDER BY BRANCH,bsource,CURRENCY,SUBSTRING(VTYPE,4,2),DATEDIFF(D,DUEDATE,'$today'),DOCNO,PROACC";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {

		if ($a['DEBTORF'] > 0) {
			$amountdue = $a['AMOUNTDUE'];
		} else {
			$amountdue = -$a['AMOUNTDUE'];
		}

		$aging[] = array(
			"bsource" => $a['bsource'],
			"currency" => $a['CURRENCY'],
			"docno" => $a['DOCNO'],
			"cob" => $a['COB'],
			"proacc" => $a['PROACC'],
			"remarks" => $a['REMARKS'],
			"voucher" => $a['VOUCHER'],
			"daydued" => $a['DAYDUED'],
			"amountdue" => $amountdue
		);
	}
	return $aging;
}

function branchList()
{
	require_once '../lib/Db.php';
	$db = new Db();
	$sql = "select * from elj_cabang order by idCabang";

	return $cabang = $db->select($sql);
}

function branchMap($code)
{

	switch ($code) {
		case '01':
			$branch = "Jakarta";
			break;
		case '02':
			$branch = "Medan";
			break;
		case '03':
			$branch = "Pekanbaru";
			break;
		case '04':
			$branch = "Surabaya";
			break;
		case '05':
			$branch = "Semarang";
			break;
		case '06':
			$branch = "Yogyakarta";
			break;
		case '07':
			$branch = "Makassar";
			break;
		case '08':
			$branch = "Padang";
			break;
		case '09':
			$branch = "Denpasar";
			break;
		case '12':
			$branch = "Manado";
			break;
		default:
			$branch = "Nasional";
			break;
	}

	return $branch;
}

function branchMapReverse($code)
{

	switch ($code) {
		case 'JAKARTA':
			$branch = "01";
			break;
		case 'MEDAN':
			$branch = "02";
			break;
		case 'PEKANBARU':
			$branch = "03";
			break;
		case 'SURABAYA':
			$branch = "04";
			break;
		case 'SEMARANG':
			$branch = "05";
			break;
		case 'YOGYAKARTA':
			$branch = "06";
			break;
		case 'MAKASSAR':
			$branch = "07";
			break;
		case 'PADANG':
			$branch = "08";
			break;
		case 'DENPASAR':
			$branch = "09";
			break;
		case 'MANADO':
			$branch = "12";
			break;
		default:
			$branch = "10";
			break;
	}

	return $branch;
}

function branchToTarget($cabang)
{
	$cBudget = currentBudget($cabang);
	$asat = gpAsAt('current', $cabang);
	$budget = $cBudget[count($cBudget) - 1][0];
	$achieved = $asat[count($asat) - 1][0];
	$pctg = $achieved / $budget * 100;
	$hasil = array(
		"budget" => $budget,
		"asat" => $achieved,
		"psn" => $pctg
	);
	return $hasil;

}

function budgetPerBulan($cabang, $bulan, $tahun)
{

	require_once '../lib/Db.php';
	$db = new Db();

	if (substr($bulan, 0, 1) == '0') $bulan = substr($bulan, 1, 1);
	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " and cabang='$cabang' ";
		}
	} else {
		$cbg = "";
	}
	$sql = "select sum(gp) as budget from elj_budgetGP where bulan = $bulan $cbg and tahun= $tahun group by bulan";
	print $sql;
	$budget = $db->select($sql);


	return $budget[0]['budget'];
}

function category3($dari, $sampai)
{
	$conn = sqlServerConnect();
	$today = date('m/d/Y');
	$sql = "
		SELECT
      A.ANO AS ANO,
			A.POLICYNO AS POLICYNO,
			A.CERTIFICATENO AS CERT,
			A.ESEQNO AS ENDORS,
                        A.ANAME AS ANAME,
                        AI.VALUEID2 + '(' + AI.VALUEDESC2 + ')' AS OKUPASI,
                        AI.VALUEDESC1 AS KELAS_KONSTRUKSI,
                        AI.VALUEID4 AS INA,
                        AI.VALUEDESC4 AS LOKASI,
	                CASE WHEN RH.PCTShare >=100 THEN RH.PCTShare-RH.MSHARE ELSE RH.PCTSHARE END AS SHARE,
                        R.RATE as RATE,
			IC.CURRENCY AS KURS,
                        CASE WHEN IC.TOI in ('B01','B02') THEN IC.AMOUNT ELSE 0 END AS BANGUNAN,
                        CASE WHEN IC.TOI IN ('S01','S13','S15','S16') THEN IC.AMOUNT ELSE 0 END AS STOCK,
                        CASE WHEN IC.TOI IN ('M01','M12','M15','M22') THEN IC.AMOUNT ELSE 0 END AS MESIN,
                        CASE WHEN IC.TOI NOT IN ('B01','B02','S01','S13','S15','S16','M01','M12','M15','M22') THEN IC.AMOUNT ELSE 0 END AS LAIN2
                INTO #TMP
                FROM ACCEPTANCE A
                JOIN AINFO AI
                ON A.ANO=AI.ANO
                JOIN ICOVER IC
                ON A.ANO = IC.ANO
                JOIN RARRHEADER RH
                ON A.ANO = RH.ANO
                JOIN COVER C
                ON A.CNO = C.CNO
                JOIN RERATE R
                ON C.TOC = R.TOC AND YEAR(A.SDATE) = R.RYEAR AND IC.CURRENCY=R.CURRENCY
                WHERE
                        AI.FLDID3='R11'
                        AND AI.VALUEID3='III'
                        AND A.ADATE BETWEEN '$dari' AND '$sampai'
                        AND A.POLICYNO<>''
			AND A.ASTATUS NOT IN ('T','W')

                GROUP BY A.POLICYNO,A.ANO,A.CERTIFICATENO,A.ESEQNO,A.ANAME,AI.VALUEID2,AI.VALUEDESC2,AI.VALUEDESC1,AI.VALUEID4,AI.VALUEDESC4,IC.CURRENCY,IC.TOI,IC.AMOUNT,RH.PCTShare,R.RATE,RH.MSHARE
                ORDER BY A.ANO;";
	$query_result = sqlsrv_query($conn, $sql);
	$sql = "
		SELECT
	                			ANO,
                        POLICYNO,
												CERT,
												ENDORS,
                        ANAME,
                        OKUPASI,
                        KELAS_KONSTRUKSI,
                        INA,
                        LOKASI,
                        SHARE,
												KURS,
                        SUM(BANGUNAN) AS BANGUNAN,
                        SUM(STOCK) AS STOCK,
                        SUM(MESIN) AS MESIN,
                        SUM(LAIN2) AS LAIN2
                INTO #TMP2
                FROM #TMP
                GROUP BY POLICYNO,ANO,KURS,CERT,ENDORS,ANAME,OKUPASI,KELAS_KONSTRUKSI,INA,LOKASI,SHARE;";
	$query_result = sqlsrv_query($conn, $sql);
	$sql = "
                SELECT * INTO #RCAL FROM RCOVER WHERE ANO IN (SELECT ANO FROM #TMP2) ORDER BY ANO;";
	$query_result = sqlsrv_query($conn, $sql);
	$sql = "SELECT * FROM #TMP2;";
	$query_result = sqlsrv_query($conn, $sql);
	$i = 0;
	while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		if ($hasil['SHARE'] < 100) {
			$arr[$i] = array(
				"ano" => $hasil['ANO'],
				"polis" => $hasil['POLICYNO'],
				"cert" => $hasil['CERT'],
				"end" => $hasil['ENDORS'],
				"aname" => $hasil['ANAME'],
				"okupasi" => $hasil['OKUPASI'],
				"kelas" => $hasil['KELAS_KONSTRUKSI'],
				"ina" => $hasil['INA'],
				"lokasi" => $hasil['LOKASI'],
				"share" => $hasil['SHARE'],
				"rate" => $hasil['RATE'],
				"kurs" => $hasil['KURS'],
				"bangunan" => $hasil['BANGUNAN'],
				"stock" => $hasil['STOCK'],
				"mesin" => $hasil['MESIN'],
				"lain" => $hasil['LAIN2']
			);
		} else {
			$arr[$i] = array(
				"ano" => $hasil['ANO'],
				"polis" => $hasil['POLICYNO'],
				"cert" => $hasil['CERT'],
				"end" => $hasil['ENDORS'],
				"aname" => $hasil['ANAME'],
				"okupasi" => $hasil['OKUPASI'],
				"kelas" => $hasil['KELAS_KONSTRUKSI'],
				"ina" => $hasil['INA'],
				"lokasi" => $hasil['LOKASI'],
				"share" => $hasil['SHARE'],
				"rate" => $hasil['RATE'],
				"kurs" => $hasil['KURS'],
				"bangunan" => $hasil['BANGUNAN'] * $hasil['SHARE'] / 100,
				"stock" => $hasil['STOCK'] * $hasil['SHARE'] / 100,
				"mesin" => $hasil['MESIN'] * $hasil['SHARE'] / 100,
				"lain" => $hasil['LAIN2'] * $hasil['SHARE'] / 100
			);
		}
		$i++;
	}
	return $arr;
}

function category3spreading($ano)
{
	$conn = sqlServerConnect();
	$sql = "SELECT * from rarrheader where ano='$ano'";
	$query_result = sqlsrv_query($conn, $sql);
	while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"ano" => $hasil['ANO'],
			"share" => $hasil['PCTSHARE'],
			"mshare" => $hasil['MSHARE'],
			"mamount" => $hasil['MAMOUNT'],
			"retention" => $hasil['RETENTION'],
			"ramount" => $hasil['RAMOUNT'],
			"shortfall" => $hasil['SHORTFALL'],
			"samount" => $hasil['SAMOUNT'],
			"cshare" => $hasil['CSHARE'],
			"camount" => $hasil['CAMOUNT'],
			"qamount" => $hasil['QSRAmount'],
			"qshare" => 100 - $hasil['CSHARE'] - $hasil['SP1SHARE'] - $hasil['FSHARE'],
			"xpshare" => $hasil['XPSHARE'],
			"xpamount" => $hasil['XPAMOUNT'],
			"spshare" => $hasil['SP1SHARE'],
			"spamount" => $hasil['SP1AMOUNT'],
			"fshare" => $hasil['FSHARE'],
			"famount" => $hasil['FAMOUNT']
		);
	}
	return $arr;
}

function category3fac($ano)
{
	$conn = sqlServerConnect();
	$sql = "SELECT * FROM FPART where ano='$ano'";
	$query_result = sqlsrv_query($conn, $sql);
	while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {

	}

}

function category3x($ano)
{
	$conn = sqlServerConnect();
	$firstDayCurrent = '01/01/' . date('Y');
	$today = date('m/d/Y');
	$sql = "SELECT *
		FROM RCOVER RC
		where rc.ano='$ano'";
	$query_result = sqlsrv_query($conn, $sql);
	while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"ano" => $hasil['ANO'],
			"code" => $hasil['Code'],
			"desc" => $hasil['REMARK'],
			"rate" => $hasil['RATE']
		);
	//		"kurs"	=>	$hasil['CURRENCY'],
	//		"gross"	=>	$hasil['Gross']);
	}
	return $arr;
}

function category3premi($ano, $kurs)
{
	$conn = sqlServerConnect();
	$sql = "select currency_oc,type,sum(amount_oc) as premi from accass where ano='$ano'  and code='P'
		group by currency_oc,type";
	$query_result = sqlsrv_query($conn, $sql);
	while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"ano" => $hasil['ANO'],
			"kurs" => $hasil['CURRENCY_OC'],
			"tipe" => $hasil['TYPE'],
			"premi" => $hasil['premi'],
			"rate" => $hasil['RATE']
		);
	}
	return $arr;
}

function cekError($batchNo)
{
//	$batchNo="0017773";
	$conn = sqlServerConnect();
	$sql = "SELECT BatchNo,ErrMsg,OBJ_INFO_03,OBJ_INFO_04 FROM SYSBATCHORIGINALUP WHERE BATCHNO LIKE '%" . $batchNo . "%' AND ERRMSG<>'' ORDER BY BATCHNO";
	$query_result = sqlsrv_query($conn, $sql);
	while ($hsl = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$hasil[] = array(
			"batchNo" => $batchNo,
			"errMsg" => $hsl['ErrMsg'],
			"nama" => $hsl['OBJ_INFO_03'],
			"tglLahir" => $hsl['OBJ_INFO_04']
		);
	}

	return $hasil;

}

function cekErrorTrial($batchNo)
{
//	$batchNo="0017773";
	$conn = sqlServerConnect('trial');
	$sql = "SELECT BatchNo,ErrMsg,OBJ_INFO_03,OBJ_INFO_04 FROM SYSBATCHORIGINALUP WHERE BATCHNO LIKE '%" . $batchNo . "%' AND ERRMSG<>'' ORDER BY BATCHNO";
	$query_result = sqlsrv_query($conn, $sql);
	while ($hsl = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$hasil[] = array(
			"batchNo" => $batchNo,
			"errMsg" => $hsl['ErrMsg'],
			"nama" => $hsl['OBJ_INFO_03'],
			"tglLahir" => $hsl['OBJ_INFO_04']
		);
	}

	return $hasil;

}

function cekLogin($login, $passwd)
{
	require_once '../lib/Db.php';
	$db = new Db();
	$passwd = md5($passwd);
	$sql = "select * from login where login='$login' and passwd='$passwd'";
	if (count($db->select($sql)) == 1) {
		return $db->select($sql);
	} else {
		return '!ok';
	}
}

function claimAsAt($par, $cabang)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = '01/01/' . date('Y');
	$today = date('m/d/Y');

	if (isset($cabang)) {

		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}

	if ($par == 'current') {
		$firstDayCurrent = '01/01/' . date('Y');
		$today = date('m/d/Y');
	} else {
		$thn = date('Y') - 1;
		$bln = date('m');
		$tgl = date('d');
		$firstDayCurrent = "01/01/" . $thn;
                // $today = $bln."/".$tgl."/".$thn;
		$today = "12/31/" . $thn;
	}


	$sql = "SELECT SUM((AMOUNT_6+AMOUNT_7+AMOUNT_8+AMOUNT_9+AMOUNT_10+AMOUNT_12+AMOUNT_17)*RATE) AS CLAIM
		FROM SUMMARY_PRODUCTION_CLAIM('$firstDayCurrent','$today','') WHERE TYPE IN $toc $cbg GROUP BY DATE";

	$query_result = sqlsrv_query($conn, $sql);
	$hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);

	return $hasil['CLAIM'];

}

function statClaimAsAt($cabang)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}
	$par = date('Y') - 5;
	$arr1 = '';
	while ($par <= date('Y')) {
		$firstDayCurrent = '01/01/' . $par;
		if ($par == date('Y')) {
			$today = date('m/d/Y');
		} else {
			$today = '12/31/' . $par;
		}

		$sql = "SELECT SUM((AMOUNT_6+AMOUNT_7+AMOUNT_8+AMOUNT_9+AMOUNT_10+AMOUNT_12+AMOUNT_17)*RATE) AS CLAIM
 		FROM SUMMARY_PRODUCTION_CLAIM('$firstDayCurrent','$today','') WHERE TYPE IN $toc $cbg GROUP BY DATE";

		$query_result = sqlsrv_query($conn, $sql);
		$hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);

		if (count($hasil) <= 0) {
			$arr[] = array("tahun" => $par, "claim" => 0);
			$arr1 .= '"0",';
		} else {
			$arr[] = array("tahun" => $par, "claim" => $hasil['CLAIM']);
			$arr1 .= '"' . (number_format($hasil['CLAIM'] / 1000000000 * -1, 2)) . '",';
		}

		$par++;
	}


	return $arr1;

}

function claimByCOB($cabang)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = '01/01/' . date('Y');
	$today = date('m/d/Y');


	// $firstDayCurrent = '01/01/'.$tahun;
	//  $today = '3/31/'.$tahun;

	if (isset($cabang)) {

		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}

	$sql = "SELECT SUBSTRING(CODE,1,2) AS COB,SUM((AMOUNT_6+AMOUNT_7+AMOUNT_8+AMOUNT_9+AMOUNT_10+AMOUNT_12+AMOUNT_17)*RATE) AS CLAIM
                FROM SUMMARY_PRODUCTION_CLAIM('$firstDayCurrent','$today','') WHERE TYPE IN $toc $cbg GROUP BY SUBSTRING(CODE,1,2)";
	$query_result = sqlsrv_query($conn, $sql);

	while ($hsl = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$hasil[] = array(
			"cob" => $hsl['COB'],
			"claim" => $hsl['CLAIM']
		);
	}

	return $hasil;
}

function cobBudget($cabang, $cob)
{
	require_once '../lib/Db.php';
	$db = new Db();
	$tahun = date('Y');
	if (isset($cabang)) {
		if (strlen($cabang) == 1)
			$cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
			if (isset($cob)) {
				$cob = " where toc='$cob' and tahun=$tahun";
			} else {
				$cob = " where tahun=$tahun";
			}
		} else {
			$cbg = " where cabang='$cabang' ";
			if (isset($cob)) {
				$cob = " and toc='$cob' and tahun=$tahun ";
			} else {
				$cob = " and tahun=$tahun";
			}
		}
	} else if (isset($cob)) {
		$cob = " where toc='$cob' ";
	} else {
		$cob = " where tahun=$tahun";
		$cbg = "";
	}


	$sql = "select toc, sum(gp) as premi from elj_budgetGP $cbg $cob group by toc";
	return $db->select($sql);
}


function cobCurrent($cob, $cabang)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = '01/01/' . date('Y');
	// $today = date('m/d/Y');
//	$firstDayCurrent = '01/01/2016';
//	$today = '12/31/2016';
	$today = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
	$today = date('m') . "/" . $today . "/" . date('Y');
  // $today = '9/30/2018';

	if (isset($cabang)) {

		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND A.BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}


	$sql = "SELECT SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) as premi FROM ADMLINK A
                JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
                WHERE A.DATE BETWEEN '$firstDayCurrent' AND '$today'
                AND A.TYPE in $toc and code like '$cob%' $cbg
                GROUP BY SUBSTRING(CODE,1,2)";
  // print $sql;
	$query_result = sqlsrv_query($conn, $sql);
	return sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);
}

function cobList($cob)
{

	switch ($cob) {
		case '01':
			$cob = "PROPERTY";
			break;
		case '02':
			$cob = "MOTOR";
			break;
		case '03':
			$cob = "CARGO";
			break;
		case '04':
			$cob = "MRN HULL";
			break;
		case '05':
			$cob = "AVI HULL";
			break;
		case '06':
			$cob = "SAT";
			break;
		case '07':
			$cob = "ENERGY";
			break;
		case '08':
			$cob = "ENGINEERING";
			break;
		case '09':
			$cob = "LIABILITY";
			break;
		case '10':
			$cob = "GA";
			break;
		case '11':
			$cob = "CREDITS";
			break;
		case '12':
			$cob = "MISC";
			break;
		default:
			if ($cob == '1006') {
				$cob = "PA";
			} else {
				$cob = "PA+";
			}
			break;
	}

	return $cob;
}

function currency($bulan, $currency)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";

	$sql = "select rate from fn_exchange('$bulan') where currency='$currency'";

	$query_result = sqlsrv_query($conn, $sql);

	return sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);
}

function currentBudget($cabang)
{
	require_once '../lib/Db.php';
	$db = new Db();
	$bulan = date('m');
	$tahun = date('Y');
	//$bulan = 12;
	if (substr($bulan, 0, 1) == '0') $bulan = substr($bulan, 1, 1);
	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " and cabang='$cabang' ";
		}
	} else {
		$cbg = "";
	}
	$sql = "select bulan,sum(gp) as budget from elj_budgetGP where bulan <= $bulan $cbg and tahun= $tahun group by bulan";
	$budget = $db->select($sql);
	$asatBudget = 0;
	foreach ($budget as $val) {
		$asatBudget += round($val['budget'] / 1000000, 2);
		$arrBudget[] = array($asatBudget);
	}

	return $arrBudget;
}

function currentBudgetByCOBnMonth($cabang, $cob)
{
	require_once '../lib/Db.php';
	$db = new Db();
	$bulan = date('m');
	$tahun = date('Y');
	//$bulan = 12;
	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " and cabang='$cabang' ";
		}
	} else {
		$cbg = "";
	}
	$cob = " and toc = '$cob' ";
	$sql = "select toc,sum(gp) as budget from elj_budgetGP where bulan = $bulan $cbg $cob and tahun=$tahun group by toc";
//        $budget = $db->select($sql);

	return $db->select($sql);
}



function debug($arr)
{
	print "<pre>";
	print_r($arr);
//	var_dump($arr);
}

function deptList()
{
	require_once '../lib/Db.php';
	$db = new Db();
	$sql = "select * from elj_department";

	return $db->select($sql);
}

function deptMap($deptID)
{
	require_once '../lib/Db.php';
	$db = new Db();
	$sql = "select * from elj_department where idDept='$deptID'";

	return $db->select($sql);
}

function displayArrayData($data, $key)
{
	$lastRowID = '';

        //Iterrate through each record
	foreach ($data as $dataRow) {
		$subTotal = 0;
                //Subtotal if this record is different ID than last
		if ($dataRow['currency'] != $lastRowID && $lastRowID != '') {
			echo "SubTotal : " . SubTotal($data, $key, $lastRowID);
			echo "<br />";
		}
                //Set last ID value
		$lastRowID = $dataRow['currency'];

                //Display current record
		echo "Currency : " . $dataRow['currency'] . " Amount : " . $dataRow[$key];
		echo "<br />";
	}
            //Close the table and final subtotal
	echo "SubTotal : " . SubTotal($data, $key, $lastRowID);
}

function distributionMix($cbg)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = "01/01/" . date('Y');
	$today = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
	$today = date('m') . "/" . $today . "/" . date('Y');


	if ($cbg < 10) {
		$cbg = "0" . $cbg;
	}

	$sql = "SELECT a.branch,month(A.DATE) as bulan,SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) as premi,a.source as bsource,L.DESCRIPTION
		AS LOB FROM ADMLINK A
                JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
		JOIN PROFILE P ON A.SOURCE = P.ID
		JOIN LOB L ON P.LOB = L.LOB
                WHERE A.DATE BETWEEN '$firstDayCurrent' AND '$today'
                AND A.TYPE in $toc and a.branch = '$cbg'
                GROUP BY MONTH(A.DATE),a.branch,a.source,L.DESCRIPTION
                ORDER BY MONTH(A.DATE)";
	$query_result = sqlsrv_query($conn, $sql);
	while ($dist = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
	//	debug($dist);
		switch (substr($dist['bsource'], 0, 1)) {
			case 'C':
				$captive[$dist['bulan']] += $dist['premi'];
				break;
			case 'D':
				$direct[$dist['bulan']] += $dist['premi'];
				break;
			case 'M':
				$intermediaries[$dist['bulan']] += $dist['premi'];
				break;
			default:
				$others[$dist['bulan']] += $dist['premi'];
				break;
		}
	}
	$arr = array(
		"CAPTIVE" => $captive,
		"INTERMEDIARIES" => $intermediaries,
		"DIRECT" => $direct,
		"OTHERS" => $others
	);
	return $arr;

}


function gpAsAt($par, $cabang)
{

	$toc = "('DI','IC','CF','IT','IR','IX')";
	$conn = sqlServerConnect();

	if ($cabang!='') {

		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND A.BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}


	if ($par == 'current') {
		$firstDayCurrent = '01/01/' . date('Y');

		$today = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
		$today = date('m') . "/" . $today . "/" . date('Y');


	} else {
		$thn = date('Y') - 1;
		$bln = date('m');
		$tgl = date('d');
		$firstDayCurrent = "01/01/" . $thn;
		$today = $bln . "/" . $tgl . "/" . $thn;
	}


	$sql = "SELECT month(A.DATE) as bulan,SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) as premi FROM ADMLINK A
                JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
                WHERE A.DATE BETWEEN '$firstDayCurrent' AND '$today'
                AND A.TYPE in $toc $cbg
                GROUP BY MONTH(A.DATE)
                ORDER BY MONTH(A.DATE)";
	//print $sql;
	$query_result = sqlsrv_query($conn, $sql);
	$asat = 0;
	while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$asat += round($row['premi'] / 1000000, 2);
		$arr[] = array($asat);
	}

	return $arr;
}

function gpBulan($cabang)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = date('m') . '/01/' . date('Y');
	$today = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
	$today = date('m') . "/" . $today . "/" . date('Y');

	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND A.BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}

	$sql = "SELECT SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) as premi FROM ADMLINK A
                JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
                WHERE A.DATE BETWEEN '$firstDayCurrent' AND '$today'
                AND A.TYPE in $toc $cbg
                GROUP BY MONTH(A.DATE)
                ORDER BY MONTH(A.DATE)";

	$query_result = sqlsrv_query($conn, $sql);
	$rownum = sqlsrv_num_rows($query_result);

	return sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);

}

function gpPerBulan($cabang, $bulan, $tahun)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";


	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND A.BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}

	$sql = "SELECT SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) as premi FROM ADMLINK A
                JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
                WHERE month(A.DATE)='$bulan' AND year(A.DATE)='$tahun'
                AND A.TYPE in $toc $cbg
                GROUP BY MONTH(A.DATE)
                ORDER BY MONTH(A.DATE)";

	$query_result = sqlsrv_query($conn, $sql);
	$rownum = sqlsrv_num_rows($query_result);
	$premi = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);

	return $premi['premi'];
}

function gpBulanByCOB($cabang, $cob, $fcob)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = date('m') . '/01/' . date('Y');
	$today = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
	$today = date('m') . "/" . $today . "/" . date('Y');

	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND A.BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}

	switch ($fcob) {
		case 1:
			$cob = " AND CODE LIKE '$cob%' ";
			$s = " CODE";
			$f = 1;
			break;
		case 2:
			$cob = " AND CODE LIKE '$cob%' ";
			$s = " CODE";
			$f = 0;
			break;
		default:
			$cob = " AND SUBSTRING(CODE,1,2) = '$cob' ";
			$s = " SUBSTRING(CODE,1,2) ";
			$f = 1;
			break;
	}
	// if(!isset($fcob)){
	// 	$cob = " AND SUBSTRING(CODE,1,2) = '$cob' ";
	// 	$s = " SUBSTRING(CODE,1,2) ";
	// }else{
	// 	$cob =" AND CODE LIKE '$cob%' ";
	// 	$s = " CODE";
	//
	// }


	$sql = "SELECT  $s as COB,SUM((A.AMOUNT_1+A.AMOUNT_5)*V.RATE) as premi FROM ADMLINK A
                JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
                WHERE A.DATE BETWEEN '$firstDayCurrent' AND '$today'
                AND A.TYPE in $toc $cbg $cob
                GROUP BY MONTH(A.DATE), $s
                ORDER BY MONTH(A.DATE), $s";
					// print $sql;
        // $query_result = sqlsrv_query($conn,$sql);
        // $rownum = sqlsrv_num_rows($query_result);


	$query_result = sqlsrv_query($conn, $sql) or die(sqlsrv_errors());

				// while($row = sqlsrv_fetch_array($query_result,SQLSRV_FETCH_ASSOC)){
				// 		$arr[] = $row;
				// }

				// $a = sqlsrv_fetch_array($query_result,SQLSRV_FETCH_ASSOC);

	if ($f == 1) {
		return sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);
	} else {
		while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
			$arr[] = $row;
		}
		return $arr;
	}


				// if(count($a)>1){
				// 		while($a){
				// 			$arr[$a['COB']] = $a['premi'];
				// 		}
				//
				//
				// 		return $arr;
				// }else{
				//
				// 		return $a;
				//
				// }



}


function gpTahunx($tahun)
{

	$premi['NASIONAL'] = gpTahun(10, $tahun);

	foreach (branchList() as $var) {

		$premi[$var['namaCabang']] = gpTahun($var['idCabang'], $tahun);
	}


	return $premi;

}

function gpTahun($cabang, $tahun)
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = $tahun - 5;
	$today = $tahun;;
	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " AND A.BRANCH='$cabang' ";
		}
	} else {
		$cbg = "";
	}
	while ($firstDayCurrent <= $today) {
		$sql = "SELECT YEAR(A.DATE) AS thn, SUM((A.AMOUNT_1+A.AMOUNT_5)*V.RATE) as premi FROM ADMLINK A
							JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
							WHERE YEAR(A.DATE) = '$firstDayCurrent'
							AND A.TYPE in $toc $cbg
							GROUP BY YEAR(A.DATE)";
		$query_result = sqlsrv_query($conn, $sql);
		$rownum = sqlsrv_num_rows($query_result);
		$a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);
		if (count($a) == 0) {
			$premi[] = array("tahun" => $firstDayCurrent, "premi" => 0);
		} else {
			$premi[] = array("tahun" => $firstDayCurrent, "premi" => $a['premi']);
		}
		$firstDayCurrent++;
	}

        // while($a = sqlsrv_fetch_array($query_result,SQLSRV_FETCH_ASSOC)){
				// 	$premi[] = array("tahun"=>$a['thn'],"premi"=>$a['premi']);
				// }

	return $premi;

}

function distTahunPremiPaPlus()
{
	$conn = sqlServerConnect();
	$sql = "SELECT DISTINCT YEAR(A.DATE) as tahun
					FROM ADMLINK A
					JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
					WHERE
						A.CODE IN ('1009','1010') AND
						A.TYPE='DI' AND
						YEAR(A.DATE)>2012
					ORDER BY YEAR(A.DATE)";
	$query_result = sqlsrv_query($conn, $sql) or die(sqlsrv_errors());

	while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = $row;
	}
	return $arr;
}

function distBranchPremiPaPlus()
{
	$conn = sqlServerConnect();
	$sql = "SELECT DISTINCT A.BRANCH
						FROM ADMLINK A
						JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
						WHERE
							A.CODE IN ('1009','1010') AND
							A.TYPE='DI' AND
							YEAR(A.DATE)>2012
							ORDER BY A.BRANCH
					";
	$query_result = sqlsrv_query($conn, $sql) or die(sqlsrv_errors());

	while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = $row;
	}
	return $arr;
}

function gpPaPlusPerTahun($par)
{
	$conn = sqlServerConnect();

	$sql = "SELECT
						YEAR(A.DATE) as tahun,
						A.BRANCH,
						SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) as premi
					FROM ADMLINK A
    			JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
					WHERE
						A.CODE IN ('1009','1010') AND
						A.TYPE='DI' AND
						YEAR(A.DATE) = " . $par['tahun'] . " AND A.BRANCH='" . $par['branch'] . "'
    			GROUP BY YEAR(A.DATE),A.BRANCH
    			ORDER BY YEAR(A.DATE),A.BRANCH";
	$query_result = sqlsrv_query($conn, $sql) or die(sqlsrv_errors());

	while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = $row;
	}

	return $arr;
}

function renewalList()
{
	$conn = sqlServerConnect();
	$now = date('m/d/Y');
	$fday = date('m/01/Y');
	$lday = date('m/t/Y');
	$toc = "('1009','1010')";

	$sql = "SELECT * into #ren FROM R_RENEWABLE_LISTINGX('$now','$fday','$lday','','') where toc not in $toc
					order by branch,toc,bsname";
	$query_result = sqlsrv_query($conn, $sql);
	// print $sql;

	foreach (branchList() as $var) {
		$cbg = ($var['idCabang'] < 10 ? "0" . $var['idCabang'] : $var['idCabang']);
//not renewed
		$sql = "SELECT branch,toc,bsname,(sum(gross*pctshare/100) - sum(discount/100*gross*pctshare/100)) as premi
			FROM #REN WHERE ANO NOT IN (
					SELECT R.ANO FROM #REN R JOIN ACCEPTANCE A ON R.ANO=A.RANO  where R.branch='$cbg')
			and branch='$cbg'
			group by branch,toc,bsname
			order by branch,toc,bsname
			";

		$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
		while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
			//	print $row['branch']." ".$row['toc']." ".$row['bsname']." ".$row['premi']."<br>";
			$arr[branchMap($row['branch'])]['notRenew'][$row['toc']][] = array(
				"source" => $row['bsname'],
				"premi" => $row['premi']
			);
		}
		//renewed
		$sql = "SELECT branch,toc,bsname,(sum(gross*pctshare/100) - sum(discount/100*gross*pctshare/100)) as premi
					FROM #REN WHERE ANO IN (
							SELECT R.ANO FROM #REN R JOIN ACCEPTANCE A ON R.ANO=A.RANO  where R.branch='$cbg')
					and branch='$cbg'
					group by branch,toc,bsname
					order by branch,toc,bsname
					";

		$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
		while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
					//	print $row['branch']." ".$row['toc']." ".$row['bsname']." ".$row['premi']."<br>";
			$arr[branchMap($row['branch'])]['renew'][$row['toc']][] = array(
				"source" => $row['bsname'],
				"premi" => $row['premi']
			);
		}

	}

	return $arr;
}

function renewSummary()
{
	$data = file_get_contents('../json/renew.json');
	$json = json_decode($data, true);

	$gtot = 0;
	foreach (branchList() as $cbg) {

		$stot = 0;

		// debug($json[branchMap($cbg['idCabang'])]['renew']);

		foreach ($json[branchMap($cbg['idCabang'])]['notRenew'] as $key => $val) {
			$totcob = 0;

			foreach ($val as $val1) {
				$totcob += $val1['premi'];
				$arr['Nasional'][$key]['notrenew'] += $val1['premi'];
			}
			$arr[branchMap($cbg['idCabang'])][$key]['notrenew'] = $totcob;
			$gtot += $totcob;
			$stot += $totcob;
		}

		$stot = 0;

		foreach ($json[branchMap($cbg['idCabang'])]['renew'] as $key => $val) {
			$totcob = 0;
			foreach ($val as $val1) {
				$totcob += $val1['premi'];
				$arr['Nasional'][$key]['renew'] += $val1['premi'];
			}
			$arr[branchMap($cbg['idCabang'])][$key]['renew'] = $totcob;
			$gtot += $totcob;
			$stot += $totcob;



		}

	}


	return $arr;
}

function renewBS($cbg, $toc)
{
	$data = file_get_contents('../json/renew.json');
	$json = json_decode($data, true);

	foreach ($json[$cbg]['notRenew'] as $key => $val) {
		foreach ($val as $val2) {
			if ($val2['source'] == '' or $val2['source'] == 'null') {
				$source = "Direct";
			} else {
				$source = $val2['source'];
			}
			$arr[$cbg][$key][$source]['notRenew'] = $val2['premi'];
		}
	}


	foreach ($json[$cbg]['renew'] as $key => $val) {
		foreach ($val as $val2) {
			if ($val2['source'] == '' or $val2['source'] == 'null') {
				$source = "Direct";
			} else {
				$source = $val2['source'];
			}
			$arr[$cbg][$key][$source]['renew'] = $val2['premi'];
		}
	}

	return $arr[$cbg][$toc];
}


function renewNew($cbg, $toc)
{
	$data = file_get_contents('../json/newEnd.json');
	$json = json_decode($data, true);
	// print $cbg." ".$toc;
	// debug($json[$cbg][$toc]);
	return $json[$cbg][$toc];
}


function riPremiPaPlusPerTahun($par)
{
	$conn = sqlServerConnect();

	$sql = "SELECT
						YEAR(A.DATE) as tahun,
						A.BRANCH,
						SUM(case when A.TYPE IN ('DC','QS','QP') THEN (A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE ELSE 0 END) as premiQS,
						SUM(case when A.TYPE IN ('S1','S2','S3') THEN (A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE ELSE 0 END) as premiSP,
						SUM(case when A.TYPE IN ('AF') THEN (A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE ELSE 0 END) as premiAF,
						SUM(case when A.TYPE IN ('FO') THEN (A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE ELSE 0 END) as premiFO,
						SUM(case when A.TYPE IN ('CP') THEN (A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE ELSE 0 END) as premiCP,
						SUM(case when A.TYPE IN ('XP') THEN (A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE ELSE 0 END) as premiXP,
						SUM(case when A.TYPE IN ('X1','X2','X3','X4','X5','X6','X7','X8','X9','XX') THEN (A.AMOUNT_1+A.AMOUNT_11)*V.RATE ELSE 0 END) as premiXL,
						SUM(case when A.TYPE IN ('RP','AP') THEN (A.AMOUNT_1+A.AMOUNT_11+A.AMOUNT_17)*V.RATE ELSE 0 END) as premiXLRPAP
					FROM ADMLINK A
    			JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
					WHERE
						A.CODE IN ('1009','1010') AND
						-- A.TYPE='DI' AND
						YEAR(A.DATE) = '" . $par['tahun'] . "' AND A.BRANCH='" . $par['branch'] . "'
    			GROUP BY YEAR(A.DATE),A.BRANCH
    			ORDER BY YEAR(A.DATE),A.BRANCH";
	$query_result = sqlsrv_query($conn, $sql);
  // print $sql;
	while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = $row;
	}

	return $arr;
}

function claimPaPlusPerTahun($par)
{
	$conn = sqlServerConnect();

	$sql = "SELECT
							YEAR(A_SDATE) as tahun,
							BRANCH,
							SUM((AMOUNT_6+AMOUNT_7+AMOUNT_8+AMOUNT_9+AMOUNT_10+AMOUNT_12+AMOUNT_17)*RATE) AS claim
					FROM
							SUMMARY_PRODUCTION_CLAIM('1/1/2013','5/17/2017','')
					WHERE
							TYPE = 'DI' AND
							CODE IN ('1009','1010')  AND
							YEAR(A_SDATE) = " . $par['tahun'] . " and
							BRANCH = " . $par['branch'] . "
							GROUP BY YEAR(A_SDATE), BRANCH
							ORDER BY YEAR(A_SDATE), BRANCH
					";
	$query_result = sqlsrv_query($conn, $sql) or die(sqlsrv_errors());

	while ($row = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = $row;
	}

	return $arr;
}


function listMenuParent()
{
	require_once '../lib/Db.php';
	$db = new Db();

	$sql = "select * from elj_menu where parentF='Y' order by orderNo";

	return $db->select($sql);
}

function listModulTicket()
{
	require_once '../lib/Db.php';
	$db = new Db();

	$sql = "select * from elj_helpdeskTOProblem";
	return $db->select($sql);

}

function listUnresolvedTicket()
{
	require_once '../lib/Db.php';
	$db = new Db();

	$sql = "select * from elj_helpdeskTicket where solutionID='0'";
	return $db->select($sql);
}

function monthBudget($cabang)
{

	require_once '../lib/Db.php';
	$db = new Db();
	$bulan = date('m');
	if (substr($bulan, 0, 1) == '0') $bulan = substr($bulan, 1, 1);
//	$bulan = 12;
	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " and cabang='$cabang' ";

		}
	} else {
		$cbg = "";
	}
	$thn = date('Y');

	$sql = "select sum(gp) as budget from elj_budgetGP where bulan = $bulan $cbg and tahun=$thn group by bulan";

	return $db->select($sql);
}

function osClaim($branch, $cob)
{
	$conn = sqlServerConnect();
	$a_date = date('m/d/Y');
	$lastDate = date("Y-m-t", strtotime($a_date));
	if (strlen($branch) == 1) $branch = "0$branch";
	$sql = "Select os_claim.branch,substring(os_claim.toc,1,2) as cob,os_claim.currency,sum(os_claim.gross_os) as os into #TEMP
		from R_OS_Claim('$a_date') OS_Claim, Claim, TOC, RArrHeader R, Acceptance A, Cover C, CArrHeader CA
		Where Claim.CNO=OS_Claim.OCNO and
		      C.CNO=A.CNO and
                      CA.CNO=OS_Claim.CNO and
                      A.ANO=OS_Claim.ANO and
                      R.ANO=OS_Claim.ANO and
                      TOC.TOC=OS_Claim.TOC and
                      OS_Claim.branch='$branch' and
                      OS_Claim.toc like '$cob%'
               group by os_claim.currency,substring(os_claim.toc,1,2),os_claim.branch";
	$query_result = sqlsrv_query($conn, $sql);
	$sql = "SELECT BRANCH,COB,SUM(OS*C.RATE) AS OS FROM #TEMP T JOIN fn_exchange('$a_date') C ON T.CURRENCY=C.CURRENCY
		GROUP BY BRANCH,COB";
	$query_result = sqlsrv_query($conn, $sql);

	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {

		$arr[] = array(
			"idCabang" => $a['BRANCH'],
			"cob" => $a['COB'],
			"os" => $a['OS']
		);

/*		$rate = currency($a_date,$a['currency']);
		$base = $a['os']*$rate['rate'];
		$arr[] = array(
			"idCabang"	=> $branch,
			"cob"		=> $cob,
			"currency"	=> $a['currency'],
			"rate"		=> $rate['rate'],
			"os"		=> $a['os'],
			"base"		=> $base); */
	}
	sqlsrv_query($conn, "drop table #TEMP");

	return $arr;
}


function getAgen()
{
	$db = new Db();
	$sql = "select id,name,branch,pcRate from eljPCAgentProfile where branch='1'";

	return $db->select($sql);
}

function pcGetUPR($tahun)
{
	$db = new Db();
	$sql = "select * from eljPCUPR where tahun=$tahun";

	return $db->select($sql);
}

function pcInsertUPR($tgl)
{
	$db = new Db();
	$thnAkhir = substr($tgl['edate'], -4) - 1;
	$premi = pcGetLastPremi($tgl);
	foreach ($premi as $key => $insUPR) {
		$upr = $insUPR * 0.25;
		$sql = "insert into eljPCUPR(id,upr,tahun)values('$key',$upr,$thnAkhir)";
		print $sql . "<br>";
		$db->exe($sql);
	}
}

function pcGetLastPremi($tgl)
{
	$conn = sqlServerConnect();
	$ag = '';
	foreach (getAgen() as $val) {
		$ag .= "'" . $val['id'] . "',";
		$arrGabung[$val['id']]['nama'] = $val['name'];
		$arrGabung[$val['id']]['pcRate'] = $val['pcRate'];
		$arrGabung[$val['id']]['branch'] = $val['branch'];
		// print $ag."\n";
	}
	$ag = substr($ag, 0, strlen($ag) - 1);
	$cob = array('03', '08', '09', '10', '12');
	$thnAwal = substr($tgl['sdate'], -4) - 1;
	$thnAkhir = substr($tgl['edate'], -4) - 1;
	$awal = "01/01/$thnAwal";
	$akhir = "12/31/$thnAkhir";
	print $awal . " " . $akhir . "<br>";

	$sql = "SELECT SOURCE_NAME,
								SOURCE_ID,
								COB,
								SUM(PREMIUM_GROSS) AS GP,
								SUM(COMMISSION_AGENT_BROKER)+SUM(TAX) AS KOMISI
					INTO #pcPremiLast
					FROM R_DETAIL_PRODUCTION ('" .
		$awal . "', '" .
		$akhir . "', '01', '%', '%', '%', '%', '%', 1)
								WHERE SOURCE_ID in (" .
		$ag . ")
								GROUP BY SOURCE_ID,SOURCE_NAME,COB";

	// print $sql."<br>";
	$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
	foreach (getAgen() as $ag) {
		foreach ($cob as $cls) {

			$sql = "select * from #pcPremiLast where SOURCE_ID='" . $ag['id'] . "' and COB='" . $cls . "'";
			$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
			// print $sql."<br>";
			while ($premi = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {

				$arrPrem[$ag['id']] += $premi['GP'];

			}
		}
	}

	return $arrPrem;
}


function pcGabung($tgl)
{
	$conn = sqlServerConnect();
	$ag = '';
	$defArray = array(
		"premi" => 0,
		"komisi" => 0,
		"klaimSettled" => 0,
		"klaimOS" => 0,
		"klaimOSLast" => 0
	);
	foreach (getAgen() as $val) {
		$ag .= "'" . $val['id'] . "',";
		$arrGabung[$val['id']]['nama'] = $val['name'];
		$arrGabung[$val['id']]['pcRate'] = $val['pcRate'];
		$arrGabung[$val['id']]['branch'] = $val['branch'];
		// print $ag."\n";
	}



	$ag = substr($ag, 0, strlen($ag) - 1);
	$cob = array('03', '08', '09', '10', '12');


	$sql = "SELECT SOURCE_NAME,
								SOURCE_ID,
								COB,
								SUM(PREMIUM_GROSS) AS GP,
								SUM(COMMISSION_AGENT_BROKER)+SUM(TAX) AS KOMISI
					INTO #pcPremi
					FROM R_DETAIL_PRODUCTION ('" .
		$tgl['sdate'] . "', '" .
		$tgl['edate'] . "', '%', '%', '%', '%', '%', '%', 1)
								WHERE SOURCE_ID in (" .
		$ag . ")
								GROUP BY SOURCE_ID,SOURCE_NAME,COB";
	// print "<br>".$sql."<br>";
	$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
	$sql = "select Acceptance.Source,
								 T.CURRENCY,
								 T.TOC,
								 SUM(T.ANR*T.RATE) as anr
					into #pcClaimSettled
					from
						R_SettledClaim('" . $tgl['sdate'] . "','" . $tgl['edate'] . "') as t,
						claim,
						acceptance,
						cover,
						toc,
						rarrheader
					where
						cover.cno=acceptance.cno and
						t.cno = claim.cno and
						claim.ano = acceptance.ano and
						acceptance.ano=rarrheader.ano and
						claim.toc=toc.toc and
						Acceptance.Source in (" . $ag . ")
				 GROUP BY
						T.TOC,
						T.CURRENCY,
						acceptance.source
				 ORDER BY T.TOC";
		// print $sql."<br>";
	$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
	$sql = "select SOURCE,
									 substring(TOC,1,2) as COB,
									 sum(ANR) as ANR
						into #pcClaimSettledEdited
						from #pcClaimSettled
						where substring(TOC,1,2) in ('03','08','09','10','12')
						group by SOURCE,TOC order by SOURCE";
		// print $sql."<br>";
	$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
	$sql = "Select OS_Claim.*,A.Source,substring(OS_Claim.TOC,1,2) as COB into #pcClaimOS from R_OS_Claim('" . $tgl['edate'] . "') OS_Claim,
								Claim,
								TOC,
								RArrHeader R,
								Acceptance A,
								Cover C,
								CArrHeader CA
						 Where
								Claim.CNO=OS_Claim.OCNO and
								C.CNO=A.CNO and
								CA.CNO=OS_Claim.CNO and
								A.ANO=OS_Claim.ANO and
								R.ANO=OS_Claim.ANO and
								TOC.TOC=OS_Claim.TOC and
								A.Source in (" . $ag . ")
						";
	$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
		// print $sql."<br>";
	$l = substr($tgl['edate'], 6, 4) - 1;
	$lastYear = "12/31/" . $l;
	$sql = "Select OS_Claim.*,A.Source,substring(OS_Claim.TOC,1,2) as COB into #pcClaimOSLast from R_OS_Claim('$lastYear') OS_Claim,
								Claim,
								TOC,
								RArrHeader R,
								Acceptance A,
								Cover C,
								CArrHeader CA
						 Where
								Claim.CNO=OS_Claim.OCNO and
								C.CNO=A.CNO and
								CA.CNO=OS_Claim.CNO and
								A.ANO=OS_Claim.ANO and
								R.ANO=OS_Claim.ANO and
								TOC.TOC=OS_Claim.TOC and
								A.Source in (" . $ag . ")
						";
	$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
		// print $sql."<br>";

	foreach (getAgen() as $ag) {
		$arrPremi[$ag['id']]['Cargo'] = array();
		$arrPremi[$ag['id']]['Engineering'] = array();
		$arrPremi[$ag['id']]['Liability'] = array();
		$arrPremi[$ag['id']]['GA'] = array();
		$arrPremi[$ag['id']]['Misc'] = array();
		$arrKlaimS[$ag['id']]['Cargo'] = array();
		$arrKlaimS[$ag['id']]['Engineering'] = array();
		$arrKlaimS[$ag['id']]['Liability'] = array();
		$arrKlaimS[$ag['id']]['GA'] = array();
		$arrKlaimS[$ag['id']]['Misc'] = array();
		$arrKlaimO[$ag['id']]['Cargo'] = array();
		$arrKlaimO[$ag['id']]['Engineering'] = array();
		$arrKlaimO[$ag['id']]['Liability'] = array();
		$arrKlaimO[$ag['id']]['GA'] = array();
		$arrKlaimO[$ag['id']]['Misc'] = array();
		$arrKlaimOL[$ag['id']]['Cargo'] = array();
		$arrKlaimOL[$ag['id']]['Engineering'] = array();
		$arrKlaimOL[$ag['id']]['Liability'] = array();
		$arrKlaimOL[$ag['id']]['GA'] = array();
		$arrKlaimOL[$ag['id']]['Misc'] = array();
		foreach ($cob as $cls) {
			$sql = "select * from #pcPremi where SOURCE_ID='" . $ag['id'] . "' and COB='" . $cls . "'";
			$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());
				// print $sql."<br>";
			while ($premi = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {

						// if ($ag['id']=='M01AU00001'){
						// 		 print "lll ".$premi['SOURCE_ID']."-".$premi['COB']."-".$premi['GP'];
						// }
				switch ($premi['COB']) {
					case '03':
						$arrPremi[$premi['SOURCE_ID']]['Cargo'] =
							array(
							"premi" => $premi['GP'],
							"komisi" => $premi['KOMISI']
						);
						break;
					case '08':
						$arrPremi[$premi['SOURCE_ID']]['Engineering'] =
							array(
							"premi" => $premi['GP'],
							"komisi" => $premi['KOMISI']
						);
						break;
					case '09':
						$arrPremi[$premi['SOURCE_ID']]['Liability'] =
							array(
							"premi" => $premi['GP'],
							"komisi" => $premi['KOMISI']
						);
						break;
					case '10':
						$arrPremi[$premi['SOURCE_ID']]['GA'] =
							array(
							"premi" => $premi['GP'],
							"komisi" => $premi['KOMISI']
						);
						break;
					case '12':
						$arrPremi[$premi['SOURCE_ID']]['Misc'] =
							array(
							"premi" => $premi['GP'],
							"komisi" => $premi['KOMISI']
						);
						break;
					default:
						break;
				}
			}
			$sql = "select * from #pcClaimSettledEdited where SOURCE='" . $ag['id'] . "' and COB='" . $cls . "'";
				// print $sql."<br>";
			$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());

			while ($klaimS = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
				if(!$klaimS['ANR']) { $klaimSe = 0;}else{$klaimSe = $klaimS['ANR'];}
				switch ($klaimS['COB']) {
					case '03':
						$arrKlaimS[$klaimS['SOURCE']]['Cargo'] =
							array("klaimSettled" => $klaimSe);
						break;
					case '08':
						$arrKlaimS[$klaimS['SOURCE']]['Engineering'] =
							array("klaimSettled" => $klaimSe);
						break;
					case '09':
						$arrKlaimS[$klaimS['SOURCE']]['Liability'] =
							array("klaimSettled" => $klaimSe);
						break;
					case '10':
						$arrKlaimS[$klaimS['SOURCE']]['GA'] =
							array("klaimSettled" => $klaimSe);
						break;
					case '12':
						$arrKlaimS[$klaimS['SOURCE']]['Misc'] =
							array("klaimSettled" => $klaimSe);
						break;
					default:
						$arrKlaimS[$klaimS['SOURCE']]['Cargo'] = array("klaimSettled" => 0);
						$arrKlaimS[$klaimS['SOURCE']]['Engineering'] = array("klaimSettled" => 0);
						$arrKlaimS[$klaimS['SOURCE']]['Liability'] = array("klaimSettled" => 0);
						$arrKlaimS[$klaimS['SOURCE']]['GA'] = array("klaimSettled" => 0);
						$arrKlaimS[$klaimS['SOURCE']]['Misc'] = array("klaimSettled" => 0);
						break;
				}
			}
			if($arrKlaimS[$klaimS['SOURCE']] == null)
				$arrGabung[$ag['id']]['Cargo'] = array(
					"klaimSettled" => 0
				);

			$sql = "select * from #pcClaimOS where Source='" . $ag['id'] . "' and COB='" . $cls . "'";
				// print $sql."<br>";
			$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());

			while ($klaimO = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {

				switch ($klaimO['COB']) {
					case '03':
						$arrKlaimO[$klaimO['Source']]['Cargo'] =
							array("klaimOS" => $klaimO['GROSS_OS']);
						break;
					case '08':
						$arrKlaimO[$klaimO['Source']]['Engineering'] =
							array("klaimOS" => $klaimO['GROSS_OS']);
						break;
					case '09':
						$arrKlaimO[$klaimO['Source']]['Liability'] =
							array("klaimOS" => $klaimO['GROSS_OS']);
						break;
					case '10':
						$arrKlaimO[$klaimO['Source']]['GA'] =
							array("klaimOS" => $klaimO['GROSS_OS']);
						break;
					case '12':
						$arrKlaimO[$klaimO['Source']]['Misc'] =
							array("klaimOS" => $klaimO['GROSS_OS']);
						break;
					default:
						break;
				}
			}

			$sql = "select * from #pcClaimOSLast where Source='" . $ag['id'] . "' and COB='" . $cls . "'";
				// print $sql."<br>";
			$query_result = sqlsrv_query($conn, $sql) or debug(sqlsrv_errors());

			while ($klaimOL = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {

				switch ($klaimOL['COB']) {
					case '03':
						$arrKlaimOL[$klaimOL['Source']]['Cargo'] =
							array("klaimOSLast" => $klaimOL['GROSS_OS']);
						break;
					case '08':
						$arrKlaimOL[$klaimOL['Source']]['Engineering'] =
							array("klaimOSLast" => $klaimOL['GROSS_OS']);
						break;
					case '09':
						$arrKlaimOL[$klaimOL['Source']]['Liability'] =
							array("klaimOSLast" => $klaimOL['GROSS_OS']);
						break;
					case '10':
						$arrKlaimOL[$klaimOL['Source']]['GA'] =
							array("klaimOSLast" => $klaimOL['GROSS_OS']);
						break;
					case '12':
						$arrKlaimOL[$klaimOL['Source']]['Misc'] =
							array("klaimOSLast" => $klaimOL['GROSS_OS']);
						break;
					default:
						break;
				}
			}
		}
					// print $ag['id']." ".debug($arrPremi[$ag['id']]['Cargo'])."-".debug($arrKlaimS[$ag['id']]['Cargo'])."<br>";
		$arrGabung[$ag['id']]['Cargo'] = array_merge(
								// array(
								// 	"nama" => $ag['id'],
								// 	"pcRate" => $ag['pcRate'],
								// 	"cabang" => $ag['branch']),
								$arrPremi[$ag['id']]['Cargo'],
								$arrKlaimS[$ag['id']]['Cargo'],
								$arrKlaimO[$ag['id']]['Cargo'],
								$arrKlaimOL[$ag['id']]['Cargo']);
		$arrGabung[$ag['id']]['Engineering'] = array_merge(
								// array(
								// 	"nama" => $ag['id'],
								// 	"pcRate" => $ag['pcRate'],
								// 	"cabang" => $ag['branch']),
								$arrPremi[$ag['id']]['Engineering'],
								$arrKlaimS[$ag['id']]['Engineering'],
								$arrKlaimO[$ag['id']]['Engineering'],
								$arrKlaimOL[$ag['id']]['Engineering']);
		$arrGabung[$ag['id']]['Liability'] = array_merge(
								// array(
								// 	"nama" => $ag['id'],
								// 	"pcRate" => $ag['pcRate'],
								// 	"cabang" => $ag['branch']),
								$arrPremi[$ag['id']]['Liability'],
								$arrKlaimS[$ag['id']]['Liability'],
								$arrKlaimO[$ag['id']]['Liability'],
								$arrKlaimOL[$ag['id']]['Liability']);
		$arrGabung[$ag['id']]['GA'] = array_merge(
								// array(
								// 	"nama" => $ag['id'],
								// 	"pcRate" => $ag['pcRate'],
								// 	"cabang" => $ag['branch']),
								$arrPremi[$ag['id']]['GA'],
								$arrKlaimS[$ag['id']]['GA'],
								$arrKlaimO[$ag['id']]['GA'],
								$arrKlaimOL[$ag['id']]['GA']);
		$arrGabung[$ag['id']]['Misc'] = array_merge(
								// array(
								// 	"nama" => $ag['id'],
								// 	"pcRate" => $ag['pcRate'],
								// 	"cabang" => $ag['branch']),
								$arrPremi[$ag['id']]['Misc'],
								$arrKlaimS[$ag['id']]['Misc'],
								$arrKlaimO[$ag['id']]['Misc'],
								$arrKlaimOL[$ag['id']]['Misc']);

		if (count($arrGabung[$ag['id']]['Cargo']) == 0) {
			$arrGabung[$ag['id']]['Cargo'] = $defArray;
		}
		if (count($arrGabung[$ag['id']]['Engineering']) == 0) {
			$arrGabung[$ag['id']]['Engineering'] = $defArray;
		}
		if (count($arrGabung[$ag['id']]['Liability']) == 0) {
			$arrGabung[$ag['id']]['Liability'] = $defArray;
		}
		if (count($arrGabung[$ag['id']]['GA']) == 0) {
			$arrGabung[$ag['id']]['GA'] = $defArray;
		}
		if (count($arrGabung[$ag['id']]['Misc']) == 0) {
			$arrGabung[$ag['id']]['Misc'] = $defArray;
		}

		$arrGabung[$ag['id']]['TotalPremi'] = $arrGabung[$ag['id']]['Cargo']['premi'] +
			$arrGabung[$ag['id']]['Engineering']['premi'] +
			$arrGabung[$ag['id']]['Liability']['premi'] +
			$arrGabung[$ag['id']]['GA']['premi'] +
			$arrGabung[$ag['id']]['Misc']['premi'];
	}
		// debug($arrGabung);
	return $arrGabung;
}
function productionByMonth($cob, $par)
{
	$conn = sqlServerConnect();
//        $toc = "('DI','IC','CF','IT','IR','IX','BA','BB','BO')";
	$firstDayCurrent = "01/01/" . date('Y');
	$today = date('m/d/Y');
	// $firstDayCurrent = '01/01/2017';
	// $today = '12/31/2017';

	$cb = $cob;

	switch ($cob) {
		case "1006":
			$cob = " and A.CODE = '1006' ";
			break;
		case "1009":
			$cob = " and A.CODE in ('1009','1010') ";
			break;
		case "12":
			$cob = " and substring(A.CODE,1,2) in ('04','05','06','07','09','12') ";
			break;
		default:
			$cob = " and substring(A.CODE,1,2) = '0$cob' ";
			break;
	}

	if (isset($par)) {
		$det = "case when a.type in ('DI','CF','IR','IT','IX','IC') THEN SUM((A.AMOUNT_1+A.AMOUNT_11)*V.RATE) ELSE 0 END AS premi,
			CASE WHEN A.TYPE IN ('BB','BA') THEN SUM((A.AMOUNT_2+A.AMOUNT_13+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) ELSE 0 END AS comInt,
			CASE WHEN A.TYPE IN ('IR','IT','IX') THEN SUM((A.AMOUNT_2+A.AMOUNT_3)*V.RATE) ELSE 0 END AS comIR,
			CASE WHEN A.TYPE IN ('IC') THEN SUM((A.AMOUNT_2+A.AMOUNT_3)*V.RATE) ELSE 0 END AS comIC,
			CASE WHEN A.TYPE IN ('DI','CF','IR','IT','IX','IC') THEN SUM(A.AMOUNT_5*V.RATE) ELSE 0 END AS disc,
			case when A.TYPE IN ('DC','QS','QP') THEN SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) ELSE 0 END AS QS,
			case when A.TYPE IN ('S1','S2','S3') THEN SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) ELSE 0 END AS SP,
			case when A.TYPE IN ('FO') THEN SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) ELSE 0 END AS FO,
			case when A.TYPE IN ('CP') THEN SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) ELSE 0 END AS CP,
			case when A.TYPE IN ('XP') THEN SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) ELSE 0 END AS XP,
			CASE WHEN A.TYPE IN ('X1','X2','X3','X4','X5','X6','X7','X8','X9','XX') THEN SUM((A.AMOUNT_1+A.AMOUNT_11)*V.RATE) ELSE 0 END AS XL,
			CASE WHEN A.TYPE IN ('RP','AP') THEN SUM((A.AMOUNT_1+A.AMOUNT_11+A.AMOUNT_17)*V.RATE) ELSE 0 END AS XLRPAP,
			CASE WHEN A.TYPE IN ('DC','QS','QP','S1','S2','S3','FO','CP','XP','X1','X2','X3','X4','X5','X6','X7','X8','X9','XX','RP','AP','CF','JP') THEN SUM((A.AMOUNT_2+A.AMOUNT_3)*V.RATE) ELSE 0 END AS COMRCVD,
			case when A.TYPE IN ('DI','IC','CF','JP','IR','IT','IX','OP') then sum((A.AMOUNT_6+A.AMOUNT_7+A.AMOUNT_8+A.AMOUNT_9+A.AMOUNT_10+A.AMOUNT_12+A.AMOUNT_17)*V.RATE) ELSE 0 END AS GROSSCLAIM,
	case when A.TYPE IN ('DC','QS','QP','S1','S2','S3','FO','CP','XP','X1','X2','X3','X4','X5','X6','X7','X8','X9','XX') then
		sum((A.AMOUNT_6+A.AMOUNT_7+A.AMOUNT_8+A.AMOUNT_9+A.AMOUNT_10+A.AMOUNT_12+A.AMOUNT_17)*V.RATE) ELSE 0 END AS RICLAIM,";
	} else {
		$det = "case when a.type in ('DI','CF','IR','IT','IX','IC') THEN SUM((A.AMOUNT_1+A.AMOUNT_5+A.AMOUNT_11)*V.RATE) ELSE 0 END AS premi, ";
	}

	foreach (branchList() as $val) {
		if ($val['idCabang'] < 10) {
			$cbg = "0" . $val['idCabang'];
		} else {
			$cbg = $val['idCabang'];
		}
		$sql = "SELECT a.branch,month(A.DATE) as bulan,$det
		  substring(A.CODE,1,2) as cob FROM ADMLINK A
                  JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
                  WHERE A.DATE BETWEEN '$firstDayCurrent' AND '$today'
                  and a.branch = '$cbg' $cob
                  GROUP BY MONTH(A.DATE),a.branch,a.code,a.branch,a.type
                  ORDER BY MONTH(A.DATE)";
		$query_result = sqlsrv_query($conn, $sql);
		$xol = xolAmort($cbg, $cb);
		if (!isset($par)) {
			while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
				$arr[$val['idCabang']][$hasil['bulan']] += $hasil['premi'];
			}
		} else {
			while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
				$arr[$val['idCabang']]['premi'][$hasil['bulan']] += $hasil['premi'];
				$arr[$val['idCabang']]['disc'][$hasil['bulan']] += $hasil['disc'];
				$arr[$val['idCabang']]['com'][$hasil['bulan']] += $hasil['comInt'] + $hasil['comIR'] + $hasil['comIC'];
				$arr[$val['idCabang']]['reas'][$hasil['bulan']] += $hasil['QS'] + $hasil['SP'] + $hasil['FO'] + $hasil['CP'] + $hasil['XP'] + $hasil['XL'] + $hasil['XLRPAP'];
				$arr[$val['idCabang']]['comRcvd'][$hasil['bulan']] += $hasil['COMRCVD'];
				$arr[$val['idCabang']]['GClaim'][$hasil['bulan']] += $hasil['GROSSCLAIM'];
				$arr[$val['idCabang']]['RIClaim'][$hasil['bulan']] += $hasil['RICLAIM'];
			}
			$arr[$val['idCabang']]['reas'][$hasil['bulan']] -= $xol;
		}
		unset($xol);
	}
//	debug($arr);
	return $arr;
}

function progressive($cb)
{
	$conn = sqlServerConnect();
  // $today = date('m/d/Y');
	$today = '12/31/' . date('Y');
	if (strlen($cb) == 1) {
		$cabang = "= '0" . $cb . "'";
	} else if ($cb == 10) {
		$cabang = " like '%'";
	} else {
		$cabang = "='" . $cb . "'";
	}

	$sql =
		"Declare @P_AsAt as smalldatetime
	  Select @P_AsAt='$today'
	  Select
		Admlink.ID,
		Profile.Name,
		Profile.TaxPCT,
		Admlink.AdmNo,
		Admlink.Type,
		Admlink.Voucher,
		Admlink.Date,
		Admlink.Currency,
		-(Admlink.Amount-Admlink.Amount_13-Admlink.Amount_14)*nVoucher.Rate as Nominal,
		Case When MParam.TaxDiscount=0 Then 100 else MParam.TaxDiscount end as TaxDiscount,
		-(Admlink.Amount-Admlink.Amount_13-Admlink.Amount_14)*Case When MParam.TaxDiscount=0 Then 100 else MParam.TaxDiscount*nVoucher.Rate end/100 as DPP,
		Admlink.Amount_14*nVoucher.Rate as Tax
	into #Tax
	from Admlink, nVoucher, MParam, Profile
	Where
		nVoucher.Voucher=Admlink.Voucher and
		Admlink.Type in ('BB','BA','BO') and
		Admlink.Amount_14<>0 and
		nVoucher.TaxType='PASAL21' and
		Admlink.TaxTableF=1 and
		MParam.Type=Admlink.Type and
		Profile.ID=Admlink.ID and
		Year(Admlink.Date)=Year(@P_AsAt) and
		Month(admlink.Date)<=Month(@P_AsAt) and

		substring(admlink.voucher,11,2) $cabang
	order by Nominal, Admlink.ID, Admlink.AdmNo;";
// admlink.code in ('1009','1010') and  << dari atas
	$query_result = sqlsrv_query($conn, $sql);

	if (strlen($cb) == 1) {
		$cabang = "= '0" . $cb . "'";
	} else if ($cb == 10) {
		$cabang = " like '%'";
	} else {
		$cabang = "='" . $cb . "'";
	}
	$sql = "
	SELECT DISTINCT ADM.ID,P.NAME
		INTO #agen
	FROM ADMLINK ADM
		JOIN PROFILE P ON ADM.ID = P.ID
	WHERE " .
		//ADM.CODE IN ('1009','1010') AND
	"ADM.BRANCH $cabang AND
		ADM.TYPE='BA' AND
		P.LOB='01'
	";
	$query_result = sqlsrv_query($conn, $sql);
	$sql = "SELECT * FROM #agen";
	$query_result = sqlsrv_query($conn, $sql);
	while ($ag = sqlsrv_fetch_array($query_result)) {


		$sql = "
			select
				t.id,
				t.name,
				sum(t.nominal) as nominal
			from #Tax t
			where t.id = '" . $ag['ID'] . "'
			group by t.id,t.name,t.taxpct
			order by nominal,t.id,t.name,t.taxpct asc
		";

		$query_result1 = sqlsrv_query($conn, $sql);

		while ($hasil = sqlsrv_fetch_array($query_result1)) {

			$arr[] = array(
				"id" => $ag['ID'],
				"name" => $ag['NAME'],
				"currency" => $hasil['currency'],
				"nominal" => $hasil['nominal']
			);

		}


	}
	usort($arr, function ($a, $b) {
		return $b['nominal'] - $a['nominal'];
	});
	return $arr;
}


function reorderMenu($arr)
{
	require_once '../lib/Db.php';
	$db = new Db();

	$i = 1;
	foreach ($arr as $val) {
		$sql = "update elj_menu set orderNo = $i where parentF='Y' and orderNo=" . $val;
		print $sql . "\n";
//		$db->exe($sql);
		$i++;
	}
}

function reportProdVTarget($cabang)
{
	require_once '../lib/Db.php';

	$cob = array(
		"01" => "PROPERTY",
		"02" => "MOTOR",
		"03" => "CARGO",
		"04" => "MRN HULL",
		"05" => "AVI HULL",
		"06" => "SAT",
		"07" => "ENERGY",
		"08" => "ENGINEERING",
		"09" => "LIABILITY",
		"10" => "GA",
		"11" => "CREDITS",
		"12" => "MISC"
	);
	$i = 1;

	foreach ($cob as $key => $val) {
		$currentCob = cobCurrent($key, $cabang);
		$cobBudget = cobBudget($cabang, $key);
		$gabungArr[$i] = array(
			"current" => $currentCob['premi'],
			"budget" => $cobBudget[0]['premi']
		);
		$i++;
	}

	return $gabungArr;
}

function reportProdVTargetResult()
{
	foreach (branchList() as $val) {
		$cobByCabang[$val['idCabang']] = reportProdVTarget($val['idCabang']);
	}
	//debug($cobByCabang);
	return $cobByCabang;
}

function reportProdVTargetResultMonth()
{
	$cob = array(
		"01" => "PROPERTY",
		"02" => "MOTOR",
		"03" => "CARGO",
		"04" => "MRN HULL",
		"05" => "AVI HULL",
		"06" => "SAT",
		"07" => "ENERGY",
		"08" => "ENGINEERING",
		"09" => "LIABILITY",
		"10" => "GA",
		"11" => "CREDITS",
		"12" => "MISC"
	);


	foreach (branchList() as $val) {
		foreach ($cob as $key => $valCOB) {
			$currGP = gpBulanByCOB($val['idCabang'], $key,'');
			$currBG = currentBudgetByCOBnMonth($val['idCabang'], $key);
			$cobByCabangMonth[$key] = array(
				"current" => $currGP['premi'],
				"budget" => $currBG[0]['budget']
			);
		}
		$cbg[$val['idCabang']] = $cobByCabangMonth;
	}
	return $cbg;
}

function reportProdVTargetResultMonthNasional()
{
	$cob = array(
		"01" => "PROPERTY",
		"02" => "MOTOR",
		"03" => "CARGO",
		"04" => "MRN HULL",
		"05" => "AVI HULL",
		"06" => "SAT",
		"07" => "ENERGY",
		"08" => "ENGINEERING",
		"09" => "LIABILITY",
		"10" => "GA",
		"11" => "CREDITS",
		"12" => "MISC"
	);


	foreach ($cob as $key => $valCOB) {
		$currGP = gpBulanByCOB('10', $key,'');
		$currBG = currentBudgetByCOBnMonth('10', $key);
		$cobByCabangMonth[$key] = array(
			"current" => $currGP['premi'],
			"budget" => $currBG[0]['budget']
		);
	}
	$cbg['10'] = $cobByCabangMonth;

	return $cbg;
}

function retrieveReminder($uid, $par)
{
	require_once '../lib/Db.php';
	$db = new Db();
	if (isset($par)) {
		$hariIni = date('Y-m-d');
		$tambah = " and time <= '$hariIni' and status='0'";
	} else {
		$tambah = "";
	}
	$sql = "select * from elj_reminder where userID='$uid' $tambah";

	return $db->select($sql);
}

function SubTotal($data, $key, $centre)
{
	$total = 0;
	foreach ($data as $subarr)
		if (isset($subarr[$key]))
		if ($subarr['currency'] == $centre)
		$total += $subarr[$key];
	return $total;
}

//function sqlServerConnect($mana){
function sqlServerConnect()
{
/*	if(isset($mana)){
		$server = '192.168.0.89';
		$db = "SEA_EKALLOYD_LIVE";
	}else{*/
	// $server = '192.168.5.2';
	$db = "SEA_EKALLOYD";
	$server = '192.168.7.2';
//	}

	$connectionOptions = array(
		"Database" => $db,
		"Uid" => "sa",
		"PWD" => "p@ssw0rd"
	);
	$conn = sqlsrv_connect($server, $connectionOptions);
	if ($conn) {
 	    // echo "Connection established.<br />";
	} else {
	    //  echo "Connection could not be established.<br />";
	    //  die( print_r( sqlsrv_errors(), true));
	}
	return $conn;
}


function tanda($par, $tanda2)
{


	if ($par <= 0) {
		$posisi = 'fa-sort-desc';
	} else {
		$posisi = 'fa-sort-asc';
	}

	if (isset($tanda2)) {
		$posisi = 'fa-sort-asc';
	}
	return $posisi;
}


function tocMap($toc)
{
	$conn = sqlServerConnect();
	$sql = "SELECT DESCRIPTION FROM TOC WHERE TOC=$toc";
	$query_result = sqlsrv_query($conn, $sql);
	$hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC);
	return $hasil['DESCRIPTION'];
}

function topBranch()
{
	$conn = sqlServerConnect();
	$toc = "('DI','IC','CF','IT','IR','IX')";
	$firstDayCurrent = '01/01/' . date('Y');
	$today = date('m/d/Y');
//	$firstDayCurrent = '01/01/2016';
//	$today = '12/31/2016';


	$sql = "SELECT A.BRANCH,SUM((A.AMOUNT_1+A.AMOUNT_5)*V.RATE) as premi FROM ADMLINK A
                JOIN VOUCHER V ON A.VOUCHER=V.VOUCHER
                WHERE A.DATE BETWEEN '$firstDayCurrent' AND '$today'
                AND A.TYPE in $toc
                GROUP BY A.BRANCH
                ORDER BY premi desc";
	$query_result = sqlsrv_query($conn, $sql);

	while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"cabang" => $hasil['BRANCH'],
			"premi" => $hasil['premi']
		);
	}

	return $arr;
}

function totalBudget($tahun, $cabang)
{
	require_once '../lib/Db.php';
	$db = new Db();
	if (isset($cabang)) {
		if (strlen($cabang) == 1) $cabang = "0$cabang";
		if ($cabang == 10) {
			$cbg = "";
		} else {
			$cbg = " and cabang='$cabang' ";
		}
	} else {
		$cbg = "";
	}
	$sql = "select sum(gp) as premi from elj_budgetGP where tahun=$tahun $cbg";
	return $db->select($sql);
}

function viewReminder($id)
{
	require_once '../lib/Db.php';
	$db = new Db();
	$sql = "select * from elj_reminder where idReminder='$id'";
	print $sql;
	return $db->select($sql);
}

function warna($par1, $par2)
{
	if ($par1 < $par2) {
		$tandaWarna = "red";
	} else {
		$tandaWarna = "green";
	}

	return $tandaWarna;
}

function cobDiBudget()
{
	$db = new Db();
	$sql = "select * from elj_budgetCOB order by cobCode";

	return $db->select($sql);
}
//
function distChannel()
{
	$db = new Db();
	$sql = "select * from elj_budgetDist";

	return $db->select($sql);
}
//
function sobBudget($dist)
{
	$db = new Db();
	$sql = "select * from elj_budgetSOB where distCode='$dist' order by sobCode";

	return $db->select($sql);
}
//
//
// function viewBudgetHeader(){
// 	$db 		= new Db();
// 	$cabang = branchList();
// 	$dist 	= distChannel();
// 	$sob 		= sob();
// 	$cob		= cob();
//
//
// }
function viewDetailBudget($cabang, $cob, $sob, $tahun)
{
	$db = new Db();
	$sql = "select * from elj_budgetProduksi where tahun='$tahun' and cabangCode='$cabang'
						and cobCode='$cob' and sobCode='$sob' order by bulan,cabangCode,sobCode,cobCode";
		//print $sql;
	return $db->select($sql);
}
//

function xolCob(){
	$db = new Db();
	$sql = "select * from eljXoLCOB";
	return $db->select($sql);
}

function xolAmort($cabang, $cob)
{
	$conn = sqlServerConnect();
	$firstDayCurrent = '01/01/' . date('Y');
	$today = date('m/d/Y');

	// switch ($cob) {
	// 	case "1006":
	// 		$cob = " and SUBSTRING(VTYPE,4,2) = '10' ";
	// 		break;
	// 	case "1009":
	// 		$cob = " and SUBSTRING(VTYPE,4,2) = '10' ";
	// 		break;
	// 	case "12":
	// 		$cob = " and SUBSTRING(VTYPE,4,2) in ('04','05','06','07','09','12') ";
	// 		break;
	// 	default:
	// 		$cob = " and SUBSTRING(VTYPE,4,2) = '0$cob' ";
	// 		break;
	// }
	$cob = " and SUBSTRING(VTYPE,4,2) = '$cob' ";
	$sql = "SELECT
			SUBSTRING(VTYPE,4,2) AS COB,
			SUM(NOMINAL-DIFF) AS PREMI
		FROM MVOUCHER
		WHERE
			VTYPE LIKE 'X%' AND
			DATE BETWEEN '$firstDayCurrent' AND '$today' AND
			BRANCH = '$cabang'
			$cob
			GROUP BY VTYPE";
	// print $sql."<br>";
	$query_result = sqlsrv_query($conn, $sql);
	while ($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr = $hasil['PREMI'];
	}

	return $arr;
}

function xolAmortNew($q){

	$conn = sqlServerConnect();
	$firstDayCurrent = '01/01/' . date('Y');
	switch($q){
		case 1	:	$today = '3/31/'.date('Y');break;
		case 2	:	$today = '6/31/'.date('Y');break;
		case 3	:	$today = '9/30/'.date('Y');break;
		case 4	:	$today = '12/31/'.date('Y');break;
		default	:	return false;break;
	}

	$sql = "SELECT * FROM ACCLINK WHERE TYPE='XA' AND DATE BETWEEN '$firstDayCurrent' and '$today' order by BRANCH";
	$query_result	=	sqlsrv_query($conn, $sql);
	while($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)){
		$arr[] = array(
			'docno'		=>	$hasil['DOCNO'],
			'code'		=>	$hasil['CODE'],
			'amount'	=>	$hasil['AMOUNT'],
			'branch'	=>	$hasil['BRANCH']
		);
	}
	return $arr;
}

function xolLayer($q){
	$conn = sqlServerConnect();
	$firstDayCurrent = '01/01/' . date('Y');
	switch($q){
		case 1	:	$today = '3/31/'.date('Y');break;
		case 2	:	$today = '6/31/'.date('Y');break;
		case 3	:	$today = '9/30/'.date('Y');break;
		case 4	:	$today = '12/31/'.date('Y');break;
		default	:	return false;break;
	}
	$sql = "SELECT distinct(substring(docno,1,2)) as layer FROM ACCLINK WHERE TYPE='XA' AND DATE BETWEEN '$firstDayCurrent' and '$today'";
	// print $sql;
	$query_result	=	sqlsrv_query($conn, $sql);
	while($hasil = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)){
		$arr[] = $hasil['layer'];

	}
	return $arr;

}


function xolJenis($tahun){
	$db = new Db();
	$sql = "select distinct(jenis) as jenis from eljXoL where tahun=$tahun";

	return $db->select($sql);
}



function summaryAging($branch, $source)
{
	$i = 1;

	while ($i <= 12) {

		unset($aging);
		unset($kur);
    // if($branch<10){ $branch = "0".$branch;}
		if ($i < 10) {
			$i = "0" . $i;
		}

		$aging = agingQuery($branch, $i, $source);

		if (count($aging) > 0) {
			unset($kur);
			unset($keydiCurrency);
			$kur = agingCurrency($branch, $i);

			foreach ($kur as $key => $v) {
				$keydiCurrency[$key] = $v['kurs'];
			}
			foreach ($aging as $val) {
				if ($val['daydued'] >= 0 && $val['daydued'] <= 30) {
					$sum[$val['bsource']][$i][$keydiCurrency[array_search($val['currency'], $keydiCurrency)]]['amount1'] += $val['amountdue'];
				} else if ($val['daydued'] > 30 && $val['daydued'] <= 60) {
					$sum[$val['bsource']][$i][$keydiCurrency[array_search($val['currency'], $keydiCurrency)]]['amount2'] += $val['amountdue'];
				} else if ($val['daydued'] > 60 && $val['daydued'] <= 120) {
					$sum[$val['bsource']][$i][$keydiCurrency[array_search($val['currency'], $keydiCurrency)]]['amount3'] += $val['amountdue'];
				} else if ($val['daydued'] > 120) {
					$sum[$val['bsource']][$i][$keydiCurrency[array_search($val['currency'], $keydiCurrency)]]['amount4'] += $val['amountdue'];
				}
			}

		}
		$i++;
	}

	return $sum;
}

function listAgen($branch)
{
	$conn = sqlServerConnect();
	$sql = "SELECT ID,Name from profile where lob='01' and branch='$branch' and dumpF='0' and substring(ID,1,1)='M'";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			"ID" => $a['ID'],
			"Name" => $a['Name']
		);
	}

	return $arr;
}

function listUser()
{
	$db = new Db();
	$sql = "select * from login";
	return $db->select($sql);
}

//
// function genUserTable(){
//   foreach(listUser() as $val){
//     switch($val['gender']){
//       case 'm'  : $gender = "<i class=\"fa fa-male\"></i>&nbsp; Male";
//                   $warna = "text-primary";
//                   $valGender = $val['gender'];
//                   break;
//       case 'f'  : $gender = "<i class=\"fa fa-female\"></i>&nbsp; Female";
//                   $warna = "text-danger";
//                   $valGender = $val['gender'];
//                   break;
//       default   : $gender = "<i class=\"fa fa-question-circle\"></i>&nbsp;";
//                   $warna = "text-warning";
//                   $valGender = $val['gender'];
//                   break;
//     }
//     $id = $val['id'];
//     $login = $val['login'];
//     $nama = $val['nama'];
//     $cabang = $val['cabang'];
//
// print "<tr id=\"$id\" class=\"$warna\"".
//       "data-gender=\"$valGender\"".
//       "data-login=\"$login\"".
//       "data-nama=\"$nama\"".
//       "data-cabang=\"$cabang\">".
//       "<td class=\"login\" style=\"height: 50px; overflow:auto;\">$login</td>".
//       "<td class=\"nama\">$nama</td>".
//       "<td class=\"cabang\">".branchMap($cabang)."</td>".
//       "<td >$gender</td>".
//       "<td>&nbsp;</td></tr>";
//  }
// }

function updateUser($par)
{
	$db = new Db();
	$sql = "update login set login = '" . $par['login'] . "', nama = '" . $par['nama'] . "', cabang='" . $par['cabang'] . "', gender='" . $par['gender'] .
		"' where id='" . $par['id'] . "'";
  // print $sql;
	return $db->exe($sql);

  // genUserTable();

}

function updatePwdUser($par)
{
	$db = new Db();
	if ($par['pwd'] != '') {
		$sql = "update login set passwd='" . md5($par['pwd']) . "' where id='" . $par['id'] . "'";
	}

}

function listPolis($branch, $sdate, $edate)
{
	if ($branch < 10) $branch = "0" . $branch;
	$conn = sqlServerConnect();
  // $edate = substr($edate,5,2).'/'.substr($edate,-2,2).'/'.substr($edate,0,4);
  // $sdate = substr($sdate,5,2).'/'.substr($sdate,-2,2).'/'.substr($sdate,0,4);
	$sql = "SELECT distinct A.POLICYNO, A.SDATE,A.EDATE,
          PR.Name as source, A.AID, A.ANAME, P.Address_1, P.Phone_1 FROM ACCEPTANCE as A
          JOIN Profile as P ON A.AID = P.ID JOIN Profile as PR ON A.Source = PR.ID WHERE
          A.ADATE between '$sdate' AND '$edate' AND A.branch = '$branch' AND SUBSTRING(A.REGNO,3,4) NOT IN ('1009','1010') order by A.POLICYNO";
  // print $sql;
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		if (strlen($a['POLICYNO']) > 14) {
			$toc = tocMap(substr($a['POLICYNO'], 4, 4));
		} else {
			$toc = tocMap(substr($a['POLICYNO'], 2, 4));
		}
		$arr[] = array(
			"polis" => $a['POLICYNO'],
			"sdate" => $a['SDATE']->format('d-M-Y'),
			"edate" => $a['EDATE']->format('d-M-Y'),
			"source" => $a['source'],
			"aid" => $a['AID'],
			"ttg" => $a['ANAME'],
			"address" => $a['Address_1'],
			"phone" => $a['Phone_1'],
			"toc" => $toc
		);
	}

	return $arr;
}

function achvq($q)
{
	$arr = json_decode(file_get_contents('../json/achv.json'), true);

	foreach ($arr[$q] as $cabang => $var) {
		$actual = 0;
		$budget = 0;
		foreach ($var as $bulan => $det) {
			$actual += $det['premi'];
			$budget += $det['budget'];
			$tercapai = 0;
			if ($actual >= $budget) {
				$tercapai = 1;
			}

			$arr2[$cabang] = array(
				'actual' => $actual,
				'budget' => $budget,
				'tercapai' => $tercapai
			);
		}
	}

	return ($arr2);
}

function insentive($q)
{
	$arr = achvq($q);
	foreach ($arr as $cabang => $det) {
		if ($det['tercapai'] == 1) {
			$cabangTercapai[$cabang] = array(
				'premi' => $det['actual']
			);
		}
		$gTot += $det['actual'];
		$bTot += $det['budget'];
	}

	$arr = array(
		'tActual' => $gTot,
		'tBudget' => $bTot,
		'cabangTercapai' => $cabangTercapai
	);

	return $arr;
}

function insentiveNew(){
	$arr = json_decode(file_get_contents('../json/achv.json'), true);
	foreach($arr as $q => $branch){
		foreach ($branch as $cabang=>$branchDet){
			foreach($branchDet as $bulan=>$det){

				$detail[$bulan][$cabang] = array(
					"premi"  => $det['premi']*1,
					"budget" => $det['budget']
			);
			}
		}
	}

	foreach($detail as $bulan=>$cabang){
		foreach($cabang as $cbg => $det) {
			if($det['premi'] >= $det['budget']){
				$cabangAch[$bulan][$cbg] = array(
					"premi" => $det['premi'],
					"budget" => $det['budget'],
					"ins" => $det['premi']*0.01
				);
				$totAch += $det['premi'];
			}
			$premiPerCabang += $det['premi'];
			$budgetPerCabang += $det['budget'];
		}

		if(($premiPerCabang/$budgetPerCabang) >= 0.85){
			$cabangAch[$bulan]['PUSAT'] = array(
				"premi" => $premiPerCabang,
				"budget" => $budgetPerCabang,
				"totAch" => $totAch,
				"ins" => ($premiPerCabang - ($totAch*0.85)) * 0.01
			);
		}
		unset($premiPerCabang,$budgetPerCabang,$totAch);
	}

	return $cabangAch;
}


function grossNetStatistik($params)
{

  // debug($params);
	$conn = sqlServerConnect();
	$sql = "DECLARE @P_RLCode as varchar(10) = '" . $params['kode'] . "'
DECLARE @P_PDate as smalldatetime = '" . $params['asAt'] . "'
DECLARE @P_UWYear as int = " . $params['uy'] . "
DECLARE @P_NOfYears as int = 5
DECLARE @P_BackdatedF as int = 0
DECLARE @P_TOC as varchar(10) = ''

Select UWYear, Subject, sum(Own_Retention)as Own_Retention, sum(Own_Retention_Non_XOL) as Own_Retention_Non_XOL,
sum(Quota_Share) as Quota_Share, sum(Surplus_1) as Surplus_1, sum(Surplus_2) as Surplus_2, sum(Surplus_Special) as Surplus_Special,
sum(Facultative) as Facultative, sum(BPPDAN) as BPPDAN, sum(MAIPARK) as MAIPARK, sum(XOL) as XOL, sum(XOL_EVENT) as XOL_EVENT
From
(
Select Acceptance.OANO, Year(RarrHeader.RDate) as UWYear, max(Acceptance.ANO) as ANO,
CASE WHEN ACCASS.CODE = 'P' THEN 'Premium' ELSE 'COMMISSION' END AS SUBJECT,
Sum(Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end)) as Own_Retention,
sum(Case When RArrHeader.EXOLF=1 then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as Own_Retention_Non_XOL,
sum(Case When AccAss.Type in ('DC','AF','QS','QP') Then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as Quota_Share,
sum(Case When coalesce(Reinsurance.SpecialF,0)=0 and AccAss.Type in ('S1') Then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as Surplus_1,
sum(Case When coalesce(Reinsurance.SpecialF,0)=0 and AccAss.Type in ('S2','S3') Then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as Surplus_2,
sum(Case When coalesce(Reinsurance.SpecialF,0)=1 and AccAss.Type in ('S1','S2','S3') Then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as Surplus_Special,
sum(Case When AccAss.Type in ('FO') Then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as Facultative,
sum(Case When AccAss.Type in ('CP') Then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as BPPDAN,
sum(Case When AccAss.Type in ('XP') Then Accass.Amount*(Case When ReRate.Rate is null then 1 else ReRate.Rate end) else 0 end) as MAIPARK,
sum(0) as XOL,
sum(0) as XOL_EVENT
from R_AccAss AccAss
Left Join Reinsurance on (Reinsurance.DocNo=AccAss.RefNo)
inner join Acceptance on (Acceptance.ANO=AccAss.ANO)
inner join Cover on (Cover.CNO=Acceptance.CNO)
inner join RArrHeader on (RarrHeader.ANO=Acceptance.ANO)
Left Join ReRate On (ReRate.TOC=Cover.TOC and ReRate.RYear=Year(RArrHeader.RDate) and ReRate.Currency=AccAss.Currency)
inner Join RLTableC On (RLTableC.TOC=Cover.TOC)
Where AccAss.Type in
('DI','IC','CF','JP','IR','OP','DC','AF','QS','QP','XP','S1','S2','S3','FO','CP','X1','X2','X3','X4','X5','X6','X7','X8','X9','XX','RP','AP')
and (@P_BackdatedF=1 or Acceptance.backdatedF=@P_BackdatedF)
and AccAss.Code in ('P','c')
and Acceptance.ADate<=@P_PDate and Acceptance.AStatus<>'W'
and Year(RArrHeader.RDate) Between @P_UWYear-@P_NOfyears+1 and @P_UWYear
and RLTableC.RLCode=@P_RLCode
and (RLTableC.TOC=@P_TOC or @P_TOC='')
Group By Acceptance.OANO, Year(RarrHeader.RDate),ACCASS.CODE

union all

Select X.OANO, X.UWYear, Max(X.ANO) as ANO, X.Subject, sum(Own_Retention) as Own_Retention, sum(Own_Retention_Non_XOL) as Own_Retention_Non_XOL, sum(Quota_Share) as Quota_Share, sum(Surplus_1) as Surplus_1, sum(Surplus_2) as Surplus_2, sum(Surplus_Special) as Surplus_Special, sum(Facultative) as Facultative, sum(BPPDAN) as BPPDAN, sum(MAIPARK) as MAIPARK, sum(XOL) as XOL, sum(XOL_EVENT) as XOL_EVENT
from
(
 Select Acceptance.OANO, Year(RarrHeader.RDate) as UWYear, (Acceptance.ANO) as ANO, 'Claim Settled' as Subject,
 (-ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end)) as Own_Retention,
 (Case When RArrHeader.EXOLF=1 then (-ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end)) else 0 end) as Own_Retention_Non_XOL,
 (Case When ClaAss.Type in ('DC','AF','QS','QP') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as Quota_Share,
 (Case When coalesce(Reinsurance.SpecialF,0)=0 and ClaAss.Type in ('S1') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as Surplus_1,
 (Case When coalesce(Reinsurance.SpecialF,0)=0 and ClaAss.Type in ('S2','S3') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as Surplus_2,
 (Case When coalesce(Reinsurance.SpecialF,0)=1 and ClaAss.Type in ('S1','S2','S3') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as Surplus_Special,
 (Case When ClaAss.Type in ('FO') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as Facultative,
 (Case When ClaAss.Type in ('CP') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as BPPDAN,
 (Case When ClaAss.Type in ('XP') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as MAIPARK,
 (Case When coalesce(Event.Category,'R')='R' and ClaAss.Type in ('X1','X2','X3','X4','X5','X6','X7','X8','X9','XX','RP','AP') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as XOL,
 (Case When coalesce(Event.Category,'R')='E' and ClaAss.Type in ('X1','X2','X3','X4','X5','X6','X7','X8','X9','XX','RP','AP') Then -ClaAss.Amount*(Case When ReRate.CRate is null then 1 else ReRate.CRate end) else 0 end) as XOL_Event
 from ClaAss
 Left Join Reinsurance on (Reinsurance.DocNo=ClaAss.RefNo)
 inner join Claim on (Claim.CNO=ClaAss.CNO)
 Left Join Event on (Event.EventNo=Claim.EventNo)
 inner join Acceptance on (Acceptance.ANO=Claim.ANO)
 inner join Cover on (Cover.CNO=Acceptance.CNO)
 inner join RArrHeader on (RarrHeader.ANO=Acceptance.ANO)
 Left Join ReRate On (ReRate.TOC=Cover.TOC and ReRate.RYear=Year(Claim.LossDate) and ReRate.Currency=ClaAss.Currency)
 inner Join RLTableC On (RLTableC.TOC=Cover.TOC)
 Where ClaAss.Type in ('DI','IC','CF','JP','IR','OP','DC','AF','QS','QP','XP','S1','S2','S3','FO','CP','X1','X2','X3','X4','X5','X6','X7','X8','X9','XX','RP','AP')
 and (@P_BackdatedF=1 or Claim.backdatedF=@P_BackdatedF)
 and ClaAss.Code in ('A','E','S','L','R','I')
 and Claim.ADate<=@P_PDate and Claim.CStatus<>'T'
 and Year(RArrHeader.RDate) Between @P_UWYear-@P_NOfyears+1 and @P_UWYear
 and RLTableC.RLCode=@P_RLCode
 and (RLTableC.TOC=@P_TOC or @P_TOC='')

 union all

 Select Acceptance.OANO, Year(RarrHeader.RDate) as UWYear, (Acceptance.ANO) as ANO, 'Claim Outstanding' as Subject,
 ((OS_Claim.Gross_Loss*CArrHeader.ERetention/100-OS_Claim.Gross_Settled*CArrHeader.Retention/100)*OS_Claim.Rate) as Own_Retention,
 (Case When RArrHeader.EXOLF=1 then ((OS_Claim.Gross_Loss*CArrHeader.ERetention/100-OS_Claim.Gross_Settled*CArrHeader.Retention/100)*OS_Claim.Rate) else 0 end) as Own_Retention_Non_XOL,
 (-(OS_Claim.Gross_OS*(RArrHeader.DShare+RArrHeader.AShare+RArrHeader.QShare)/100)*OS_Claim.Rate) as Quota_Share,
 (Case When coalesce(SP1Reinsurance.Specialf,0)=0 then (-(OS_Claim.Gross_OS*RArrHeader.SP1Share/100)*OS_Claim.Rate) else 0 end) as Surplus_1,
 (Case When coalesce(SP2Reinsurance.Specialf,0)=0 then (-(OS_Claim.Gross_OS*RArrHeader.SP2Share/100)*OS_Claim.Rate) else 0 end)+(Case When coalesce(SP3Reinsurance.Specialf,0)=0 then (-(OS_Claim.Gross_OS*RArrHeader.SP3Share/100)*OS_Claim.Rate) else 0 end) as Surplus_2,
 (Case When coalesce(SP1Reinsurance.Specialf,0)=1 then (-(OS_Claim.Gross_OS*RArrHeader.SP1Share/100)*OS_Claim.Rate) else 0 end)+(Case When coalesce(SP2Reinsurance.Specialf,0)=1 then (-(OS_Claim.Gross_OS*RArrHeader.SP2Share/100)*OS_Claim.Rate) else 0 end)+(Case When coalesce(SP3Reinsurance.Specialf,0)=1 then (-(OS_Claim.Gross_OS*RArrHeader.SP3Share/100)*OS_Claim.Rate) else 0 end) as Surplus_Special,
 (-(OS_Claim.Gross_OS*(RArrHeader.FShare)/100)*OS_Claim.Rate) as Facultative,
 (-(OS_Claim.Gross_OS*(RArrHeader.CShare)/100)*OS_Claim.Rate) as BPPDAN,
 (-(OS_Claim.Gross_OS*(RArrHeader.XPShare)/100)*OS_Claim.Rate) as MAIPARK,
 (Case When coalesce(Event.Category,'R')='R' then -(OS_Claim.Gross_Loss*(CArrHeader.XL1EShare+CArrHeader.XL2EShare+CArrHeader.XL3EShare+CArrHeader.XL4EShare+CArrHeader.XL5EShare+CArrHeader.XL6EShare+CArrHeader.XL7EShare)/100-OS_Claim.Gross_Settled*(CArrHeader.XL1Share+CArrHeader.XL2Share+CArrHeader.XL3Share+CArrHeader.XL4Share+CArrHeader.XL5Share+CArrHeader.XL6Share+CArrHeader.XL7Share)/100)*OS_Claim.Rate else 0 end) as XOL,
 (Case When coalesce(Event.Category,'R')='E' then -(OS_Claim.Gross_Loss*(CArrHeader.XL1EShare+CArrHeader.XL2EShare+CArrHeader.XL3EShare+CArrHeader.XL4EShare+CArrHeader.XL5EShare+CArrHeader.XL6EShare+CArrHeader.XL7EShare)/100-OS_Claim.Gross_Settled*(CArrHeader.XL1Share+CArrHeader.XL2Share+CArrHeader.XL3Share+CArrHeader.XL4Share+CArrHeader.XL5Share+CArrHeader.XL6Share+CArrHeader.XL7Share)/100)*OS_Claim.Rate else 0 end) as XOL_Event
 from OS_CLAIM_BY_DETAIL(@P_PDate) OS_Claim
 inner join Claim on (Claim.CNO=OS_Claim.CNO)
 Left Join Event on (Event.EventNo=Claim.EventNo)
 inner join Acceptance on (Acceptance.ANO=Claim.ANO)
 inner join Cover on (Cover.CNO=Acceptance.CNO)
 inner join RArrHeader on (RarrHeader.ANO=Acceptance.ANO)
 Left Join Reinsurance SP1Reinsurance on (SP1Reinsurance.RID=RArrHeader.SP1ID)
 Left Join Reinsurance SP2Reinsurance on (SP2Reinsurance.RID=RArrHeader.SP2ID)
 Left Join Reinsurance SP3Reinsurance on (SP3Reinsurance.RID=RArrHeader.SP3ID)
 inner Join RLTableC On (RLTableC.TOC=Cover.TOC)
 inner join CArrHeader on (Claim.CNO=CArrHeader.CNO)
 Where Year(RArrHeader.RDate) Between @P_UWYear-@P_NOfyears+1 and @P_UWYear and RLTableC.RLCode=@P_RLCode and (RLTableC.TOC=@P_TOC or @P_TOC='')
) X
Group By X.OANO, X.UWYear, X.Subject
) R
Group By UWYear, Subject
";

	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$hasil['OR'][$a['UWYear']][$a['Subject']] = $a['Own_Retention'];
		$hasil['QS'][$a['UWYear']][$a['Subject']] = $a['Quota_Share'];
		$hasil['Sp1'][$a['UWYear']][$a['Subject']] = $a['Surplus_1'];
		$hasil['Sp2'][$a['UWYear']][$a['Subject']] = $a['Surplus_2'];
		$hasil['SpS'][$a['UWYear']][$a['Subject']] = $a['Surplus_Special'];
		$hasil['Fac'][$a['UWYear']][$a['Subject']] = $a['Facultative'];
		$hasil['CP'][$a['UWYear']][$a['Subject']] = $a['BPPDAN'];
		$hasil['XP'][$a['UWYear']][$a['Subject']] = $a['MAIPARK'];
		$hasil['WXoL'][$a['UWYear']][$a['Subject']] = $a['XOL'];
		$hasil['CXoL'][$a['UWYear']][$a['Subject']] = $a['XOL_EVENT'];
	}

	return $hasil;
}

function RLCode()
{
	$conn = sqlServerConnect();
	$sql = "select RLCode, Description from RLTableH where Basis='R'";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$hasil[] = array(
			"code" => $a['RLCode'],
			"desc" => $a['Description']
		);
	}

	return $hasil;
}

function opex($mo, $tahun, $cabang)
{
	$conn = sqlServerConnect();
  // $bln = array();

	if ($cabang < 10) {
		$cabang = "and GL.Branch = '0" . $cabang . "' ";

	} else if ($cabang == 10) {
		$cabang = "";
	} else {
		$cabang = "and GL.Branch = '" . $cabang . "' ";
	}
	if (!isset($flag)) {
		$account = "in (81310000,81320000,81330000)";
	} else {
		$account = "in (80000000,81000000,82000000,83000000,89000000)";
	}
	$i = 1;
	$blnc = "(";
	while ($mo >= $i) {
		$blnc .= $i++ . ",";
	}

  // $account = "(81310000,81320000,81330000)";

	$blnc = substr($blnc, 0, strlen($blnc) - 1) . ")";
  //
  // debug($blnc);
	$sql = "
  Select Account.Account as Account, Account.Description, gl.[month],gl.[Year] as [Year],
  sum(case when GL.Month in $blnc  then GL.Starting else 0 end) as Starting,
  sum(case when GL.Month in $blnc  then GL.Ending else 0 end) as Ending,
  sum(Case When GL.Debit is null Then 0 else GL.Debit end) as Debit,
  sum(Case When GL.Credit is null Then 0 else GL.Credit end) as Credit,
  sum(Case When GL.Revaluation is null Then 0 else GL.Revaluation end) as Revaluation,
  sum(case When GL.Month in $blnc  then GL.Closing else 0 end) as Closing
  into #GL
  from Account
  Left Join Type on (Type.Type=Account.Type)
  Left Join GL on GL.Account=Account.Account and GL.[Month] in $blnc and  GL.[Year]=$tahun $cabang
  where account.account $account
  Group By Account.Account, gl.[month], gl.[Year],Account.Description

  Select Account.Account as Account,gl.[Month],gl.[Year] as [Year],
  sum(Case when GL.Month in $blnc  then GL.Starting else 0 end) as Starting,
  sum(Case when GL.Month in $blnc  then GL.Ending else 0 end) as Ending,
  sum(Case When GL.Debit is null Then 0 else GL.Debit end) as Debit,
  sum(Case When GL.Credit is null Then 0 else GL.Credit end) as Credit,
  sum(Case When GL.Revaluation is null Then 0 else GL.Revaluation end) as Revaluation,
  sum(case When GL.Month in $blnc  then GL.Closing else 0 end) as Closing
  into #GL_BS
  from Account
  inner join Type on (Type.Type=Account.Type and Type.BS_Flag=1)
  inner join GL on GL.Account=Account.Account and GL.[Month] in $blnc and  GL.[Year]=$tahun $cabang
  where account.account $account
  Group By Account.Account,gl.[Month], gl.[Year]

  Update #GL
  Set #GL.Debit=#GL.Debit+Case When #GL_BS.Starting>#GL.Starting+#GL.Debit-#GL.Credit Then #GL_BS.Starting-(#GL.Starting+#GL.Debit-#GL.Credit) else 0 end,
  #GL.Credit=#GL.Credit+Case When #GL_BS.Starting<#GL.Starting+#GL.Debit-#GL.Credit Then -(#GL_BS.Starting-(#GL.Starting+#GL.Debit-#GL.Credit)) else 0 end
  from #GL, #GL_BS Where #GL.Account=#GL_BS.Account and #GL.Branch=#GL_BS.Branch and round(#GL_BS.Starting,2)<>round(#GL.Starting+#GL.Debit-#GL.Credit,2) and #GL.Account like '5%'
  ";
  // print $sql;
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	$sql1 = "select * from #gl";
  //
	$query_result1 = sqlsrv_query($conn, $sql1) or die(debug(sqlsrv_errors()));
	while ($a1 = sqlsrv_fetch_array($query_result1, SQLSRV_FETCH_ASSOC)) {

		$hasil[$a1['Account']][$a1['month']] = $a1['Closing'];
		$hasil[$a1['Account']]['Desc'] = $a1['Description'];

         // array(
                // 'acc'  => $a1['Account'],
                // 'bulan' => $a1['month'],
                // 'thn'   => $a1['Year'],
                // 'starting' => $a1['Starting'],
                // 'ending' => $a1['Ending'],
                // 'debit' => $a1['Debit'],
                // 'credit' => $a1['Credit'],
                // 'reval' => $a1['Revaluation'],
                // 'closing' => $a1['Closing']
        // );

	}
  //   print $count++;
  //

	return $hasil;
}


function soaTreaty($par)
{
  // debug($par);
	$conn = sqlServerConnect();
	$asAt = $par['blnAsAt'] . "/" . $par['tglAsAt'] . "/" . $par['tahun2'];
	switch ($par['quarter']) {
		case 1:
			$dari = '1/1/' . $par['tahun'];
			$sampai = '3/31/' . $par['tahun'];
			break;
		case 2:
			$dari = '4/1/' . $par['tahun'];
			$sampai = '6/30/' . $par['tahun'];
			break;
		case 3:
			$dari = '7/1/' . $par['tahun'];
			$sampai = '9/30/' . $par['tahun'];
			break;
		case 4:
			$dari = '10/1/' . $par['tahun'];
			$sampai = '12/31/' . $par['tahun'];
			break;
		default:
			break;
	};
	$sql = "
  Select admlink.id, admlink.A_PolicyNo as polis, admlink.subject, Voucher.voucher, voucher.currency, voucher.vtype,
  case when debtorF=1 then -voucher.amountdue else voucher.amountdue end as amountdue,
  case when debtorF=1 then -voucher.amountundue else voucher.amountundue end as amountundue from Admlink
    Left Join Reinsurance on
    (Reinsurance.DocNo=Admlink.RefNo),
    nVoucher Voucher, TOC, Profile
  Where
    Admlink.Date Between '$dari' and '$sampai' and
    Profile.ID=Admlink.ID and
    TOC.TOC=Admlink.Code and
    Voucher.Voucher=Admlink.Voucher and
    Voucher.Date<='$asAt'

    and TOC.ConsortiumF=0
    and (voucher.vtype like 'qs%' or voucher.vtype like 'sp%')";

  // print $sql;
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
    // debug($a);
		$hasil[] = array(
			"id" => $a['id'],
			"tipe" => $a['subject'],
			"polis" => $a['polis'],
			"voucher" => $a['voucher'],
			"currency" => $a['currency'],
			"vtype" => $a['vtype'],
			"amountdue" => $a['amountdue'],
			"amountundue" => $a['amountundue']
		);
	}

	return $hasil;
}

function namaProfile($id){
	$conn = sqlServerConnect();
	$sql = "SELECT NAME from Profile where ID='$id'";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$hasil = $a['NAME'];
	}

	return $hasil;
}


function facBayar($par){
	$conn = sqlServerConnect();
	$tglAwal = $par['tglAwal'];
	$tglAkhir = $par['tglAkhir'];
	// $sql = "select * from acceptance where policyno='01012119000001'";
	$sql = "
SELECT
	VOUCHER, DATE, REFNO, PROACC,DOCNO,VTYPE, REMARKS, CURRENCY, NOMINAL_CC, BRANCH,
	DUEDATE, DEBTORF, CREDITORF, DIFF_CC, PAYMENT_CC, NPAYMENT, INSTALLMENT,
	AMOUNTDUE, AMOUNTUNDUE, NDUEDATE, MDUEDATE, DUEDATE_1, AMOUNTDUE_1,
	DUEDATE_2, AMOUNTDUE_2, DUEDATE_3, AMOUNTDUE_3, DUEDATE_4, AMOUNTDUE_4,DUEDATE_5,AMOUNTDUE_5,
	DUEDATE_6, AMOUNTDUE_6, DUEDATE_7, AMOUNTDUE_7, DUEDATE_8, AMOUNTDUE_8, DUEDATE_9, AMOUNTDUE_9, DUEDATE_10,
	AMOUNTDUE_10, DUEDATE_11, AMOUNTDUE_11, DUEDATE_12, AMOUNTDUE_12
FROM NVOUCHER WHERE VTYPE LIKE 'FO%' AND (
		DUEDATE_1 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_2 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_3 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_4 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_5 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_6 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_7 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_8 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_9 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_10 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_11 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE_12 between '$tglAwal' and '$tglAkhir' OR
		DUEDATE BETWEEN '$tglAwal' AND '$tglAkhir')
		ORDER BY PROACC, CURRENCY, DATE, INSTALLMENT";

	// print $sql;
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));

	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$tot = round($a['NOMINAL_CC'] - $a['DIFF_CC'] - $a['PAYMENT_CC'],4);
		$total = $a['NOMINAL_CC'] - $a['DIFF_CC'];
		$paid = $a['PAYMENT_CC'];
		if($tot != 0) {
			$bdate = $a['DATE']->format("d M Y");
			if($a['INSTALLMENT'] == 0){
				$duedate = $a['DUEDATE']->format("d M Y");
				$installment = 0;
				$amount = $a['AMOUNTDUE'];

			}else{
				if($a['NPAYMENT'] == $a['INSTALLMENT']) {
					$angka = $a['INSTALLMENT'];
				} else{
					$angka = $a['NPAYMENT']+1;
				}
				$installment = $angka." of ".$a['INSTALLMENT'];
				$due = "DUEDATE_".$angka;
				$amt = "AMOUNTDUE_".$angka;
				$duedate = $a[$due]->format('d M Y');
				// $duedate = $duedate->format('d M Y');
				$amount = $a[$amt];
			}

			if($a['DEBTORF'] == 1) {
				$amount *=-1;
				$paid *=-1;
				$tot *=-1;
				$total *=-1;
			}
			$id = explode('-',$a['PROACC']);
			$nama = namaProfile($id[0]);
			$arr[] = array(
				"voucher" => $a['VOUCHER'],
				"bookingDate" => $bdate,
				"polis" => $a['DOCNO'],
				"reas" => $nama,
				"RefNo" => $a['REFNO'],
				"vType" => $a['VTYPE'],
				"remarks" => $a['REMARKS'],
				"kurs" => $a['CURRENCY'],
				"cabang" => branchMap($a['BRANCH']),
				"total" => $total,
				"paid" => $paid,
				"amountDue" => $amount,
				"amountUndue" => $tot-$amount,
				"dueDate" => $duedate,
				"installment" => $installment
			);
		}

	}

	return $arr;

}


function borderaux($par){
	$conn = sqlServerConnect();
	$tglAwal = $par['tglAwal'];
	$tglAkhir = $par['tglAkhir'];
	$bCutoff = substr($tglAkhir,5,2) + 1;
	$cutOff = substr($tglAkhir,0,4)."-$bCutoff".'-05';  // Tanggal cut off tanggal 5 bulan berikut
	// $sql = "select * from acceptance where policyno='01012119000001'";
	$sql1 = "
	select a.POLICYNO, sum(ac.AMOUNT) as adm from ACCFD ac
		join ACCEPTANCE A on a.ANO = ac.ANO
		where ac.ANO in (
		SELECT min(R.ANO) FROM RCOVER R
		JOIN ACCEPTANCE A ON A.ANO = R.ANO WHERE R.DEPARTDATE BETWEEN '$tglAwal' AND '$tglAkhir'
		AND ADATE<='$cutOff'
		AND FLDID<>'' AND A.POLICYNO LIKE '0103%' AND A.SOURCE='M01IT00001'
		group by A.POLICYNO, A.CERTIFICATENO
		) AND A.ATYPE!='C' group by ac.ANO, a.POLICYNO";

	// print $sql1;
	$query_result1 = sqlsrv_query($conn, $sql1) or die(debug(sqlsrv_errors()));

	while ($b = sqlsrv_fetch_array($query_result1, SQLSRV_FETCH_ASSOC)) {
		$mat[$b['POLICYNO']] = $b['adm'];
	}

	$sql = "
	select r.ANO,m.Address_1 AS MOP, a.Aname AS Tertanggung,
	r.vessel as kapal, r.departdate as ETD, p.tsi as TSI, r.rate as RATE, p.gross as PREMI, r.remark as KONDISI,a.policyno + '/' +
	a.certificateno as POLIS,
	IC.REMARK AS interest,
	a.adate as book
	from rcover r
	join acceptance a on a.ano = r.ano
	join pcalc p on p.ano = r.ano
	join profile m on m.id = a.aid
	join icover ic on ic.ano = a.ano
	where r.ano in (
	select max(a.ano) from rcover r
	join acceptance a on a.ano = r.ano where r.departdate BETWEEN '$tglAwal' AND '$tglAkhir' AND ADATE<='$cutOff'
	and fldid<>'' and policyno like '0103%' and a.source = 'M01IT00001' group by a.policyno, a.certificateno
	)  AND ATYPE!='C'
	ORDER BY m.Address_1,A.POLICYNO,A.CERTIFICATENO";

	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));

	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$pol = substr($a['POLIS'],0,14);
		$dcl = str_split(substr($a['Tertanggung'],strlen($a['Tertanggung'])-6,6));
		$dc = '';
		foreach($dcl as $cek){
			if(is_numeric($cek)) {
				$dc .= $cek;
			}
		}

		$arr[$a['MOP']][] = array(
			"MOP" => $a['MOP'],
			"Tertanggung" => $a['Tertanggung'],
			"DCL" => $dc,
			"kapal" => $a['kapal'],
			"ETD" => $a['ETD'],
			// "currency" => $a['CURRENCY'],
			"TSI" => $a['TSI'],
			"RATE" => $a['RATE'],
			"INTEREST" => $a['interest'],
			"PREMI" => $a['PREMI'],
			"BIAYA" => $mat[$pol],
			"KONDISI" => $a['KONDISI'],
			"POLIS" => $a['POLIS'],
			"book"	=> $a['book']
		);
		// unset($dc);

	}
	return $arr;
}

function lph($par){
	$conn = sqlServerConnect();
	$tglAwal = $par['tglAwal'];
	$tglAkhir = $par['tglAkhir'];

	$sql = "select * from r_pasarconsortium('$tglAwal', '$tglAkhir')";
	$query_result = sqlsrv_query($conn, $sql) or die(debug(sqlsrv_errors()));
	while ($a = sqlsrv_fetch_array($query_result, SQLSRV_FETCH_ASSOC)) {
		$arr[] = array(
			'refNo' => $a['PolicyNo'],
			'sdate' => $a['SDate'],
			'edate' => $a['EDate'],
			'nkr'		=> $a['ValueID'],
			'fZone'	=> $a['Flood_Zone'],
			'lph'		=> $a['LPH'],
			'remark'=> $a['AName'],
			'fire'	=> abs($a['Premium_Fire']/$a['TSI'])*1000,
			'flood' => abs($a['Premium_Flood']/$a['TSI'])*1000,
			'41a'		=> abs($a['Premium_RSMD_A']/$a['TSI'])*1000,
			'41b'		=> abs($a['Premium_RSMD_B']/$a['TSI'])*1000,
			'landslide' => abs($a['Premium_LandSlide']/$a['TSI'])*1000,
			'rod'		=> abs($a['Premium_ROD']/$a['TSI'])*1000,
			'vehicle' => abs($a['Premium_Vehicle_Impact']/$a['TSI'])*1000,
			'eq'		=> abs($a['Premium_EQ']/$a['TSI']*1000),
			'kurs'	=> $a['Currency'],
			'bangunan' => $a['Building'],
			'others'	=> $a['Others'],
			'stock'		=> $a['Stock'],
			'rentV'		=> $a['RentValue'],
			'bDate'		=> $a['Date'],
			'premium'	=> ($a['Premium_Fire'] + $a['Premium_Flood'] + $a['Premium_RSMD_A'] + $a['Premium_RSMD_B'] + $a['Premium_ROD'] + $a['Premium_LandSlid'] + $a['Premium_Vehicle_Impact'] + $a['Premium_Others'])*-1,
			'premiEQ'	=> $a['Premium_EQ']*-1
		);
	}

	return $arr;
}

?>
