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

$thisUser = intval($_GET["supervisorId"]);


$data = R::getAll("SELECT
diary_orders.id, diary_orders.status, diary_orders.place, dashboard_customers.lastName, diary_orders.count_hp, diary_orders.type, dashboard_workers.dashboard_workers_surname,  diary_orders.date, diary_orders.times, diary_worker_task.title_diary_worker_task, diary_worker_task.money_worker_diary_worker_task, diary_worker_task.money_supervisor_diary_worker_task, diary_worker_task.category_diary_worker_task, dashboard_users.dashboard_users_name,dashboard_users.salaryPerDay, dashboard_workers.dashboard_workers_name, dashboard_workers.dashboard_workers_patronymic, dashboard_workers.dashboard_workers_phone, diary_orders.customerId, diary_orders.workerId, dashboard_users.dashboard_users_id, dashboard_users.dashboard_users_name,diary_orders.comment
FROM diary_orders
INNER JOIN dashboard_customers ON diary_orders.customerId = dashboard_customers.customerId
INNER JOIN dashboard_workers ON diary_orders.workerId = dashboard_workers.id
INNER JOIN diary_worker_task ON diary_orders.type = diary_worker_task.id_diary_worker_task
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = diary_orders.byWhomAdding
WHERE (DATE(date) BETWEEN ? AND ?) AND diary_orders.status = 4 AND diary_orders.pay_super = 0 AND diary_orders.byWhomAdding = ?
ORDER BY diary_orders.date DESC", [$dateFrom, $dateTo, $thisUser]);

$userData = R::getRow("SELECT * FROM dashboard_users WHERE dashboard_users_id = ?", [$thisUser]);

$days = R::getAll("SELECT * FROM basic_day_registration WHERE basic_day_registration_who = ? AND basic_day_registration_status = 0", [$thisUser]);

$out = "";
$outDays = "";
$moneyPay = 0;
$moneyPayDays = 0;
$daysMoney = $userData["salaryPerDay"];
$nameUser = $userData["dashboard_users_name"];
$everyDayMoney = $daysMoney;

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
  $moneyPay += $moneySupervisor;

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
    <td><span class="money">{$moneySupervisor}</span></td>
  </tr>
EOF;
}

foreach ($days as $key => $value) {
  $outDays .= <<<EOF
  <tr>
    <td width="30"><input type="checkbox" class="form-control checkOrderPay" data-id="{$value["basic_day_registration_id"]}"></td>
    <td>{$value["basic_day_registration_date_start"]}</td>
    <td>{$value["basic_day_registration_date_stop"]}</td>
  </tr>
EOF;
  $moneyPayDays++;
}

$daysMoney *= $moneyPayDays;

$output = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-10">
            <h2>Зарплата супервайзера</h2>
            <a href="/crm/salaryLast.php">
            <button class="btn btn-default mr-10" type="button">
                Вернуться ко всем сотрудникам&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-left" aria-hidden="true"></i>
            </button><br /><br />
            </a>

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
                  </tr>
                </thead>
                <tbody>
                  {$out}
                  <tr><td colspan="6" align="right">Всего задолжность</td><td>{$moneyPay} грн</td></tr>
                </tbody>
            </table>
            <button id="paySuperVisor" class="btn btn-default" data-toggle="modal" data-target="#confirmPaySuper" data-dismiss="modal">Оплатить выбранные заказы</button>
            <br />
            <br />
            <table class="table table-striped table-bordered dataTable no-footer">
                <thead>
                  <tr>
                    <th></th>
                    <th>Начало рабочего дня</th>
                    <th>Конец рабочего дня</th>
                  </tr>
                </thead>
                <tbody>
                  {$outDays}
                  <tr><td colspan="2" align="right">Всего задолжность</td><td>{$daysMoney} грн</td></tr>
                </tbody>
            </table>
            <button id="paySuperVisorDay" class="btn btn-default" data-toggle="modal" data-target="#confirmPaySuperDays" data-dismiss="modal">Оплатить выбранные дни</button>
        </div>
        <div class="col-md-2">
        <h2>{$nameUser}</h2>
          <table class="table table-striped table-bordered dataTable no-footer">
              <tbody>
                <tr><td>Ставка</td><td><span id="moneyEveryDay">{$everyDayMoney}</span> грн</td></tr>
              </tbody>
          </table>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmPaySuper" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите рассчитать супервайзера?
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body ta-c">
        Итого к оплате: <span id="finalMoney"></span> грн
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-info confirmPayingSuper" data-dismiss="modal">Оплатить</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="confirmPaySuperDays" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите рассчитать супервайзера?
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body ta-c">
        Итого к оплате: <span id="finalMoneyDay"></span> грн
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-info confirmPayingSuperDay" data-dismiss="modal">Оплатить</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;
print($output);
?>

<?php require_once(APP_DIR_INC . "footer.php"); ?>
