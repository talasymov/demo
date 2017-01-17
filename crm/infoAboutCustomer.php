<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$customer = R::getALL("SELECT * FROM ");
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