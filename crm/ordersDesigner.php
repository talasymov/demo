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

$customerArray = ArrayToLi("dashboard_customers", "lastName", "customerId", "sucks");

$companyArray = getCompany("option");

$outModalsAdd = <<<EOF
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
                      {$companyArray}
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
          <tr><td>Ориентация: </td><td>
            <select id="newOrderDesigner_orientation" class="form-control" name="">
              <option value="1">Горизонтальная</option>
              <option value="2">Вертикальная</option>
            </select></td><td></td></tr>
          <tr><td>Фирменные цвета: </td><td><input id="newOrderDesigner_colors" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Какой пакет?: </td><td>
            <select id="newOrderDesigner_packets" class="form-control" name="">
              <option value="1">Простой</option>
              <option value="2">Средний</option>
              <option value="3">Сложный</option>
            </select></td><td></td></tr>
          <tr><td>Слоган: </td><td><input id="newOrderDesigner_slogan" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Название компани: </td><td><input id="newOrderDesigner_company_name" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
          <tr><td>Описание: </td><td>
            <textarea id="newOrderDesigner_comment" type="text" class="form-control" rows="5" name="name" value="" placeholder=""></textarea>
            <br />
            <!--<button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#modalAddOrderDesigner" aria-haspopup="true" aria-expanded="true">
                Прикрепить файлы&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>-->
          </td><td></td></tr>
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
WHERE dashboard_orders_designers_whowork = 0");

$outOrdersText = "";
$outModalsEdit = "";
foreach($outOrders as $key => $value)
{
    $buttonAddToMe = "";
    $buttonEdit = "";
    $buttonStatus = "";
    $optionTarif = "";
    $optionOrientir = "";
    $optionQuick = "";

    $arrayPacket = array(
        "1" => "Простой",
        "2" => "Средний",
        "3" => "Сложный"
    );

    $arrayOrientir = array(
        "1" => "Горизонтальная",
        "2" => "Вертикальная"
    );

    $arrayQuick = array(
        "1" => "Срочно",
        "0" => "Не срочно"
    );




    $statusThis = "";

    $idOrder = $value["dashboard_orders_designers_id"];

    $queryTarif = $value["dashboard_orders_designers_tarif"];
    $queryOrientir = $value["dashboard_orders_designers_orientir"];
    $queryName = $value["dashboard_orders_designers_name"];
    $queryType = $value["dashboard_orders_designers_type"];
    $queryPrice = $value["dashboard_orders_designers_price"];
    $querySlogan = $value["dashboard_orders_designers_slogan"];
    $queryColors = $value["dashboard_orders_designers_colors"];
    $querySize = $value["dashboard_orders_designers_size"];
    $queryDescription = $value["dashboard_orders_designers_description"];
    $queryUserName = $value["dashboard_users_name"];
    $queryQuick = $value["dashboard_orders_designers_quick"];
    $queryDate = $value["dashboard_orders_designers_date"];
    $queryStatus = $value["dashboard_orders_designers_status_order"];
    $selectedCompanyId = $value["dashboard_orders_designers_company"];
    $selectedCustomerId = $value["dashboard_orders_designers_customer"];

    /*
    *
    *
    * set select elements for Customers and Companies
    *
    *
    */

    //get customerId from diary_orders
    $queryCustomer= $value["dashboard_orders_designers_customer"];

    //get companyId from dashboard_customers
//    $selectedCompanyId = R::getRow("SELECT dashboard_companies.id FROM dashboard_orders_designers
//WHERE dashboard_orders_designers.dashboard_orders_designers_id = " . $idOrder);


    //set companies to <select> element
    $companyArrayEdit = ""; //так называется переменная которая выводит все компании
    $allCompanies = R::getAll("SELECT id, companyName FROM dashboard_companies");

    foreach ($allCompanies as $subkey => $subvalue)
    {
//        echo $subvalue['id']." = ".$selectedCompanyId . "<br />";
        if ($subvalue['id'] == $selectedCompanyId)
        {
            $companyArrayEdit .= "<option value=\"" . $subvalue['id'] . "\" selected>" . $subvalue['companyName'] . "</option>";
        }
        else
        {
            $companyArrayEdit .= "<option value=\"" . $subvalue['id'] . "\">" . $subvalue['companyName'] . "</option>";
        }
    }
    //set customers to <select> element
    $customerArrayEdit = "";
    $allCustomers = R::getAll("SELECT customerId, lastName, firstName, patronymicName FROM dashboard_customers WHERE companyId = " . $selectedCompanyId);

    foreach ($allCustomers as $subvalue)
    {
//        echo $subvalue['customerId']." = ".$selectedCustomerId . "<br />";
        if ($subvalue['customerId'] == $selectedCustomerId)
        {
            $customerArrayEdit .= "<option value=\"" . $subvalue['customerId'] . "\" selected>" . $subvalue['lastName'] . " " . $subvalue['firstName'] . " " . $subvalue['patronymicName'] . "</option>";
        }
        else
        {
            $customerArrayEdit .= "<option value=\"" . $subvalue['customerId'] . "\">" . $subvalue['lastName'] . " " . $subvalue['firstName'] . " " . $subvalue['patronymicName'] . "</option>";
        }
    }



  if($_COOKIE["permission"] == "designer" && $value["dashboard_orders_designers_status_order"] == 1)
  {
    $buttonAddToMe = <<<EOF
    <button class="btn btn-default dropdown-toggle addOrderDesigner" type="button" data-id="{$idOrder}">
      <span class="status">
        <i class="fa fa-plus" aria-hidden="true"></i>
      </span>
    </button>
EOF;
  }
  if($_COOKIE["permission"] != "designer") {
      $buttonEdit = <<<EOF
    <button class="btn btn-default dropdown-toggle" data-toggle="modal" data-target="#editOrder{$idOrder}" aria-haspopup="true" aria-expanded="true" type="button" data-id="{$idOrder}">
      <span class="status">
        <i class="fa fa-cog" aria-hidden="true"></i>
      </span>
    </button>
EOF;
  }
    foreach($arrayPacket as $subkey => $subvalue)
    {
        if($subkey == $queryTarif)
        {
            $optionTarif .= "<option value=\"{$subkey}\" selected>{$subvalue}</option>";
        }
        else
        {
            $optionTarif .= "<option value=\"{$subkey}\">{$subvalue}</option>";
        }
    }

    foreach($arrayOrientir as $subkey => $subvalue)
    {
        if($subkey == $queryOrientir)
        {
            $optionOrientir .= "<option value=\"{$subkey}\" selected>{$subvalue}</option>";
        }
        else
        {
            $optionOrientir .= "<option value=\"{$subkey}\">{$subvalue}</option>";
        }
    }

    foreach($arrayQuick as $subkey => $subvalue)
    {
        if($subkey == $queryQuick)
        {
            $optionQuick .= "<option value=\"{$subkey}\" selected>{$subvalue}</option>";
        }
        else
        {
            $optionQuick .= "<option value=\"{$subkey}\">{$subvalue}</option>";
        }
    }

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

$outModalsEdit .= <<<EOF
<div class="modal fade" id="editOrder{$idOrder}" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    Информация о заказе
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
    <table class="table">
      <tbody>
        <tr><td>Название: </td><td><input id="editOrderDesigner_name{$idOrder}" type="text" class="form-control" name="name" value="{$queryName}" placeholder=""></td><td></td></tr>
        <tr>
            <td>Компания: </td>
            <td>
                <select id="editOrderDesigner_selectCompany{$idOrder}" class="form-control selectCompanyClass" name="">
                    {$companyArrayEdit}
                </select>
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>Клиент: </td>
            <td>
                <select id="editOrderDesigner_selectClients{$idOrder}" class="form-control selectClientsClass" name="">
                    {$customerArrayEdit}
                </select>
            </td>
            <td>

            </td>
        </tr>
        <tr><td>Тип работы: </td><td><input id="editOrder_type{$idOrder}" type="text" class="form-control" name="name" value="{$queryType}" placeholder=""></td><td></td></tr>
        <tr><td>Стоимость: </td><td><input id="editOrder_price{$idOrder}" type="text" class="form-control" name="name" value="{$queryPrice}" placeholder=""></td><td></td></tr>
        <tr><td>Размер: </td><td><input id="editOrder_size{$idOrder}" type="text" class="form-control" name="name" value="{$querySize}" placeholder=""></td><td></td></tr>
        <tr><td>Ориентация: </td><td>
            <select id="editOrderDesigner_orientir{$idOrder}" class="form-control" name="">
             {$optionOrientir}
            </select>
        </td><td></td></tr>
        <tr><td>Фирменные цвета: </td><td><input id="editOrder_colors{$idOrder}" type="text" class="form-control" name="name" value="{$queryColors}" placeholder=""></td><td></td></tr>
        <tr><td>Какой пакет?: </td><td>
        <select id="editOrderDesigner_packets{$idOrder}" class="form-control" name="">
            {$optionTarif}
        </select>
        </td><td></td></tr>
        <tr><td>Слоган: </td><td><input id="editOrder_slogan{$idOrder}" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
        <tr><td>Название компани: </td><td><input id="editOrder_company_name{$idOrder}" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
        <tr><td>Описание: </td><td>
          <textarea id="editOrder_comment{$idOrder}" type="text" class="form-control" rows="5" name="name" value="" placeholder=""></textarea>
          <br />
        </td><td></td></tr>
        <tr><td>Срочность: </td><td>
          <select class="form-control" id="editOrder_quick{$idOrder}">
            <option value="none">Выберите</option>
            {$optionQuick}
          </select>
        </td><td></td></tr>
      </tbody>
    </table>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmEdit{$idOrder}" data-dismiss="modal">Редактировать</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
  </div>
</div>
</div>
</div>
<div class="modal fade" id="myModalConfirmEdit{$idOrder}" role="dialog">
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    Подтвердите ваше действие <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default editOrderDesigner" data-toggle="modal" data-id="{$idOrder}" data-dismiss="modal">Подтвердить</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
  </div>
</div>
</div>
</div>
EOF;

    if($queryStatus == 0)
    {
        $statusThis = "clock-o";
    }
    else {
        $statusThis = "check";
    }

$buttonStatus = <<<EOF
  <div class="dropdown status-designer-order-select">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    <span class="status-0">
      <i class="fa fa-{$statusThis}" aria-hidden="true"></i>
    </span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
    <li data-id="0"><a href="#"><span class="status-0">
      <i class="fa fa-clock-o" aria-hidden="true"></i>
    </span> Не обработан</a></li>
    <li data-id="1"><a href="#"><span class="status-0">
      <i class="fa fa-check" aria-hidden="true"></i>
    </span> Готов к работе</a></li>
  </ul>
</div>
EOF;

  $tarif = GetTarifsDesigner($queryTarif);
  $orientation = GetOrientationDesigner($queryOrientir);
  $outOrdersText .=  <<<EOF
  <tr>
    <td>
      <input type="hidden" value="{$idOrder}" class="idOrder">
      {$buttonStatus}
    </td>
    <td>{$queryName}</td>
    <td>{$queryType}</td>
    <td>{$queryPrice}</td>
    <td>
      <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-comments" aria-hidden="true"></i>
        </button>
        <div class="dropdown-menu" style="width: 250px;">
          <p style="padding: 10px;">
            Размер: {$querySize}<br />
            Ориентация: {$orientation}<br />
            Цвета: {$queryColors}<br />
            Тариф: {$tarif}<br />
            Слоган: {$querySlogan}<br />
            Описание: {$queryDescription}<br />
          </p>
        </div>
      </div>
    </td>
    <td>{$textAboutCustomer}</td>
    <td>{$queryQuick}</td>
    <td>{$queryDate}</td>
    <td>{$queryUserName}</td>
    <td>
      {$buttonAddToMe}
      {$buttonEdit}
      {$outModalsAdd}
    </td>
  </tr>
EOF;
}
$out = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Стол заказов</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#modalAddOrderDesigner" aria-haspopup="true" aria-expanded="true">
                Добавление заказа&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
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
            {$outModalsAdd}
            {$outModalsEdit}
        </div>
    </div>
</div>
EOF;
print($out);
?>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
