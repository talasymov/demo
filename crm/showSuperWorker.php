<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$orders = R::getAll("SELECT * FROM diary_orders
  INNER JOIN dashboard_workers ON diary_orders.workerId = dashboard_workers.id
  WHERE diary_orders.workerId = ?", [$_GET["id_worker"]]);
$outData = "";
$name = "";

            foreach($orders as $value)
            {
                $outData .= <<<EOF

        <tr>
          <td>{$value['place']}</td>
          <td>{$value['date']} {$value['times']}</td>
          <td>{$value['type']}</td>
          <td>{$value['customerId']}</td>
          <td>{$value['pay']}</td>
          <td>{$value['status']}</td>
        </tr>
EOF;
                $name = $value["dashboard_workers_surname"] . " " . $value["dashboard_workers_name"] . " " . $value["dashboard_workers_patronymic"];
            }
            $out = <<<EOF
   <div class="dt-bootstrap">
   <table id="example" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Место</th>
            <th>Дата заказа</th>
            <th>Тип работы</th>
            <th>Заказчик</th>
            <th>Оплата</th>
            <th>Статус заказа</th>
          </tr>
        </thead>
        <tbody>
{$outData}
        </tbody>
        </table>
</div>
EOF;
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Работник - <?php echo $name; ?>, отчет &nbsp;<a href="/crm/statisticWorkers.php">
              <button class="btn btn-default dib" type="button">
                <span class="status">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </span>
              </button>
            </a>
          </h2>
            <?php
            print($out);
            ?>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
