<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$people = R::getRow("SELECT * FROM dashboard_users WHERE dashboard_users_id = ?", [$_COOKIE["userId"]]);

$out = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Настройка аккаунта</h2>
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-6">
                  <table id="setting-people">
                    <tr>
                      <td>Логин</td>
                      <td><div class="input-label">
                          <input id="newOrder_name_login" type="text" class="form-control" name="name" value="{$people["dashboard_users_login"]}" placeholder="">
                          <span class="line-input"></span>
                      </div></td>
                    </tr>
                    <tr>
                      <td>Пароль</td>
                      <td><div class="input-label">
                          <input id="newOrder_name_password" type="password" class="form-control" name="name" value="{$people["dashboard_users_password"]}" placeholder="">
                          <span class="line-input"></span>
                      </div></td>
                    </tr>
                    <tr>
                      <td>ФИО</td>
                      <td><div class="input-label">
                          <input id="newOrder_name_name" type="text" class="form-control" name="name" value="{$people["dashboard_users_name"]}" placeholder="">
                          <span class="line-input"></span>
                      </div></td>
                    </tr>
                    <tr>
                      <td>Дата рождения</td>
                      <td>
                        <script type="text/javascript">
                          $(function () {
                            $('#timeEditOrder1').datetimepicker({
                              format: 'YYYY-MM-DD',
                              locale: 'ru'
                            });
                          });
                        </script>
                        <div class='input-group date' id='datetimepicker1'>
                            <input type='text' class="form-control" id="timeEditOrder1" value="{$people["dashboard_users_date"]}" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>Номер телефона</td>
                      <td><div class="input-label">
                          <input id="newOrder_name_phone" type="text" class="form-control" name="name" value="{$people["dashboard_users_phone"]}" placeholder="">
                          <span class="line-input"></span>
                      </div></td>
                    </tr>
                    <tr>
                      <td>E-mail</td>
                      <td><div class="input-label">
                          <input id="newOrder_name_email" type="text" class="form-control" name="name" value="{$people["dashboard_users_email"]}" placeholder="">
                          <span class="line-input"></span>
                      </div></td>
                    </tr>
                    <tr>
                      <td>Skype</td>
                      <td><div class="input-label">
                          <input id="newOrder_name_skype" type="text" class="form-control" name="name" value="{$people["dashboard_users_skype"]}" placeholder="">
                          <span class="line-input"></span>
                      </div></td>
                    </tr>
                  </table>
                  <br />
                  <br />
                  <button class="btn btn-info dropdown-toggle" id="save-account" type="button" data-toggle="modal" data-target="#modalAddOrderDesigner" aria-haspopup="true" aria-expanded="true">
                      Сохранить&nbsp;&nbsp;<i class="fa fa-cloud-download" aria-hidden="true"></i>
                  </button>
                  <br />
                  <br />
                </div>
                <div class="col-md-6"></div>
              </div>
            </div>
        </div>
    </div>
</div>
EOF;
print($out);
?>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
