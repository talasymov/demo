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

$categoryAddNewProduct = R::getCell("SELECT id FROM dashboard_productsCategory");
$subCategoryAddNewProduct = R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = {$categoryAddNewProduct}");

$categoryAddNewProduct = R::getAll("SELECT * FROM dashboard_productsCategory");

$outProductSubCategoryForAdding = "";

foreach ($subCategoryAddNewProduct as $valuesubCategoryAddNewProduct)
{
    $outProductSubCategoryForAdding .= "<option value='{$valuesubCategoryAddNewProduct['id']}'>{$valuesubCategoryAddNewProduct['name']}</option>";
}

$outProductCategoryForAdding = "";

foreach ($categoryAddNewProduct as $valueCategoryAddNewProduct)
{
    $outProductCategoryForAdding .= "<option value='{$valueCategoryAddNewProduct['id']}'>{$valueCategoryAddNewProduct['name']}</option>";
}

$products = R::getAll("SELECT dashboard_products.id, dashboard_products.name AS nameProduct, dashboard_productsCategory.id AS productCategoryId, dashboard_productsCategory.name AS nameProductCategory, dashboard_productsSubCategory.id AS productSubCategoryId, dashboard_productsSubCategory.name AS nameProductSubCategory, dashboard_users.dashboard_users_name AS byWhomAdding FROM dashboard_products
INNER JOIN dashboard_productsCategory ON dashboard_products.categoryId = dashboard_productsCategory.id
INNER JOIN dashboard_productsSubCategory ON dashboard_products.subCategoryId = dashboard_productsSubCategory.id
INNER JOIN dashboard_users ON dashboard_products.byWhomAdding = dashboard_users.dashboard_users_id");

$outTable = "";

$outModalsEdit = "";


$outProductCategoryForEdit = "";
$outProductSubCategoryForEdit = "";



$outModalsConfirmEdit = "";

$deleteSubCategoryModalWindow = "";




foreach ($products as $valueProducts)
{
    $outProductCategoryForEdit = "";
    $outProductSubCategoryForEdit = "";







    $categoryEditProduct = R::getCell("SELECT id FROM dashboard_productsCategory");
    $subCategoryEditProduct = R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = {$categoryEditProduct}");

    $categoryEditProduct = R::getAll("SELECT * FROM dashboard_productsCategory");

    foreach ($categoryEditProduct as $valueCategoryEditProduct)
    {
        if ($valueCategoryEditProduct['id'] == $valueProducts['productCategoryId'])
        {
            $outProductCategoryForEdit .= "<option value='{$valueCategoryEditProduct['id']}' selected>{$valueCategoryEditProduct['name']}</option>";
        }
        else
        {
            $outProductCategoryForEdit .= "<option value='{$valueCategoryEditProduct['id']}'>{$valueCategoryEditProduct['name']}</option>";
        }
    }

    $subCategoryEditProduct = R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = {$valueProducts['productCategoryId']}");
    
    foreach ($subCategoryEditProduct as $valueSubCategoryEditProduct)
    {
        if ($valueSubCategoryEditProduct['id'] == $valueProducts['productSubCategoryId'])
        {
            $outProductSubCategoryForEdit .= "<option value='{$valueSubCategoryEditProduct['id']}' selected>{$valueSubCategoryEditProduct['name']}</option>";
        }
        else
        {
            $outProductSubCategoryForEdit .= "<option value='{$valueSubCategoryEditProduct['id']}'>{$valueSubCategoryEditProduct['name']}</option>";
        }
    }










    $outTable .= <<<EOF
  <tr>
    <td>{$valueProducts["id"]}</td>
    <td>{$valueProducts["nameProduct"]}</td>
    <td>{$valueProducts["nameProductCategory"]}</td>
    <td>{$valueProducts["nameProductSubCategory"]}</td>
    <td>{$valueProducts["byWhomAdding"]}</td>
    <td>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$valueProducts["id"]}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalDelete{$valueProducts["id"]}" name="button"><i class="fa fa-times" aria-hidden="true"></i></i></button>
    </td>
  </tr>
EOF;
    $outModalsEdit .= <<<EOF
<div class="modal fade" id="myModal{$valueProducts["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Информация о продукте<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr>
                <td>Выберите категорию: </td>
                <td>
                    <div class="input-label">
                        <select id="editProductCategoryId{$valueProducts["id"]}" type="text" class="form-control editProductCategoryId" data-order="{$valueProducts["id"]}" placeholder="">
                            {$outProductCategoryForEdit}?>
                        </select>
                        <span class="line-input"></span>
                    </div>
                </td>
            </tr>



            <tr>
                <td>Выберите подкатегорию: </td>
                <td>
                    <div class="input-label">
                        <select id="editProductSubCategoryId{$valueProducts["id"]}" type="text" class="form-control" placeholder="">
                            {$outProductSubCategoryForEdit}?>
                        </select>
                        <span class="line-input"></span>
                    </div>
                </td>
            </tr>

             <tr>
                <td>Название: </td>
                <td>
                    <div class="input-label">
                        <input id="editProductName{$valueProducts["id"]}" type="text" class="form-control" name="name" value="{$valueProducts["nameProduct"]}" placeholder="">
                        <span class="line-input"></span>
                    </div>
                </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirm{$valueProducts["id"]}" data-dismiss="modal">Редактировать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

    $outModalsConfirmEdit .= <<<EOF
<div class="modal fade" id="myModalConfirm{$valueProducts["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите отредактировать подкатегорию продукта?<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmEditProduct" data-order="{$valueProducts["id"]}" data-dismiss="modal">Да, внести изменения</button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{$valueProducts["id"]}" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;


    $deleteSubCategoryModalWindow .= <<<EOF
<div class="modal fade" id="myModalDelete{$valueProducts["id"]}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите удалить подкатегорию?<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default deleteProduct" data-order="{$valueProducts["id"]}" data-dismiss="modal">Удалить</button>
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
            <h2 class="min-h2">Продукты</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newProductSubCategory" aria-haspopup="true" aria-expanded="true">
                Добавление продуктов&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Подкатегория</th>
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
                Добавление нового продукта<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Выберите категорию: </td>
                            <td>
                                <div class="input-label">
                                    <select id="newProductCategoryId" type="text" class="form-control" placeholder="">
                                        <?php print($outProductCategoryForAdding); ?>
                                    </select>
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Выберите подкатегорию: </td>
                            <td>
                                <div class="input-label">
                                    <select id="newProductSubCategoryId" type="text" class="form-control" placeholder="">
                                        <?php print($outProductSubCategoryForAdding); ?>
                                    </select>
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Название: </td>
                            <td>
                                <div class="input-label">
                                    <input id="newProductName" type="text" class="form-control" name="name" value="" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="addNewProduct">Добавить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>

    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>