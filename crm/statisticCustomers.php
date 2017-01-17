<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Отчеты по заказчикам</h2>
            <?php
            $orders = R::getAll("SELECT dashboard_customers.customerId, dashboard_companies.companyName, dashboard_customers.lastName, dashboard_customers.firstName, diary_orders.type, diary_worker_task.category_diary_worker_task AS categoryDiaryWorkerTask, diary_orders.count_hp FROM diary_orders

INNER JOIN dashboard_customers ON dashboard_customers.customerId = diary_orders.customerId
INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId
INNER JOIN dashboard_workers ON dashboard_workers.id = diary_orders.workerId
INNER JOIN diary_worker_task ON diary_worker_task.id_diary_worker_task = diary_orders.type

WHERE diary_orders.status = 4

ORDER BY dashboard_customers.customerId ASC");

            function ifItIsPromoteOrSticker($categoryDiaryWorkerTask)
            {
                if ($categoryDiaryWorkerTask == 'distribution' || $categoryDiaryWorkerTask == 'promo')
                {
                    return '2';
                }
                if ($categoryDiaryWorkerTask == 'posting' || $categoryDiaryWorkerTask == 'delivery')
                {
                    return '3';
                }
            }

            $ordersArray = [];
            $outDataHTML = "";
            foreach ($orders as $value)
            {
                if (array_key_exists($value['customerId'], $ordersArray)) //если в массиве есть такой customerId
                {
                    $ordersArray[$value['customerId']][ifItIsPromoteOrSticker($value['categoryDiaryWorkerTask'])] += $value['count_hp'];
                }
                else
                {
                    $ordersArray[$value['customerId']] = "";
                    $ordersArray[$value['customerId']][0] = $value['companyName']; // устанавливаем название компании заказчика
                    $ordersArray[$value['customerId']][1] = $value['lastName'] . " " . $value['firstName']; // устанавливаем фамилию и имя заказчика
                    $ordersArray[$value['customerId']][2] = 0;
                    $ordersArray[$value['customerId']][3] = 0;
                    $ordersArray[$value['customerId']][ifItIsPromoteOrSticker($value['categoryDiaryWorkerTask'])] += $value['count_hp'];
                }
            }

            foreach ($ordersArray as $arrayVal)
            {
                $outDataHTML .= <<<EOF
                        <tr>
                        <td>{$arrayVal[0]}</td>
                        <td>{$arrayVal[1]}</td>
                        <td>{$arrayVal[2]}</td>
                        <td>{$arrayVal[3]}</td>
                        </tr>

EOF;
            }


            $out = <<<EOF
   <div class="dt-bootstrap">
   <table id="example" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Компания</th>
            <th>Фамилия и имя заказчика</th>
            <th>Количество часов</th>
            <th>Количество материала</th>
          </tr>
        </thead>
        <tbody>
{$outDataHTML}
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
