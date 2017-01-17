<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");


$offersTypes = R::getAll("SELECT * FROM dashboard_offers_types");
$offers = R::getAll("
SELECT * FROM dashboard_offers
INNER JOIN dashboard_offers_types ON dashboard_offers_types.dashboard_offers_types_id = dashboard_offers.dashboard_offers_type
");

$selectOffersTypes = "";
$offersBody = "";

foreach($offersTypes as $key => $value)
{
    $nameOfferType = $value["dashboard_offers_types_name"];
    $idOfferType = $value["dashboard_offers_types_id"];

    $selectOffersTypes .= "<option value='{$idOfferType}'>{$nameOfferType}</option>";
}

foreach($offers as $key => $value)
{
    $offersBody .= <<<EOF
    <tr>
        <td>{$value["dashboard_offers_id"]}</td>
        <td>{$value["dashboard_offers_types_name"]}</td>
        <td>{$value["dashboard_offers_text"]}</td>
    </tr>
EOF;
}
$output = <<<EOF
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Предложения</h2>

            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newOffer"
                    aria-haspopup="true" aria-expanded="true">
                Новое предложение&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table class="table exampleDataTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Тип</th>
                    <th>Какое предложение</th>
                </tr>
                </thead>
                <tbody>
                    {$offersBody}
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="newOffer" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Добавить новое предложение
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>Тип</td>
                        <td>
                            <select name="" class="form-control" id="selectNewOffer">
                                <option value="0">Выберите тип предложения</option>
                                {$selectOffersTypes}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Расскажите о Вашем предложении или о жалобе</td>
                        <td><textarea name="" id="textareaNewOffer" class="form-control"></textarea>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="addNewOffer">Добавить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
EOF;
print($output);
?>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
