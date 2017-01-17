<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
$outTable = "";
$outProductsTop = "";
if (isset($_POST['dtpLeadsFrom']) && isset($_POST['dtpLeadsTo']))
{
    if ($_POST['dtpLeadsFrom'] == "0" || $_POST['dtpLeadsTo'] == "0" || $_POST['dtpLeadsFrom'] == "" || $_POST['dtpLeadsTo'] == "")
    {
        $dtpLeadsFrom = date("Y-m-d");
        $dtpLeadsTo = date("Y-m-d");
    }
    else
    {
        $explode = explode(" ", $_POST['dtpLeadsFrom']);
        $explodeMinus = explode(".", $explode[0]);
        $dtpLeadsFrom = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];

        $explode = explode(" ", $_POST['dtpLeadsTo']);
        $explodeMinus = explode(".", $explode[0]);
        $dtpLeadsTo = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];

        /*
          print_r("SELECT
          (SELECT COUNT(diary_orders.id) FROM diary_orders INNER JOIN diary_worker_task ON diary_worker_task.id_diary_worker_task = diary_orders.type
          WHERE diary_orders.date >= '{$dtpLeadsFrom}' AND diary_orders.date <= '{$dtpLeadsTo}') AS countOfOrdersSupervisor,
          (SELECT SUM(diary_worker_task.money_all_diary_worker_task * diary_orders.count_hp) FROM diary_orders INNER JOIN diary_worker_task ON diary_worker_task.id_diary_worker_task = diary_orders.type
          WHERE diary_orders.date >= '{$dtpLeadsFrom}' AND diary_orders.date <= '{$dtpLeadsTo}') AS totalSumOfOrderSupervisor,
          (SELECT COUNT(dashboard_productsOrderGroup.id) FROM dashboard_productsOrderGroup
          WHERE dashboard_productsOrderGroup.dateOfOrder >= '{$dtpLeadsFrom}'  AND dashboard_productsOrderGroup.dateOfOrder <= '{$dtpLeadsTo}') AS countOfOrdersByProducts,
          (SELECT SUM(dashboard_productsOrderGroup.totalSumOfOrder) FROM dashboard_productsOrderGroup
          WHERE dashboard_productsOrderGroup.dateOfOrder >= '{$dtpLeadsFrom}' AND dashboard_productsOrderGroup.dateOfOrder <= '{$dtpLeadsTo}') AS totalSumOfOrdersByProducts");
         */

        $companyStatistic = R::getAll("SELECT
(SELECT COUNT(diary_orders.id) FROM diary_orders INNER JOIN diary_worker_task ON diary_worker_task.id_diary_worker_task = diary_orders.type
WHERE diary_orders.date >= '{$dtpLeadsFrom}' AND diary_orders.date <= '{$dtpLeadsTo}') AS countOfOrdersSupervisor,
(SELECT SUM(diary_worker_task.money_all_diary_worker_task * diary_orders.count_hp) FROM diary_orders INNER JOIN diary_worker_task ON diary_worker_task.id_diary_worker_task = diary_orders.type
WHERE diary_orders.date >= '{$dtpLeadsFrom}' AND diary_orders.date <= '{$dtpLeadsTo}') AS totalSumOfOrderSupervisor,
(SELECT COUNT(dashboard_productsOrderGroup.id) FROM dashboard_productsOrderGroup
WHERE dashboard_productsOrderGroup.dateOfOrder >= '{$dtpLeadsFrom}'  AND dashboard_productsOrderGroup.dateOfOrder <= '{$dtpLeadsTo}') AS countOfOrdersByProducts,
(SELECT SUM(dashboard_productsOrderGroup.totalSumOfOrder) FROM dashboard_productsOrderGroup
WHERE dashboard_productsOrderGroup.dateOfOrder >= '{$dtpLeadsFrom}' AND dashboard_productsOrderGroup.dateOfOrder <= '{$dtpLeadsTo}') AS totalSumOfOrdersByProducts");



        foreach ($companyStatistic as $val)
        {
            if ($val['countOfOrdersSupervisor'] == NULL)
            {
                $val['countOfOrdersSupervisor'] = 0;
            }
            if ($val['totalSumOfOrderSupervisor'] == NULL)
            {
                $val['totalSumOfOrderSupervisor'] = 0;
            }
            if ($val['countOfOrdersByProducts'] == NULL)
            {
                $val['countOfOrdersByProducts'] = 0;
            }
            if ($val['totalSumOfOrdersByProducts'] == NULL)
            {
                $val['totalSumOfOrdersByProducts'] = 0;
            }
            $totalSum = $val['totalSumOfOrderSupervisor'] + $val['totalSumOfOrdersByProducts'];
            $outTable .= <<<EOF
            <td>{$val['countOfOrdersSupervisor']}</td>
            <td>{$val['totalSumOfOrderSupervisor']}</td>
            <td>{$val['countOfOrdersByProducts']}</td>
            <td>{$val['totalSumOfOrdersByProducts']}</td>
            <td>{$totalSum}</td>
EOF;
        }
    }
}
else
{
    $dtpLeadsFrom = date("Y-m-d");
    $dtpLeadsTo = date("Y-m-d");
}

?>
<div class="container">
    <div class="row">
        <div class="col-md-12" id="statisticDivForGoogleCharts">
            <h2>Оборот компании</h2>
            <form action="/crm/statistic.php" method="POST">
                <label for="input">С какого числа: </label>
                <div class="form-group">
                    <div class="input-group date" id="dtpLeadsFrom1">
                        <input type="text" name="dtpLeadsFrom" id="inputLeadsFrom" class="form-control" style="background: lightgray;">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    <script type="text/javascript">
                        $(function ()
                        {
                            $('#dtpLeadsFrom1').datetimepicker({
                                locale: 'ru'
                            });
                        });
                    </script>
                </div>
                <label for="input">По какое число: </label>
                <div class="form-group">
                    <div class="input-group date" id="dtpLeadsTo1">
                        <input type="text" name="dtpLeadsTo" id="inputLeadsTo" class="form-control" style="background: lightgray;">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    <script type="text/javascript">
                        $(function ()
                        {
                            $('#dtpLeadsTo1').datetimepicker({
                                locale: 'ru'
                            });
                        });
                    </script>
                </div>
                <button id="btnLeadsSort" class="btn btn-info" type="submit" style="width: 100%;">
                    Отсортировать
                </button>
            </form>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Количество заказов на продукты</th>
                        <th>Оборот на заказанных продуктах</th>
                        <th>Кол-во заказов супервайзера</th>
                        <th>Оборот на заказах супервайзера</th>
                        <th>Итоговый оборот компании</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        print($outTable);
                        ?>
                    </tr>
                </tbody>
            </table>
            <hr>
            <div id="bestDays" style="width: 100%; height: 500px;"></div>
            <div id="bestMonths" style="width: 100%; height: 500px;"></div>
            
            <div id="topProductsUpStatistic" style="width: 100%; height: 500px;"></div>
            <div id="topProductsDownStatistic" style="width: 100%; height: 500px;"></div>
            <div id="topWorkersUpStatistic" style="width: 100%; height: 500px;"></div>
            <div id="topWorkersDownStatistic" style="width: 100%; height: 500px;"></div>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>