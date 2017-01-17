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

$outModalsEdit = <<<EOF
<div class="modal fade" id="modalAddOrderDesigner" role="dialog">
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    Добавление заказа <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
    <table class="table">
      <tbody>
          <tr><td>Название: </td><td><input id="newOrderDesigner_name" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr>
              <td>Компания: </td>
              <td>
                  <select id="newOrderDesigner_selectCompany" class="form-control" name="">
                      <?php echo $companyArray; ?>
                  </select>
              </td>
              <td>

              </td>
          </tr>
          <tr>
              <td>Клиент: </td>
              <td>
                  <select id="newOrderDesigner_selectClients" class="form-control" name="">
                      <?php echo $customerArray; ?>
                  </select>
              </td>
              <td>

              </td>
          </tr>
          <tr><td>Тип работы: </td><td><input id="newOrderDesigner_type" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Стоимость: </td><td><input id="newOrderDesigner_price" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Описание: </td><td><textarea id="newOrderDesigner_comment" type="text" class="form-control" rows="5" name="name" value="" placeholder=""></textarea></td><td></td></tr>
          <tr><td>Срочность: </td><td>
            <select class="form-control" id="newOrderDesigner_quick">
              <option value="none">Выберите</option>
              <option value="1">Срочно</option>
              <option value="0">Не срочно</option>
            </select>
          </td><td></td></tr>
      </tbody>
    </table>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmAdd" data-dismiss="modal">Создать</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
  </div>
</div>
</div>
</div>
<div class="modal fade" id="myModalConfirmAdd" role="dialog">
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    Подтвердите ваше действие <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmAdd" data-dismiss="modal">Подтвердить</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
  </div>
</div>
</div>
</div>
EOF;
$outOrders = R::getAll("SELECT * FROM dashboard_orders_designers
INNER JOIN dashboard_customers ON dashboard_orders_designers.dashboard_orders_designers_customer = dashboard_customers.customerId
INNER JOIN dashboard_users ON dashboard_orders_designers.dashboard_orders_designers_whoadd = dashboard_users.dashboard_users_id
WHERE dashboard_orders_designers_whowork = ?", [$_COOKIE['userId']]);
$outOrdersText = "";
foreach($outOrders as $key => $value)
{
  $outOrdersText .=  <<<EOF
  <tr>
    <td>{$value["dashboard_orders_designers_name"]}</td>
    <td>{$value["dashboard_orders_designers_type"]}</td>
    <td>{$value["dashboard_orders_designers_price"]}</td>
    <td>{$value["dashboard_orders_designers_description"]}</td>
    <td>{$value["firstName"]} {$value["lastName"]}</td>
    <td>{$value["dashboard_orders_designers_quick"]}</td>
    <td>{$value["dashboard_orders_designers_date"]}</td>
    <td>{$value["dashboard_users_name"]}</td>
    <td>
      <input type="hidden" value="{$value["dashboard_users_id"]}" class="idOrder">
      <div class="dropdown status-designer-select">
      <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <span class="status stat-{$value["dashboard_orders_designers_status"]}">
          <i class="fa fa-star" aria-hidden="true"></i>
        </span>
      </button>
      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
        <li data-id="0"><a href="#"><i class="fa fa-star stat-0" aria-hidden="true"></i> В обработке</a></li>
        <li data-id="1"><a href="#"><i class="fa fa-star stat-1" aria-hidden="true"></i> Макет проверен на ошибки</a></li>
        <li data-id="2"><a href="#"><i class="fa fa-star stat-2" aria-hidden="true"></i> Заказчик подтвердил</a></li>
        <li data-id="3"><a href="#"><i class="fa fa-star stat-3" aria-hidden="true"></i> Выслан в печать</a></li>
        <li data-id="4"><a href="#"><i class="fa fa-star stat-4" aria-hidden="true"></i> Работа выполнена</a></li>
        <li role="separator" class="divider"></li>
        <li data-id="5"><a href="#"><i class="fa fa-star stat-5" aria-hidden="true"></i> Заказ отменен</a></li>
        <li data-id="6"><a href="#"><i class="fa fa-star stat-6" aria-hidden="true"></i> Заказ провален</a></li>
      </ul>
    </div>
    </td>
  </tr>
EOF;
}

$getAllWorks = R::getAll('
SELECT * FROM basic_day_registration
WHERE basic_day_registration_who = ? AND basic_day_registration_status = 0', [$_COOKIE['userId']]);
$aboutUser = R::getRow("SELECT * FROM dashboard_users WHERE dashboard_users_id = ?", [$_COOKIE['userId']]);
$countOfNotPaymentDays = 0;

foreach($getAllWorks as $key => $value)
{
  $countOfNotPaymentDays++;
}
$moneyForDay = $aboutUser["salaryPerDay"];
$summary = $countOfNotPaymentDays * $moneyForDay;

$outOrders = R::getAll("SELECT * FROM dashboard_orders_designers
INNER JOIN dashboard_customers ON dashboard_orders_designers.dashboard_orders_designers_customer = dashboard_customers.customerId
INNER JOIN dashboard_users ON dashboard_orders_designers.dashboard_orders_designers_whoadd = dashboard_users.dashboard_users_id
WHERE dashboard_orders_designers_whowork = ? AND dashboard_orders_designers_status = 4", [$_COOKIE['userId']]);
$outOrdersText = "";
$moneyAllOrders = 0;
foreach($outOrders as $key => $value)
{
  $moneyAllOrders += $value["dashboard_orders_designers_price"];

  $outOrdersText .=  <<<EOF
  <tr>
    <td>{$value["dashboard_orders_designers_name"]}</td>
    <td>{$value["dashboard_orders_designers_type"]}</td>
    <td>{$value["dashboard_orders_designers_price"]}</td>
    <td>{$value["dashboard_orders_designers_description"]}</td>
    <td>{$value["firstName"]} {$value["lastName"]}</td>
    <td>{$value["dashboard_orders_designers_quick"]}</td>
    <td>{$value["dashboard_orders_designers_date"]}</td>
    <td>{$value["dashboard_users_name"]}</td>
  </tr>
EOF;
}
$moneyAllOrders = $moneyAllOrders / 2 + $summary;
$out = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Зарплата дизайнера</h2>
            <h4>Ставка</h4>
            <table class="table table-striped table-bordered dataTable no-footer">
                <thead>
                  <tr>
                    <th>Кол-во дней</th>
                    <th>Ставка</th>
                    <th>Итого</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{$countOfNotPaymentDays}</td>
                    <td>{$moneyForDay} грн</td>
                    <td>{$summary} грн</td>
                  </tr>
                </tbody>
            </table>
            <button type="button" name="button" id="paymentDesignerDays">Оплатить</button>
            <h4>Неоплаченные заказы</h4>
            <table class="table table-striped table-bordered dataTable no-footer">
                <thead>
                  <tr>
                    <th>Название</th>
                    <th>Тип работы</th>
                    <th>Стоимость</th>
                    <th>Описание</th>
                    <th>Заказчик</th>
                    <th>Срочность</th>
                    <th>Дата добавления</th>
                    <th>Кто добавил</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    {$outOrdersText}
                  </tr>
                </tbody>
            </table>
            <h3 style="text-align: right"><strong>Итого:</strong> {$moneyAllOrders} грн</h3>
            <button type="button" name="button" id="paymentDesignerOrders">Оплатить</button>
            {$outModalsEdit}
        </div>
    </div>
</div>
EOF;
print($out);
?>
<?php require_once(APP_DIR_INC . "footer.php"); ?>