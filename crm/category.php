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

$products = R::getAll("SELECT dashboard_productsCategory.id, dashboard_productsCategory.name, dashboard_users.dashboard_users_name AS byWhomAdding FROM dashboard_productsCategory INNER JOIN dashboard_users ON dashboard_productsCategory.byWhomAdding = dashboard_users.dashboard_users_id");


$outTable = "";

$outModalsEdit = "";
$outModalsConfirmEdit = "";

$deleteCategoryModalWindow = "";

foreach ($products as $value)
{
    $outTable .= <<<EOF
  <tr>
    <td>{$value["id"]}</td>
    <td>{$value["name"]}</td>
    <td>{$value["byWhomAdding"]}</td>
    <td>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$value["id"]}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalDelete{$value["id"]}" name="button"><i class="fa fa-times" aria-hidden="true"></i></i></button>
    </td>
  </tr>
EOF;
    $outModalsEdit .= <<<EOF
<div class="modal fade" id="myModal{$value["id"]}" role="dialog">
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
            <th colspan="2"><h4>Информация о категории продуктов</h4></th><th width="95"></th>
          </tr>
          </thead>
          <tbody>
              <tr><td>Название: </td><td><input type="text" class="form-control" id="editProductCategoryName{$value["id"]}" value="{$value["name"]}"></td><td></td></tr>
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
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
          <tr>
            <th colspan="2"><h4>Действительно хотите отредактировать категорию продукта?</h4></th><th width="95"></th>
          </tr>
          </thead>
          <tbody>
              <tr>
                  <td>
<button type="button" class="btn btn-info confirmEditProductCategory" data-order="{$value["id"]}" data-dismiss="modal">Да, внести изменения</button>
<button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal{$value["id"]}" data-dismiss="modal">Закрыть</button>
                  </td>
              </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
EOF;


    $deleteCategoryModalWindow .= <<<EOF
<div class="modal fade" id="myModalDelete{$value["id"]}" role="dialog">
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
            <th colspan="2"><h4>Действительно хотите удалить категорию?</h4></th><th width="95"></th>
          </tr>
          </thead>
          <tbody>
              <tr>
                  <td>
<button type="button" class="btn btn-info deleteProductCategory" data-order="{$value["id"]}" data-dismiss="modal">Удалить</button>
<button type="button" class="btn btn-info" data-dismiss="modal">Закрыть</button>
                  </td>
              </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
EOF;
}
print($outModalsEdit);
print($outModalsConfirmEdit);
print($deleteCategoryModalWindow);
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="min-h2">Категории продуктов</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newProductCategory" aria-haspopup="true" aria-expanded="true">
                Добавление категории&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <a href="/crm/subCategory.php">
                <button class="btn btn-default" type="button">
                    Подкатегории&nbsp;&nbsp;<i class="fa fa-asterisk" aria-hidden="true"></i>
                </button>
            </a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Кто добавил</th>
                        <th>Редактирование/Удаление</th>
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


<div class="modal fade" id="newProductCategory" role="dialog">
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
                            <th colspan="2"><h4>Добавление новой категории</h4></th><th width="95"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Название: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newProductCategoryName" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal" id="addNewProductCategory">Добавить</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Закрыть</button>
            </div>
        </div>

    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>