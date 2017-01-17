<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

//$filesList = R::getAll();;
$customerId = 91; // $_COOKIE["userId"]
$whoIsIt = "company"; // client OR company $_COOKIE["whoIsIt"]

$filelist = array();
// if ($handle = opendir("/var/www/html/disk/customers/company" . $customerId . "/")) {
//     while ($entry = readdir($handle)) {
//         if (is_file($entry)) {
//             $filelist[] = $entry;
//         }
//     }
//     closedir($handle);
// }
if($whoIsIt == "client")
{
    $custom = "clients";
}
else {
    $custom = "customers";
}
$dir = "/var/www/html/disk/{$custom}/{$whoIsIt}" . $customerId . "/";
$dirUser = "/disk/{$custom}/{$whoIsIt}" . $customerId . "/";
$files1 = scandir($dir);

$tr = "";

foreach ($files1 as $key => $value) {
    if($value != "." && $value != "..")
    {
        $dateFile = date("Y-m-d H:i:s", filemtime($dir . $value));
        $tr .= <<<EOF
    <tr><td>{$value}</td><td>{$dateFile}</td><td>
    <a href="{$dirUser}{$value}"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#modalAddOrderDesigner" aria-haspopup="true" aria-expanded="true">
        <i class="fa fa-arrow-up" aria-hidden="true"></i>
    </button></a>
    </td></tr>
EOF;
    }
}
$output = <<<EOF
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Файловый менеджер</h2>
            <button class="btn btn-default dropdown-toggle open-file-manager" type="button" data-toggle="modal" data-target="#newExpense"
                    aria-haspopup="true" aria-expanded="true">
                Добавить файл&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table id="example" class="table">
                <thead>
                    <tr>
                        <th>Имя файла</th>
                        <th>Дата добавления</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                {$tr}
                </tbody>
              </table>
        </div>
    </div>
</div>

<div class="modal fade" id="newExpense" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Создание нового расхода
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="">Создать</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
EOF;
print($output);
?>