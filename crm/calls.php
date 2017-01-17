<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$calls = R::getAll("
SELECT * FROM dashboard_calls
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = dashboard_calls.dashboard_calls_whoadd
ORDER BY dashboard_calls_id DESC
");

$outTable = "";

$outModalsEdit = "";
$outModalsConfirmEdit = "";
$outModalsConfirmCall = "";
$buttonEditCall = "";
$modalCalls = "";

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

foreach ($calls as $key => $value)
{
    $timeAll = $value["dashboard_calls_date"];

    $class = "";

    $timeAllDate = explode(" ", $timeAll);

    $time2explodeDate = explode("-", $timeAllDate[0]);
    $time2explodeTime = explode(":", $timeAllDate[1]);

    $time2 = $time2explodeDate[2] . "." . $time2explodeDate[1] . "." . $time2explodeDate[0] . " " . $time2explodeTime[0] . ":" . $time2explodeTime[1];

    if($timeAllDate[0] == date("Y-m-d"))
    {
        $class = "today";
    }

    $buttonEditCall = <<<EOF
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalEditCall{$value["dashboard_calls_id"]}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
EOF;
    $outTable .= <<<EOF
    <tr class="{$class}">
        <td>{$value["dashboard_calls_id"]}</td>
        <td>{$value["dashboard_calls_date"]}</td>
        <td>{$value["dashboard_calls_name"]}</td>
        <td>{$value["dashboard_calls_comment"]}</td>
        <td>{$value["dashboard_calls_phone"]}</td>
        <td>{$value["dashboard_users_name"]}</td>
        <td>{$buttonEditCall}</td>
    </tr>
EOF;

    $modalCalls .= <<<EOF
    <div class="modal fade" id="modalEditCall{$value["dashboard_calls_id"]}" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            Действительно хотите отредактировать звонок? <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <table>
                <tbody>
                    <tr>
                        <td>
                            Дата и время звонка:
                        </td>
                        <td>
                            <div class='input-group date' id='datetimepicker{$value["dashboard_calls_id"]}'>
                              <input type='text' class="form-control" id="editCallTime{$value["dashboard_calls_id"]}" value="{$time2}" />
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                            </div>
                              <script type="text/javascript">
                                $(function () {
                                  $('#editCallTime{$value["dashboard_calls_id"]}').datetimepicker({
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
                                <input id="editCallName{$value["dashboard_calls_id"]}" type="text" class="form-control"
                                       value="{$value["dashboard_calls_name"]}" placeholder="">
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
                                <input id="editCallPhone{$value["dashboard_calls_id"]}" type="text" class="form-control phone"
                                       value="{$value["dashboard_calls_phone"]}" placeholder="">
                                <span class="line-input"></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            По какому вопросу Вы хотите позвонить Лиду?
                        </td>
                        <td>
                            <textarea id="editCallText{$value["dashboard_calls_id"]}" class="form-control">{$value["dashboard_calls_comment"]}</textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default confirmDeleteCall" data-order="{$value["dashboard_calls_id"]}" data-dismiss="modal">Удалить звонок</button>
            <button type="button" class="btn btn-default confirmEditCall" data-order="{$value["dashboard_calls_id"]}" data-dismiss="modal">Да, внести изменения</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>
EOF;
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once(APP_DIR_INC . "quickLeft.php"); ?>
        </div>
        <div class="col-md-10">
            <h2 class="min-h2">Звонки</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newLead" aria-haspopup="true" aria-expanded="true">
                Добавление звонка&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table id="example" class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Когда перезвонить?</th>
                    <th>ФИО</th>
                    <th>Комментарий</th>
                    <th>Телефон</th>
                    <th>Кто Добавил</th>
                    <th></th>
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
<?php print($modalCalls); ?>
<div class="modal fade" id="newLead" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Когда перезвонить <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table>
                    <tbody>
                    <tr>
                        <td>
                            Дата и время звонка:
                        </td>
                        <td>
                            <div class='input-group date' id='datetimepicker'>
                                <input type='text' class="form-control" id="addCallTime" value="" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                            </div>
                            <script type="text/javascript">
                                $(function () {
                                    $('#addCallTime').datetimepicker({
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
                                <input id="addCallName" type="text" class="form-control"
                                value="" placeholder="">
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
                                <input id="addCallPhone" type="text" class="form-control phone"
                                value="" placeholder="">
                                <span class="line-input"></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            По какому вопросу Вы хотите позвонить Лиду?
                        </td>
                        <td>
                            <textarea id="addCallText" class="form-control"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="addNewCall">Добавить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>