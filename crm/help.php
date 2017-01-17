<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Помощь</h2>
            <h4>Здесь Вы найдете ответы на все вопросы, если чего-то здесь нет, спросите у Владислава</h4>
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-3">
                  <div id="menu-help">
                    <h3>Супервайзер</h3>
                    <ul>
                      <li><span class="link" data-link="supervisor">Управление дневником супервайзера</span></li>
                    </ul>
                    <h3>Дизайнер</h3>
                    <ul>
                      <li><span class="link" data-link="supervisor">Стол заказов</span></li>
                      <li><span class="link" data-link="supervisor">Схема работы в программе</span></li>
                    </ul>
                    <h3>Менеджер</h3>
                    <ul>
                      <li><span class="link" data-link="supervisor">Стол заказов</span></li>
                      <li><span class="link" data-link="supervisor">Лиды</span></li>
                      <li><span class="link" data-link="supervisor">Продукты</span></li>
                      <li><span class="link" data-link="supervisor">Заказы</span></li>
                      <li><span class="link" data-link="supervisor">Счета</span></li>
                    </ul>
                    <h3>Общее</h3>
                    <ul>
                      <li><span class="link" data-link="supervisor">Управление компаниями и клиентами</span></li>
                      <li><span class="link" data-link="supervisor">Champ Disk</span></li>
                      <li><span class="link" data-link="supervisor">Зарплата</span></li>
                      <li><span class="link" data-link="supervisor">Настройки</span></li>
                    </ul>
                  </div>
                </div>
                <div class="col-md-9">
                  <div id="result-help">

                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>