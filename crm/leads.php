<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
//echo date("Y-m-d");
//$company = R::getAll("SELECT * FROM dashboard_company");
//$clients = R::getAll("SELECT * FROM dashboard_clients");
//$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");
//update workers status if it is need now
//get worker and him/her status


$customers = R::getAll("
SELECT dashboard_customers.customerId,
dashboard_customers.companyId,
dashboard_companies.companyName,
lastName,
firstName,
patronymicName,
street,
build,
apartment,
phone,
email,
url,
bankDetails,
dashboard_money_from.name AS fromWhom,
dashboard_users.dashboard_users_name AS byWhomAdding
FROM dashboard_customers
INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId
INNER JOIN dashboard_money_from ON dashboard_customers.fromWhom = dashboard_money_from.id
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = dashboard_customers.byWhomAdding");
$leads = R::getAll("
SELECT diary_leads.id,
diary_leads.lastName,
diary_leads.firstName,
diary_leads.whenCall,
diary_leads.patronymicName,
diary_leads.byWhomAdding,
diary_leads.phone,
diary_leads.phone_2,
diary_leads.phone_3,
diary_leads.status,
dashboard_users.dashboard_users_name,
diary_leads.whenAdding,
diary_leads.comment
FROM diary_leads
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = diary_leads.byWhomAdding
WHERE inTableCustomer = 0
ORDER BY diary_leads.id DESC");
$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");
$companies = R::getAll("SELECT * FROM dashboard_companies");


$outputFrom = "<option value=\"0\">Выберите источник</option>";
$outputCompany = "<option value='0'>Новая компания . . .</option>";


$outTable = "";

$outModalsEdit = "";
$outModalsConfirmEdit = "";
$outModalsConfirmCall = "";

$deleteWorkerModalWindow = "";

$outModalsCreateCustomerByLead = "";
$outModalsConfirmCreateCustomerByLead = "";

function returnFontStatus($var)
{
  $name = "star";

  if($var == 1)
  {
    $name = "clock-o";
  }
  else if($var == 2)
  {
    $name = "phone";
  }
  else if($var == 3)
  {
    $name = "shopping-cart";
  }
  else if($var == 4)
  {
    $name = "lightbulb-o";
  }
  else if($var == 5)
  {
    $name = "deaf";
  }
  else {
    $name = "times";
  }
  return $name;
}

foreach ($companies as $value)
{
    $outputCompany .= "<option value=\"" . $value["id"] . "\">" . $value["companyName"] . "</option>";
}

foreach ($clientsFrom as $key => $value)
{
    $outputFrom .= "<option value=\"" . $value["id"] . "\">" . $value["name"] . "</option>";
}
foreach ($leads as $key => $value)
{
    $button = "";
    $statusLead = "";
    $buttonCall = <<<EOF
      <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalCall{$value["id"]}" name="button"><i class="fa fa-phone" aria-hidden="true"></i></button>
EOF;

    if($value["byWhomAdding"] == $_COOKIE["userId"])
    {
      $button = <<<EOF
      <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalCreateCustomerByLead{$value["id"]}" name="button"><i class="fa fa-eject" aria-hidden="true"></i></button>
EOF;
    $statusLead = "last";
    }



    $statusThis = returnFontStatus($value["status"]);
    $status_1 = returnFontStatus(1);
    $status_2 = returnFontStatus(2);
    $status_3 = returnFontStatus(3);
    $status_4 = returnFontStatus(4);
    $status_5 = returnFontStatus(5);
    $status_6 = returnFontStatus(6);

    $outTable .= <<<EOF
  <tr class="{$statusLead}">
    <td>
    <span class="hidden">{$value["status"]}</span>
    <input type="hidden" value="{$value["id"]}" class="idOrder">
    <div class="dropdown status-lead-select">
    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <span class="status-0">
        <i class="fa fa-{$statusThis}" aria-hidden="true"></i>
      </span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
      <a href="#"><li data-id="1"><span class="status-0">
        <i class="fa fa-$status_1" aria-hidden="true"></i>
      </span> Не обработан</a></li>
      <li data-id="2"><a href="#"><span class="status-0">
        <i class="fa fa-$status_2" aria-hidden="true"></i>
      </span> Нужно перезвонить</a></li>
      <li data-id="3"><a href="#"><span class="status-0">
        <i class="fa fa-$status_3" aria-hidden="true"></i>
      </span> Готов к покупке</a></li>
      <li role="separator" class="divider"></li>
      <li data-id="4"><a href="#"><span class="status-0">
        <i class="fa fa-$status_4" aria-hidden="true"></i>
      </span> Заинтерисован</a></li>
      <li data-id="5"><a href="#"><span class="status-0">
        <i class="fa fa-$status_5" aria-hidden="true"></i>
      </span> Не заинтерисован</a></li>
      <li data-id="6"><a href="#"><span class="status-0">
        <i class="fa fa-$status_6" aria-hidden="true"></i>
      </span> Заказ отменен</a></li>
    </ul>
  </div>
    </td>
    <td>{$value["id"]}</td>
    <td>{$value["lastName"]}</td>
    <td>{$value["firstName"]}</td>
    <td>{$value["patronymicName"]}</td>
    <td>{$value["phone"]}</td>
    <td>{$value["comment"]}</td>
    <td>{$value["dashboard_users_name"]}</td>
    <td>{$value["whenAdding"]}</td>
    <td>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$value["id"]}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalDelete{$value["id"]}" name="button"><i class="fa fa-times" aria-hidden="true"></i></i></button>
    </td>
    <td>
      {$button}
    </td>
    <td>
      {$buttonCall}
    </td>
  </tr>
EOF;
$timeExp = explode(" ", $value["whenCall"]);

$time2explodeDate = explode("-", $timeExp[0]);
$time2explodeTime = explode(":", $timeExp[1]);

$time2 = $time2explodeDate[2] . "." . $time2explodeDate[1] . "." . $time2explodeDate[0] . " " . $time2explodeTime[0] . ":" . $time2explodeTime[1];
    $outModalsEdit .= <<<EOF
<div class="modal fade" id="myModal{$value["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Информация о лиде
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table">
          <tbody>
              <tr><td>Фамилия: </td><td><input type="text" class="form-control" id="editLastNameLead{$value["id"]}" value="{$value["lastName"]}"></td><td></td></tr>
              <tr><td>Имя: </td><td><input type="text" class="form-control" id="editFirstNameLead{$value["id"]}" value="{$value["firstName"]}"></td><td></td></tr>
              <tr><td>Отчество: </td><td><input type="text" class="form-control" id="editPatronymicNameLead{$value["id"]}" value="{$value["patronymicName"]}"></td><td></td></tr>
              <tr><td>Номер телефона: </td><td><input type="text" class="form-control phone" id="editPhoneLead{$value["id"]}" value="{$value["phone"]}"></td><td></td></tr>
              <tr><td>Номер телефона 2: </td><td><input type="text" class="form-control phone" id="editPhoneLead{$value["id"]}" value="{$value["phone_2"]}"></td><td></td></tr>
              <tr><td>Номер телефона 3: </td><td><input type="text" class="form-control phone" id="editPhoneLead{$value["id"]}" value="{$value["phone_3"]}"></td><td></td></tr>
              <tr><td>Комментарий: </td><td><textarea class="form-control" id="editCommentLead{$value["id"]}">{$value["comment"]}</textarea></td><td></td></tr>
              <tr><td>Когда перезвонить? </td><td>
              <div class='input-group date' id='datetimepicker{$value["id"]}'>
                  <input type='text' class="form-control" id="timeEditOrderOp{$value["id"]}" value="{$time2}" />
                  <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </span>
              </div>
              <script type="text/javascript">
                $(function () {
                  $('#timeEditOrderOp{$value["id"]}').datetimepicker({
                    locale: 'ru'
                  });
                });
              </script>
              </td><td></td></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirm{$value["id"]}" data-dismiss="modal">Редактировать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

    $outModalsConfirmEdit .= <<<EOF
<div class="modal fade" id="myModalConfirm{$value["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите отредактировать сотрудника? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmEditLead" data-order="{$value["id"]}" data-dismiss="modal">Да, внести изменения</button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$value["id"]}" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;
    $outModalsConfirmCall .= <<<EOF
<div class="modal fade" id="modalCall{$value["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Когда Вы хотите перезвонить Лиду? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table>
            <tbody>
                <tr>
                    <td>
                        Дата и время звонка:
                    </td>
                    <td>
                        <div class='input-group date' id='datetimepicker{$value["id"]}'>
                          <input type='text' class="form-control" id="callTime{$value["id"]}" value="" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                          <script type="text/javascript">
                            $(function () {
                              $('#callTime{$value["id"]}').datetimepicker({
                                locale: 'ru'
                              });
                            });
                          </script>
                    </td>
                </tr>
                <tr>
                    <td>
                        ФИО
                    </td>
                    <td>
                        <div class="input-label">
                            <input id="callName{$value["id"]}" type="text" class="form-control"
                                   value="{$value["lastName"]} {$value["firstName"]} {$value["patronymicName"]}" placeholder="">
                            <span class="line-input"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Номер телефона
                    </td>
                    <td>
                        <div class="input-label">
                            <input id="callPhone{$value["id"]}" type="text" class="form-control phone"
                                   value="{$value["phone"]} {$value["phone_2"]} {$value["phone_3"]}" placeholder="">
                            <span class="line-input"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        По какому вопросу Вы хотите позвонить Лиду?
                    </td>
                    <td>
                        <textarea id="callText{$value["id"]}" class="form-control"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmSetCall" data-order="{$value["id"]}" data-dismiss="modal">Добавить в звонки</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

    $outModalsConfirmCreateCustomerByLead .= <<<EOF
<div class="modal fade" id="myModalConfirmCreateCustomerByLead{$value["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите перенести лида в заказчики? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmCreateCustomerByLeadNewCompany" data-order="{$value["id"]}" data-dismiss="modal">Да, внести изменения</button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalCreateCustomerByLead{$value["id"]}" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

    $deleteWorkerModalWindow .= <<<EOF
<div class="modal fade" id="myModalDelete{$value["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите удалить сотрудника? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default deleteLead" data-order="{$value["id"]}" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;






    $outModalsCreateCustomerByLead .= <<<EOF
<div class="modal fade" id="myModalCreateCustomerByLead{$value["id"]}" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                Создание нового заказчика из лида <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr id='company{$value["id"]}'>
                            <td>Компания: </td>
                            <td>
                                <select id="companyNameCreateCustomerByLead{$value["id"]}" class="form-control companyNameCreateCustomerByLead" data-order="{$value["id"]}" name="company">
                                    {$outputCompany}
                                </select>
                            </td>
                            <td>
                            </td>
                        </tr>
                      <tr id='companyName{$value["id"]}'>
                            <td>
                                Название компании:
                            </td>
                            <td>
                                <input id="newCompanyNameCreateCustomerByLead{$value["id"]}" type="text" class="form-control" value="" placeholder="">
                            </td>
                        </tr>
                        <tr>
                            <td>Фамилия: </td>
                            <td>
                                <div class="input-label">
                                    <input id="lastNameCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="lastName" value="{$value['lastName']}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Имя: </td>
                            <td>
                                <div class="input-label">
                                    <input id="firstNameCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="name" value="{$value['firstName']}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>Отчество: </td>
                            <td>
                                <div class="input-label">
                                    <input id="patronymicNameCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="patronymic" value="{$value['patronymicName']}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Город: </td>
                            <td>
                                <div class="input-label">
                                    <input id="cityCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="street" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Улица: </td>
                            <td>
                                <div class="input-label">
                                    <input id="streetCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="street" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Дом: </td>
                            <td>
                                <div class="input-label">
                                    <input id="buildCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="build" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Квартира: </td>
                            <td>
                                <div class="input-label">
                                    <input id="apartmentCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="apartment" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Номер телефона: </td>
                            <td>
                                <div class="input-label">
                                    <input id="phoneCreateCustomerByLead{$value["id"]}" type="text" class="form-control phone" name="phoneNumber" value="{$value['phone']}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Номер телефона 2: </td>
                            <td>
                                <div class="input-label">
                                    <input id="phone2CreateCustomerByLead{$value["id"]}" type="text" class="form-control phone" name="phoneNumber" value="{$value['phone_2']}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Номер телефона 3: </td>
                            <td>
                                <div class="input-label">
                                    <input id="phone3CreateCustomerByLead{$value["id"]}" type="text" class="form-control phone" name="phoneNumber" value="{$value['phone_3']}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Email: </td>
                            <td>
                                <div class="input-label">
                                    <input id="emailCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="email" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Сайт: </td>
                            <td>
                                <div class="input-label">
                                    <input id="urlCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="url" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Реквизиты: </td>
                            <td>
                                <div class="input-label">
                                    <input id="bankDetailsCreateCustomerByLead{$value["id"]}" type="text" class="form-control" name="bankDetails" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Откуда узнали о нас: </td>
                            <td>
                                <select id="fromCreateCustomerByLead{$value["id"]}" class="form-control" name="fromWhom">
                                    {$outputFrom}
                                </select>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmCreateCustomerByLead{$value["id"]}" data-dismiss="modal">Редактировать</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
EOF;
}
echo($outModalsEdit . $outModalsConfirmEdit . $outModalsConfirmCall . $deleteWorkerModalWindow . $outModalsCreateCustomerByLead . $outModalsConfirmCreateCustomerByLead);
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
           <?php require_once(APP_DIR_INC . "quickLeft.php"); ?>
        </div>
        <div class="col-md-10">
            <h2 class="min-h2">Лиды</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newLead" aria-haspopup="true" aria-expanded="true">
                Добавление лида&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table id="example" class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th style="width: 120px">Телефон</th>
                        <th>Комментарий</th>
                        <th>Кто добавил</th>
                        <th style="width: 120px">Когда Добавил</th>
                        <th>Редактирование/Удаление</th>
                        <th>Перенести в заказчики</th>
                        <th>Перезвонить</th>
                    </tr>
                </thead>
                <tbody>
<?php
print($outTable);
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="newLead" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Добавление нового лида <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Фамилия: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newLeadLastName" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr><td>Имя: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newLeadFirstName" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr><td>Отчество: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newLeadPatronymicName" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr><td>Телефон: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newLeadPhone" type="text" class="form-control phone" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr><td>Телефон 2: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newLeadPhone2" type="text" class="form-control phone" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr><td>Телефон 3: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newLeadPhone3" type="text" class="form-control phone" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr><td>Комментарий: </td><td><textarea class="form-control" id="newLeadComment"></textarea></td><td></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="addNewLead">Добавить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>

    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>