<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

//initialize values
$outOrders = "";
$outModals = "";

$workersArray = "";
$customerArray = "";
$companyArray = "";

$dateFrom = date("Y-m-d");
$dateTo = date("Y-m-d");

if(isset($_GET["date_from"]) && $_GET["date_from"] != null)
{
  $dateFrom = $_GET["date_from"];
}
if(isset($_GET["date_to"]) && $_GET["date_to"] != null)
{
  $dateTo = $_GET["date_to"];
}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        <h2>Дневник супервайзера</h2>

        <button class="btn btn-default dropdown-toggle mr-10" type="button" data-toggle="modal" data-target="#newOrder" aria-haspopup="true" aria-expanded="true">
            Создать заказ&nbsp;&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
        </button>

        <button id="addMultiOrder" class="btn btn-default mr-10" type="button">
            Мультизаказ&nbsp;&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
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
        <div class='input-group date dateFromTo' id='datetimepickerTo'>
            <input type='text' class="form-control" id="timeEditOrderTo" value="<?php echo $dateTo ?>" />
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>

        <button class="btn btn-default ml-10" type="button" id="showThisDate">
            Показать&nbsp;&nbsp;&nbsp;<i class="fa fa-eye" aria-hidden="true"></i>
        </button>

        <button class="btn btn-default ml-10" type="button" id="checkOrders">
            Копировать&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>
        </button>

        <script type="text/javascript">
          $(function () {
            $('#timeCopyOrder').datetimepicker({
              format: 'YYYY-MM-DD',
              locale: 'ru'
            });
          });
        </script>
        <div class='input-group date dateFromTo ml-10' id='datetimepickerCopy'>
            <input type='text' class="form-control" id="timeCopyOrder" value="" />
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>

        <table id="example" class="table">
            <thead>
                <tr>
                    <th width="70"></th>
                    <th width="70">Статус</th>
                    <th>Место</th>
                    <th>Заказчик</th>
                    <th>Работник</th>
                    <th>Время</th>
                    <th>Тип</th>
                    <th>Комментарий</th>
                    <th>Редактировать</th>
                    <th>Кто создал</th>
                </tr>
            </thead>
            <tbody>
                <?php

                //  echo phpinfo();
                //function transform aaray to option list
                function ArrayToLi($data, $colName, $idPar, $checkSelected)
                {
                    $bean = R::getAll("SELECT * FROM " . $data);
                    //$out = "<option value=\"0\">Не выбрано</option>";
                    $out = "";
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

                function ArrayToLiCompany($data, $colName, $idPar, $checkSelected)
                {
                    $bean = R::getAll("SELECT * FROM " . $data." WHERE id IN (SELECT dashboard_customers.companyId FROM dashboard_customers)");
                    //$out = "<option value=\"0\">Не выбрано</option>";
                    $out = "";
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
                function ArrayToLiNew($data, $colDataName, $idPar, $checkSelected)
                {
                    $bean = R::getAll("SELECT * FROM " . $data . " ORDER BY dashboard_workers_promoterLevel DESC, dashboard_workers_stickerLevel DESC");
                    //$out = "<option value=\"0\">Не выбрано</option>";
                    $out = "";
                    foreach ($bean as $key => $value)
                    {
                        $liInfo = "";
                        foreach($colDataName as $subkey => $subvalue)
                        {
                            $liInfo .= $value[$subvalue] . " ";
                        }
                        if ($value[$idPar] == $checkSelected)
                        {
                            $out .= "<option value=\"" . $value[$idPar] . "\" selected>" . $liInfo . "</option>";
                        }
                        else
                        {
                            $out .= "<option value=\"" . $value[$idPar] . "\">" . $liInfo . "</option>";
                        }
                    }
                    return $out;
                }

                //value company array we set tag `<option>` with value id of company and text = companyName
                $companyArray = ArrayToLiCompany("dashboard_companies", "companyName", "id", "sucks");
                $customerArray = ArrayToLi("dashboard_customers", "lastName", "customerId", "sucks");
                $workersArray = ArrayToLiNew("dashboard_workers", ["dashboard_workers_surname", "dashboard_workers_name", "dashboard_workers_patronymic"], "id", "sucks");

                //echo $customerArray;
                //echo $_SERVER["DOCUMENT_ROOT"];


                $workTypes = R::getAll("SELECT * FROM diary_worker_task");

                $data = R::getAll("SELECT
                diary_orders.id, diary_orders.status, diary_orders.place, dashboard_customers.lastName, diary_orders.count_hp, diary_orders.type, dashboard_workers.dashboard_workers_surname,  diary_orders.date, diary_orders.times, diary_worker_task.title_diary_worker_task, diary_worker_task.category_diary_worker_task, dashboard_users.dashboard_users_name, dashboard_workers.dashboard_workers_name, dashboard_workers.dashboard_workers_patronymic, dashboard_workers.dashboard_workers_phone, diary_orders.customerId, diary_orders.workerId, dashboard_users.dashboard_users_id, diary_orders.comment
                FROM diary_orders
            		INNER JOIN dashboard_customers ON diary_orders.customerId = dashboard_customers.customerId
            		INNER JOIN dashboard_workers ON diary_orders.workerId = dashboard_workers.id
            		INNER JOIN diary_worker_task ON diary_orders.type = diary_worker_task.id_diary_worker_task
                INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = diary_orders.byWhomAdding
                WHERE (DATE(date) BETWEEN ? AND ?) OR diary_orders.status <> 4 ORDER BY diary_orders.date", [$dateFrom, $dateTo]);

                foreach ($data as $key => $value)
                {

                    $idOrder = $value['id'];

                    $place = $value['place'];
                    $client = $value['lastName'];
                    $count = $value['count_hp'];
                    $types = $value['type'];
                    $category = $value['category_diary_worker_task'];
                    $byWhomAdding = $value['dashboard_users_name'];
                    $byWhomAddingId = $value['dashboard_users_id'];
                    $comment = $value['comment'];
                    $currentDate = $value["date"];


                    //$companyArrayEdit = ArrayToLi("dashboard_companies", "companyName", "id", $value['customerId']);
                    //$customerArrayEdit = ArrayToLi("dashboard_customers", "lastName", "customerId", $value['customerId']);
                    $workersArrayEdit = ArrayToLiNew("dashboard_workers", ["dashboard_workers_surname", "dashboard_workers_name", "dashboard_workers_patronymic"], "id", $value['workerId']);


                    //print_r($customerArrayEdit);
                    //print_r($companyArrayEdit);


                    $listCategory = array(
                        "0" => "Выберите тип",
                        "distribution" => "Раздача",
                        "posting" => "Расклейка",
                        "delivery" => "Разноска",
                        "promo" => "Промо-акция",
                    );

                    $option = "<select name=\"\" id=\"select-type2\" class=\"form-control\">";
                    foreach ($listCategory as $subkey => $subvalue)
                    {
                        if ($subkey == $category)
                        {
                            $option .= "<option value=\"" . $subkey . "\" selected>" . $subvalue . "</option>";
                        }
                        else
                        {
                            $option .= "<option value=\"" . $subkey . "\">" . $subvalue . "</option>";
                        }
                    }
                    $option .= "</select>";

                    $optionResult = "<br /><select name=\"\" id=\"select-type2-result\"  class=\"form-control\">";
                    foreach ($workTypes as $subkey => $subvalue)
                    {
                        if ($subvalue["id_diary_worker_task"] == $types)
                        {
                            $optionResult .= "<option value=\"" . $subvalue["id_diary_worker_task"] . "\" selected>" . $subvalue["title_diary_worker_task"] . "</option>";
                        }
                        else
                        {
                            $optionResult .= "<option value=\"" . $subvalue["id_diary_worker_task"] . "\">" . $subvalue["title_diary_worker_task"] . "</option>";
                        }
                    }
                    $optionResult .= "</select>";

                    $worker = $value['dashboard_workers_name'] . " " . $value['dashboard_workers_patronymic'];
                    $workerPhoneExplode = explode(",", $value['dashboard_workers_phone']);
                    $workerPhone = "";

                    $time = $value['date'] . " " . $value['times'];

                    $time2explodeDate = explode("-", $value['date']);
                    $time2explodeTime = explode(":", $value['times']);

                    $time2 = $time2explodeDate[2] . "." . $time2explodeDate[1] . "." . $time2explodeDate[0] . " " . $time2explodeTime[0] . ":" . $time2explodeTime[1];

                    $type = $value['category_diary_worker_task'];
                    if ($type == "posting")
                    {
                        $type = "Расклейка";
                    }
                    else if ($type == "delivery")
                    {
                        $type = "Разноска";
                    }
                    else if ($type == "distribution")
                    {
                        $type = "Раздача";
                    }
                    else if ($type == "promo")
                    {
                        $type = "Промо-акция";
                    }

                    $status = $value['status'];
                    foreach ($workerPhoneExplode as $subkey => $subvalue)
                    {
                        $workerPhone .= "<li><a href=\"#\">$subvalue</a></li>";
                    }
                    //echo '$byWhomAdding = `' . $byWhomAddingId . "`";
                    //echo '$_COOKIE[\'userId\'] = `' . $_COOKIE['userId'] . '`';
                    //function to get all rows from specific column

                    /*
                     *
                     *
                     * set select elements for Customers and Companies
                     *
                     *
                     */

                    //get customerId from diary_orders
                    $selectedCustomerId = R::getCol("SELECT customerId FROM diary_orders WHERE id = " . $idOrder);

                    //get companyId from dashboard_customers
                    $selectedCompanyId = R::getRow("SELECT dashboard_companies.id FROM diary_orders
INNER JOIN dashboard_customers ON diary_orders.customerId = dashboard_customers.customerId
INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId WHERE diary_orders.id = " . $idOrder);
                    $selectedCompanyId = $selectedCompanyId["id"];

                    //set companies to <select> element
                    $companyArrayEdit = ""; //так называется переменная которая выводит все компании
                    $allCompanies = R::getAll("SELECT id, companyName FROM dashboard_companies");
                    foreach ($allCompanies as $value)
                    {
                        if ($value['id'] == $selectedCompanyId)
                        {
                            $companyArrayEdit .= "<option value=\"" . $value['id'] . "\" selected>" . $value['companyName'] . "</option>";
                        }
                        else
                        {
                            $companyArrayEdit .= "<option value=\"" . $value['id'] . "\">" . $value['companyName'] . "</option>";
                        }
                    }

                    //set customers to <select> element
                    $customerArrayEdit = "";
                    $allCustomers = R::getAll("SELECT customerId, lastName, firstName, patronymicName FROM dashboard_customers WHERE companyId = " . $selectedCompanyId);
                    foreach ($allCustomers as $value)
                    {
                        //echo $value['id']." = ".$selectedCompanyId;
                        if ($value['customerId'] == $selectedCustomerId)
                        {
                            $customerArrayEdit .= "<option value=\"" . $value['customerId'] . "\" selected>" . $value['lastName'] . " " . $value['firstName'] . " " . $value['patronymicName'] . "</option>";
                        }
                        else
                        {
                            $customerArrayEdit .= "<option value=\"" . $value['customerId'] . "\">" . $value['lastName'] . " " . $value['firstName'] . " " . $value['patronymicName'] . "</option>";
                        }
                    }



                    $classTr = "";

                    if($currentDate == date("Y-m-d"))
                    {
                      $classTr = "today";
                    }
                    else if(strtotime($currentDate) < time())
                    {
                      $classTr = "last";
                    }
                    else {
                      $classTr = "future";
                    }

//if it is user that create order, so he can edit it, else - no!
                    if ($byWhomAddingId == $_COOKIE['userId'])
                    {


                        $outOrders .= <<<EOF
        <tr class="{$classTr}">
          <td><input type="checkbox" class="form-control checkOrder" data-id="{$idOrder}"></td>
          <td>
            <input type="hidden" value="{$idOrder}" class="idOrder">
            <div class="dropdown status-select">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
              <span class="status-{$status}">
                <i class="fa fa-star" aria-hidden="true"></i>
              </span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
              <li data-id="1"><a href="#">Подготовка</a></li>
              <li data-id="2"><a href="#">Материал выдан</a></li>
              <li data-id="3"><a href="#">На месте</a></li>
              <li data-id="4"><a href="#">Работа выполнена</a></li>
              <li role="separator" class="divider"></li>
              <li data-id="5"><a href="#">Заказ отменен</a></li>
            </ul>
          </div>
          </td>
          <td>{$place}</td>
          <td>{$client}</td>
          <td>{$worker}</td>
          <td>{$time}</td>
          <td>{$type}</td>
          <td>

        <div class="dropdown">

            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="status-circle">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                </span>
            </button>
            <textarea class="dropdown-menu" aria-labelledby="dropdownMenu1">
                {$comment}
            </textarea>
         </div>

          </td>
          <td>
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$idOrder}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
          </td>
            <td>
            {$byWhomAdding}
            </td>
        </tr>
EOF;
                    }
                    else
                    {
                        $outOrders .= <<<EOF
        <tr class="{$classTr}">
        <td><input type="checkbox" class="form-control checkOrder" data-id="{$idOrder}"></td>
          <td>
            <input type="hidden" value="{$idOrder}" class="idOrder">
            <div class="dropdown status-select">
            <button class="btn btn-default dropdown-toggle disabled" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
              <span class="status-{$status}">
                <i class="fa fa-star" aria-hidden="true"></i>
              </span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
              <li data-id="1"><a href="#">Подготовка</a></li>
              <li data-id="2"><a href="#">Материал выдан</a></li>
              <li data-id="3"><a href="#">На месте</a></li>
              <li data-id="4"><a href="#">Работа выполнена</a></li>
              <li role="separator" class="divider"></li>
              <li data-id="5"><a href="#">Заказ отменен</a></li>
            </ul>
          </div>
          </td>
          <td>{$place}</td>
          <td>{$client}</td>
          <td>{$worker}</td>
          <td>{$time}</td>
          <td>{$type}</td>
          <td>
            <div class="dropdown">

            <button class="btn btn-default dropdown-toggle disabled" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="status-circle">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                </span>
            </button>
            <textarea class="dropdown-menu" aria-labelledby="dropdownMenu1">
                {$comment}
            </textarea>
         </div>
          </td>
          <td>
            <button type="button" class="btn btn-default disabled" data-target="#myModal{$idOrder}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
          </td>
            <td>
            {$byWhomAdding}
            </td>
        </tr>
EOF;
                    }
                    $outModals .= <<<EOF
        <div class="modal fade" id="myModal{$idOrder}" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                Информация о заказе <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <table class="table">
                  <tbody>
                      <tr><td>Место: </td><td><input type="text" class="form-control" id="placeEditOrder{$idOrder}" value="{$place}"></td><td></td></tr>
                      <tr><td>Компания: </td><td>
                        <select class="form-control companyEditOrder" id="companyEditOrder{$idOrder}">
                          {$companyArrayEdit}
                        </select>
                      </td>
                      </tr>
                      <tr><td>Заказчик: </td><td>
                        <select class="form-control customersEditOrder" id="clientEditOrder{$idOrder}">
                          {$customerArrayEdit}
                        </select>
                      </td>
                      </tr>
                      <tr><td>Работник: </td>
                      <td>
                        <select class="form-control" id="workersEditOrder{$idOrder}">
                          {$workersArrayEdit}
                        </select>
                      </td>
                      <td>
                      <div class="dropdown">
                      <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="status-circle">
                          <i class="fa fa-phone" aria-hidden="true"></i>
                        </span>
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        {$workerPhone}
                      </ul>
                    </div>
                      </td>
                      </tr>
                      <tr><td>Время: </td><td>
                      <div class='input-group date' id='datetimepicker{$idOrder}'>
                          <input type='text' class="form-control" id="timeEditOrder{$idOrder}" value="{$time2}" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                      </div>
                      <script type="text/javascript">
                        $(function () {
                          $('#timeEditOrder{$idOrder}').datetimepicker({
                            locale: 'ru'
                          });
                        });
                      </script>
                      </td><td></td></tr>
                      <tr>
                        <td>Тип: </td>
                        <td>
                          {$option}<br />
                          {$optionResult}
                        </td>
                        <td></td>
                      </tr>
                      <tr><td>Количество: </td><td><input id="editOrder_count{$idOrder}" type="text" class="form-control" name="name" value="{$count}" placeholder=""></td><td></td></tr>
                      <tr><td>Комментарий к заказу: </td><td><textarea id="editOrder_comment{$idOrder}" type="text" class="form-control" rows="5" name="name" value="" placeholder="">{$comment}</textarea></td><td></td></tr>
   </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button class="btn btn-warning dropdown-toggle mr-10" type="button" data-toggle="modal" data-target="#deleteOrder{$idOrder}" aria-haspopup="true" aria-expanded="true">
                    Удалить
                </button>
                <button type="button" class="btn btn-default edit-order" data-order="{$idOrder}" data-dismiss="modal">Редактировать</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="deleteOrder{$idOrder}" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                  Удалить заказ? <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default delete-order" data-order="{$idOrder}" data-dismiss="modal">Удалить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
EOF;
                }
                print($outOrders);
                ?>
            </tbody>
        </table>

        <div class="modal fade" id="newOrder" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                       Создание нового заказа <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tbody>
                                <tr><td>Место: </td><td><input id="newOrder_place" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
                                <tr>
                                    <td>Компания: </td>
                                    <td>
                                        <select id="newOrder_selectCompany" class="form-control" name="">
                                            <?php echo $companyArray; ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td>Клиент: </td>
                                    <td>
                                        <select id="newOrder_selectClients" class="form-control" name="">
                                            <?php echo $customerArray; ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr><td>Работник: </td>
                                    <td>
                                        <select id="newOrder_selectWorkers" class="form-control" name="">
                                            <?php echo $workersArray; ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td>Время: </td>
                                    <td>
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type='text' class="form-control" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <script type="text/javascript">
                                                $(function ()
                                                {
                                                    $('#datetimepicker2').datetimepicker({
                                                        locale: 'ru'
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Тип: </td>
                                    <td>
                                        <select name="" id="select-type" class="form-control">
                                            <option value="0">Выберите тип</option>
                                            <option value="distribution">Раздача</option>
                                            <option value="posting">Расклейка</option>
                                            <option value="delivery">Разноска</option>
                                            <option value="promo">Промо-акция</option>
                                        </select>
                                        <div id="">
                                            <select name="" id="select-type-result"  class="form-control">

                                            </select>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr><td>Количество: </td><td><input id="newOrder_count" type="text" class="form-control" name="name" value="" placeholder=""></td><td></td></tr>
                                <tr><td>Комментарий к заказу: </td><td><textarea id="newOrder_comment" type="text" class="form-control" rows="5" name="name" value="" placeholder=""></textarea></td><td></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="addNewOrder">Добавить</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>

            </div>
        </div>
        <div id="results">

        </div>
        <?php print($outModals); ?>
    </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
</body>
</html>
