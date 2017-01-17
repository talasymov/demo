<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$customerArray = "";
$companyArray = "";

function ArrayToLi($data, $colName, $idPar, $checkSelected)
{
    $bean = R::getAll("SELECT * FROM " . $data);
    $out = "<option value=\"0\">Не выбрано</option>";
    foreach ($bean as $key => $value)
    {
        if ($value[$idPar] == $checkSelected)
        {
            $out .= "<option value=\"" . $value[$idPar] . "\" selected>" . $value[$colName] . "</option>";
        }
        else
        {
            $out .= "<option value=\"" . $value[$idPar] . "\">" . $value[$colName] . "</option>";
        }
    }
    return $out;
}

$companyArray = ArrayToLi("dashboard_companies", "companyName", "id", "sucks");
$customerArray = ArrayToLi("dashboard_customers", "lastName", "customerId", "sucks");

$outOrders = R::getAll("SELECT * FROM dashboard_orders_designers
INNER JOIN dashboard_customers ON dashboard_orders_designers.dashboard_orders_designers_customer = dashboard_customers.customerId
INNER JOIN dashboard_users ON dashboard_orders_designers.dashboard_orders_designers_whoadd = dashboard_users.dashboard_users_id");
$outOrdersText = "";
$modals = "";

foreach($outOrders as $key => $value)
{
  $modals .= <<<EOF
  <div class="modal fade" data-id="{$value["dashboard_orders_designers_id"]}" id="modalEditMakets{$value["dashboard_orders_designers_id"]}" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      Настройка доступа к макетам <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
      <table class="table">
        <tbody>
            <tr><td>Лицо: </td><td><input type="text" class="form-control newOrderDesigner_face" name="name" value="" placeholder=""></td><td></td></tr>
            <tr><td>Оброт: </td><td><input type="text" class="form-control newOrderDesigner_turn" name="name" value="" placeholder=""></td><td></td></tr>
            <tr><td>Исходник: </td><td><input type="text" class="form-control newOrderDesigner_base" name="name" value="" placeholder=""></td><td></td></tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default confirmMaket" data-dismiss="modal">Готово</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
  </div>
  </div>
  </div>
EOF;
  $outOrdersText .=  <<<EOF
  <tr>
    <td>{$value["dashboard_orders_designers_name"]}</td>
    <td>{$value["dashboard_orders_designers_type"]}</td>
    <td>{$value["firstName"]} {$value["lastName"]}</td>
    <td>{$value["dashboard_orders_designers_date"]}</td>
    <td>{$value["dashboard_users_name"]}</td>
    <td style="width: 160px;"><img width="60" height="60" src="{$value["dashboard_orders_designers_face"]}"> &nbsp;&nbsp;<img width="60" height="60" src="{$value["dashboard_orders_designers_turn"]}"> &nbsp;<a href="{$value["dashboard_orders_designers_base"]}" target="_blank">
    <button class="btn btn-default dib" type="button">
      <span class="status">
        <i class="fa fa-cloud-download" aria-hidden="true"></i>
      </span>
    </button>
    </a>
    <a href="{$value["dashboard_orders_designers_base_2"]}" target="_blank">
    <button class="btn btn-default dib" type="button">
      <span class="status">
        <i class="fa fa-cloud-download" aria-hidden="true"></i>
      </span>
    </button>
    </a></td>
  </tr>
EOF;
}
$out = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Менеджер макетов</h2>
            <!--<button class="btn btn-default mr-10" type="button" data-toggle="modal" data-target="#whatsNew" aria-haspopup="true" aria-expanded="true">
                Загрузить макет&nbsp;&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>-->
            <table id="example" class="table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Тип работы</th>
                        <th>Заказчик</th>
                        <th>Дата добавления</th>
                        <th>Дизайнер</th>
                        <th>Исходники</th>
                    </tr>
                </thead>
                <tbody>
                  {$outOrdersText}
                </tbody>
              </table>
        </div>
    </div>
</div>
EOF;
print($out . $modals);
?>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
