<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$dateFrom = date("1970-m-d");
$dateTo = date("Y-m-d");
$getWorkerId  = "";

if(isset($_GET["idWorker"]))
{
  $getWorkerId = $_GET["idWorker"];
}

if(isset($_GET["date_from"]) && $_GET["date_from"] != null)
{
  $dateFrom = $_GET["date_from"];
}
if(isset($_GET["date_to"]) && $_GET["date_to"] != null)
{
  $dateTo = $_GET["date_to"];
}


$data = R::getAll("SELECT
diary_orders.id, diary_orders.status, diary_orders.place, dashboard_customers.lastName, diary_orders.count_hp, diary_orders.type, dashboard_workers.dashboard_workers_surname,  diary_orders.date, diary_orders.times, diary_worker_task.title_diary_worker_task, diary_worker_task.money_worker_diary_worker_task, diary_worker_task.money_supervisor_diary_worker_task, diary_worker_task.category_diary_worker_task, dashboard_users.dashboard_users_name, dashboard_workers.dashboard_workers_name, dashboard_workers.dashboard_workers_patronymic, dashboard_workers.dashboard_workers_phone, diary_orders.customerId, diary_orders.workerId, dashboard_users.dashboard_users_id, diary_orders.comment
FROM diary_orders
INNER JOIN dashboard_customers ON diary_orders.customerId = dashboard_customers.customerId
INNER JOIN dashboard_workers ON diary_orders.workerId = dashboard_workers.id
INNER JOIN diary_worker_task ON diary_orders.type = diary_worker_task.id_diary_worker_task
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = diary_orders.byWhomAdding
WHERE (DATE(date) BETWEEN ? AND ?) AND diary_orders.status = 4 AND diary_orders.pay = 0
ORDER BY diary_orders.date DESC", [$dateFrom, $dateTo]);

$out = "";
$moneyPay = 0;

foreach ($data as $key => $value) {

  $idOrder = $value['id'];

  $place = $value['place'];
  $client = $value['lastName'];
  $count = $value['count_hp'];
  $types = $value['type'];
  $category = $value['category_diary_worker_task'];
  $byWhomAdding = $value['dashboard_users_name'];
  $byWhomAddingId = $value['dashboard_users_id'];
  $worker = $value['dashboard_workers_name'] . " " . $value['dashboard_workers_patronymic'];
  $type = $value['category_diary_worker_task'];
  $time = $value['date'] . " " . $value['times'];

  $moneyWorker = $value["count_hp"] * $value["money_worker_diary_worker_task"];
  $moneySupervisor = $value["count_hp"] * $value["money_supervisor_diary_worker_task"];
  // $moneyAll = $moneyWorker + $moneySupervisor;
  $moneyPay += $moneyWorker;

  if ($type == "posting")
  {
      $type = "Расклейка";
  }
  else if ($type == "delivery")
  {
      $type = "Разноска";
  }
  else if ($type == "distribution")
  {
      $type = "Раздача";
  }
  else if ($type == "promo")
  {
      $type = "Промо-акция";
  }

  $out .= <<<EOF
  <tr>
    <td width="30"><input type="checkbox" class="form-control checkOrder" data-id="{$idOrder}"></td>
    <td>{$place}</td>
    <td>{$client}</td>
    <td>{$worker}</td>
    <td>{$time}</td>
    <td>{$type}</td>
    <td><span class="money">{$moneyWorker}</span></td>
    <td>{$byWhomAdding}</td>
  </tr>
EOF;
}
$output = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Зарплата работников</h2>
            <table class="table table-striped table-bordered dataTable no-footer">
                <thead>
                  <tr>
                    <th></th>
                    <th>Место</th>
                    <th>Клиент</th>
                    <th>Работник</th>
                    <th>Дата</th>
                    <th>Тип работы</th>
                    <th>Стоимость</th>
                    <th>Супервайзер</th>
                  </tr>
                </thead>
                <tbody>
                  {$out}
                  <tr><td colspan="7" align="right">Всего задолжность</td><td>{$moneyPay} грн</td></tr>
                </tbody>
            </table>
            <button id="payWorkers" class="btn btn-default" data-toggle="modal" data-target="#confirmPay" data-dismiss="modal">Оплатить выбранные заказы</button>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmPay" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите рассчитать сотрудника?
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body ta-c">
        Итого к оплате: <span id="finalMoney"></span> грн
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-info confirmPaying" data-dismiss="modal">Оплатить</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;
print($output);
?>

<?php require_once(APP_DIR_INC . "footer.php"); ?>
