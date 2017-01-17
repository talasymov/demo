<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$customerArray = "";
$companyArray = "";

function ArrayToLi($data, $colName, $idPar, $checkSelected)
{
    $bean = R::getAll("SELECT * FROM " . $data);
    $out = "<option value=\"0\">Не выбрано</option>";
    foreach ($bean as $key => $value)
    {
        if ($value[$idPar] == $checkSelected)
        {
            $out .= "<option value=\"" . $value[$idPar] . "\" selected>" . $value[$colName] . "</option>";
        }
        else
        {
            $out .= "<option value=\"" . $value[$idPar] . "\">" . $value[$colName] . "</option>";
        }
    }
    return $out;
}

$companyArray = ArrayToLi("dashboard_companies", "companyName", "id", "sucks");
$customerArray = ArrayToLi("dashboard_customers", "lastName", "customerId", "sucks");

$outModalsEdit = <<<EOF
<div class="modal fade" id="modalAddOrderDesigner" role="dialog">
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    Добавление заказа <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
    <table class="table">
      <tbody>
          <tr><td>Название: </td><td><input id="newOrderDesigner_name" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr>
              <td>Компания: </td>
              <td>
                  <select id="newOrderDesigner_selectCompany" class="form-control" name="">
                      <?php echo $companyArray; ?>
                  </select>
              </td>
              <td>

              </td>
          </tr>
          <tr>
              <td>Клиент: </td>
              <td>
                  <select id="newOrderDesigner_selectClients" class="form-control" name="">
                      <?php echo $customerArray; ?>
                  </select>
              </td>
              <td>

              </td>
          </tr>
          <tr><td>Тип работы: </td><td><input id="newOrderDesigner_type" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Стоимость: </td><td><input id="newOrderDesigner_price" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Размер: </td><td><input id="newOrderDesigner_size" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Ориентация: </td><td><input id="newOrderDesigner_orientation" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Фирменные цвета: </td><td><input id="newOrderDesigner_colors" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Какой пакет?: </td><td><input id="newOrderDesigner_packets" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Слоган: </td><td><input id="newOrderDesigner_slogan" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Название компани: </td><td><input id="newOrderDesigner_company_name" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Описание: </td><td><textarea id="newOrderDesigner_comment" type="text" class="form-control" rows="5" name="name" value="" placeholder=""></textarea></td><td></td></tr>
          <tr><td>Срочность: </td><td>
            <select class="form-control" id="newOrderDesigner_quick">
              <option value="none">Выберите</option>
              <option value="1">Срочно</option>
              <option value="0">Не срочно</option>
            </select>
          </td><td></td></tr>
      </tbody>
    </table>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmAdd" data-dismiss="modal">Создать</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="myModalConfirmAdd" role="dialog">
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    Подтвердите ваше действие <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmAdd" data-dismiss="modal">Подтвердить</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
  </div>
</div>
</div>
</div>
EOF;
$outOrders = R::getAll("SELECT * FROM dashboard_orders_designers
INNER JOIN dashboard_users ON dashboard_orders_designers.dashboard_orders_designers_whoadd = dashboard_users.dashboard_users_id
WHERE dashboard_orders_designers_whowork = ?", [$_COOKIE['userId']]);
$outOrdersText = "";
$modals = "";

foreach($outOrders as $key => $value)
{
    $link = "";
    $selectedCompanyId = $value["dashboard_orders_designers_company"];
    $selectedCustomerId = $value["dashboard_orders_designers_customer"];

    $textCompany = R::getRow("SELECT * FROM dashboard_companies WHERE id = ? ", [$selectedCompanyId]);
    $textCustomer = R::getRow("SELECT * FROM dashboard_customers WHERE customerId = ? ", [$selectedCustomerId]);
    $textAboutCustomer = "";
    if(isset($textCompany["id"]))
    {
        $textAboutCustomer .= $textCompany["companyName"] . " | ";
    }
    if(isset($textCustomer["customerId"]))
    {
        $textAboutCustomer .= $textCustomer["lastName"] . " " . $textCustomer["firstName"] . " " . $textCustomer["patronymicName"];
    }

    if($value["dashboard_orders_designers_company"] != 0 && $value["dashboard_orders_designers_company"] != 1)
    {
        $link = "customers/company" . $value["dashboard_orders_designers_company"]. "/";
    }
    if( ($value["dashboard_orders_designers_company"] != 0 && ( $value["dashboard_orders_designers_customer"] != 0)) || ($value["dashboard_orders_designers_company"] == 0 && ( $value["dashboard_orders_designers_customer"] != 0)) )
    {
        $link = "clients/client" . $value["dashboard_orders_designers_customer"]. "/";
    }
  $modals .= <<<EOF
  <div class="modal fade" data-id="{$value["dashboard_orders_designers_id"]}" id="modalEditMakets{$value["dashboard_orders_designers_id"]}" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      Настройка доступа к макетам<button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
      <table class="table">
        <tbody>
            <tr><td>В печать 1: </td><td><input type="text" class="form-control newOrderDesigner_print" name="name" value="{$value["dashboard_orders_designers_print"]}" placeholder=""></td><td><button class="btn btn-primary open-fm" data-return=".newOrderDesigner_print" data-folder="{$link}"><i class="fa fa-folder-open" aria-hidden="true"></i></button></td></tr>
            <tr><td>В печать 2: </td><td><input type="text" class="form-control newOrderDesigner_print_2" name="name" value="{$value["dashboard_orders_designers_print_2"]}" placeholder=""></td><td><button class="btn btn-primary open-fm" data-return=".newOrderDesigner_print_2" data-folder="{$link}"><i class="fa fa-folder-open" aria-hidden="true"></i></button></td></tr>
            <tr><td>Првеью: </td><td><input type="text" class="form-control newOrderDesigner_preview" name="name" value="{$value["dashboard_orders_designers_preview"]}" placeholder=""></td><td><button class="btn btn-primary open-fm" data-return=".newOrderDesigner_preview" data-folder="{$link}"><i class="fa fa-folder-open" aria-hidden="true"></i></button></td></tr>
            <tr><td>Исходник 1: </td><td><input type="text" class="form-control newOrderDesigner_base" name="name" value="{$value["dashboard_orders_designers_base"]}" placeholder=""></td><td><button class="btn btn-primary open-fm" data-return=".newOrderDesigner_base" data-folder="{$link}"><i class="fa fa-folder-open" aria-hidden="true"></i></button></td></tr>
            <tr><td>Исходник 2: </td><td><input type="text" class="form-control newOrderDesigner_base_2" name="name" value="{$value["dashboard_orders_designers_base_2"]}" placeholder=""></td><td><button class="btn btn-primary open-fm" data-return=".newOrderDesigner_base_2" data-folder="{$link}"><i class="fa fa-folder-open" aria-hidden="true"></i></button></td></tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default confirmMaket" data-dismiss="modal">Готово</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
  </div>
  </div>
  </div>
EOF;
  $outOrdersText .=  <<<EOF
  <tr>
    <td>{$value["dashboard_orders_designers_name"]}</td>
    <td>{$value["dashboard_orders_designers_type"]}</td>
    <td>{$value["dashboard_orders_designers_price"]}</td>
    <td>{$value["dashboard_orders_designers_description"]}</td>
    <td>{$textAboutCustomer}</td>
    <td>{$value["dashboard_orders_designers_quick"]}</td>
    <td>{$value["dashboard_orders_designers_date"]}</td>
    <td>{$value["dashboard_users_name"]}</td>
    <td style="width: 100px">
      <input type="hidden" value="{$value["dashboard_orders_designers_id"]}" class="idOrder">
      <div class="dropdown status-designer-select">
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
    </div>
    <button class="btn btn-default dib" data-toggle="modal" data-target="#modalEditMakets{$value["dashboard_orders_designers_id"]}" data-dismiss="modal" type="button" aria-haspopup="true" aria-expanded="true">
      <span class="status">
        <i class="fa fa-cog" aria-hidden="true"></i>
      </span>
    </button>
    </td>
  </tr>
EOF;
}
$out = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Мои заказы</h2>
            <button class="btn btn-info dropdown-toggle" type="button" data-toggle="modal" data-target="#modalAddOrderDesigner" aria-haspopup="true" aria-expanded="true">
                Добавление заказа&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
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
                  {$outOrdersText}
                </tbody>
              </table>
            {$outModalsEdit}
        </div>
    </div>
</div>
EOF;
print($out . $modals);
?>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
