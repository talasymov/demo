<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
if (isset($_COOKIE["login_user"]) && $_COOKIE["login_user"] != null)
{
  if($_COOKIE["permission"] == "supervisor")
  {
    header("Location: /crm/diary.php");
  }
  else if($_COOKIE["permission"] == "designer")
  {
    header("Location: /crm/ordersDesigner.php");
  }
  else if($_COOKIE["permission"] == "director" || $_COOKIE["permission"] == "manager")
  {
    header("Location: /crm/leads.php");
  }
  else if($_COOKIE["permission"] == "webdeveloper")
  {
      header("Location: /crm/leads.php");
  }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Вход в CRM</title>
        <link rel="stylesheet" href="/css/login.css" media="screen" type="text/css" />
        <script src="http://momentjs.com/downloads/moment-with-locales.js"></script>
        <script src="/js/libs/jquery-1.12.4.min.js" charset="utf-8"></script>
        <script src="/js/main.js" charset="utf-8"></script>
        <script src="/js/jquery.dataTables.min.js" charset="utf-8"></script>
    </head>
    <body>
        <div id="login-form">
            <h1>Авторизация в CRM CHAMP</h1>
            <fieldset>
                <form action="javascript:void(0);" method="get">
                    <input id="login" type="text" required value="Логин" onBlur="if (this.value == '')
                            this.value = 'Логин'" onFocus="if (this.value == 'Логин')
                                        this.value = ''">
                    <input id="password" type="password" required value="Пароль" onBlur="if (this.value == '')
                            this.value = 'Пароль'" onFocus="if (this.value == 'Пароль')
                                        this.value = ''">
                    <input type="submit" value="ВОЙТИ" onclick="submitForm()">
                    <footer class="clearfix">
                        <!-- <p><span class="info">?</span><a href="#">Забыли пароль?</a></p> -->
                    </footer>
                </form>
            </fieldset>
        </div>
    </body>
</html>
