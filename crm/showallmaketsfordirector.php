<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$outOrders = "";
$outModals = "";

$workersArray = "";
$customerArray = "";
$companyArray = "";

$dateFrom = date("1970-m-d");
$dateTo = date("Y-m-d");

if(isset($_GET["date_from"]) && $_GET["date_from"] != null)
{
  $dateFrom = $_GET["date_from"];
}
if(isset($_GET["date_to"]) && $_GET["date_to"] != null)
{
  $dateTo = $_GET["date_to"];
}

$outOrders = R::getAll("SELECT * FROM dashboard_orders_designers
INNER JOIN dashboard_customers ON dashboard_orders_designers.dashboard_orders_designers_customer = dashboard_customers.customerId
INNER JOIN dashboard_users ON dashboard_orders_designers.dashboard_orders_designers_whoadd = dashboard_users.dashboard_users_id
WHERE DATE(dashboard_orders_designers_date) BETWEEN ? AND ?", [$dateFrom, $dateTo]);
$outOrdersText = "";

$arrayStatus = array("В обработке", "Макет проверен на ошибки", "Заказчик подтвердил", "Выслан в печать", "Работа выполнена", "Заказ отменен", "Заказ провален");

foreach($outOrders as $key => $value)
{
  $outOrdersText .=  <<<EOF
  <tr>
    <td>{$value["dashboard_orders_designers_name"]}</td>
    <td>{$value["dashboard_orders_designers_type"]}</td>
    <td>{$value["dashboard_orders_designers_price"]}</td>
    <td>{$value["dashboard_orders_designers_description"]}</td>
    <td>{$value["firstName"]} {$value["lastName"]}</td>
    <td>{$value["dashboard_orders_designers_quick"]}</td>
    <td>{$value["dashboard_orders_designers_date"]}</td>
    <td>{$value["dashboard_users_name"]}</td>
    <td style="width: 100px">
      <input type="hidden" value="{$value["dashboard_orders_designers_id"]}" class="idOrder">
      <a href="#" title="{$arrayStatus[$value["dashboard_orders_designers_status"]]}"><div class="dropdown status-designer-select">
      <button class="btn btn-default dropdown-toggle dib" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <span class="status stat-{$value["dashboard_orders_designers_status"]}">
          <i class="fa fa-star" aria-hidden="true"></i>
        </span>
      </button>
      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
        <li data-id="0"><a href="#"><i class="fa fa-star stat-0" aria-hidden="true"></i> В обработке</a></li>
        <li data-id="1"><a href="#"><i class="fa fa-star stat-1" aria-hidden="true"></i> Макет проверен на ошибки</a></li>
        <li data-id="2"><a href="#"><i class="fa fa-star stat-2" aria-hidden="true"></i> Заказчик подтвердил</a></li>
        <li data-id="3"><a href="#"><i class="fa fa-star stat-3" aria-hidden="true"></i> Выслан в печать</a></li>
        <li data-id="4"><a href="#"><i class="fa fa-star stat-4" aria-hidden="true"></i> Работа выполнена</a></li>
        <li role="separator" class="divider"></li>
        <li data-id="5"><a href="#"><i class="fa fa-star stat-5" aria-hidden="true"></i> Заказ отменен</a></li>
        <li data-id="6"><a href="#"><i class="fa fa-star stat-6" aria-hidden="true"></i> Заказ провален</a></li>
      </ul>
    </div></a>
    </td>
  </tr>
EOF;
}
?>
<div class="container">
    <div class="row">
        <h2>Занятость работников</h2>

        <button class="btn btn-default mr-10" type="button" id="showThisDateEmployment">
            Показать&nbsp;&nbsp;&nbsp;<i class="fa fa-eye" aria-hidden="true"></i>
        </button>

        <script type="text/javascript">
          $(function () {
            $('#timeEditOrderFrom').datetimepicker({
              format: 'YYYY-MM-DD',
              locale: 'ru'
            });
          });
        </script>
        <div class='input-group date dateFromTo' id='datetimepickerFrom'>
            <input type='text' class="form-control" id="timeEditOrderFrom" value="<?php echo $dateFrom ?>" />
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>

        <script type="text/javascript">
          $(function () {
            $('#timeEditOrderTo').datetimepicker({
              format: 'YYYY-MM-DD',
              locale: 'ru'
            });
          });
        </script>
        <div class='input-group date dateFromTo ml-10' id='datetimepickerTo'>
            <input type='text' class="form-control" id="timeEditOrderTo" value="<?php echo $dateTo ?>" />
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
        <table id="example" class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Тип работы</th>
                    <th>Стоимость</th>
                    <th>Описание</th>
                    <th>Заказчик</th>
                    <th>Срочность</th>
                    <th>Дата добавления</th>
                    <th>Кто добавил</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
              <?php echo $outOrdersText; ?>
            </tbody>
          </table>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>