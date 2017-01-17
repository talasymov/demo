<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Отчеты по работникам</h2>
            <?php
            $orders = R::getAll("SELECT id, dashboard_workers_name, dashboard_workers_surname, dashboard_workers_patronymic, dashboard_workers_promoterLevel, dashboard_workers_stickerLevel, penaltyCount FROM dashboard_workers");
$outData = "";
            foreach($orders as $value)
            {
                $money = "<span class='good-line'>0</span>";
                $countOrder = R::getAll("SELECT * FROM diary_orders WHERE pay = 0 AND status = 4 AND workerId = ?", [$value["id"]]);

                $count = count($countOrder);

                if($count > 0)
                {
                  $money = "<span class='bad-line'>" . $count . "</span>";
                }

                $outData .= <<<EOF

        <tr>
          <td><a href="/crm/showSuperWorker.php?id_worker={$value["id"]}" target="_blank">{$value['dashboard_workers_surname']} {$value['dashboard_workers_name']} {$value['dashboard_workers_patronymic']}</a></td>
          <td>{$value['dashboard_workers_stickerLevel']} шт.</td>
          <td>{$value['dashboard_workers_promoterLevel']} ч.</td>
          <td>{$value['penaltyCount']}</td>
          <td>{$money}</td>
        </tr>
EOF;
            }
            $out = <<<EOF
   <div class="dt-bootstrap">
   <table id="example" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>ФИО работника</th>
            <th>Количество розданного материала (шт.)</th>
            <th>Количество проработанных часов (ч.)</th>
            <th>Количество штрафов</th>
            <th>Расчет</th>
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
