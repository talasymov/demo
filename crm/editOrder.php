<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$outConfirmNewOrder = "";
$outDensity = "";
$outOrders = "";
$outColor = "";

$density = array("90", "100", "130");
$color = array("4+4", "4+0", "2+2", "2+0");

$idOrderFromGet = $_GET["id"];

$orderGroup = R::getRow("
SELECT *,dashboard_productsOrderGroup.id AS groupId  FROM dashboard_productsOrderGroup

INNER JOIN dashboard_customers ON dashboard_customers.customerId = dashboard_productsOrderGroup.customerId
INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId

WHERE dashboard_productsOrderGroup.id = ?
", [$idOrderFromGet]);

$orders = R::getAll("
SELECT *,dashboard_products.name AS productName, dashboard_productsOrders.id AS idProduct  FROM dashboard_productsOrders

INNER JOIN dashboard_products ON dashboard_products.id = dashboard_productsOrders.productId

WHERE productsOrderGroupId = ?", [$idOrderFromGet]);

$countOrders = count($orders) + 1;

$company = R::getCell("SELECT id FROM dashboard_companies");
$customer = R::getAll("SELECT * FROM dashboard_customers WHERE companyId = {$company}");

$outCustomer = "";
foreach ($customer as $customerValue)
{
    $outCustomer .= "<option value='{$customerValue['customerId']}'>{$customerValue['lastName']} {$customerValue['firstName']} {$customerValue['patronymicName']}</option>";
}

$company = R::getAll("SELECT * FROM dashboard_companies WHERE id IN (SELECT dashboard_customers.companyId FROM dashboard_customers)");
$outCompany = "";

foreach ($company as $companyValue)
{
    $outCompany .= "<option value='{$companyValue['id']}'>{$companyValue['companyName']}</option>";
}

$category = R::getCell("SELECT id FROM dashboard_productsCategory WHERE id IN (SELECT dashboard_productsSubCategory.categoryId FROM dashboard_productsSubCategory)");
$subCategory = R::getCell("SELECT id FROM dashboard_productsSubCategory WHERE categoryId = {$category}");

$product = R::getAll("SELECT * FROM dashboard_products WHERE categoryId = {$category} AND subCategoryId = {$subCategory}");

$outProduct = "";

foreach ($product as $productValue)
{
    $outProduct .= "<option value = {$productValue['id']}>{$productValue['name']}</option>";
}

$subCategory = R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = {$category} AND id IN (SELECT dashboard_products.subCategoryId FROM dashboard_products)");

$category = R::getAll("SELECT * FROM dashboard_productsCategory");

$outSubCategory = "";

foreach ($subCategory as $valueSubCategory)
{
    $outSubCategory .= "<option value='{$valueSubCategory['id']}'>{$valueSubCategory['name']}</option>";
}

$outCategory = "";

foreach ($category as $valueCategory)
{
    $outCategory .= "<option value='{$valueCategory['id']}'>{$valueCategory['name']}</option>";
}

foreach($density as $key => $value)
{
    $outDensity .= "<option value='{$value}'>{$value}</option>";
}

foreach($color as $key => $value)
{
    $outColor .= "<option value='{$value}'>{$value}</option>";
}

$nameOrder = $orderGroup["name"];
$totalSumOfOrder = $orderGroup["totalSumOfOrder"];
$groupId = $orderGroup["groupId"];

$btnIdOrder = $orderGroup["customerId"];
$btnNameClient = $orderGroup["patronymicName"] . " " . $orderGroup["firstName"] . " " . $orderGroup["lastName"];

$button = PrintButtonSelect("Выберите клиента", "companies", "inputSelectClient", $btnIdOrder, $btnNameClient);

foreach($orders as $key => $value)
{
    $buttonProduct = PrintButtonSelectProduct("Выберите продукт", "inputSelectProduct", $value["productId"], $value["productName"], $value["idProduct"]);

    $count = $value["count"];
    $cost = $value["cost"];
    $information = $value["information"];
    $keyPlus = $key + 1;

  $outOrders  .=  <<<EOF
    <tr id="lastProductRowInTable">
        <td>
        {$buttonProduct}
            <!--<select id="selectCategoryProductNewProductOrder{$key}" type="text" class="form-control selectCategoryNewProductOrder" data-order="1">
                {$outCategory}
            </select>-->
        </td>
        <!--<td>
            <select id="selectSubCategoryProductNewProductOrder1" type="text" class="form-control selectSubCategoryNewProductOrder" data-order="1">
                {$outSubCategory}
            </select>
        </td>
        <td>
            <select id="selectProductNewProductOrder1" type="text" class="form-control selectProductNewProductOrder" data-order="1">
                {$outProduct}
            </select>
        </td>-->
        <td>
            <input type="text" id="productTextNewOrder{$keyPlus}" class="form-control productTextNewOrder" value="{$information}" data-order="{$keyPlus}">
        </td>
        <td>
            <input type="text" id="productCountNewOrder{$keyPlus}" class="form-control productCountNewOrder" value="{$count}" data-order="{$keyPlus}">
        </td>
        <td>
            <input type="text" id="productPricePerOneNewOrder{$keyPlus}" class="form-control productPricePerOneNewOrder" value="{$cost}" data-order="{$keyPlus}">
        </td>
    </tr>
EOF;
}

$outConfirmNewOrder .= <<<EOF
<div class="modal fade" id="myModalCreateNewOrder" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите создать заказ? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default confirmEditOrder" data-order="2" totalSum="0" data-dismiss="modal">Создать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

$outBody = <<<EOF
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="min-h2" style="float: none;">Редактирование заказа</h2>
                <table class="table">
                    <tbody>
                    <tr>
                        <td>
                            Название заказа:
                        </td>
                        <td>
                            <input type="text" id="productOrderName" class="form-control" value="{$nameOrder}">
                            <input type="hidden" id="productOrderGroupId" value="{$groupId}">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Заказчик:
                        </td>
                        <td>
                        {$button}

                            <!--<select id="newProductOrderCompany" type="text" class="form-control" placeholder="">
                                {$outCompany}
                            </select>-->
                        </td>
                        <td>
                            <!--<select id="newProductOrderCustomer" type="text" class="form-control" placeholder="">
                                {$outCustomer}
                            </select>-->
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h3 class="min-h2" style="float: none;">Список продуктов</h3>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <td>Выберите продукт</td>
                        <td>Информация о продукте</td>
                        <td>Количество</td>
                        <td>Цена за единицу</td>
                        <td><button type="button" class="btn btn-default btnAddRowNewOrder" data-order='{$countOrders}'>Добавить ещё один продукт</button></td>
                    </tr>
                    </thead>
                    <tbody id="productListTable">
                        {$outOrders}
                    </tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td id="totalSumTd">Итого: {$totalSumOfOrder}</td>
                    </tr>
                </table>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModalCreateNewOrder" style="width:100%;">
                    Редактировать
                </button>
                <br />
                <br />
            </div>
        </div>
    </div>
    {$outConfirmNewOrder}

EOF;
echo $outBody;
?>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
<script>
    $(document).ready(function()
    {
        $(".select-client-modal").click(function()
        {
            SelectClientModal($(this).attr("data-name-db"), $(this).attr("data-name-result"));
        });
        $("body").on("click", ".select-product-modal", function(){
            idInput = $(this).attr("data-name-result");
            nameResult = idInput + "printResult";

            SelectProductModal(idInput, nameResult);
        });
    });
</script>