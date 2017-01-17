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

$subCategory = R::getAll("SELECT dashboard_productsSubCategory.id, dashboard_productsSubCategory.name AS subCategory, dashboard_productsCategory.name AS category, dashboard_productsCategory.id AS categoryId, dashboard_users.dashboard_users_name AS byWhomAdding FROM dashboard_productsSubCategory
INNER JOIN dashboard_productsCategory ON dashboard_productsCategory.id = dashboard_productsSubCategory.categoryId
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = dashboard_productsSubCategory.byWhomAdding");

$category = R::getAll("SELECT * FROM dashboard_productsCategory");

$outTable = "";

$outModalsEdit = "";
$outProductCategoryForAdding = "<option value='0' selected>Выберите категорию</option>";
foreach ($category as $value)
{
    $outProductCategoryForAdding .= "<option value='{$value['id']}'>{$value['name']}</option>";
}

$outProductCategoryForEdit = "";
$outProductsSubCategoryForEdit = "";
$outModalsConfirmEdit = "";

$deleteSubCategoryModalWindow = "";

foreach ($subCategory as $subCategoryValue)
{
    $outProductCategoryForEdit = "";
    
    foreach ($category as $categoryValue)
    {
        if ($categoryValue['id'] == $subCategoryValue['categoryId'])
        {
            $outProductCategoryForEdit .= "<option value='{$categoryValue['id']}' selected>{$categoryValue['name']}</option>";
        }
        else
        {
            $outProductCategoryForEdit .= "<option value='{$categoryValue['id']}'>{$categoryValue['name']}</option>";
        }
    }
    $outTable .= <<<EOF
  <tr>
    <td>{$subCategoryValue["id"]}</td>
    <td>{$subCategoryValue["subCategory"]}</td>
    <td>{$subCategoryValue["category"]}</td>
    <td>{$subCategoryValue["byWhomAdding"]}</td>
    <td>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$subCategoryValue["id"]}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalDelete{$subCategoryValue["id"]}" name="button"><i class="fa fa-times" aria-hidden="true"></i></i></button>
    </td>
  </tr>
EOF;
    $outModalsEdit .= <<<EOF
<div class="modal fade" id="myModal{$subCategoryValue["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Информация о подкатегории<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table">
          <tbody>
                        <tr>
                            <td>Выберите подкатегорию: </td>
                            <td>
                                <div class="input-label">
                                    <select id="editProductSubCategoryId{$subCategoryValue["id"]}" type="text" class="form-control" placeholder="">
                                        {$outProductCategoryForEdit}?>
                                    </select>
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
              <tr><td>Название: </td><td><input type="text" class="form-control" id="editProductSubCategoryName{$subCategoryValue["id"]}" value="{$subCategoryValue["subCategory"]}"></td><td></td></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirm{$subCategoryValue["id"]}" data-dismiss="modal">Редактировать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>

  </div>
</div>
EOF;

    $outModalsConfirmEdit .= <<<EOF
<div class="modal fade" id="myModalConfirm{$subCategoryValue["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите отредактировать подкатегорию продукта?<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmEditProductSubCategory" data-order="{$subCategoryValue["id"]}" data-dismiss="modal">Да, внести изменения</button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$subCategoryValue["id"]}" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;


    $deleteSubCategoryModalWindow .= <<<EOF
<div class="modal fade" id="myModalDelete{$subCategoryValue["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите удалить подкатегорию?<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default deleteProductSubCategory" data-order="{$subCategoryValue["id"]}" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>

  </div>
</div>
EOF;
}
print($outModalsEdit);
print($outModalsConfirmEdit);
print($deleteSubCategoryModalWindow);
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="min-h2">Подкатегории продуктов</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newProductSubCategory" aria-haspopup="true" aria-expanded="true">
                Добавление подкатегории&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <a href="/crm/products.php">
                <button class="btn btn-default" type="button">
                    Подкатегории&nbsp;&nbsp;<i class="fa fa-asterisk" aria-hidden="true"></i>
                </button>
            </a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
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


<div class="modal fade" id="newProductSubCategory" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                Добавление новой подкатегории<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Выберите подкатегорию: </td>
                            <td>
                                <div class="input-label">
                                    <select id="newProductSubCategoryId" type="text" class="form-control" placeholder="">
                                        <?php print($outProductCategoryForAdding); ?>
                                    </select>
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Название: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newProductSubCategoryName" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="addNewProductSubCategory">Добавить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>

    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>