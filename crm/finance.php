<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$users = R::getAll("SELECT * FROM dashboard_users");
$movement = R::getAll("
SELECT * FROM dashboard_movement
INNER JOIN dashboard_movement_types ON dashboard_movement_types.dashboard_movement_types_id = dashboard_movement.dashboard_movement_type
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = dashboard_movement.dashboard_movement_worker
");
$movementType = R::getAll("SELECT * FROM dashboard_movement_types");

$option = "";
$optionMovementType = "";
$financeOut = "";

foreach ($users as $key => $value) {
    $option .= "<option value=\"{$value['dashboard_users_id']}\">{$value['dashboard_users_name']} | {$value['dashboard_users_permissions']}</option>";
}

foreach ($movementType as $key => $value) {
    $optionMovementType .= "<option value=\"{$value['dashboard_movement_types_id']}\">{$value['dashboard_movement_types_name']}</option>";
}

foreach ($movement as $key => $value) {
    $financeOut .= <<<EOF
        <tr>
            <td>{$value["dashboard_movement_id"]}</td>
            <td>{$value["dashboard_movement_money"]}</td>
            <td>{$value["dashboard_movement_types_name"]}</td>
            <td>{$value["dashboard_users_name"]}</td>
            <td>{$value["dashboard_movement_date"]}</td>
        </tr>
EOF;
}

$output = <<<EOF
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Финансы</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newExpense"
                    aria-haspopup="true" aria-expanded="true">
                Добавить расход&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table id="example" class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Чек</th>
                    <th>Назначение</th>
                    <th>Кому адресован</th>
                    <th>Дата создания</th>
                </tr>
                </thead>
                <tbody>
                    {$financeOut}
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
                <table class="table">
                    <tbody>
                    <tr>
                        <td>Назначение</td>
                        <td>
                            <select name="" class="form-control" id="movementType">
                                {$optionMovementType}
                            </select>
                         </td>
                    </tr>
                    <tr>
                        <td>Чек</td>
                        <td><input id="movementMoney" class="form-control" type="text" name=""/></td>
                    </tr>
                    <tr>
                        <td>Кому адресован</td>
                        <td>
                            <select name="" class="form-control" id="movementWorker">
                                {$option}
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="newMovement">Создать новый расход</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
EOF;
print($output);
?>

<?php require_once(APP_DIR_INC . "footer.php"); ?>