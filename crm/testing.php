<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/libs/phpexcel/PHPExcel/IOFactory.php");

//// Открываем файл
//$xls = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"] . '/libs/phpexcel/companies.xls');
//// Устанавливаем индекс активного листа
//$xls->setActiveSheetIndex(0);
//// Получаем активный лист
//$sheet = $xls->getActiveSheet();
//
////echo "<table>";
//
//for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {
////    echo "<tr>";
//
//    $nColumn = PHPExcel_Cell::columnIndexFromString(
//    $sheet->getHighestColumn());
//    $phones = "";
//    $nameCompany = "";
//    $emailCompany = "";
//    $logoPath = "";
//
//    for ($j = 0; $j < $nColumn; $j++) {
//        $value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
//        if($j == 0 || $j == 1 || $j == 15 || $j == 16)
//        {
//
//            if($j == 0)
//            {
//                $file = $value;
//                $pos      = strripos($file, ".");
//                $rest = substr($file, $pos, strlen($file));
//                $newfile = $_SERVER["DOCUMENT_ROOT"] . "/disk/logotypes/" . md5($file) . $rest;
//
//                if (!copy($file, $newfile)) {
//                    echo "не удалось скопировать $file...\n";
//                }
//
//                $logoPath = "/disk/logotypes/" . md5($file) . $rest;
//            }
//            if($j == 1)
//            {
//                $nameCompany = $value;
//            }
//            if($j == 16)
//            {
//                $emailCompany = $value;
//            }
//            if($j == 15)
//            {
//                $phones = explode(",", $value);
//            }
//
////            echo "<td>$value</td>";
//        }
//    }
//    if(!isset($phones[0]))
//    {
//        $phones[0] = "";
//    }
//    if(!isset($phones[1]))
//    {
//        $phones[1] = "";
//    }
//    if(!isset($phones[2]))
//    {
//        $phones[2] = "";
//    }

//    if($nameCompany != null)
//    {
//        R::exec("INSERT INTO dashboard_companies (companyName, logotype, dashboard_companies_mobile, dashboard_companies_phone, dashboard_companies_any_phone, dashboard_companies_email, byWhomAdding) VALUES(?, ?, ?, ?, ?, ?, ?)", [$nameCompany, $logoPath, $phones[0], $phones[1], $phones[2], $emailCompany, $_COOKIE["userId"]]);
//
//        $lastIdCompany = R::getRow("SELECT id FROM dashboard_companies ORDER BY id DESC");
//
//        $idCompany = $lastIdCompany["id"];
//
//        $filenameCompany = "/disk/customers/company$idCompany";
//
//        if (file_exists($filenameCompany)) {
//            $filenameCompany = "/disk/customers/company_$idCompany";
//        } else {
//            mkdir( $_SERVER["DOCUMENT_ROOT"] . $filenameCompany, 0777);
//        }
//
//        R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd, dashboard_peoples_whoisit, dashboard_peoples_id_client) VALUES(?, ?, ?, 2, ?)", ["company" . $idCompany, "_op" . rand(1111,9999) . "tyi_", $_COOKIE["userId"], $idCompany]);
//    }


//    echo "</tr>";
//}
//echo "</table>";
$get = "(093) 456 63 84";

$subString = str_replace(" ", "", $get);
$subString = str_replace(")", "", $subString);
$subString = str_replace("(", "", $subString);
$subString = str_replace("-", "", $subString);
$subString = str_replace("+38", "", $subString);
$subString = substr($subString, strlen($subString) - 9, strlen($subString));

$firstName = "Айхам";
$strLen = strlen($firstName);


if($strLen > 4){
    $firstName = substr($firstName, 0, $strLen - 2);
}
$contacts = R::getAll("
SELECT * FROM
(
  SELECT
  REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(phone, '-', ''), '+38', ''), ')', ''), '(', ''), ' ', '') AS replacePhone,
  REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(phone_2, '-', ''), '+38', ''), ')', ''), '(', ''), ' ', '') AS replacePhone_2,
  REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(phone_3, '-', ''), '+38', ''), ')', ''), '(', ''), ' ', '') AS replacePhone_3,
  lastName, firstName, patronymicName, customerId
  FROM dashboard_customers
) AS innerTable

WHERE
( replacePhone LIKE '%" . $subString . "%' AND firstName LIKE '%" . $firstName . "%' ) OR
( replacePhone LIKE '%" . $subString . "%' AND lastName LIKE '%" . $firstName . "%' ) OR
( replacePhone LIKE '%" . $subString . "%' AND patronymicName LIKE '%" . $firstName . "%' ) OR
( replacePhone_2 LIKE '%" . $subString . "%' AND patronymicName LIKE '%" . $firstName . "%' ) OR
( replacePhone_2 LIKE '%" . $subString . "%' AND lastName LIKE '%" . $firstName . "%' ) OR
( replacePhone_2 LIKE '%" . $subString . "%' AND firstName LIKE '%" . $firstName . "%' ) OR
( replacePhone_3 LIKE '%" . $subString . "%' AND patronymicName LIKE '%" . $firstName . "%' ) OR
( replacePhone_3 LIKE '%" . $subString . "%' AND lastName LIKE '%" . $firstName . "%' ) OR
( replacePhone_3 LIKE '%" . $subString . "%' AND firstName LIKE '%" . $firstName . "%' )
");

//print(count($contacts));
//$contacts = R::getAll("
//SELECT REPLACE( REPLACE( REPLACE( REPLACE(phone, '+38', ''), ')', ''), '(', ''), ' ', '') AS replacePhone
//FROM
//dashboard_customers
//WHERE replacePhone LIKE '%4%'
//");
//print_r($contacts);
foreach($contacts as $key => $value)
{
    echo  $value["customerId"] . " " . $value["patronymicName"] . " " . $value["lastName"] . " " . $value["firstName"] . " " . $value["replacePhone"] . "<br />";
}
?>
