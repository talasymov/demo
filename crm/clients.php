<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$countOnPage = 20;
$from = 0;
$to = $countOnPage;

if(isset($_GET["from"]))
{
    $from = $_GET["from"];
}
if(isset($_GET["to"]))
{
    $to = $_GET["to"];
}
$thisNumPage = $from / $countOnPage;
$li = "";

$customerCount = R::getRow("SELECT COUNT(*) as count FROM dashboard_customers");

$customerCountDel = $customerCount["count"] / $countOnPage;

if(($customerCount % $countOnPage) == 0)
{
    $customerCountDelAll = $customerCountDel;
}
else
{
    $customerCountDelAll = $customerCountDel + 1;
}

$customerLast = $customerCountDel * $countOnPage;

if($thisNumPage > 3)
{
    $li .= "<li class=\"disable\"><a href=\"/crm/clients.php?from=0&to={$countOnPage}\">...</a></li>";
}
for($i = 0; $i < $customerCountDelAll; $i++)
{
    $page = $i * $countOnPage;
    $iPlus = $i + 1;

    if($i >= ($thisNumPage - 3) && $i <= ($thisNumPage + 3))
    {
        if($from == $page)
        {
            $li .= "<li class=\"active\"><a href=\"/crm/clients.php?from={$page}&to={$countOnPage}\">{$iPlus}</a></li>";
        }
        else
        {
            $li .= "<li><a href=\"/crm/clients.php?from={$page}&to={$countOnPage}\">{$iPlus}</a></li>";
        }
    }
}
if($thisNumPage < $customerCountDelAll - 3)
{
    $li .= "<li class=\"disable\"><a href=\"#\">...</a></li>";
}

$customers = R::getAll("
SELECT dashboard_customers.customerId,
dashboard_customers.companyId,
dashboard_companies.companyName,
lastName,
firstName,
patronymicName,
dashboard_customes_city,
street,
build,
apartment,
phone,
phone_2,
phone_3,
email,
url,
bankDetails,
dashboard_money_from.name AS fromWhom, dashboard_users.dashboard_users_name AS byWhomAdding
FROM dashboard_customers
INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId
INNER JOIN dashboard_money_from ON dashboard_customers.fromWhom = dashboard_money_from.id
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = dashboard_customers.byWhomAdding
INNER JOIN dashboard_peoples ON dashboard_customers.customerId = dashboard_peoples.dashboard_peoples_id_client
WHERE dashboard_peoples.dashboard_peoples_whoisit = 1 LIMIT {$from}, {$to}");

$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");
$allCompanies = R::getAll("SELECT * FROM dashboard_companies");

$outputCompany                = "";
$outputClients                = "";
$outModals                    = "";
$outModalsConfirmEdit         = "";
$outModalsCustomerEdit        = "";
$outModalsCustomerEditConfirm = "";
$outputFrom                   = "<option value=\"8\">Выберите источник</option>";

$outputCompanyLi     = "<option value=\"1\">Выберите компанию</option>";

foreach ($clientsFrom as $value)
{
    $outputFrom .= "<option value=\"" . $value["id"] . "\">" . $value["name"] . "</option>";
}

foreach ($customers as $key => $value)
{
    $customerId             = $value['customerId'];
    $companyId              = $value['companyId'];
    $companyName            = $value["companyName"];
    $lastNameCustomer       = $value["lastName"];
    $firstNameCustomer      = $value["firstName"];
    $patronymicNameCustomer = $value["patronymicName"];
    $city                   = $value["dashboard_customes_city"];
    $street                 = $value["street"];
    $build                  = $value["build"];
    $apartment              = $value["apartment"];
    $phone                  = $value["phone"];
    $phone2                 = $value["phone_2"];
    $phone3                 = $value["phone_3"];
    $email                  = $value["email"];
    $url                    = $value["url"];
    $bankDetails            = $value["bankDetails"];
    $fromWhom               = $value["fromWhom"];
    $byWhomAdding           = $value["byWhomAdding"];

    $outputCompanyLiEdit = "";

    foreach ($allCompanies as $key => $value)
    {
        $outputCompanyLi .= "<option value=\"" . $value["id"] . "\">" . $value["companyName"] . "</option>";
        if ($value['id'] == $companyId)
        {
            $outputCompanyLiEdit .= "<option value=\"" . $value["id"] . "\" selected>" . $value["companyName"] . "</option>";
        }
        else
        {
            $outputCompanyLiEdit .= "<option value=\"" . $value["id"] . "\">" . $value["companyName"] . "</option>";
        }
    }

    $outputClients .= <<<EOF
  <tr>

    <td>{$companyName}</td>
    <td>{$lastNameCustomer} {$firstNameCustomer} {$patronymicNameCustomer}</td>
    <td>ул.{$street}, д.{$build}, кв.{$apartment}</td>
    <td>{$phone}</td>
    <td>{$phone2}</td>
    <td>{$phone3}</td>
    <td>{$email}</td>
    <td>{$url}</td>
    <td>{$bankDetails}</td>
    <td>{$fromWhom}</td>
    <td>{$byWhomAdding}</td>
    <td><button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalEditCustomer{$customerId}" name="button"><i class="fa fa-cog" aria-hidden="true"></i></button></td>
    <td><button type="button" class="btn btn-default edit-client" data-id="{$customerId}"><i class="fa fa-cog" aria-hidden="true"></i></button></td>
  </tr>
EOF;

    $outModalsCustomerEdit .= <<<EOF
<div class="modal fade" id="myModalEditCustomer{$customerId}" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                Редактирование заказчика <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Компания: </td>
                            <td>
                                <select id="companyNameCustomerEdit{$customerId}" class="form-control" name="company">
                                    {$outputCompanyLiEdit}
                                </select>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Фамилия: </td>
                            <td>
                                <div class="input-label">
                                    <input id="lastNameCustomerEdit{$customerId}" type="text" class="form-control" name="lastName" value="{$lastNameCustomer}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Имя: </td>
                            <td>
                                <div class="input-label">
                                    <input id="firstNameCustomerEdit{$customerId}" type="text" class="form-control" name="name" value="{$firstNameCustomer}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>Отчество: </td>
                            <td>
                                <div class="input-label">
                                    <input id="patronymicNameCustomerEdit{$customerId}" type="text" class="form-control" name="patronymic" value="{$patronymicNameCustomer}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Город: </td>
                            <td>
                                <div class="input-label">
                                    <input id="cityCustomerEdit{$customerId}" type="text" class="form-control" name="street" value="{$city}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Улица: </td>
                            <td>
                                <div class="input-label">
                                    <input id="streetCustomerEdit{$customerId}" type="text" class="form-control" name="street" value="{$street}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Дом: </td>
                            <td>
                                <div class="input-label">
                                    <input id="buildCustomerEdit{$customerId}" type="text" class="form-control" name="build" value="{$build}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Квартира: </td>
                            <td>
                                <div class="input-label">
                                    <input id="apartmentCustomerEdit{$customerId}" type="text" class="form-control" name="apartment" value="{$apartment}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Номер телефона: </td>
                            <td>
                                <div class="input-label">
                                    <input id="phoneCustomerEdit{$customerId}" type="text" class="form-control phone" name="phoneNumber" value="{$phone}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Номер телефона 2: </td>
                            <td>
                                <div class="input-label">
                                    <input id="phone2CustomerEdit{$customerId}" type="text" class="form-control phone" name="phoneNumber" value="{$phone2}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td>Номер телефона 3: </td>
                            <td>
                                <div class="input-label">
                                    <input id="phone3CustomerEdit{$customerId}" type="text" class="form-control phone" name="phoneNumber" value="{$phone3}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Email: </td>
                            <td>
                                <div class="input-label">
                                    <input id="emailCustomerEdit{$customerId}" type="text" class="form-control" name="email" value="{$email}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Сайт: </td>
                            <td>
                                <div class="input-label">
                                    <input id="urlCustomerEdit{$customerId}" type="text" class="form-control" name="url" value="{$url}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Реквизиты: </td>
                            <td>
                                <div class="input-label">
                                    <input id="bankDetailsCustomerEdit{$customerId}" type="text" class="form-control" name="bankDetails" value="{$bankDetails}" placeholder="">
                                    <span class="line-input"></span>
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>

                        <tr><td>Откуда узнали о нас: </td>
                            <td>
                                <select id="newOrder_from_user{$customerId}" class="form-control" name="fromWhom">
                                    <?php print($outputFrom); ?>
                                </select>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmEditCustomer{$customerId}" data-dismiss="modal">Редактировать</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
EOF;


    $outModalsCustomerEditConfirm .= <<<EOF
    <div class="modal fade" id="myModalConfirmEditCustomer{$customerId}" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        Действительно хотите отредактировать сотрудника? <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info confirmEditCustomer" data-order="{$customerId}" data-dismiss="modal">Да, внести изменения</button>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal{$key}" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
EOF;
}

$outNav = <<<EOF
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li>
                            <a href="/crm/clients.php?from=0&to={$countOnPage}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        {$li}
                        <li>
                            <a href="/crm/clients.php?from={$customerLast}&to={$countOnPage}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
EOF;

$leftMenu = getLeftMenu();

$outBody = <<<EOF
<div class="container-fluid">
	<div class="row">
        <div class="col-md-2">
            {$leftMenu}
        </div>
        <div class="col-md-10">
			<h2>Клиенты</h2>

			<button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#newClient"
					aria-haspopup="true" aria-expanded="true">
				Добавить Заказчика&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i>
			</button>
            <input class="form-control search" data-table="#clientTable" data-name="customers" />
            <div class="result-ajax-search" data-id="#clientTable"></div>
			<table id="clientTable" class="table">
				<thead>
				<tr>
					<th>Компания</th>
					<th>ФИО</th>
					<th>Адрес</th>
					<th>Номер телефона</th>
					<th>Номер телефона 2</th>
					<th>Номер телефона 3</th>
					<th>Email</th>
					<th>Сайт</th>
					<th>Реквизиты</th>
					<th>Откуда</th>
					<th>Кем добавлен</th>
					<th>Редактировать</th>
				</tr>
				</thead>
				<tbody>
                {$outputClients}
				</tbody>
			</table>
            {$outNav}
		</div>
	</div>
</div>

<div class="modal fade" id="newClient" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				Создание нового заказчика
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<tbody>
					<tr>
						<td>Выберите компанию или введите название новой:</td>
						<td>
                            <div class="select-DB">
                                <input type="hidden" id="newOrder_company_user">
                                <button class="btn btn-primary" data-name-db="companies">Выбрать компанию &nbsp;&nbsp;<i class="fa fa-database" aria-hidden="true"></i></button>
                                <span class="result-txt"></span>
                            </div>
                            <div class="input-label">
                                <input id="newOrder_company_name" type="text" class="form-control"
                                       value="" placeholder="Новая компания">
                                <span class="line-input"></span>
                            </div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Фамилия:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_surname_user" type="text" class="form-control" name="lastName"
									   value="" placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Имя:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_name_user" type="text" class="form-control" name="name" value=""
									   placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td>Отчество:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_patronymic_user" type="text" class="form-control" name="patronymic"
									   value="" placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

                    <tr>
                        <td>Город:</td>
                        <td>
                            <div class="input-label">
                                <input id="newOrder_city_user" type="text" class="form-control" name="street" value=""
                                       placeholder="">
                                <span class="line-input"></span>
                            </div>
                        </td>
                        <td>
                        </td>
                    </tr>

					<tr>
						<td>Улица:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_street_user" type="text" class="form-control" name="street" value=""
									   placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Дом:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_build_user" type="text" class="form-control" name="build" value=""
									   placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Квартира:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_apartment_user" type="text" class="form-control" name="apartment"
									   value="" placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Номер телефона:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_phone_user" type="text" class="form-control phone" name="phoneNumber"
									   value="" placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

                    <tr>
                        <td>Номер телефона 2:</td>
                        <td>
                            <div class="input-label">
                                <input id="newOrder_phone2_user" type="text" class="form-control phone" name="phoneNumber"
                                       value="" placeholder="">
                                <span class="line-input"></span>
                            </div>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td>Номер телефона 3:</td>
                        <td>
                            <div class="input-label">
                                <input id="newOrder_phone3_user" type="text" class="form-control phone" name="phoneNumber"
                                       value="" placeholder="">
                                <span class="line-input"></span>
                            </div>
                        </td>
                        <td>
                        </td>
                    </tr>

					<tr>
						<td>Email:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_email_user" type="text" class="form-control" name="email" value=""
									   placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Сайт:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_site_user" type="text" class="form-control" name="url" value=""
									   placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Реквизиты:</td>
						<td>
							<div class="input-label">
								<input id="newOrder_bankDetails_user" type="text" class="form-control"
									   name="bankDetails" value="" placeholder="">
								<span class="line-input"></span>
							</div>
						</td>
						<td>
						</td>
					</tr>

					<tr>
						<td>Откуда узнали о нас:</td>
						<td>
							<select id="newOrder_from_user" class="form-control" name="fromWhom">
                                {$outputFrom}
							</select>
						</td>
						<td>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="addNewClient">Добавить</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
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
{$outModalsCustomerEdit}
{$outModalsCustomerEditConfirm}
EOF;

echo $outBody;

require_once(APP_DIR_INC . "footer.php");

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Page generated in '.$total_time.' seconds.'."\n";
?>
