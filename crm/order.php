<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$company = R::getAll("SELECT * FROM dashboard_company");
$clients = R::getAll("SELECT * FROM dashboard_clients");
$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");

$order = R::getAll("SELECT * FROM dashboard_order
INNER JOIN dashboard_orders_group ON dashboard_order.dashboard_order_group_id = dashboard_orders_group.dashboard_orders_id
INNER JOIN dashboard_goods ON dashboard_orders_group.dashboard_orders_id_prod = dashboard_goods.dashboard_goods_id
WHERE dashboard_order_group_id = 1");

// var_dump($order);
$tdGoods = "";
$data = "";
$numOrder = "";
$allSum = 0;

foreach ($order as $key => $value) {
  $tdGoods .= "<tr>
    <td>" . $value["dashboard_order_id"] . "</td>
    <td>" . $value["dashboard_order_name"] . "</td>
    <td style=\"text-align: center\">" . $value["dashboard_order_count"] . "</td>
    <td style=\"text-align: right\">" . $value["dashboard_goods_price"] . "</td>
    <td style=\"text-align: right\">" . $value["dashboard_order_allsum"] . "</td>
  </tr>";
  $allSum += $value["dashboard_order_allsum"];
  $data = date("d.m.Y H:i:s", $value["dashboard_orders_date"]);
  $numOrder = $value["dashboard_orders_id"];
}
// <td>" . $value["dashboard_goods_unit"] . "</td>

?>
<div id="score" class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <img src="/images/Logo-B-05.svg" width="300" style="float: left">
      <h2>Счет №<?php print($numOrder); ?> от <?php print($data); ?></h2>
      <!-- <button class="btn btn-info dropdown-toggle" type="button" data-toggle="modal" data-target="#newCompany" aria-haspopup="true" aria-expanded="true">
          Выписать счет&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
      </button> -->
      <b>Плательщик:</b> ООО "Новая почта"<br />
      <b>Ответственный:</b> Леонид Рымский<br />
      <b>Контактный телефон:</b> (048) 785 69 87
      <br />
      <br />
      <table class="width-100">
        <thead>
          <tr>
            <th>№</th>
            <th>Наименование</th>
            <th style="text-align: center" width="120px">Кол-во</th>
            <!-- <th>Ед. изм.</th> -->
            <th style="text-align: right" width="120px">Цена, грн.</th>
            <th style="text-align: right" width="120px">Сумма, грн.</th>
          </tr>
        </thead>
        <tbody>
          <?php
            print($tdGoods);
          ?>
        </tbody>
      </table>
      <table class="width-25">
        <thead>
          <tr>
            <th style="text-align: right"><b>Итого: </b></th>
            <th style="text-align: right"><b><?php echo $allSum; ?></b></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="text-align: right">Предоплата 30%: </td>
            <td style="text-align: right"><?php echo round($allSum/33.3, 2); ?></td>
          </tr>
        </tbody>
      </table>
      Сумма заказа - <?php echo $allSum; ?> &#8372;. Условия оплаты - 30% предоплаты, остальная часть после выполнения заказа.<br />
      Директор: Попович К.В.<br />
      Главный бухгалтер: Кирилюк Г.А.

    </div>
  </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
