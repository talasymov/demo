<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
?>
<div class="container">
    <div class="row">
        <h2>Дневник супервайзера</h2>

        <button class="btn btn-info dropdown-toggle" type="button" data-toggle="modal" data-target="#newOrder" aria-haspopup="true" aria-expanded="true">
            Создать заказ&nbsp;&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
        </button>

        <table id="example" class="table">
            <thead>
                <tr>
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
                $outOrders = "";
                $outModals = "";

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

                $workersArray = "";
                $clientArray = "";
                $companyArray = "";

                $workersArray = ArrayToLi("dashboard_workers", "dashboard_workers_name", "id", "sucks");
                $clientArray = ArrayToLi("dashboard_clients", "name", "id", "sucks");
                //echo $clientArray;
                //echo $_SERVER["DOCUMENT_ROOT"];
                $companyArray = ArrayToLi("dashboard_company", "name", "id", "sucks");

                $workTypes = R::getAll("SELECT * FROM diary_worker_task");

                $data = R::getAll("SELECT * FROM diary_orders
    		INNER JOIN dashboard_clients ON diary_orders.customer = dashboard_clients.id
    		INNER JOIN dashboard_workers ON diary_orders.worker = dashboard_workers.id
    		INNER JOIN diary_worker_task ON diary_orders.type = diary_worker_task.id_diary_worker_task
        ");

                foreach ($data as $key => $value)
                {

                    $idOrder = $value['id'];

                    $place = $value['place'];
                    $client = $value['surname'];
                    $count = $value['count_hp'];
                    $types = $value['type'];
                    $category = $value['category_diary_worker_task'];

                    $workersArrayEdit = ArrayToLi("dashboard_workers", "dashboard_workers_name", "id", $value['worker']);
                    $clientArrayEdit = ArrayToLi("dashboard_clients", "name", "id", $value['customer']);
                    $companyArrayEdit = ArrayToLi("dashboard_company", "name", "id", $value['customer']);
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

                    $optionResult = "<div id=\"\"><select name=\"\" id=\"select-type2-result\"  class=\"form-control\">";
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
                    $optionResult .= "</select></div>";

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

                    $outOrders .= <<<EOF
        <tr>
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
            <button type="button" class="btn btn-default" name="button"><i class="fa fa-comment" aria-hidden="true"></i></button>
          </td>
          <td>
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$idOrder}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
          </td>
            <td>
            
        </tr>
EOF;
                    $outModals .= <<<EOF
        <div class="modal fade" id="myModal{$idOrder}" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <table class="table">
                  <thead>
                  <tr>
                    <th colspan="2"><h4>Информация о заказе</h4></th><th width="95"></th>
                  </tr>
                  </thead>
                  <tbody>
                      <tr><td>Место: </td><td><input type="text" class="form-control" id="placeEditOrder{$idOrder}" value="{$place}"></td><td></td></tr>
                      <tr><td>Компания: </td><td>
                        <select class="form-control" id="companyEditOrder{$idOrder}">
                          {$companyArrayEdit}
                        </select>
                      </td>
                      <td>
                      <button class="btn btn-default " type="button" id="dropdownMenu2" aria-haspopup="true" aria-expanded="true">
                        <span class="status-circle">
                          <i class="fa fa-users" aria-hidden="true"></i>
                        </span>
                      </button>
                      </td>
                      </tr>
                      <tr><td>Заказчик: </td><td>
                        <select class="form-control" id="clientEditOrder{$idOrder}">
                          {$clientArrayEdit}
                        </select>
                      </td>
                      <td>
                      <button class="btn btn-default " type="button" id="dropdownMenu2" aria-haspopup="true" aria-expanded="true">
                        <span class="status-circle">
                          <i class="fa fa-users" aria-hidden="true"></i>
                        </span>
                      </button>
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
                      <button class="btn btn-default " type="button" id="dropdownMenu2" aria-haspopup="true" aria-expanded="true">
                        <span class="status-circle">
                          <i class="fa fa-user" aria-hidden="true"></i>
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
                          {$option}
                          {$optionResult}
                        </td>
                        <td></td>
                      </tr>
                      <tr><td>Количество: </td><td><input id="editOrder_count{$idOrder}" type="text" class="form-control" name="name" value="{$count}" placeholder=""></td><td></td></tr>
                  </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-info edit-order" data-order="{$idOrder}" data-dismiss="modal">Редактировать</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Закрыть</button>
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

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2"><h4>Информация о заказе</h4></th><th width="95"></th>
                                </tr>
                            </thead>
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
<?php echo $clientArray; ?>
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
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal" id="addNewOrder">Добавить</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>

            </div>
        </div>
        <div id="results">

        </div>
<?php print($outModals); ?>
    </div>
</div>
</body>
</html>
