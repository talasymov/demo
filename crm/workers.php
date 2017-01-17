 <?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

//$company = R::getAll("SELECT * FROM dashboard_company");
//$clients = R::getAll("SELECT * FROM dashboard_clients");
//$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");

//update workers status if it is need now

//get worker and him/her status
$workers = R::getAll("SELECT dashboard_workers.id, dashboard_workers_patronymic, dashboard_workers_name, dashboard_workers_surname, dashboard_workers_phone, dashboard_workers_promoterLevel, dashboard_workers_stickerLevel, penaltyCount FROM dashboard_workers
");
$td = "";
$outModals = "";
$outModalsConfirmEdit = "";
$deleteWorkerModalWindow = "";
$outModalsPenalty = "";

foreach ($workers as $key => $value)
{

    $statusWorker = "Свободен";

    $countOrdersThisWorker = count(R::getRow("SELECT * FROM diary_orders WHERE workerId = ? AND status <> 4 AND status <> 5", [$value["id"]]));

    if($countOrdersThisWorker > 0)
    {
      $statusWorker = "Занят";
    }
    $td .= <<<EOF
  <tr>
    <td>{$value["id"]}</td>
    <td>{$value["dashboard_workers_patronymic"]}</td>
    <td>{$value["dashboard_workers_name"]}</td>
    <td>{$value["dashboard_workers_surname"]}</td>
    <td>{$value["dashboard_workers_phone"]}</td>
    <td>{$value["dashboard_workers_promoterLevel"]}</td>
    <td>{$value["dashboard_workers_stickerLevel"]}</td>
    <td>{$statusWorker}</td>
    <td>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$key}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalDelete{$key}" name="button"><i class="fa fa-times" aria-hidden="true"></i></i></button>
    </td>
    <td>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalPenalty{$key}" name="button"><i class="fa fa-bomb" aria-hidden="true"></i></button>
    </td>
        <td>
        {$value["penaltyCount"]}
        </td>
  </tr>
EOF;
    $outModals .= <<<EOF
<div class="modal fade" id="myModal{$key}" role="dialog">
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
            <th colspan="2"><h4>Информация о работнике</h4></th><th width="95"></th>
          </tr>
          </thead>
          <tbody>
              <tr><td>Фамилия: </td><td><input type="text" class="form-control" id="editpartonymic{$value["id"]}" value="{$value["dashboard_workers_patronymic"]}"></td><td></td></tr>
              <tr><td>Имя: </td><td><input type="text" class="form-control" id="editName{$value["id"]}" value="{$value["dashboard_workers_name"]}"></td><td></td></tr>
              <tr><td>Отчество: </td><td><input type="text" class="form-control" id="editSurname{$value["id"]}" value="{$value["dashboard_workers_surname"]}"></td><td></td></tr>
              <tr><td>Номер телефона: </td><td><input type="text" class="form-control" id="editPhone{$value["id"]}" value="{$value["dashboard_workers_phone"]}"></td><td></td></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirm{$key}" data-dismiss="modal">Редактировать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>

  </div>
</div>
EOF;

            $outModalsConfirmEdit .= <<<EOF
<div class="modal fade" id="myModalConfirm{$key}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите отредактировать сотрудника? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmEdit" data-order="{$value["id"]}" data-dismiss="modal">Да, внести изменения</button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$key}" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

            $outModalsPenalty .= <<<EOF
<div class="modal fade" id="myModalPenalty{$key}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите оштрафовать сотрудника {$value["dashboard_workers_patronymic"]} {$value["dashboard_workers_name"]} {$value["dashboard_workers_surname"]}? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmPenalty" data-order="{$value["id"]}" data-dismiss="modal">Да, оштрафовать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

    $deleteWorkerModalWindow .= <<<EOF
<div class="modal fade" id="myModalDelete{$key}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите удалить сотрудника? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default deleteWorker" data-order="{$value["id"]}" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;
}
print($outModals);
print($deleteWorkerModalWindow);
print($outModalsConfirmEdit);
print($outModalsPenalty);
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="min-h2">Работники</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newWorker" aria-haspopup="true" aria-expanded="true">
                Добавление работника&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Телефон</th>
                        <th>Рейтинг промоутера</th>
                        <th>Рейтинг расклейщика</th>
                        <th>Статус</th>
                        <th>Редактирование/Удаление</th>
                        <th>Оштрафовать</th>
                        <th>Количество штрафов</th>
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
<div class="modal fade" id="newWorker" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                Добавление нового работника <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Фамилия: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newOrder_surname_worker" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr><td>Имя: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newOrder_name_worker" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr><td>Отчество: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newOrder_patronymic_worker" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr><td>Телефон: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newOrder_phone_worker" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="addNewWorker">Добавить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>

    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
