<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$outConfirmNewOrder = <<<EOF
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
        <button type="button" class="btn btn-default confirmCreateNewOrder" data-order="2" totalSum="0" data-dismiss="modal">Создать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;

$buttonSelectProduct = PrintButtonSelectProduct("Выберите продукт", "inputSelectProduct");
$buttonSelectClient = PrintButtonSelect("Выберите клиента", "companies", "inputSelectClient");

$outBody = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="min-h2" style="float: none;">Сделать заказ</h2>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Название заказа:</td>
                        <td><input type="text" id="productOrderName" class="form-control" value="" /></td>
                    </tr>

                    <tr>
                        <td>Заказчик:</td>
                        <td>{$buttonSelectClient}</td>
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
                        <td><button type="button" class="btn btn-default btnAddRowNewOrder" data-order='2'>Добавить ещё один продукт</button></td>
                    </tr>
                </thead>
                <tbody id="productListTable">
                    <tr id="lastProductRowInTable">
                        <td>{$buttonSelectProduct}</td>
                        <td>
                            <input type="text" id="productTextNewOrder1" class="form-control productTextNewOrder" value="" data-order="1">
                        </td>
                        <td>
                            <input type="text" id="productCountNewOrder1" class="form-control productCountNewOrder" value="" data-order="1">
                        </td>
                        <td>
                            <input type="text" id="productPricePerOneNewOrder1" class="form-control productPricePerOneNewOrder" value="" data-order="1">
                        </td>
                    </tr>
                </tbody>
                <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td id="totalSumTd">Итого: </td>
                    </tr>
            </table>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModalCreateNewOrder" style="width:100%;">
                ОФОРМИТЬ
            </button>
            <br />
            <br />
        </div>
    </div>
</div>
{$outConfirmNewOrder}
EOF;

echo $outBody;

require_once(APP_DIR_INC . "footer.php");
?>
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
