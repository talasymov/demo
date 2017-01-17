<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/libs/phpexcel/PHPExcel/IOFactory.php");

// Открываем файл
$xls = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"] . '/libs/phpexcel/contacts.xls');
// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();

//echo "<table>";

for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {
//    echo "<tr>";

    $nColumn = PHPExcel_Cell::columnIndexFromString(
    $sheet->getHighestColumn());

    $name = "";
    $surName = "";
    $patronymicName = "";
    $mobile = "";
    $phone = "";
    $email = "";
    $companyName = "";

    for ($j = 0; $j < $nColumn; $j++) {
        $value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
        if($j == 0 || $j == 1 || $j == 2 || $j == 5 || $j == 8 || $j == 15 || $j == 16)
        {
            if($j == 0)
            {
                $name = $value;
            }
            if($j == 1)
            {
                $patronymicName = $value;
            }
            if($j == 2)
            {
                $surName = $value;
            }
            if($j == 5)
            {
                $companyName = $value;
            }
            if($j == 8)
            {
                $mobile = $value;
            }
            if($j == 16)
            {
                $phone = $value;
            }
            if($j == 16)
            {
                $email = $value;
            }
        }
//        echo "<td>$value</td>";
    }

    $companyR = R::getRow("SELECT * FROM dashboard_companies WHERE companyName LIKE '%" . $companyName . "%'");
    $compId = $companyR["id"];
//    echo $companyName . "_" . $compId . "<br />";
    if($companyR == null)
    {
        $compId = 1;
    }

    R::exec("INSERT INTO dashboard_customers (companyId, lastName, firstName, patronymicName, phone, phone_2, email, fromWhom, byWhomAdding) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [$compId, $surName, $name, $patronymicName, $mobile, $phone, $email, 1, $_COOKIE["userId"]]);

    $lastId = R::getRow("SELECT customerId FROM dashboard_customers ORDER BY customerId DESC");

    $id = $lastId["customerId"];

    $filename = "/disk/clients/client$id";

    if (file_exists($filename)) {
        $filename = "/disk/clients/client_$id";
    } else {
        mkdir( $_SERVER["DOCUMENT_ROOT"] . $filename, 0777);
    }

    R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd, dashboard_peoples_whoisit, dashboard_peoples_id_client) VALUES(?, ?, ?, 1, ?)", ["client$id", "_op" . rand(1111,9999) . "tyi_", $_COOKIE["userId"], $id]);


//    echo "</tr>";
}
//echo "</table>";
?>
