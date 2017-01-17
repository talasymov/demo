<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Отчеты по супервайзерам</h2>
            <?php
            $orders = R::getAll("SELECT dashboard_users.dashboard_users_name AS supervisorLastName, COUNT(diary_orders.id) AS countCompletedOrders,  ROUND(SUM(diary_orders.count_hp*(diary_worker_task.money_all_diary_worker_task))) AS moneyGetFromCustomer, ROUND(SUM(diary_orders.count_hp*diary_worker_task.money_company_diary_worker_task)) AS income, ROUND(SUM(diary_orders.count_hp*(diary_worker_task.money_worker_diary_worker_task+diary_worker_task.money_supervisor_diary_worker_task+diary_worker_task.money_any_diary_worker_task))) AS consumption
FROM diary_orders
INNER JOIN dashboard_users ON diary_orders.byWhomAdding = dashboard_users.dashboard_users_id
INNER JOIN diary_worker_task ON diary_orders.type = diary_worker_task.id_diary_worker_task

GROUP BY dashboard_users.dashboard_users_id");
$outData = "";
            foreach($orders as $value)
            {
                $outData .= <<<EOF
        
        <tr>
          <td>{$value['supervisorLastName']}</td>
          <td>{$value['countCompletedOrders']}</td>
          <td>{$value['moneyGetFromCustomer']}</td>
          <td>{$value['income']}</td>
          <td>{$value['consumption']}</td>
        </tr>
EOF;
            }
            $out = <<<EOF
   <div class="dt-bootstrap">     
   <table id="example" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>ФИО супервайзера</th>
            <th>Количество выполненных заказов</th>
            <th>Денег получено от заказчика</th>
            <th>Доход</th>
            <th>Расход</th>
          </tr>
        </thead>
        <tbody>
{$outData}
        </tbody>
        </table>
</div>
EOF;
            print($out);
            ?>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>