<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

//$company = R::getAll("SELECT * FROM dashboard_company");
//$clients = R::getAll("SELECT * FROM dashboard_clients");
//$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");
//update workers status if it is need now
//get worker and him/her status
$productOrders = R::getAll("
SELECT dashboard_productsOrderGroup.id,
dashboard_productsOrderGroup.name AS orderGroupName,
dashboard_productsOrderGroup.dateOfOrder AS dateOfOrder,
CONCAT(dashboard_customers.lastName, ' ', dashboard_customers.firstName, ' ', dashboard_customers.patronymicName) AS customerFIO,
COUNT(dashboard_productsOrders.id) AS productCount,
dashboard_productsOrderGroup.totalSumOfOrder,
dashboard_productsOrderGroup.dashboard_productsOrderGroup_status,
dashboard_users.dashboard_users_name AS byWhomAdding
FROM dashboard_productsOrderGroup

INNER JOIN dashboard_productsOrders ON dashboard_productsOrders.productsOrderGroupId = dashboard_productsOrderGroup.id
INNER JOIN dashboard_customers ON dashboard_customers.customerId = dashboard_productsOrderGroup.customerId
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = dashboard_productsOrderGroup.byWhomAdding

GROUP BY id
ORDER BY id DESC");
$td = "";

function returnFontStatus($var)
{
    $name = "star";

    if($var == 1)
    {
        $name = "clock-o";
    }
    else if($var == 2)
    {
        $name = "rocket";
    }
    else if($var == 3)
    {
        $name = "check";
    }
    else if($var == 4)
    {
        $name = "usd";
    }
    return $name;
}

foreach ($productOrders as $key => $value)
{
    $statusThis = returnFontStatus($value["dashboard_productsOrderGroup_status"]);
    $status_1 = returnFontStatus(1);
    $status_2 = returnFontStatus(2);
    $status_3 = returnFontStatus(3);
    $status_4 = returnFontStatus(4);

    $td .= <<<EOF
  <tr>
    <td><input type="checkbox" class="form-control checkInvoice" data-id="{$value["id"]}"></td>
    <td><span class="hidden">{$value["dashboard_productsOrderGroup_status"]}</span>
    <input type="hidden" value="{$value["id"]}" class="idOrder">
    <div class="dropdown status-invoice-order-select">
    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <span class="status-0">
        <i class="fa fa-{$statusThis}" aria-hidden="true"></i>
      </span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
      <a href="#"><li data-id="1"><span class="status-0">
        <i class="fa fa-$status_1" aria-hidden="true"></i>
      </span> В ожидании</a></li>
      <li data-id="2"><a href="#"><span class="status-0">
        <i class="fa fa-$status_2" aria-hidden="true"></i>
      </span> Начата работа над заказом</a></li>
      <li data-id="3"><a href="#"><span class="status-0">
        <i class="fa fa-$status_3" aria-hidden="true"></i>
      </span> Заказ выполнен</a></li>
      <li role="separator" class="divider"></li>
      <li data-id="4"><a href="#"><span class="status-0">
        <i class="fa fa-$status_4" aria-hidden="true"></i>
      </span> Счет оплачен</a></li>
    </ul>
  </div>
    </td>
    <td>{$value["id"]}</td>
    <td>{$value["orderGroupName"]}</td>
    <td>{$value["dateOfOrder"]}</td>
    <td>{$value['customerFIO']}</td>
    <td>{$value["productCount"]}</td>
    <td>{$value["totalSumOfOrder"]} грн.</td>
    <td>{$value["byWhomAdding"]} грн.</td>
    <td>
        <button type="button" class="btn btn-default addOrderDesignerFromInvoice" data-order="{$value["id"]}"><i class="fa fa-print" aria-hidden="true"></i></button>
    </td> 
    <td>
        <button type="button" class="btn btn-default createInvoice" data-order="{$value["id"]}"><i class="fa fa-file-text-o" aria-hidden="true"></i></button>
    </td>
    <td>
        <a href="/crm/editOrder.php?id={$value["id"]}"><button type="button" class="btn btn-default"><i class="fa fa-cog" aria-hidden="true"></i></button></a>
    </td>
  </tr>
EOF;
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once(APP_DIR_INC . "quickLeft.php"); ?>
        </div>
        <div class="col-md-10">
            <h2 class="min-h2">Заказы на продукты компании</h2>
            <br />
            <br />
            <br />
            <button class="btn btn-default ml-10" type="button" id="copyInvoice">
                Дублировать заказ&nbsp;&nbsp;&nbsp;<i class="fa fa-clone" aria-hidden="true"></i>
            </button>
            <table id="example" class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>Название заказа</th>
                        <th>Дата создания заказа</th>
                        <th>ФИО заказчика</th>
                        <th>Количество продуктов</th>
                        <th>Общая сумма заказа</th>
                        <th>Кто создал</th>
                        <th>Выслать в печать</th>
                        <th>Сформировать счет</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
<?php
print($td);
?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>