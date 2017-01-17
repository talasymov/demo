<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
if(!isset($_COOKIE["userId"]))
{
  header("Location: /crm/");
}
$notification = R::getAll("SELECT * FROM dashboard_notifications WHERE dashboard_notifications_whoadd = ? AND dashboard_notifications_delete = 0", [$_COOKIE["userId"]]);
$users = R::getAll("SELECT * FROM dashboard_users");
$notificationLi = "";
$listUsers = "";
$notificationButton = "default";
$countNotification = count($notification);


foreach($users as $key => $value)
{
    $userName = $value["dashboard_users_name"];
    $userId = $value["dashboard_users_id"];

    if($userId == $_COOKIE["userId"])
    {
        $listUsers .= "<option value=\"{$userId}\" selected>{$userName}</option>";
    }
    else
    {
        $listUsers .= "<option value=\"{$userId}\">{$userName}</option>";
    }
}

foreach($notification as $key => $value)
{
    $notificationId = $value["dashboard_notifications_id"];
    $notificationDate = $value["dashboard_notifications_date"];
    $notificationText = $value["dashboard_notifications_text"];

    $notificationLi .= "<li><a href='#'><i class='fa fa-times delete-notification' data-id='{$notificationId}' aria-hidden='true'></i> <span class='label label-primary'>{$notificationDate}</span> <span class='label label-default'>{$notificationText}</span></a></li>";
}

if($countNotification > 0)
{
    $notificationButton = "primary";
}

$buttonNotification = <<<EOF
<div id="notification-list" class="btn-group">
    <button type="button" class="btn btn-{$notificationButton} dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><i class="fa fa-bell" aria-hidden="true"></i> <span class="badge">
    {$countNotification}
    </span></button>
    <ul class="dropdown-menu">
    {$notificationLi}
    </ul>
</div>
<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="modal" data-target="#newNotification"
aria-haspopup="true" aria-expanded="true">
<i class="fa fa-plus"></i>
</button>
EOF;
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>Champ CRM</title>

        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/font-awesome.css">
        <link rel="stylesheet" href="/libs/bootstrap/bootstrap-sweane.css">
        <link rel="stylesheet" href="/css/bootstrap-datetimepicker.css">
        <link rel="stylesheet" type="text/css" href="/libs/semantic/dist/semantic.min.css">
        <link rel="stylesheet" type="text/css" href="/libs/select/css/select2.css">

        <script src="/js/libs/jquery-1.12.4.min.js" charset="utf-8"></script>

        <link rel="icon" type="image/png" href="/crm/favi-01.png">
    </head>
    <body>
      <script type="text/javascript">
      $(document).ready(function() {
        $("select").select2({width: "100%"});
      });
      </script>
      <script type="text/javascript">
          jQuery(function($){
              $(".phone").mask("(999) 999 99 99");
          });
      </script>
       <nav class="navbar navbar-default">
          <div class="bbg"></div>
            <div class="container-fluid pr-2">
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars menu-sweane-bar" style="font-size: 2rem" aria-hidden="true"></i></a>
                            <?php print($buttonNotification); ?>
                            <ul class="dropdown-menu">
                                <li><a href="/crm/clients.php"><i class="fa fa-user" aria-hidden="true"></i> Клиенты</a></li>
                                <li><a href="/crm/companys.php"><i class="fa fa-user" aria-hidden="true"></i> Компании</a></li>
                                <?php if($_COOKIE["permission"] == "admin" || $_COOKIE["permission"] == "director" || $_COOKIE["permission"] == "webdeveloper" ){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/infoAboutStartTime.php"><i class="fa fa-clock-o" aria-hidden="true"></i> Время работы</a></li>
                                    <li><a href="/crm/finance.php"><i class="fa fa-usd" aria-hidden="true"></i> Финансы</a></li>
                                    <li><a href="/crm/calls.php"><i class="fa fa-phone" aria-hidden="true"></i> Звонки</a></li>
                                <?php } ?>
                                <?php if($_COOKIE["permission"] == "manager" || $_COOKIE["permission"] == "webdeveloper" || $_COOKIE["permission"] == "admin" || $_COOKIE["permission"] == "director" || $_COOKIE["permission"] == "supervisor"){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/leads.php"><i class="fa fa-bell" aria-hidden="true"></i> Лид-менеджмент</a></li>
                                    <li><a href="/crm/statisticAllLeads.php"><i class="fa fa-bell" aria-hidden="true"></i> Статистика лидов</a></li>
                                <?php } ?>
                                <?php if($_COOKIE["permission"] == "admin" || $_COOKIE["permission"] == "webdeveloper" || $_COOKIE["permission"] == "director" || $_COOKIE["permission"] == "manager"){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/ordersDesigner.php"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Стол заказов дизайнеров</a></li>
                                    <li><a href="/crm/managerofmakets.php"><i class="fa fa-folder-open" aria-hidden="true"></i> Менеджер макетов</a></li>
                                    <li><a href="/crm/showallmaketsfordirector.php"><i class="fa fa-folder-open" aria-hidden="true"></i> Занятость работников</a></li>
                                <?php } ?>
                                <?php if($_COOKIE["permission"] == "designer"){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/ordersDesigner.php"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Стол заказов дизайнеров</a></li>
                                    <li><a href="/crm/managerofmakets.php"><i class="fa fa-folder-open" aria-hidden="true"></i> Менеджер макетов</a></li>
                                    <li><a href="/crm/ordersDesignerMe.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Мои заказы</a></li>
                                <?php } ?>
                                <?php if($_COOKIE["permission"] == "designer"){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/viewProductOrders.php"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Заказы</a></li>
                                <?php } ?>
                                <?php if($_COOKIE["permission"] == "manager" || $_COOKIE["permission"] == "webdeveloper" || $_COOKIE["permission"] == "admin" || $_COOKIE["permission"] == "director"){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/viewProductOrders.php"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Заказы</a></li>
                                    <li><a href="/crm/createOrder.php"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Новый заказ</a></li>
                                <?php } ?>
                                <?php if($_COOKIE["permission"] == "manager" || $_COOKIE["permission"] == "webdeveloper" || $_COOKIE["permission"] == "admin" || $_COOKIE["permission"] == "director"){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/category.php"><i class="fa fa-bell" aria-hidden="true"></i> Категории</a></li>
                                    <li><a href="/crm/subCategory.php"><i class="fa fa-bell" aria-hidden="true"></i> Подкатегории</a></li>
                                    <li><a href="/crm/products.php"><i class="fa fa-bell" aria-hidden="true"></i> Продукты</a></li>
                                <?php } ?>
                                <?php if($_COOKIE["permission"] == "supervisor" || $_COOKIE["permission"] == "webdeveloper" || $_COOKIE["permission"] == "admin" || $_COOKIE["permission"] == "director"){ ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/diary.php"><i class="fa fa-database" aria-hidden="true"></i> Дневник супервайзера</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/workers.php"><i class="fa fa-user" aria-hidden="true"></i> Работники</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/crm/statisticWorkers.php"><i class="fa fa-pie-chart" aria-hidden="true"></i> Отчеты по работникам</a></li>
                                    <li><a href="/crm/statisticSupervisors.php"><i class="fa fa-pie-chart" aria-hidden="true"></i> Отчеты по супервайзерам</a></li>
                                    <li><a href="/crm/statisticCustomers.php"><i class="fa fa-pie-chart" aria-hidden="true"></i> Отчеты по заказчикам</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                    <div id="start-end-button">

                        <?php
                        $userDay = R::getRow("SELECT * FROM basic_day_registration WHERE DATE(basic_day_registration_date_start) = ? AND basic_day_registration_who = ?", [date("Y-m-d"), $_COOKIE["userId"]]);

                        if (isset($userDay["basic_day_registration_id"]) && $userDay["basic_day_registration_date_start"] != "1970-10-10 00:00:00" && $userDay["basic_day_registration_date_stop"] != "1970-10-10 00:00:00")
                        {
                            $startTime = strtotime($userDay["basic_day_registration_date_start"]);
                            $endTime = strtotime($userDay["basic_day_registration_date_stop"]);

                            $allTime = $endTime - $startTime;

                            echo "Вы всего проработали: ", (int) ($allTime / 3600), " часов ", ($allTime / 60 % 60), " минут ", ($allTime % 60), " секунд";
                        }
                        else if (isset($userDay["basic_day_registration_id"]))
                        {
                            echo <<<EOF
                            <button class="stop buttonDay">Стоп</button>
                            <button class="btnDay" data-toggle="tooltip" data-placement="bottom" data-trigger="click" data-html="true" data-title="Вы начали свой рабочий день<br />{$userDay["basic_day_registration_date_start"]}">
                              <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </button>
                            Удачного рабочего дня! Я в тебя верю :)

EOF;
                        }
                        else
                        {
                            echo "<button class=\"start buttonDay\">Старт</button> Ты еще не начал рабочий день?";
                        }
                        ?>
                    </div>
                    <ul class="nav navbar-nav navbar-right pr-a">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user" aria-hidden="true"></i> <?php
                                echo $_COOKIE["name_user"];
                                if ($_COOKIE["permission"] == "admin")
                                {
                                    echo " | <span class=\"admin-color\">Администратор</span>";
                                }
                                ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="/crm/settings.php"><i class="fa fa-cog" aria-hidden="true"></i> Настройки</a></li>
                                <?php if($_COOKIE["permission"] == "director"){ ?>
                                <li><a href="/crm/salaryLast.php"><i class="fa fa-usd" aria-hidden="true"></i> Зарплата</a></li>
                                <?php } ?>
                                <!-- <li><a href="/crm/salary.php"><i class="fa fa-usd" aria-hidden="true"></i> Зарплата</a></li> -->
                                <li><a href="/crm/statistic.php"><i class="fa fa-pie-chart" aria-hidden="true"></i> Статистика</a></li>
                                <li><a href="/disk/" target="_blank"><i class="fa fa-cloud-download" aria-hidden="true"></i> Champ Disk</a></li>
                                <li><a href="/crm/help.php"><i class="fa fa-info-circle" aria-hidden="true"></i> Помощь</a></li>
                                <li><a href="/crm/offers.php"><i class="fa fa-life-ring" aria-hidden="true"></i> Предложения</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#" onclick="logout()"><i class="fa fa-sign-out" aria-hidden="true"></i> Выход</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
<div class="modal fade" id="whatsNew" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        Что нового в Champ CRM 1.1 - 21.12.2016?
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <h4>Вот несколько нововведений!</h4>
        <ul>
            <li>Исправлены ошибки при редактировании компаний.</li>
            <li>Добавлена возможность сортировки лидов и заказов по статусу (При нажатии на заголовок).</li>
            <li>Добавлено дублирование заказа.</li>
            <li>Добавлены статусы у заказа.</li>
            <li>Исправлена ошибка при просмотре счета. Сумма выводилась с дефектами.</li>
            <li>Добавлены Финансы.</li>
            <li>Добавлены Звонки.</li>
            <li>Добавлена возможность редактировать заказ в Стол заказов дизайнеров.</li>
            <li>Отображение старта рабочего дня вверху.</li>
            <li>Добавлена кнопка печати счета.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal11" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="newNotification" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              Добавить напоминание
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
              <table class="table">
                  <tbody>
                  <tr>
                      <td>Дата и время</td>
                      <td>
                          <div class='input-group date' id='timeNewNotification'>
                              <input type='text' class="form-control" id="inputTimeNewNotification" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          </div>
                          <script type="text/javascript">
                              $(function () {
                                  $('#inputTimeNewNotification').datetimepicker({
                                      locale: 'ru'
                                  });
                              });
                          </script>
                      </td>
                  </tr>
                  <tr>
                      <td>Напоминание</td>
                      <td><textarea name="" id="textNewNotification" class="form-control"></textarea>
                  </tr>
                  <?php
                    if($_COOKIE["permission"] == "director")
                    {
                        $print = <<<EOF
                            <tr>
                              <td>Для кого?</td>
                              <td><select id="forPeople">{$listUsers}</select></td>
                            </tr>
EOF;
                        print($print);
                    }
                  ?>
                  </tbody>
              </table>
          </div>
          <div class="modal-footer">
              <button type="button" id="addNewNotification" class="btn btn-default" data-toggle="modal" data-dismiss="modal">Добавить</button>
              <button type="button" class="btn btn-default" data-toggle="modal" data-dismiss="modal">Закрыть</button>
          </div>
      </div>
  </div>
</div>
<script>
  $(function () {
      $("[data-toggle='tooltip']").tooltip();
  });
</script>