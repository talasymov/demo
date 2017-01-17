<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$salaryPerNotPaymentDays = R::getAll("SELECT dashboard_users.dashboard_users_id AS userId, dashboard_users.dashboard_users_name AS userName, COUNT(basic_day_registration.basic_day_registration_id) AS countOfNotPaymentDays, dashboard_users.salaryPerDay AS salaryPerDay, SUM(dashboard_users.salaryPerDay) AS salary FROM basic_day_registration
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = basic_day_registration.basic_day_registration_who

WHERE basic_day_registration.basic_day_registration_status = 0

GROUP BY dashboard_users.dashboard_users_name, dashboard_users.dashboard_users_id");

$userId = "";
$userName = "";

//out HTML variables
$outsalaryPerNotPaymentDays = "";
$outNonPaymentOrders = "";
$outNonPaymentOrdersTable = "";
$outModalsConfirmEdit = "";

/* echo "<pre>\n";
  print_r($salaryPerNotPaymentDays); */

foreach ($salaryPerNotPaymentDays as $val)
{
    $outNonPaymentOrdersTable = "";
    $outNonPaymentOrders = "";
    
    $userId = $val['userId'];
    $userName = $val['userName'];
    $countOfNotPaymentDays = $val['countOfNotPaymentDays'];
    $salaryPerDay = $val['salaryPerDay'];
    $salary = $val['salary'];

    $outsalaryPerNotPaymentDays .= <<<EOF
<h3 style="text-align: center">{$userName}</h3>
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
<td>{$countOfNotPaymentDays}</td>
<td>{$salaryPerDay}</td>
<td>{$salary}</td>
                </tbody>
            </table>       
EOF;

    $nonPaymentOrders = R::getAll("SELECT diary_orders.place AS place, dashboard_companies.companyName AS companyName, diary_orders.date AS `date`, diary_worker_task.category_diary_worker_task AS type, diary_orders.count_hp AS `count`, diary_orders.count_hp*diary_worker_task.money_supervisor_diary_worker_task AS inTotal FROM diary_orders
INNER JOIN dashboard_customers ON dashboard_customers.customerId = diary_orders.customerId
INNER JOIN dashboard_companies ON dashboard_customers.companyId = dashboard_companies.id
INNER JOIN diary_worker_task ON diary_orders.type = diary_worker_task.id_diary_worker_task

WHERE diary_orders.byWhomAdding = {$userId} AND diary_orders.pay = 0");

$salaryByNonPaymentOrdersInTotal = 0;
    foreach ($nonPaymentOrders as $npoval)
    {
        if ($npoval['type'] == "delivery" || $npoval['type'] == "posting")
        {
        $outNonPaymentOrders .= <<<EOF
                <tr>
            <td>{$npoval['place']}</td>
            <td>{$npoval['companyName']}</td>
            <td>{$npoval['date']}</td>
            <td>{$npoval['type']}</td>
            <td>{$npoval['count']} шт.</td>
            <td>{$npoval['inTotal']} грн.</td>
            </tr>
EOF;
            $salaryByNonPaymentOrdersInTotal += (int)$npoval['inTotal'];
        }
        if ($npoval['type'] == "distribution" || $npoval['type'] == "promo")
        {
        $outNonPaymentOrders .= <<<EOF
                <tr>
            <td>{$npoval['place']}</td>
            <td>{$npoval['companyName']}</td>
            <td>{$npoval['date']}</td>
            <td>{$npoval['type']}</td>
            <td>{$npoval['count']} ч.</td>
            <td>{$npoval['inTotal']} грн.</td>
            </tr>
EOF;
            $salaryByNonPaymentOrdersInTotal += (int)$npoval['inTotal'];
        }
    }

    $salary += $salaryByNonPaymentOrdersInTotal;
    $outNonPaymentOrdersTable .= <<<EOF
<h4>Неоплаченные заказы</h4>
            <table class="table table-striped table-bordered dataTable no-footer">
                <thead>
                    <tr>
                        <th>Место</th>
                        <th>Заказчик</th>
                        <th>Время</th>
                        <th>Тип</th>
                        <th>Количество</th>
                        <th>Итого</th>
                    </tr>
                </thead>
                <tbody>
                {$outNonPaymentOrders}
                </tbody>
            </table> 
                <b><h1 style="text-align: right">Итого: {$salary}</h1></b>
                <button id="btnTakeSalary{$userId}" class="btn btn-info" type="submit" data-toggle="modal" data-target="#myModalConfirmPay{$userId}" data-dismiss="modal" style="width: 100%;">
                    Рассчитать
                </button>
EOF;
                
                
                    $outModalsConfirmEdit .= <<<EOF
<div class="modal fade" id="myModalConfirmPay{$userId}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
          <tr>
            <th colspan="2"><h4>Действительно хотите рассчитать сотрудника?</h4></th><th width="95"></th>
          </tr>
          </thead>
          <tbody>
              <tr>
                  <td>
<button type="button" class="btn btn-info confirmPaying" data-order="{$userId}" data-dismiss="modal">Да, рассчитать сотрудника</button>
<button type="button" class="btn btn-info" data-dismiss="modal">Закрыть</button>
                  </td>
              </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
EOF;

                $outsalaryPerNotPaymentDays .= $outNonPaymentOrdersTable;



/*
    echo "<pre>\n";
    print_r($nonPaymentOrders);
    echo "</pre>";
 * 
 */
                
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Зарплата</h2>
            <hr>
            <?php
            print($outsalaryPerNotPaymentDays);
            print($outModalsConfirmEdit);
            ?>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>