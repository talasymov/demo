<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$company = R::getAll("
SELECT id, companyName, dashboard_companies_mobile, dashboard_companies_phone, dashboard_companies_any_phone, dashboard_companies_email, dashboard_companies_address, logotype, dashboard_users.dashboard_users_name AS byWhomAdding FROM dashboard_companies
INNER JOIN dashboard_users ON dashboard_companies.byWhomAdding = dashboard_users.dashboard_users_id
INNER JOIN dashboard_peoples ON dashboard_companies.id = dashboard_peoples.dashboard_peoples_id_client
WHERE dashboard_peoples.dashboard_peoples_whoisit = 2");
//$customers = R::getAll("
//SELECT dashboard_customers.customerId,
//dashboard_customers.companyId,
//dashboard_companies.companyName,
//lastName,
//firstName,
//patronymicName,
//dashboard_customes_city,
//street,
//build,
//apartment,
//phone,
//phone_2,
//phone_3,
//email,
//url,
//bankDetails,
//dashboard_money_from.name AS fromWhom, dashboard_users.dashboard_users_name AS byWhomAdding
//FROM dashboard_customers
//INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId
//INNER JOIN dashboard_money_from ON dashboard_customers.fromWhom = dashboard_money_from.id
//INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = dashboard_customers.byWhomAdding
//INNER JOIN dashboard_peoples ON dashboard_customers.customerId = dashboard_peoples.dashboard_peoples_id_client
//WHERE dashboard_peoples.dashboard_peoples_whoisit = 1 LIMIT 30");
$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");
$allCompanies = R::getAll("SELECT * FROM dashboard_companies");

$outputCompany                = "";
$outputClients                = "";
$outModals                    = "";
$outModalsConfirmEdit         = "";
$outModalsCustomerEdit        = "";
$outModalsCustomerEditConfirm = "";
$outputFrom                   = "<option value=\"8\">Выберите источник</option>";

$outputCompanyLiEdit = "";
$outputCompanyLi     = "<option value=\"1\">Выберите компанию</option>";

foreach ($clientsFrom as $value)
{
    $outputFrom .= "<option value=\"" . $value["id"] . "\">" . $value["name"] . "</option>";
}

foreach ($company as $key => $value)
{
    $id              = $value['id'];
    $nameCompany     = html_entity_decode($value["companyName"]);
    $companyMobile                = $value["dashboard_companies_mobile"];
    $companyPhone                = $value["dashboard_companies_phone"];
    $companyAnyPhone                = $value["dashboard_companies_any_phone"];
    $companyEmail                = $value["dashboard_companies_email"];
    $companyAddress                = $value["dashboard_companies_address"];
    $logotypeCompany = $value["logotype"];
    $byWhomAdding    = $value["byWhomAdding"];
    $outClientsInThisCompany = "";

    $clients = R::getAll("SELECT * FROM dashboard_customers WHERE companyId = ?", [$id]);

    if(count($clients) > 0)
    {
        foreach($clients as $subkey => $subvalue)
        {
            $nameClient = "<span class='list-clients'>" . $subvalue["patronymicName"] . " " . $subvalue["firstName"] .  " "  . $subvalue["lastName"] . "</span>";

            $outClientsInThisCompany .= $nameClient;
        }
    }

    if (is_null($logotypeCompany))
    {
        $outputCompany .= <<<EOF
  <tr>
    <td>{$nameCompany}</td>
    <td>Нет логотипа</td>
    <td>{$byWhomAdding}</td>
    <td><button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalEditCompany{$id}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button></td>
  </tr>
EOF;
    }
    else
    {
        $outputCompany .= <<<EOF
  <tr>
    <td>{$nameCompany}</td>
    <td><img src="{$logotypeCompany}" class="img-rounded" style="max-width: 100px; max-height: 100px"></td>
    <td>{$byWhomAdding}</td>
    <td><button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalEditCompany{$id}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button></td>
  </tr>
EOF;
    }

    $outModals .= <<<EOF
<div class="modal fade" id="myModalEditCompany{$id}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Информация о компании <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table">
          <tbody>
            <tr>
                <td>Название компании: </td><td><input class="form-control" id="editCompanyName{$id}" value="{$nameCompany}"></td>
            </tr>
            <tr>
                <td>Мобильный: </td><td><input class="form-control phone" id="editCompanyMobile{$id}" value="{$companyMobile}"></td>
            </tr>
            <tr>
                <td>Домашний номер телефона: </td><td><input class="form-control phone" id="editCompanyPhone{$id}" value="{$companyPhone}"></td>
            </tr>
            <tr>
                <td>Дополнительынй номер телефона: </td><td><input class="form-control phone" id="editCompanyAnyPhone{$id}" value="{$companyAnyPhone}"></td>
            </tr>
            <tr>
                <td>Email: </td><td><input class="form-control" id="editCompanyEmail{$id}" value="{$companyEmail}"></td>
            </tr>
            <tr>
                <td>Адрес: </td><td><input class="form-control" id="editCompanyAddress{$id}" value="{$companyAddress}"></td>
            </tr>
            <tr>
                <td>Логотип компании</td>
                <td><input id="editCompanyLogo{$id}" type="file" name="editCompanyLogo{$id}"/></td>
            </tr>
          </tbody>
        </table>
        <h3 style="text-align: center;">Контакты компании</h3>
        {$outClientsInThisCompany}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirm{$id}" data-dismiss="modal">Редактировать</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>

  </div>
</div>
EOF;

    $outModalsConfirmEdit .= <<<EOF
<div class="modal fade" id="myModalConfirm{$id}" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите отредактировать сотрудника? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info confirmEditCompany" data-order="{$id}" data-dismiss="modal">Да, внести изменения</button>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal{$id}" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;
    $outputCompanyLi .= "<option value=\"" . $value["id"] . "\">" . $value["companyName"] . "</option>";
}
print($outModals);
print($outModalsConfirmEdit);

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Компании</h2>
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newCompany"
                    aria-haspopup="true" aria-expanded="true">
                Добавить Компанию&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <table class="table exampleDataTable">
                <thead>
                <tr>
                    <th>Название компании</th>
                    <th>Логотип компании</th>
                    <th>Кем добавлена</th>
                    <th>Редактировать</th>
                </tr>
                </thead>
                <tbody>
                <?php
                print($outputCompany);
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="newCompany" role="dialog">
    <div class="modal-dialog">
        <form action="../disp.php?command=" id="addNewCompanyForms" method="post" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    Создание новой компании
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td>Название компании</td>
                            <td>
                                <div class="input-label">
                                    <input id="newCompanyName" type="text" class="form-control"
                                           name="newCompanyName" value="" placeholder="">
                                    <span class="line-input"></span> <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Мобильный</td>
                            <td>
                                <div class="input-label">
                                    <input id="newCompanyPhoneMobile" type="text" class="form-control phone"
                                           value="" placeholder="">
                                    <span class="line-input"></span> <i class="fa fa-phone" aria-hidden="true"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Домашний номер телефона</td>
                            <td>
                                <div class="input-label">
                                    <input id="newCompanyPhoneHome" type="text" class="form-control phone"
                                           value="" placeholder="">
                                    <span class="line-input"></span> <i class="fa fa-phone" aria-hidden="true"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Дополнительынй номер телефона</td>
                            <td>
                                <div class="input-label">
                                    <input id="newCompanyPhoneAny" type="text" class="form-control phone"
                                           value="" placeholder="">
                                    <span class="line-input"></span> <i class="fa fa-phone" aria-hidden="true"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>
                                <div class="input-label">
                                    <input id="newCompanyEmail" type="text" class="form-control"
                                           value="" placeholder="">
                                    <span class="line-input"></span> <i class="fa fa-envelope" aria-hidden="true"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Адрес</td>
                            <td>
                                <div class="input-label">
                                    <input id="newCompanyAddress" type="text" class="form-control"
                                           value="" placeholder="">
                                    <span class="line-input"></span> <i class="fa fa-map-marker" aria-hidden="true"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Логотип компании</td>
                            <td><input id="newCompanyLogo" class="form-control" type="file" name="newCompanyLogo"/></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="uploadNewCompany">Добавить
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
