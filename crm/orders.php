<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$company = R::getAll("SELECT * FROM dashboard_company");
$clients = R::getAll("SELECT * FROM dashboard_clients");
$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");

$goods = R::getAll("SELECT * FROM dashboard_goods");

// $outputCompany = "";
// $outputClients = "";
// $outputFrom = "<option value=\"0\">Выберите источник</option>";
// $outputCompanyLi = "<option value=\"0\">Выберите команию</option>";
//
// foreach ($clientsFrom as $key => $value) {
//   $outputFrom .= "<option value=\"" . $value["dashboard_money_id"] . "\">" . $value["dashboard_money_name"] . "</option>";
// }
//
// foreach ($company as $key => $value)
// {
//   $nameCompany = $value["name"];
//
//   $outputCompany .= <<<EOF
//   <tr>
//     <td>{$nameCompany}</td>
//   </tr>
// EOF;
//   $outputCompanyLi .= "<option value=\"" . $value["id"] . "\">" . $value["name"] . "</option>";
// }
//
// foreach ($clients as $key => $value)
// {
//   $nameClient = $value["name"];
//   $lastNameClient = $value["surname"];
//   $patronymicClient = $value["patronymic"];
//   $phoneClient = $value["phone"];
//   $emailClient = $value["email"];
//
//   $outputClients .= <<<EOF
//   <tr>
//     <td>{$lastNameClient} {$nameClient} {$patronymicClient}</td>
//     <td>{$phoneClient}</td>
//     <td>{$emailClient}</td>
//   </tr>
// EOF;
// }
$tdGoods = "";
// print_r($goods);
foreach ($goods as $key => $value) {
  $tdGoods .= "<tr>
    <td>" . $value["dashboard_goods_id"] . "</td>
    <td>" . $value["dashboard_goods_name"] . "</td>
    <td>" . $value["dashboard_goods_price"] . "</td>
    <td>" . $value["dashboard_goods_unit"] . "</td>
    <td>" . $value["dashboard_goods_provider"] . "</td>
    <td>" . $value["dashboard_goods_category"] . "</td>
  </tr>";
}
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="min-h2">Продукты</h2>
      <button class="btn btn-info dropdown-toggle" type="button" data-toggle="modal" data-target="#newCompany" aria-haspopup="true" aria-expanded="true">
          Выписать счет&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
      </button>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Наименование</th>
            <th>Цена</th>
            <th>Ед. изм.</th>
            <th>Поставщик</th>
            <th>Категория</th>
          </tr>
        </thead>
        <tbody>
          <?php
            print($tdGoods);
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
