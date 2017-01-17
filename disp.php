<?php
session_start();

$cookieUserId = $_COOKIE["userId"];
$command = $_GET["command"];

require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/andreyDisp.php");

//update workers status if they have complete/non-complete orders
if($command == "selectCategoryOfProducts")
{
    $type = $_POST["type"];
    $out = "";

    if($type == "category")
    {
        $query = R::getAll("SELECT * FROM dashboard_productsCategory");

        foreach($query as $key => $value)
        {
            $out .= "<span class='list-category-in-modal click-category' data-category-id='" . $value["id"] . "'>" . $value["name"] . "</span>";
        }
    }
    else if($type == "subCategory")
    {
        $categoryId = $_POST["categoryId"];

        $query = R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = ?", [$categoryId]);

        foreach($query as $key => $value)
        {
            $out .= "<span class='list-category-in-modal click-subCategory' data-category-id='" . $value["id"] . "'>" . $value["name"] . "</span>";
        }
    }
    else if($type == "product")
    {
        $categoryId = $_POST["categoryId"];

        $query = R::getAll("SELECT * FROM dashboard_products WHERE subCategoryId = ?", [$categoryId]);

        foreach($query as $key => $value)
        {
            $out .= "<span class='list-category-in-modal click-product' data-category-id='" . $value["id"] . "'>" . $value["name"] . "</span>";
        }
    }

    echo $out;
}

if($command == "getClientsFromThisCompany")
{
    $idCompany = $_POST["idCompany"];

    $result = R::getAll("SELECT * FROM dashboard_customers WHERE companyId = ?", [$idCompany]);

    $out = "<input class='form-control search-client' placeholder='Поиск по клиентам' data-name-db='clients' /><ul class=\"ul-result-ajax\">";

    foreach($result as $key => $value)
    {
        $idClient = $value["customerId"];
        $nameClient = $value["patronymicName"] . " " .$value["firstName"] . " " . $value["lastName"];

        $out .= <<<EOF
        <li class='set-client' data-id='$idClient'>$nameClient</li>
EOF;

    }
    $out .= "</ul>";

    echo $out;
}
if ($command == "update_status_worker_onLoadPage")
{
    $workersID = R::getCol("select id FROM dashboard_workers");
    foreach ($workersID as $value)
    {
        //get all orders that completing on the current time
        $ordersByWorker = R::getCol("SELECT id FROM diary_orders WHERE (diary_orders.status = 1 OR diary_orders.status = 2 OR diary_orders.status = 3) AND diary_orders.workerId = " . $value);

        if ($ordersByWorker == null)
        {
            R::exec("UPDATE dashboard_workers SET dashboard_workers_status = 1 WHERE id = " . $value);
        }
        else
        {
            R::exec("UPDATE dashboard_workers SET dashboard_workers_status = 2 WHERE id = " . $value);
        }
    }
}

if ($command == "payingUser")
{
    //update all days where not paying
    R::exec("UPDATE basic_day_registration SET basic_day_registration_status = 1 WHERE basic_day_registration_who = {$_POST['userId']}");
    //update all orders where not paying
    R::exec("UPDATE diary_orders SET diary_orders.pay = 1 WHERE diary_orders.byWhomAdding = {$_POST['userId']}");

    echo 'all good';
}



/*
 *
 * Working with leads
 *
 */
if ($command == "addNewLead")
{
    R::exec("INSERT INTO diary_leads (lastName, firstName, patronymicName, phone, phone_2, phone_3, byWhomAdding, whenAdding, comment) VALUES ('{$_POST['lastName']}', '{$_POST['firstName']}', '{$_POST['patronymicName']}', '{$_POST['phone']}', '{$_POST['phone2']}', '{$_POST['phone3']}', {$cookieUserId}, '" . date("Y-m-d H:i:s") . "', '{$_POST['comment']}')");
}

if ($command == "createCustomerByLeadNewCompany")
{
    R::exec("INSERT INTO dashboard_companies (companyName, byWhomAdding) VALUES ('{$_POST['companyName']}', '{$cookieUserId}')"); //add new company

    $lastIdCompany = R::getRow("SELECT id FROM dashboard_companies ORDER BY id DESC");

    $idCompany = $lastIdCompany["id"];

    $filenameCompany = "/disk/customers/company$idCompany";

    if (file_exists($filenameCompany)) {
      $filenameCompany = "/disk/customers/company_$idCompany";
    } else {
      mkdir( __DIR__ . $filenameCompany, 0777);
    }

    R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd, dashboard_peoples_whoisit, dashboard_peoples_id_client) VALUES(?, ?, ?, 2, ?)", ["company" . $idCompany, "_op" . rand(1111,9999) . "tyi_", $_COOKIE["userId"], $idCompany]);

    if($_POST["apartment"] == null)
    {
      $_POST["apartment"] = 0;
    }

    $company = intval($idCompany);

    if($company == 0)
    {
        $company = 1;
    }

    R::exec("INSERT INTO dashboard_customers (companyId, lastName, firstName, patronymicName, dashboard_customes_city, street, build, apartment, phone, phone_2, phone_3, email, url, bankDetails, fromWhom, byWhomAdding) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$idCompany, $_POST['lastName'], $_POST['firstName'], $_POST['patronymicName'], $_POST['city'], $_POST['street'], $_POST['build'], $_POST['apartment'], $_POST['phone'], $_POST['phone2'], $_POST['phone3'], $_POST['email'], $_POST['url'], $_POST['bankDetails'], $_POST['fromWhom'], $cookieUserId]);

    $lastId = R::getRow("SELECT customerId FROM dashboard_customers ORDER BY customerId DESC");

    $id = $lastId["customerId"];

    $filename = "/disk/clients/client$id";

    if (file_exists($filename)) {
      $filename = "/disk/clients/client_$id";
    } else {
      mkdir( __DIR__ . $filename, 0777);
    }

    R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd, dashboard_peoples_whoisit, dashboard_peoples_id_client) VALUES(?, ?, ?, 1, ?)", ["client$id", "_op" . rand(1111,9999) . "tyi_", $_COOKIE["userId"], $id]);

    R::exec("UPDATE diary_leads SET inTableCustomer = 1 WHERE id = {$_POST['leadId']}");
}

if($command == "newMovement")
{
    R::exec("INSERT INTO dashboard_movement(dashboard_movement_money, dashboard_movement_type, dashboard_movement_worker, dashboard_movement_who_created) VALUES(?, ?, ?, ?)", [$_POST["money"], $_POST["type"], $_POST["worker"], $_COOKIE["userId"]]);
}

if ($command == "createCustomerByLead")
{
    if($_POST["apartment"] == null)
    {
      $_POST["apartment"] = 0;
    }

    $company = intval($_POST['companyId']);

    if($company == 0)
    {
        $company = 1;
    }

    R::exec("INSERT INTO dashboard_customers (companyId, lastName, firstName, patronymicName, street, build, apartment, phone, phone_2, phone_3, email, url, bankDetails, fromWhom, byWhomAdding) VALUES ({$company}, '{$_POST['lastName']}', '{$_POST['firstName']}', '{$_POST['patronymicName']}', '{$_POST['street']}', '{$_POST['build']}', '{$_POST['apartment']}', '{$_POST['phone']}', '{$_POST['phone2']}', '{$_POST['phone3']}', '{$_POST['email']}', '{$_POST['url']}', '{$_POST['bankDetails']}', {$_POST['fromWhom']}, {$cookieUserId})");

    $lastId = R::getRow("SELECT customerId FROM dashboard_customers ORDER BY customerId DESC");

    $id = $lastId["customerId"];

    $filename = "/disk/clients/client$id";

    if (file_exists($filename)) {
      $filename = "/disk/clients/client_$id";
    } else {
      mkdir( __DIR__ . $filename, 0777);
    }

    R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd, dashboard_peoples_whoisit, dashboard_peoples_id_client) VALUES(?, ?, ?, 1, ?)", ["client$id", "_op" . rand(1111,9999) . "tyi_", $_COOKIE["userId"], $id]);


    R::exec("UPDATE diary_leads SET inTableCustomer = 1 WHERE id = {$_POST['leadId']}");
}

/*
 *
 * ==================================
 *
 */

if ($command == "select_customers_by_company")
{
    $customerId = $_POST['companyId'];
    $array = R::getAll("SELECT customerId, lastName, firstName, patronymicName FROM dashboard_customers WHERE companyId = " . $customerId);
    echo json_encode($array);
}

if ($command == "add_order_designer")
{
    $orderId = $_POST['orderId'];
    R::exec("UPDATE dashboard_orders_designers SET dashboard_orders_designers_whowork = ? WHERE dashboard_orders_designers_id = ?", [$cookieUserId, $orderId]);
}

if ($command == "add_new_offer")
{
    R::exec("INSERT INTO dashboard_offers(dashboard_offers_type, dashboard_offers_text, dashboard_offers_whoadd) VALUES(?, ?, ?)", [$_POST["type"], $_POST["text"], $_COOKIE["userId"]]);
}
if ($command == "add_new_notification")
{
    $date = returnCurrentTimeFromBootstrap($_POST["date"]);
    if($_POST["forPeople"] == 0)
    {
        $_POST["forPeople"] = $_COOKIE["userId"];
    }

    R::exec("INSERT INTO dashboard_notifications(dashboard_notifications_date, dashboard_notifications_text, dashboard_notifications_whoadd) VALUES(?, ?, ?)", [$date, $_POST["text"], $_POST["forPeople"]]);
}
if($command == "addNewCall")
{
    $time = returnCurrentTimeFromBootstrap($_POST["time"]);

    R::exec("INSERT INTO dashboard_calls(dashboard_calls_date, dashboard_calls_comment, dashboard_calls_whoadd, dashboard_calls_name, dashboard_calls_phone) VALUES(?, ?, ?, ?, ?)", [$time, $_POST["comment"], $cookieUserId, $_POST["name"], $_POST["phone"]]);
}

if($command == "deleteCall")
{
    R::exec("DELETE FROM dashboard_calls WHERE dashboard_calls_id = ?", [$_POST["idCall"]]);
}

if($command == "delete_notification")
{
    R::exec("UPDATE dashboard_notifications SET dashboard_notifications_delete = 1  WHERE dashboard_notifications_id = ?", [$_POST["id"]]);
}

if($command == "editNewCall")
{
    $time = returnCurrentTimeFromBootstrap($_POST["time"]);
    $idLead = $_POST["idLead"];

    R::exec("UPDATE dashboard_calls SET dashboard_calls_date = ?, dashboard_calls_comment = ?, dashboard_calls_name = ?, dashboard_calls_phone = ? WHERE dashboard_calls_id = ?", [$time, $_POST["comment"], $_POST["name"], $_POST["phone"], $idLead]);
}

if ($command == "addNewOrderDesign")
{
    $quick = 0;
    if($_POST['quick'] != null)
    {
      $quick = $_POST['quick'];
    }

    R::exec("INSERT INTO dashboard_orders_designers (dashboard_orders_designers_name, dashboard_orders_designers_type, dashboard_orders_designers_price, dashboard_orders_designers_description, dashboard_orders_designers_company, dashboard_orders_designers_customer, dashboard_orders_designers_quick, dashboard_orders_designers_size, dashboard_orders_designers_orientir, dashboard_orders_designers_colors, dashboard_orders_designers_tarif, dashboard_orders_designers_slogan, dashboard_orders_designers_whoadd) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$_POST['name'], $_POST['type'], intval($_POST['price']), $_POST['comment'], $_POST['company'], $_POST['client'], intval($quick), $_POST['size'], $_POST['orientation'], $_POST['colors'], $_POST['packets'], $_POST['slogan'], $cookieUserId]);
}

if ($command == "editNewOrderDesign")
{
    $quick = 0;
    if($_POST['quick'] != null)
    {
        $quick = $_POST['quick'];
    }
    R::exec("
      UPDATE dashboard_orders_designers SET dashboard_orders_designers_name = ?, dashboard_orders_designers_type = ?, dashboard_orders_designers_price = ?, dashboard_orders_designers_description = ?, dashboard_orders_designers_customer = ?, dashboard_orders_designers_quick = ?, dashboard_orders_designers_size = ?, dashboard_orders_designers_orientir = ?, dashboard_orders_designers_colors = ?, dashboard_orders_designers_tarif = ?, dashboard_orders_designers_slogan = ?, dashboard_orders_designers_whoadd = ? WHERE dashboard_orders_designers_id = ?", [$_POST['name'], $_POST['type'], intval($_POST['price']), $_POST['comment'], $_POST['client'], intval($quick), $_POST['size'], $_POST['orientation'], $_POST['colors'], $_POST['packets'], $_POST['slogan'], $cookieUserId, $_POST["idOrder"]]);
}
if($command == "addOrderDesignerFromInvoice")
{
    $out = "";
    $outProducts = "";

    foreach($_POST[orders] as $key => $value)
    {
        $out .= "" . $value;
        $products = R::getAll("SELECT dashboard_productsOrderGroup.id AS orderGroupId, dashboard_productsCategory.name AS productsCategoryName, dashboard_productsSubCategory.name AS productsSubCategoryName, dashboard_customers.customerId, dashboard_customers.companyId, dashboard_products.name, dashboard_productsOrders.count, dashboard_productsOrders.cost, dashboard_productsOrders.id AS idProduct, dashboard_productsOrders.information, ROUND(dashboard_productsOrders.count*dashboard_productsOrders.cost, 2) AS totalPricePerThisProduct FROM dashboard_productsOrders

INNER JOIN dashboard_productsOrderGroup ON dashboard_productsOrderGroup.id = dashboard_productsOrders.productsOrderGroupId
INNER JOIN dashboard_customers ON dashboard_customers.customerId = dashboard_productsOrderGroup.customerId
INNER JOIN dashboard_products ON dashboard_productsOrders.productId = dashboard_products.id
INNER JOIN dashboard_productsCategory ON dashboard_productsCategory.id = dashboard_products.categoryId
INNER JOIN dashboard_productsSubCategory ON dashboard_productsSubCategory.id = dashboard_products.subCategoryId

WHERE dashboard_productsOrders.id = ?", [$value]);
        foreach ($products as $subkey => $productsValue)
        {
            $nameDesign = $productsValue["productsCategoryName"] . "," . $productsValue["productsSubCategoryName"] . "," . $productsValue['name'] . "," . $productsValue['information'];
            $type = "В печать! Номер заказа - " . $productsValue["orderGroupId"];
            $company = $productsValue["companyId"];
            $customer = $productsValue["customerId"];
//            $description = ;
//            echo $nameDesign, " ", $type, " ", $productsValue['totalPricePerThisProduct'], " ", $_POST['comment'], " ", $company, " ", $customer, " ", $cookieUserId;
            R::exec("INSERT INTO dashboard_orders_designers (dashboard_orders_designers_name, dashboard_orders_designers_type, dashboard_orders_designers_price, dashboard_orders_designers_company, dashboard_orders_designers_customer, dashboard_orders_designers_status_order, dashboard_orders_designers_whoadd) VALUES (?, ?, ?, ?, ?, ?, ?)", [$nameDesign, $type, $productsValue['totalPricePerThisProduct'], $company, $customer, 1, $cookieUserId]);

//            $outProducts .= <<<EOF
//                  <tr>
//                    <td>{$key}</td>
//                    <td>{$productsValue["productsCategoryName"]}, {$productsValue["productsSubCategoryName"]}, {$productsValue['name']} {$productsValue['information']}</td>
//                    <td>шт.</td>
//                    <td>{$productsValue['count']}</td>
//                    <td>{$productsValue['cost']}</td>
//                    <td>{$productsValue['totalPricePerThisProduct']}</td>
//                  </tr>
//EOF;
        }
    }

//    print($outProducts);
}

if($command == "getInfoAboutOrderGroup")
{
    $products = R::getAll("SELECT dashboard_productsOrderGroup.id AS numberOfOrderGroup, dashboard_productsCategory.name AS productsCategoryName, dashboard_productsSubCategory.name AS productsSubCategoryName, dashboard_productsOrderGroup.dateOfOrder AS dateOfOrder, dashboard_customers.phone, dashboard_customers.email, dashboard_customers.firstName, dashboard_customers.lastName, dashboard_customers.patronymicName, dashboard_products.name, dashboard_productsOrders.count, dashboard_productsOrders.cost, dashboard_productsOrders.id AS idProduct, dashboard_productsOrders.information, ROUND(dashboard_productsOrders.count*dashboard_productsOrders.cost, 2) AS totalPricePerThisProduct FROM dashboard_productsOrders

INNER JOIN dashboard_productsOrderGroup ON dashboard_productsOrderGroup.id = dashboard_productsOrders.productsOrderGroupId
INNER JOIN dashboard_customers ON dashboard_customers.customerId = dashboard_productsOrderGroup.customerId
INNER JOIN dashboard_products ON dashboard_productsOrders.productId = dashboard_products.id
INNER JOIN dashboard_productsCategory ON dashboard_productsCategory.id = dashboard_products.categoryId
INNER JOIN dashboard_productsSubCategory ON dashboard_productsSubCategory.id = dashboard_products.subCategoryId

WHERE dashboard_productsOrderGroup.id = ?", [$_POST["idInvoice"]]);

    $outProducts = "";
    foreach ($products as $key => $productsValue)
    {
//        $companyData = $productsValue["lastName"] . " " . $productsValue["firstName"] . " " . $productsValue["patronymicName"] . ", " . $productsValue["phone"] . ", " . $productsValue["email"];
        $outProducts .= <<<EOF
                  <tr>
                    <td><input type="checkbox" class="form-control checkOrderInvoice" data-id="{$productsValue["idProduct"]}"></td>
                    <td>{$productsValue["productsCategoryName"]}, {$productsValue["productsSubCategoryName"]}, {$productsValue['name']} {$productsValue['information']}</td>
                  </tr>
EOF;
    }
    $out = <<<EOF
    <table class="table">
        {$outProducts}
    </table>
    <select class="form-control">
        <option value="">
    </select>
EOF;


    echo($out);
}

if ($command == "add_order")
{
    $array = json_decode($_POST['data']);

    $orderBean = R::xdispense('diary_orders');

    foreach ($array as $key => $value)
    {
        if ($value->title == "place")
        {
            $orderBean["place"] = $value->value;
        }
        if ($value->title == "date")
        {
            $explode = explode(" ", $value->value);
            $explodeMinus = explode(".", $explode[0]);

            $orderBean["date"] = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];
        }
        if ($value->title == "time")
        {
            $explode = explode(" ", $value->value);
            $orderBean["time"] = $explode[1] . ":00";
        }
        if ($value->title == "worker")
        {
            $orderBean["worker"] = $value->value;
        }
        if ($value->title == "customer")
        {
            $orderBean["customer"] = $value->value;
        }
        if ($value->title == "type")
        {
            $orderBean["type"] = $value->value;
        }
        if ($value->title == "count")
        {
            $orderBean["count_hp"] = $value->value;
        }
        if ($value->title == "comment")
        {
            $orderBean["comment"] = $value->value;
        }
    }
    print($orderBean["date"] . $orderBean["time"]);
    $orderBean["status"] = 1;
    //$orderBean["comment"] = "Blabla";
    $id = R::exec("
    	INSERT INTO diary_orders(status, place, workerId, customerId, date, times, comment, type, count_hp, byWhomAdding)
    	VALUES(
    	" . $orderBean["status"] . ",
    	'" . $orderBean["place"] . "',
    	" . $orderBean["worker"] . ",
    	" . $orderBean["customer"] . ",
    	'" . $orderBean["date"] . "',
    	'" . $orderBean["time"] . "',
    	'" . $orderBean["comment"] . "',
    	" . $orderBean["type"] . ",
      " . $orderBean["count_hp"] . ",
      " . $cookieUserId . ")");
    if ($orderBean["worker"] != 0)
    {
        $query = R::exec("UPDATE dashboard_workers SET dashboard_workers_status = 2 WHERE id = " . $orderBean["worker"]);
    }
}
if ($command == "edit_order")
{
    $array = json_decode($_POST['data']);

    $idOrder = $_POST['id'];

    foreach ($array as $key => $value)
    {
        if ($value->title == "place")
        {
            $orderBean["place"] = $value->value;
        }
        if ($value->title == "date")
        {
            $explode = explode(" ", $value->value);
            $explodeMinus = explode(".", $explode[0]);

            $orderBean["date"] = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];
        }
        if ($value->title == "time")
        {
            $explode = explode(" ", $value->value);
            $orderBean["time"] = $explode[1] . ":00";
        }
        if ($value->title == "worker")
        {
            $orderBean["worker"] = $value->value;
        }
        if ($value->title == "customer")
        {
            $orderBean["customer"] = $value->value;
        }
        if ($value->title == "type")
        {
            $orderBean["type"] = $value->value;
        }
        if ($value->title == "count")
        {
            $orderBean["count_hp"] = $value->value;
        }
        if ($value->title == "comment")
        {
            $orderBean["comment"] = $value->value;
        }
    }
    R::exec("UPDATE diary_orders SET place = ?, times = ?, date = ?, workerId = ?, customerId = ?, type = ?, count_hp = ?, comment = ? WHERE id = ?", [$orderBean["place"], $orderBean["time"], $orderBean["date"], $orderBean["worker"], $orderBean["customer"], $orderBean["type"], $orderBean["count_hp"], $orderBean["comment"], $idOrder]);

  print_r($orderBean);
  print($idOrder);
}
if ( $command== "delete_order")
{
  R::exec("DELETE FROM diary_orders WHERE id = ?", [$_POST["id"]]);
}
if ($command == "get_worker_types")
{
    $category = $_POST['category'];
    $type = R::getAll("SELECT * FROM diary_worker_task WHERE category_diary_worker_task = ?", [$category]);

    $result = "";
    foreach ($type as $key => $value)
    {
        $result .= "<option value=\"" . $value["id_diary_worker_task"] . "\">" . $value["title_diary_worker_task"] . "</option>";
    }
    print($result . $category);
}
if ($command == "getTypesSuper")
{
//    $types = R::getAll("SELECT * FROM diary_worker_task");
}
if ($command == "change_status_order")
{
    $idOrder = $_POST['idOrder'];
    $status = $_POST['status'];

    //if status = Work is Done! we set column `alreadySetRatingForOrder` on 1(true) and + value of column `count_hp` to `dashboard_workers`.`dashboard_workers_promoterLevel`
    $tmp = R::getCol("SELECT alreadySetRatingForOrder FROM diary_orders WHERE id = " . $idOrder)[0];
    //echo $tmp;

    $idWorker = R::getCol("SELECT workerId FROM diary_orders WHERE id = " . $idOrder);
    $idWorker = $idWorker[0];

    $rating = R::getCol("SELECT count_hp FROM diary_orders WHERE id = " . $idOrder);
    $rating = $rating[0];

    $orderTypeID = R::getCol("SELECT type FROM diary_orders WHERE id = " . $idOrder);
    $orderTypeID = $orderTypeID[0];

    $orderTypeName = R::getCol("SELECT category_diary_worker_task FROM diary_worker_task WHERE id_diary_worker_task = " . $orderTypeID);
    $orderTypeName = $orderTypeName[0];


    if ($status == "4")
    {
        //get value of `alreadySetRatingForOrder` if we do not + points to rating so we do it
        if ($tmp == "0")
        {
            //if it is `distribution` or `promo` so we update `dashboard_workers`.`dashboard_workers_promoterLevel`
            if ($orderTypeName == "promo" || $orderTypeName == "distribution")
            {
                //console.log("UPDATE dashboard_workers SET dashboard_workers_promoterLevel = dashboard_workers_promoterLevel+" . $rating . " WHERE id = " . $idWorker);
                R::exec("UPDATE dashboard_workers SET dashboard_workers_promoterLevel = dashboard_workers_promoterLevel+" . $rating . " WHERE id = " . $idWorker);
                R::exec("UPDATE diary_orders SET alreadySetRatingForOrder = 1 WHERE id = " . $idOrder);
            }
            //if it is `posting` or `delivery` so we update `dashboard_workers`.`dashboard_workers_stickerLevel`
            if ($orderTypeName == "posting" || $orderTypeName == "delivery")
            {
                R::exec("UPDATE dashboard_workers SET dashboard_workers_stickerLevel = dashboard_workers_stickerLevel+" . $rating . " WHERE id = " . $idWorker);
                R::exec("UPDATE diary_orders SET alreadySetRatingForOrder = 1 WHERE id = " . $idOrder);
            }
        }
    }

    if ($status == "1" || $status == "2" || $status == "3" || $status == "5")
    {
        //get value of `alreadySetRatingForOrder` if we do not + points to rating so we do it
        if ($tmp == "1")
        {
            //if it is `distribution` or `promo` so we update `dashboard_workers`.`dashboard_workers_promoterLevel`
            if ($orderTypeName == "promo" || $orderTypeName == "distribution")
            {
                echo "orderTypeName = " . $orderTypeName;
                R::exec("UPDATE dashboard_workers SET dashboard_workers_promoterLevel = dashboard_workers_promoterLevel-" . $rating . " WHERE id = " . $idWorker);
                R::exec("UPDATE diary_orders SET alreadySetRatingForOrder = 0 WHERE id = " . $idOrder);
            }
            //if it is `posting` or `delivery` so we update `dashboard_workers`.`dashboard_workers_stickerLevel`
            if ($orderTypeName == "posting" || $orderTypeName == "delivery")
            {
                echo "orderTypeName = " . $orderTypeName;
                R::exec("UPDATE dashboard_workers SET dashboard_workers_stickerLevel = dashboard_workers_stickerLevel-" . $rating . " WHERE id = " . $idWorker);
                R::exec("UPDATE diary_orders SET alreadySetRatingForOrder = 0 WHERE id = " . $idOrder);
            }
        }
    }

    //change status of order
    R::exec("UPDATE diary_orders SET status = " . $status . " WHERE id = " . $idOrder);
    //echo $status;
}

if ($command == "change_status_design_order")
{
    R::exec("UPDATE dashboard_orders_designers SET dashboard_orders_designers_status = ? WHERE dashboard_orders_designers_id = ?", [$_POST["status"], $_POST["idOrder"]]);
}
if ($command == "change_status_order_invoice")
{
    R::exec("UPDATE dashboard_productsOrderGroup SET dashboard_productsOrderGroup_status = ? WHERE id = ?", [$_POST["status"], $_POST["idOrder"]]);
}
if ($command == "change_status_design_order_makets")
{
    R::exec("UPDATE dashboard_orders_designers SET dashboard_orders_designers_status_order = ? WHERE dashboard_orders_designers_id = ?", [$_POST["status"], $_POST["idOrder"]]);
}
if ($command == "change_status_lead")
{
    R::exec("UPDATE diary_leads SET status = ? WHERE id = ?", [$_POST["status"], $_POST["idOrder"]]);
}
if ($command == "add_new_client")
{
    $checkCatalog = checkCustomerInCatalog($_POST["phone"], $_POST["name"], $_POST["surname"], $_POST["patronymic"]);

    if($checkCatalog == 0 || $checkCatalog == "nonePhone")
    {
        if($_POST["apartment"] == null)
        {
            $_POST["apartment"] = 0;
        }

        $company = intval($_POST["company"]);

        $companyName = 0;

        if($company == 0)
        {
            $company = 1;
        }

        if(isset($_POST["companyName"]) && $_POST["companyName"] != null && $_POST["companyName"] != "")
        {
            $companyName = $_POST["companyName"];

            R::exec("INSERT INTO dashboard_companies(companyName, byWhomAdding) VALUES(?, ?)", [$companyName, $_COOKIE["userId"]]);

            $lastIdCompany = R::getRow("SELECT * FROM dashboard_companies ORDER BY id DESC");

            $company = $lastIdCompany["id"];
        }

        R::exec("INSERT INTO dashboard_customers (companyId, lastName, firstName, patronymicName, dashboard_customes_city, street, build, apartment, phone, phone_2, phone_3, email, url, bankDetails, fromWhom, byWhomAdding)
	VALUES(
		\"" . $company . "\",
		\"" . $_POST["surname"] . "\",
		\"" . $_POST["name"] . "\",
		\"" . $_POST["patronymic"] . "\",
		\"" . $_POST["city"] . "\",
		\"" . $_POST["street"] . "\",
		\"" . $_POST["build"] . "\",
		\"" . $_POST["apartment"] . "\",
        \"" . $_POST["phone"] . "\",
        \"" . $_POST["phone2"] . "\",
        \"" . $_POST["phone3"] . "\",
		\"" . $_POST["email"] . "\",
		\"" . $_POST["site"] . "\",
		\"" . $_POST["bankDetails"] . "\",
		\"" . $_POST["from"] . "\",
        ?)
	", [$_COOKIE["userId"]]);

        $lastId = R::getRow("SELECT customerId FROM dashboard_customers ORDER BY customerId DESC");

        $id = $lastId["customerId"];

        $filename = "/disk/clients/client$id";

        if (file_exists($filename)) {
            $filename = "/disk/clients/client_$id";
        } else {
            mkdir( __DIR__ . $filename, 0777);
        }

        R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd, dashboard_peoples_whoisit, dashboard_peoples_id_client) VALUES(?, ?, ?, 1, ?)", ["client$id", "_op" . rand(1111,9999) . "tyi_", $_COOKIE["userId"], $id]);

        echo json_encode(["result" => 1]);
    }
    else
    {
        echo json_encode(["result" => 0]);
    }
}
function checkCustomerInCatalog($get, $firstName, $lastName, $patronymicName)
{
//    $get = "(093) 456 63 84";

    $subString = str_replace(" ", "", $get);
    $subString = str_replace(")", "", $subString);
    $subString = str_replace("(", "", $subString);
    $subString = str_replace("-", "", $subString);
    $subString = str_replace("+38", "", $subString);
    $subString = substr($subString, strlen($subString) - 9, strlen($subString));

//    $firstName = "Айхам";
    $strLen = strlen($firstName);


    if($strLen > 4){
        $firstName = substr($firstName, 0, $strLen - 2);
    }
    if($subString != null)
    {
        $contacts = R::getAll("
SELECT * FROM
(
  SELECT
  REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(phone, '-', ''), '+38', ''), ')', ''), '(', ''), ' ', '') AS replacePhone,
  REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(phone_2, '-', ''), '+38', ''), ')', ''), '(', ''), ' ', '') AS replacePhone_2,
  REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(phone_3, '-', ''), '+38', ''), ')', ''), '(', ''), ' ', '') AS replacePhone_3,
  lastName, firstName, patronymicName, customerId
  FROM dashboard_customers
) AS innerTable

WHERE
( replacePhone LIKE '%" . $subString . "%' ) OR
( replacePhone LIKE '%" . $subString . "%' AND firstName LIKE '%" . $firstName . "%' ) OR
( replacePhone LIKE '%" . $subString . "%' AND lastName LIKE '%" . $lastName . "%' ) OR
( replacePhone LIKE '%" . $subString . "%' AND patronymicName LIKE '%" . $patronymicName . "%' ) OR
( replacePhone_2 LIKE '%" . $subString . "%' AND patronymicName LIKE '%" . $patronymicName . "%' ) OR
( replacePhone_2 LIKE '%" . $subString . "%' AND lastName LIKE '%" . $lastName . "%' ) OR
( replacePhone_2 LIKE '%" . $subString . "%' AND firstName LIKE '%" . $firstName . "%' ) OR
( replacePhone_3 LIKE '%" . $subString . "%' AND patronymicName LIKE '%" . $patronymicName . "%' ) OR
( replacePhone_3 LIKE '%" . $subString . "%' AND lastName LIKE '%" . $lastName . "%' ) OR
( replacePhone_3 LIKE '%" . $subString . "%' AND firstName LIKE '%" . $firstName . "%' )
");
    return count($contacts);
    }
    else return "nonePhone";


}
if ($command == "add_new_worker")
{
    R::exec("INSERT INTO dashboard_workers (dashboard_workers_name, dashboard_workers_surname, dashboard_workers_patronymic, dashboard_workers_phone)
	VALUES(
		\"" . $_POST["name"] . "\",
		\"" . $_POST["surname"] . "\",
		\"" . $_POST["patronymic"] . "\",
		\"" . $_POST["phone"] . "\")
	");

    echo "Успешно добавлен!";
}
if ($command == "edit_worker")
{
    R::exec("UPDATE dashboard_workers SET dashboard_workers_name = \"" . $_POST["name"] . "\", dashboard_workers_surname = \"" . $_POST["surname"] . "\", dashboard_workers_patronymic =\"" . $_POST["patronymic"] . "\" , dashboard_workers_phone =
	\"" . $_POST["phone"] . "\" WHERE id = " . $_POST["id"]);

    echo "Успешно отредактирован!";
}

if ($command == "save_account")
{
    R::exec("UPDATE dashboard_users SET dashboard_users_login = ?, dashboard_users_password = ?, dashboard_users_name = ? , dashboard_users_date = ? , dashboard_users_email = ?, dashboard_users_skype = ?, dashboard_users_phone = ? WHERE dashboard_users_id = ?", [$_POST["login"], $_POST["password"], $_POST["name"], $_POST["birth"], $_POST["email"], $_POST["skype"], $_POST["phone"], $cookieUserId]);

    echo "Успешно отредактирован!";
}


if ($command == "edit_designer_maket")
{
    R::exec('UPDATE dashboard_orders_designers SET dashboard_orders_designers_print = ?, dashboard_orders_designers_print_2 = ?, dashboard_orders_designers_preview = ?, dashboard_orders_designers_base = ?, dashboard_orders_designers_base_2 = ? WHERE dashboard_orders_designers_id = ?', [$_POST['print'], $_POST['print2'], $_POST['preview'], $_POST['base'], $_POST['base2'], $_POST['idDesignerOrder']]);

    echo "Успешно отредактирова н!";
}

if ($command == "editCustomer")
{
    if($_POST["apartment"] == null)
    {
      $_POST["apartment"] = 0;
    }
    R::exec("UPDATE dashboard_customers SET companyId = ?, lastName = ?, firstname = ?, patronymicName = ?, dashboard_customes_city = ?, street = ?, build = ?, apartment = ?, phone = ?, phone_2 = ?, phone_3 = ?, email = ?, url = ?, bankDetails = ?, fromWhom = ? WHERE customerId = ?", [$_POST['company'], $_POST['surname'], $_POST['name'], $_POST['patronymic'], $_POST['city'], $_POST['street'], $_POST['build'], $_POST['apartment'], $_POST['phone'], $_POST['phone2'], $_POST['phone3'], $_POST['email'], $_POST['site'], $_POST['bankDetails'], $_POST['from'], $_POST['id']]);
    print_r($_POST);

    //echo 'UPDATE dashboard_customers SET companyId="'.$_POST['company'].'", lastName="'.$_POST['surname'].'", firstname="'.$_POST['name'].'", patronymicName="'.$_POST['patronymic'].'", street="'.$_POST['street'].'", build="'.$_POST['build'].'", apartment="'.$_POST['apartment'].'", phone="'.$_POST['phone'].'", email="'.$_POST['email'].'", url="'.$_POST['site'].'", bankDetails="'.$_POST['bankDetails'].'", fromWhom="'.$_POST['from'].'" WHERE customerId='.$_POST['id'];
    //echo "Успешно отредактирован!";
}

if($command == "getInfoAboutClient")
{
    $client = R::getRow("
    SELECT * FROM dashboard_customers
    INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId
    WHERE customerId = ?", [$_POST["dataId"]]);

    $companyId = $client["companyId"];
    $companyName = $client["companyName"];
    $lastName = StyleInput("editClient", "lastName", $client["lastName"]);
    $firstName = StyleInput("editClient", "firstName", $client["firstName"]);
    $patronymicName = StyleInput("editClient", "patronymicName", $client["patronymicName"]);
    $street = StyleInput("editClient", "street", $client["street"]);
    $build = StyleInput("editClient", "build", $client["build"]);
    $apartment = StyleInput("editClient", "apartment", $client["apartment"]);
    $phone = StyleInput("editClient", "phone", $client["phone"], "phone");
    $phone_2 = StyleInput("editClient", "phone_2", $client["phone_2"], "phone");
    $phone_3 = StyleInput("editClient", "phone_3", $client["phone_3"], "phone");
    $email = StyleInput("editClient", "email", $client["email"]);
    $url = StyleInput("editClient", "url", $client["url"]);
    $bankDetails = StyleInput("editClient", "bankDetails", $client["bankDetails"]);
    $dashboard_customes_city = StyleInput("editClient", "dashboard_customes_city", $client["dashboard_customes_city"]);
    $fromWhom = $client["fromWhom"];

    $buttonSelectClient = PrintButtonSelectCompany("Выберите компанию", "companies", "inputSelectCompany", $companyId, $companyName);

    $out = <<<EOF
    <table class="table">
        <tbody>
            <tr><td>Компания</td><td>{$buttonSelectClient}</td></tr>
            <tr><td>Фамилия</td><td>{$lastName}</td></tr>
            <tr><td>Имя</td><td>{$firstName}</td></tr>
            <tr><td>Отчество</td><td>{$patronymicName}</td></tr>
            <tr><td>Город</td><td>{$dashboard_customes_city}</td></tr>
            <tr><td>Улица</td><td>{$street}</td></tr>
            <tr><td>Дом</td><td>{$build}</td></tr>
            <tr><td>Квартира</td><td>{$apartment}</td></tr>
            <tr><td>Номер телефона</td><td>{$phone}</td></tr>
            <tr><td>Номер телефона 2</td><td>{$phone_2}</td></tr>
            <tr><td>Номер телефона 3</td><td>{$phone_3}</td></tr>
            <tr><td>Почта</td><td>{$email}</td></tr>
            <tr><td>Сайт</td><td>{$url}</td></tr>
            <tr><td>Реквизиты</td><td>{$bankDetails}</td></tr>
        </tbody>
    </table>
    <script>
        $(document).ready(function()
        {
            $(".select-company-modal").click(function()
            {
                SelectCompanyModal($(this).attr("data-name-db"), $(this).attr("data-name-result"));
            });
        });
        jQuery(function($){
              $(".phone").mask("(999) 999 99 99");
          });
    </script>
EOF;
    print($out);
}

if($command == "getWindowMultiOrder")
{
    if($_POST["type"] && $_POST["type"] == "justRow")
    {
        $count = intval($_POST["count"]) + 1;

        $buttonSelectClient = PrintButtonSelect("Выберите клиента", "companies", "inputSelectClient" . $count);
        $buttonSelectWorker = ButtonSelectSupervisorWorkers("Выберите работника", "workers", "inputSelectWorker" . $count);
        $dateSelector = SelectorDate("addMultiDate" . $count);
        $inputTime = StyleInput("new", "time" . $count, "", "timeFromTo");
        $inputPlace = StyleInput("new", "place" . $count);
        $inputCount = StyleInput("new", "count" . $count);
        $typeOrder = PrintButtonSelect("Выберите тип", "diary_worker_task", "new_type" . $count);
        $textAreaComment = StyleTextArea("new", "comment" . $count);

        $out =  <<<EOF
        <tr>
            <td><input type="hidden" class="oneMultiOrder" data-count="{$count}" />{$inputPlace}</td><td>{$buttonSelectWorker}</td><td>{$inputTime}</td><td>{$typeOrder}</td><td>{$inputCount}</td><td>{$textAreaComment}</td><td><button class="btn btn-default deleteLineMultiOrder"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>
        </tr>";
EOF;
    }
    else
    {
        $buttonSelectClient = PrintButtonSelect("Выберите клиента", "companies", "inputSelectClient1");
        $buttonSelectWorker = ButtonSelectSupervisorWorkers("Выберите работника", "workers", "inputSelectWorker1");
        $dateSelector = SelectorDate("addMultiDate1");
        $inputTime = StyleInput("new", "time1", "", "timeFromTo");
        $inputPlace = StyleInput("new", "place1");
        $inputCount = StyleInput("new", "count1");
        $typeOrder = ButtonSupervisorType("Выберите тип", "diary_worker_task", "new_type1");
        $textAreaComment = StyleTextArea("new", "comment1");

        $out = <<<EOF
    <table class="table">
        <tbody>
            <tr><td>Выберите клиента</td><td>{$buttonSelectClient}</td></tr>
            <tr><td>Дата заказов</td><td>{$dateSelector}</td></tr>
        </tbody>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th>Место</th><th>Работник</th><th>Время</th><th>Тип работы</th><th>Количество</th><th>Комментарий</th><th><button id="addLineMultiOrder" data-count="1" class="btn btn-default"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></th>
            </tr>
        </thead>
        <tbody class="tr-line">
            <tr>
                <td><input type="hidden" class="oneMultiOrder" data-count="1" />{$inputPlace}</td><td>{$buttonSelectWorker}</td><td>{$inputTime}</td><td>{$typeOrder}</td><td>{$inputCount}</td><td>{$textAreaComment}</td><td><button class="btn btn-default deleteLineMultiOrder"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>
            </tr>
        </tbody>
    </table>
    <script>
    $(document).ready(function()
    {
        $(".select-client-modal").click(function()
        {
            SelectClientModal($(this).attr("data-name-db"), $(this).attr("data-name-result"));
        });
        $(".select-super-worker-modal").click(function()
        {
            SelectWorkerModal($(this).attr("data-name-db"), $(this).attr("data-name-result"));
        });
        $(".select-super-type-modal").click(function()
        {
            SelectSuperTypeModal($(this).attr("data-name-db"), $(this).attr("data-name-result"));
        });
    });
    jQuery(function($){
      $(".timeFromTo").mask("99:99 - 99:99");
    });
</script>
EOF;
    }
    print($out);
}

if ($command == "add_penalty")
{
    R::exec("UPDATE dashboard_workers SET penaltyCount = penaltyCount + 1 WHERE id = " . $_POST["id"]);

    echo "Успешно оштрафован!";
}

if ($command == "delete_worker")
{
    R::exec("DELETE FROM dashboard_workers WHERE id = " . $_POST["id"]);

    echo "Успешно удален!";
}

if ($command == "editLead")
{
  $explode = explode(" ", $_POST['whenCall']);
  $explodeMinus = explode(".", $explode[0]);

  $date = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];
  $time = $explode[1] . ":00";

  $allDate = $date . " " . $time;

  R::exec("UPDATE diary_leads SET lastName = '{$_POST['lastName']}', firstName = '{$_POST['firstName']}', patronymicName = '{$_POST['patronymicName']}', phone = '{$_POST['phone']}', comment = '{$_POST['comment']}', whenCall = '{$allDate}' WHERE id = {$_POST['id']}");
}

if($command == "copyOrdersDiary")
{
    foreach ($_POST["array"] as $key => $value) {
        $getRow = R::getRow("SELECT * FROM diary_orders WHERE id = ?", [$value]);

        R::exec("INSERT INTO diary_orders(status, place, workerId, customerId, date, times, type, count_hp, alreadySetRatingForOrder, byWhomAdding, pay) VALUES(0,?,?,?,?,?,?,?,?,?,0)", [$getRow["place"], $getRow["workerId"], $getRow["customerId"], $_POST["date"], "00:00:00", $getRow["type"], $getRow["count_hp"], $getRow["alreadySetRatingForOrder"], $_COOKIE["userId"]]);
    }
}
if($command == "copyInvoices")
{
    foreach ($_POST["array"] as $key => $value) {
        $getRowOrder = R::getRow("SELECT * FROM dashboard_productsOrderGroup WHERE id = ?", [$value]);
        $getAllProducts = R::getAll("SELECT * FROM dashboard_productsOrders WHERE productsOrderGroupId = ?", [$value]);

        R::exec("INSERT INTO dashboard_productsOrderGroup(name, customerId, totalSumOfOrder, byWhomAdding, dashboard_productsOrderGroup_status) VALUES(?, ?, ?, ?, 1)", [$getRowOrder["name"], $getRowOrder["customerId"], $getRowOrder["totalSumOfOrder"], $getRowOrder["byWhomAdding"]]);
        $id = R::getInsertID();
        foreach($getAllProducts as $subkey => $subvalue)
        {
            R::exec("INSERT INTO dashboard_productsOrders(productsOrderGroupId, productId, count, cost, information) VALUES(?, ?, ?, ?, ?)", [$id, $subvalue["productId"], $subvalue["count"], $subvalue["cost"], $subvalue["information"]]);
        }
    }
}
if($command == "payWorkers")
{
  foreach ($_POST["array"] as $key => $value) {
    R::exec("UPDATE diary_orders SET pay = 1 WHERE id = ?", [$value]);
  }
}

if($command == "paySuperVisor")
{
  foreach ($_POST["array"] as $key => $value) {
    R::exec("UPDATE diary_orders SET pay_super = 1 WHERE id = ?", [$value]);
  }
}

if($command == "paySuperVisorDays")
{
  foreach ($_POST["array"] as $key => $value) {
    R::exec("UPDATE basic_day_registration SET basic_day_registration_status = 1 WHERE basic_day_registration_id = ?", [$value]);
  }
}

if ($command == "deleteLead")
{
    R::exec("DELETE FROM diary_leads WHERE id = " . $_POST["id"]);

    //echo "DELETE FROM diary_leads WHERE id = " . $_POST["id"];
}

if ($command == "check_user_in_system") // SECURITY F-
{
    $login = checkPost("login");
    $password = checkPost("password");

    if ($login && $password)
    {
        $count = R::getRow("select * from dashboard_users where dashboard_users_login = ? and dashboard_users_password = ?", [$login, $password]);
        if ($count["dashboard_users_login"] != null)
        {
            // setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
            setcookie("login_user", $login, time() + (86400 * 30), "/");
            setcookie("name_user", $count["dashboard_users_name"], time() + (86400 * 30), "/");
            setcookie("permission", $count["dashboard_users_permissions"], time() + (86400 * 30), "/");
            setcookie("userId", $count["dashboard_users_id"], time() + (86400 * 30), "/");
            // $_COOKIE["name_user"] = $count["dashboard_users_name"];
            // $_COOKIE["permission"] = $count["dashboard_users_permissions"];
            // $_COOKIE["userId"] = $count["dashboard_users_id"];
        }
        else
        {
            setcookie("login_user", "", time() - 3600);
        }
    }
}
if ($command == "check_custom_in_system") // SECURITY F-
{
    $login = checkPost("login");
    $password = checkPost("password");

    if ($login && $password)
    {
        $count = R::getRow("select * from dashboard_peoples where dashboard_peoples_login = ? and dashboard_peoples_password = ?", [$login, $password]);
        if ($count["dashboard_peoples_login"] != null)
        {
            setcookie("login_user", $login, time() + (86400 * 30), "/");
            setcookie("userId", $count["dashboard_peoples_id_client"], time() + (86400 * 30), "/");
            if($count["dashboard_peoples_whoisit"] == 1)
            {
              setcookie("whoIsIt", "client", time() + (86400 * 30), "/");
            }
            else {
              setcookie("whoIsIt", "company", time() + (86400 * 30), "/");
            }
        }
        else
        {
            setcookie("login_user", "", time() - 3600);
        }
    }
}
if($command == "FM_getFiles")
{
    $customerId = 91; // $_COOKIE["userId"]
    $whoIsIt = "company"; // client OR company $_COOKIE["whoIsIt"]
    $buttonDownload = "";

    $filelist = array();

//    if($whoIsIt == "client")
//    {
//        $custom = "clients";
//    }
//    else {
//        $custom = "customers";
//    }
    $folder = $_POST["folder"];

//    $dir = "/var/www/html/disk/{$custom}/{$whoIsIt}" . $customerId . "/";
//    $dirUser = "/disk/{$custom}/{$whoIsIt}" . $customerId . "/";

    $dir = "/var/www/html/disk/$folder";
    $dirUser = "/disk/$folder";

    $files1 = scandir($dir);

    $tr = "";

    foreach ($files1 as $key => $value) {
        if($value != "." && $value != "..")
        {
            $dateFile = date("Y-m-d H:i:s", filemtime($dir . $value));
//            $tr .= <<<EOF
//    <tr><td>{$value}</td><td>{$dateFile}</td><td>
//    <a href="{$dirUser}{$value}"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="modal" data-target="#modalAddOrderDesigner" aria-haspopup="true" aria-expanded="true">
//        <i class="fa fa-arrow-up" aria-hidden="true"></i>
//    </button></a>
//    </td></tr>
//EOF;
            $pos = strripos($value, ".");
            $rest = substr($value, ++$pos, strlen($value));
            $img = "<img src=\"/images/photo-camera.svg\">";

            if($rest == "jpg" || $rest == "jpeg" || $rest == "gif" || $rest == "png")
            {
                $img = "<img src=\"{$dirUser}{$value}\" alt=\"NO IMAGE\">";
            }

            if($_POST["button_link"] == "true")
            {
                $buttonDownload = <<<EOF
                <script>
                    (function () {
                        new Clipboard('#copy-button');
                    })();
                </script>
                <input id="post-shortlink" style="opacity: 0; width: 10px;" value="{$dirUser}{$value}">
                <button class="btn btn-info" id="copy-button" data-clipboard-target="#post-shortlink"><i class="fa fa-cloud-download" aria-hidden="true"></i></button>
EOF;
            }

            $tr .= <<<EOF
            <div class="list-one-file" data-toggle="tooltip" data-link="{$dirUser}{$value}" data-placement="bottom" data-html="true" data-title="{$dateFile}">
                <div class="image-file">
                    {$img}
                </div>
                <div class="name-file">
                {$value}
                </div>
            </div>
            {$buttonDownload}
EOF;
        }
    }
    print("<table class='table'>" . $tr . "</table> <script>
  $(function () {
      $(\"[data-toggle='tooltip']\").tooltip();
  });
</script>");
}
if($command == "inputSearch_customer")
{
    $query = $_POST["query"];

    $result = R::getAll("SELECT dashboard_customers.lastName, dashboard_customers.firstName, dashboard_customers.patronymicName, dashboard_companies.companyName  FROM dashboard_customers, dashboard_companies WHERE
lastName LIKE '%". $query ."%' OR
firstName LIKE '%". $query ."%' OR
patronymicName LIKE '%". $query ."%' OR
companyName LIKE '%". $query ."%'
");

    $resultLook = "";

    foreach($result as $key => $value)
    {
        $resultLook .= <<<EOF
        <span class="result-search">{$value["companyName"]}</span><br />
EOF;

    }
}
if($command == "search_customers")
{
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
        WHERE dashboard_peoples.dashboard_peoples_whoisit = 1 AND
        (
        (lastName LIKE '%" . $_POST["query"] . "%') OR
        (firstName LIKE '%" . $_POST["query"] . "%') OR
        (patronymicName LIKE '%" . $_POST["query"] . "%') OR
        (dashboard_customes_city LIKE '%" . $_POST["query"] . "%') OR
        (street LIKE '%" . $_POST["query"] . "%') OR
        (phone LIKE '%" . $_POST["query"] . "%') OR
        (phone_2 LIKE '%" . $_POST["query"] . "%') OR
        (phone_3 LIKE '%" . $_POST["query"] . "%') OR
        (email LIKE '%" . $_POST["query"] . "%')
        ) ");

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
  </tr>
EOF;

//        $outModalsCustomerEdit .= <<<EOF
//<div class="modal fade" id="myModalEditCustomer{$customerId}" role="dialog">
//    <div class="modal-dialog">
//
//        <!-- Modal content-->
//        <div class="modal-content">
//            <div class="modal-header">
//                Создание нового заказчика <button type="button" class="close" data-dismiss="modal">&times;</button>
//            </div>
//            <div class="modal-body">
//                <table class="table">
//                    <tbody>
//                        <tr>
//                            <td>Компания: </td>
//                            <td>
//                                <select id="companyNameCustomerEdit{$customerId}" class="form-control" name="company">
//                                    {$outputCompanyLiEdit}
//                                </select>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Фамилия: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="lastNameCustomerEdit{$customerId}" type="text" class="form-control" name="lastName" value="{$lastNameCustomer}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Имя: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="firstNameCustomerEdit{$customerId}" type="text" class="form-control" name="name" value="{$firstNameCustomer}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//                        <tr>
//                            <td>Отчество: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="patronymicNameCustomerEdit{$customerId}" type="text" class="form-control" name="patronymic" value="{$patronymicNameCustomer}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Город: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="cityCustomerEdit{$customerId}" type="text" class="form-control" name="street" value="{$city}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Улица: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="streetCustomerEdit{$customerId}" type="text" class="form-control" name="street" value="{$street}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Дом: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="buildCustomerEdit{$customerId}" type="text" class="form-control" name="build" value="{$build}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Квартира: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="apartmentCustomerEdit{$customerId}" type="text" class="form-control" name="apartment" value="{$apartment}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Номер телефона: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="phoneCustomerEdit{$customerId}" type="text" class="form-control phone" name="phoneNumber" value="{$phone}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Номер телефона 2: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="phone2CustomerEdit{$customerId}" type="text" class="form-control phone" name="phoneNumber" value="{$phone2}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr>
//                            <td>Номер телефона 3: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="phone3CustomerEdit{$customerId}" type="text" class="form-control phone" name="phoneNumber" value="{$phone3}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr><td>Email: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="emailCustomerEdit{$customerId}" type="text" class="form-control" name="email" value="{$email}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr><td>Сайт: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="urlCustomerEdit{$customerId}" type="text" class="form-control" name="url" value="{$url}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr><td>Реквизиты: </td>
//                            <td>
//                                <div class="input-label">
//                                    <input id="bankDetailsCustomerEdit{$customerId}" type="text" class="form-control" name="bankDetails" value="{$bankDetails}" placeholder="">
//                                    <span class="line-input"></span>
//                                </div>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//
//                        <tr><td>Откуда узнали о нас: </td>
//                            <td>
//                                <select id="newOrder_from_user{$customerId}" class="form-control" name="fromWhom">
//                                    print($outputFrom);
//                                </select>
//                            </td>
//                            <td>
//                            </td>
//                        </tr>
//                    </tbody>
//                </table>
//            </div>
//            <div class="modal-footer">
//              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalConfirmEditCustomer{$customerId}" data-dismiss="modal">Редактировать</button>
//              <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
//            </div>
//        </div>
//    </div>
//</div>
//EOF;
//
//
//        $outModalsCustomerEditConfirm .= <<<EOF
//    <div class="modal fade" id="myModalConfirmEditCustomer{$customerId}" role="dialog">
//  <div class="modal-dialog">
//    <div class="modal-content">
//      <div class="modal-header">
//        Действительно хотите отредактировать сотрудника? <button type="button" class="close" data-dismiss="modal">&times;</button>
//      </div>
//      <div class="modal-body">
//      </div>
//      <div class="modal-footer">
//        <button type="button" class="btn btn-info confirmEditCustomer" data-order="{$customerId}" data-dismiss="modal">Да, внести изменения</button>
//        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal{$key}" data-dismiss="modal">Закрыть</button>
//      </div>
//    </div>
//  </div>
//</div>
//EOF;
    }
    print ($outModalsCustomerEdit);
    print ($outModalsCustomerEditConfirm);
//    print ($outputClients);

    $out = <<<EOF
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
EOF;
    print($out);

}
if($command == "getsearch_companies")
{
    $result = R::getAll("SELECT * FROM dashboard_companies WHERE companyName LIKE '%" . $_POST["query"] . "%'");

    $out = "<ul class=\"ul-result-ajax\">";
    $liClass = $_POST["liClass"];

    foreach($result as $key => $value)
    {
        $idCompany = $value["id"];
        $nameCompany = $value["companyName"];

        $out .= <<<EOF
        <li class='{$liClass}' data-id='$idCompany'>$nameCompany</li>
EOF;

    }
    $out .= "</ul>";
    echo $out;
}
if($command == "getsearch_clients")
{
    $query = $_POST["query"];

    $query = explode(" ", $query);

    $sql = "SELECT * FROM dashboard_customers WHERE ";

    foreach($query as $key => $value)
    {
        if(count($query) > 1 && $key > 0)
        {
            $sql .= " AND ";
        }

        $sql .= "(firstName LIKE '%" . $value . "%' OR
        lastName LIKE '%" . $value . "%' OR
        patronymicName LIKE '%" . $value . "%')";
    }
    $result = R::getAll($sql);

    $out = "";

    foreach($result as $key => $value)
    {
        $idClient = $value["customerId"];
        $nameClient = $value["patronymicName"] . " " . $value["firstName"] . " " . $value["lastName"];

        $out .= <<<EOF
        <li class='set-client' data-id='$idClient'>$nameClient</li>
EOF;

    }
    echo $out;
}
if($command == "getsearch_worker")
{
    $query = $_POST["query"];

    $query = explode(" ", $query);

    $sql = "SELECT * FROM dashboard_workers WHERE ";

    foreach($query as $key => $value)
    {
        if(count($query) > 1 && $key > 0)
        {
            $sql .= " AND ";
        }

        $sql .= "(dashboard_workers_name LIKE '%" . $value . "%' OR
        dashboard_workers_surname LIKE '%" . $value . "%' OR
        dashboard_workers_patronymic LIKE '%" . $value . "%')";
    }
    $result = R::getAll($sql);

    $out = "";

    foreach($result as $key => $value)
    {
        $idWorker = $value["id"];
        $nameWorker = $value["dashboard_workers_surname"] . " " . $value["dashboard_workers_name"] . " " . $value["dashboard_workers_patronymic"];

        $out .= <<<EOF
        <li class='set-worker li-blue' data-id='$idWorker'>$nameWorker</li>
EOF;

    }
    echo $out;
}
if ($command == "out_from_system")
{
    setcookie("login_user", "", time() - 3600);
}
if ($command == "start_working_day")
{
    $checkDay = R::getRow("SELECT * FROM basic_day_registration WHERE DATE(basic_day_registration_date_start) = ? AND basic_day_registration_who = ?", [date("Y-m-d"), $cookieUserId]);

    if (isset($checkDay["basic_day_registration_id"]) && $checkDay["basic_day_registration_id"] != null && $checkDay["basic_day_registration_date_stop"] == "1970-10-10 00:00:00")
    {
        R::exec("UPDATE basic_day_registration SET basic_day_registration_date_stop = ? WHERE basic_day_registration_id = ?", [date("Y-m-d H:i:s"), $checkDay["basic_day_registration_id"]]);
    }
    else if (!isset($checkDay["basic_day_registration_id"]) || $checkDay["basic_day_registration_id"] == null)
    {
        R::exec("INSERT INTO basic_day_registration(basic_day_registration_date_start, basic_day_registration_date_stop, basic_day_registration_who) VALUES(?, ?, ?)", [date("Y-m-d H:i:s"), "1970-10-10 00:00:00", $cookieUserId]);
    }
}

/*
 *
 * Work with CATEGORY of products
 *
 */
if ($command == "addNewProductCategory")
{
    //echo "INSERT INTO dashboard_productsCategory (name, byWhomAdding) VALUES ('{$_POST['name']}', {$_SESSION['userId']})";
    R::exec("INSERT INTO dashboard_productsCategory (name, byWhomAdding) VALUES ('{$_POST['name']}', {$_COOKIE['userId']})");
}

if ($command == "editProductCategory")
{
    //echo "INSERT INTO dashboard_productsCategory (name, byWhomAdding) VALUES ('{$_POST['name']}', {$_COOKIE['userId']})";
    R::exec("UPDATE dashboard_productsCategory SET name='{$_POST['name']}' WHERE id = {$_POST['id']}");
}

if ($command == "deleteProductCategory")
{
    //echo "INSERT INTO dashboard_productsCategory (name, byWhomAdding) VALUES ('{$_POST['name']}', {$_COOKIE['userId']})";
    R::exec("DELETE FROM dashboard_productsCategory WHERE id = {$_POST['id']}");
}


/*
 *
 * Work with SubCATEGORY of products
 *
 */
if ($command == "addNewProductSubCategory")
{
    R::exec("INSERT INTO dashboard_productsSubCategory (name, categoryId, byWhomAdding) VALUES ('{$_POST['name']}', '{$_POST['categoryId']}', {$_COOKIE['userId']})");
    //echo "INSERT INTO dashboard_productsSubCategory (name, categoryId, byWhomAdding) VALUES ('{$_POST['name']}', '{$_POST['categoryId']}', {$_SESSION['userId']})";
}

if ($command == "editProductSubCategory")
{
    R::exec("UPDATE dashboard_productsSubCategory SET name='{$_POST['name']}', categoryId='{$_POST['categoryId']}' WHERE id = {$_POST['id']}");
    //echo "UPDATE dashboard_productsSubCategory SET name='{$_POST['name']}', categoryId='{$_POST['categoryId']}' WHERE id = {$_POST['id']}";
}

if ($command == "deleteProductSubCategory")
{
    R::exec("DELETE FROM dashboard_productsSubCategory WHERE id = {$_POST['id']}");
    //echo "DELETE FROM dashboard_productsSubCategory WHERE id = {$_POST['id']}";
}


/*
 *
 * WORK WITH product
 *
 */
if ($command == "changeCategoryAtNewProduct")
{
    echo json_encode(R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = {$_POST['categoryId']}"));
}

if ($command == "addNewProduct")
{
    //echo 'asdasd';
    //echo "INSERT INTO dashboard_products (name, categoryId, subCategoryId, byWhomAdding) VALUES ('{$_POST['newProductName']}', {$_POST['categoryId']}, {$_POST['subCategoryId']}, {$_SESSION['userId']})";
    R::exec("INSERT INTO dashboard_products (name, categoryId, subCategoryId, byWhomAdding) VALUES ('{$_POST['newProductName']}', {$_POST['categoryId']}, {$_POST['subCategoryId']}, {$_COOKIE['userId']})");
}

if ($command == "editProduct")
{
    //echo 'asdasd';
    //echo "INSERT INTO dashboard_products (name, categoryId, subCategoryId, byWhomAdding) VALUES ('{$_POST['newProductName']}', {$_POST['categoryId']}, {$_POST['subCategoryId']}, {$_SESSION['userId']})";
    R::exec("UPDATE dashboard_products SET name='{$_POST['newProductName']}', categoryId={$_POST['categoryId']}, subCategoryId={$_POST['subCategoryId']} WHERE id = {$_POST['id']}");
    //echo "UPDATE dashboard_products SET name='{$_POST['newProductName']}', categoryId={$_POST['categoryId']}, subCategoryId={$_POST['subCategoryId']} WHERE id = {$_POST['id']}";
}

if ($command == "deleteProduct")
{
    //echo 'asdasd';
    //echo "INSERT INTO dashboard_products (name, categoryId, subCategoryId, byWhomAdding) VALUES ('{$_POST['newProductName']}', {$_POST['categoryId']}, {$_POST['subCategoryId']}, {$_SESSION['userId']})";
    R::exec("DELETE FROM dashboard_products WHERE id = {$_POST['id']}");
}
/*
 *
 * =========================================
 *
 */

/*
 *
 * WORKING WITH creating order
 *
 */
if ($command == "changeCategoryNewProductOrder")
{
    $subCategory = R::getCell("SELECT id FROM dashboard_productsSubCategory WHERE categoryId = ?", [$_POST['categoryId']]);

    $product = R::getAll("SELECT * FROM dashboard_products WHERE categoryId = ? AND subCategoryId = ?", [$_POST['categoryId'], $subCategory]);

    $newArray = [];
    array_push($newArray, $product);

    $subCategory = R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = ? AND id IN (SELECT dashboard_products.subCategoryId FROM dashboard_products)", [$_POST['categoryId']]);

    array_push($newArray, $subCategory);
    echo json_encode($newArray);
}

if ($command == "changeSubCategoryNewProductOrder")
{
    $subCategory = R::getCell("SELECT id FROM dashboard_productsSubCategory WHERE categoryId = {$_POST['categoryId']} AND id = {$_POST['subCategoryId']}");

    $product = R::getAll("SELECT * FROM dashboard_products WHERE categoryId = {$_POST['categoryId']} AND subCategoryId = {$subCategory}");

    echo json_encode($product);
}

if ($command == "addedByButtonNewRowFroNewProductOrder")
{
    $newArray = [];

    //category, subCategory and products
    $category    = R::getCell("SELECT id FROM dashboard_productsCategory WHERE id IN (SELECT dashboard_productsSubCategory.categoryId FROM dashboard_productsSubCategory)");
    $subCategory = R::getCell("SELECT id FROM dashboard_productsSubCategory WHERE categoryId = {$category}");

    $product = R::getAll("SELECT * FROM dashboard_products WHERE categoryId = {$category} AND subCategoryId = {$subCategory}");

    array_push($newArray, $product);
    $subCategory = R::getAll("SELECT * FROM dashboard_productsSubCategory WHERE categoryId = {$category} AND id IN (SELECT dashboard_products.subCategoryId FROM dashboard_products)");
    array_push($newArray, $subCategory);
    $category = R::getAll("SELECT * FROM dashboard_productsCategory");
    array_push($newArray, $category);
    echo json_encode($newArray);
}

if ($command == "createNewOrder")
{
    $idGroup = $_POST["info"]["idGroup"];
    $idClient = $_POST["info"]["client"];
    $totalSum = $_POST["info"]["totalSum"];
    $nameOrderGroup = $_POST["info"]["nameOrder"];

    R::exec("INSERT INTO dashboard_productsOrderGroup (name, dateOfOrder, customerId, totalSumOfOrder, byWhomAdding)
                 VALUES (?, ?, ?, ?, ?)", [$nameOrderGroup, date("Y-m-d H:i:s"), $idClient, $totalSum, $_COOKIE['userId']]);

    $newOrderReturnedId = R::getInsertID();

    foreach($_POST["orders"] as $key => $value)
    {
        $idOrder = $value["idOrder"];

        $count = R::getRow("SELECT COUNT(*) AS countRow FROM dashboard_productsOrders WHERE id = ?", [$idOrder]);
        {
            R::exec("INSERT INTO dashboard_productsOrders (productsOrderGroupId, productId, count, cost, information)
                 VALUES (?, ?, ?, ?, ?)", [$newOrderReturnedId, $value["idProduct"], $value["count"], $value["price"], $value["information"]]);
        }
    }

//
//    //echo json_encode($_POST['jsonArray']);
//    $arrayWithDataToCreateNewOrder = $_POST['jsonArray'];
//    print_r($arrayWithDataToCreateNewOrder);
//    //echo $arrayWithDataToCreateNewOrder['orderData']['productOrderName'];
//    $newOrderName       = $arrayWithDataToCreateNewOrder['orderData']['productOrderName'];
//    $newOrderCustomerId = $arrayWithDataToCreateNewOrder['orderData']['newProductOrderCustomer'];
//    $totalSum           = $arrayWithDataToCreateNewOrder['orderData']['totalSum'];
//    //echo "INSERT INTO dashboard_productsOrderGroup (name, dateOfOrder, customerId, totalSumOfOrder) VALUES ('{$newOrderName}',  '" . date("Y-m-d") . "' {$newOrderCustomerId}, {$totalSum})";
//    R::exec("INSERT INTO dashboard_productsOrderGroup (name, dateOfOrder, customerId, totalSumOfOrder, byWhomAdding) VALUES ('{$newOrderName}',  '" . date("Y-m-d H:i:s") . "', {$newOrderCustomerId}, {$totalSum}, {$_COOKIE['userId']})");
//    //echo R::getInsertID();
//    $newOrderReturnedId = R::getInsertID();
//    //echo "Количество элементов в массиве: ".count($arrayWithDataToCreateNewOrder).". ID продукта = ".$arrayWithDataToCreateNewOrder[1]['productId'].", Количество продукта = ".$arrayWithDataToCreateNewOrder[1]['count'].", ЦЕНА ПРОДУКТА = ".$arrayWithDataToCreateNewOrder[1]['costPerOne'];
//    for ($i = 1; $i < count($arrayWithDataToCreateNewOrder); $i++)
//    {
//        $productId  = $arrayWithDataToCreateNewOrder[$i]['productId'];
//        $count      = $arrayWithDataToCreateNewOrder[$i]['count'];
//        $costPerOne = $arrayWithDataToCreateNewOrder[$i]['costPerOne'];
//        $information = $arrayWithDataToCreateNewOrder[$i]['information'];
//        print($productId . " " . $count . " " . $costPerOne . " " . $information . "----");
//        R::exec("INSERT INTO dashboard_productsOrders (productsOrderGroupId, productId, count, cost, information)
//                 VALUES (?, ?, ?, ?, ?)", [$newOrderReturnedId, $productId, $count, $costPerOne, $information]);
//        //echo "INSERT INTO dashboard_productsOrders (productsOrderGroupId, productId, count, cost) VALUES ({$newOrderReturnedId}, {$productId}, {$count}, {$costPerOne})";
//    }
}

if($command == "editOrder")
{
    $idGroup = $_POST["info"]["idGroup"];
    $idClient = $_POST["info"]["client"];
    $totalSum = $_POST["info"]["totalSum"];
    $nameOrderGroup = $_POST["info"]["nameOrder"];

    foreach($_POST["orders"] as $key => $value)
    {
        $idOrder = $value["idOrder"];

        $count = R::getRow("SELECT COUNT(*) AS countRow FROM dashboard_productsOrders WHERE id = ?", [$idOrder]);

        if($count["countRow"] > 0)
        {
            R::exec("UPDATE dashboard_productsOrders SET productId = ?, count = ?, cost = ?, information = ? WHERE id = ?", [$value["idProduct"], $value["count"], $value["price"], $value["information"], $idOrder]);
        }
        else
        {
            R::exec("INSERT INTO dashboard_productsOrders(productsOrderGroupId, productId, count, cost, information) VALUES(?, ?, ?, ?, ?)", [$idGroup, $value["idProduct"], $value["count"], $value["price"], $value["information"]]);
        }
    }

    R::exec("UPDATE dashboard_productsOrderGroup SET customerId = ?, totalSumOfOrder = ?, name = ? WHERE id = ?", [$idClient, $totalSum, $nameOrderGroup, $idGroup]);
}

if ($command == "getTopUpProducts")
{
    $productsTop = R::getAll("SELECT dashboard_products.name AS productName, SUM(dashboard_productsOrders.count*dashboard_productsOrders.cost) AS totalSum FROM dashboard_productsOrders

INNER JOIN dashboard_products ON dashboard_productsOrders.productId = dashboard_products.id

GROUP BY productName
ORDER BY totalSum  DESC");

    echo json_encode($productsTop);
}


if ($command == "getTopDownProducts")
{
    $productsTop = R::getAll("SELECT dashboard_products.name AS productName, SUM(dashboard_productsOrders.count*dashboard_productsOrders.cost) AS totalSum FROM dashboard_productsOrders

INNER JOIN dashboard_products ON dashboard_productsOrders.productId = dashboard_products.id

GROUP BY productName
ORDER BY totalSum  ASC");

    echo json_encode($productsTop);
}

if ($command == "getTopUpWorkers")
{
    $productsTop = R::getAll("SELECT dashboard_users.dashboard_users_name AS userName, SUM(dashboard_productsOrderGroup.totalSumOfOrder) AS totalSumOfOrder FROM dashboard_productsOrderGroup

INNER JOIN dashboard_users ON dashboard_productsOrderGroup.byWhomAdding = dashboard_users.dashboard_users_id

GROUP BY dashboard_users_name

ORDER BY totalSumOfOrder DESC");

    echo json_encode($productsTop);
}

if ($command == "getTopDownWorkers")
{
    $productsTop = R::getAll("SELECT dashboard_users.dashboard_users_name AS userName, SUM(dashboard_productsOrderGroup.totalSumOfOrder) AS totalSumOfOrder FROM dashboard_productsOrderGroup

INNER JOIN dashboard_users ON dashboard_productsOrderGroup.byWhomAdding = dashboard_users.dashboard_users_id

GROUP BY dashboard_users_name

ORDER BY totalSumOfOrder ASC");

    echo json_encode($productsTop);
}

if ($command == "getBestDays")
{
    //lets work with best day in 2016
    /* $a_date = date('Y-m-d');

      $time2explodeDate = explode("-", $a_date);

      $time2 = $time2explodeDate[2] . "." . $time2explodeDate[1] . "." . $time2explodeDate[0];

      echo date(" t", strtotime($time2)); */

    $arrayMonthNames = ["Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь"];

    $arrayCoolestDayMoney = [];
    $arrayCoolestDayDate  = [];
    $mainArrayCoolestDay  = [];


    $arrayCoolestMonthMoney = [];
    $arrayCoolestMonthName  = [];
    $mainArrayCoolestMonth  = [];

    $currentDaySum   = 0;
    $currentMonthSum = 0;


//cycle that we use for months
    for ($monthIncr = 1; $monthIncr <= 12; $monthIncr++)
    {
        $dayInMonth      = date('Y-' . $monthIncr . '-d');
        $dayInMonth      = date("t", strtotime($dayInMonth));
        $currentMonthSum = 0;
        for ($dayIncr = 1; $dayIncr <= $dayInMonth; $dayIncr++)
        {
            $currentDaySum = (int)R::getCell("SELECT

((SELECT SUM(diary_orders.count_hp*diary_worker_task.money_all_diary_worker_task) FROM diary_orders

INNER JOIN diary_worker_task ON diary_worker_task.id_diary_worker_task = diary_orders.type

WHERE diary_orders.date = '2016-{$monthIncr}-{$dayIncr}')

 +

(SELECT IFNULL(SUM(dashboard_productsOrderGroup.totalSumOfOrder),0) FROM dashboard_productsOrderGroup

WHERE dashboard_productsOrderGroup.dateOfOrder = '2016-{$monthIncr}-{$dayIncr}')) AS totalByThisDay");
            if ($currentDaySum !== 0)
            {
                if (count($arrayCoolestDayMoney) < 20)
                {
                    array_push($arrayCoolestDayMoney, $currentDaySum);
                    array_push($arrayCoolestDayDate, '2016-' . $monthIncr . '-' . $dayIncr);
                }

                //echo 'За '.date('2016-'.$monthIncr.'-'.$dayIncr).' есть НЕНУЛЕВОЙ оборот на '.$currentDaySum . '<br>';
                $currentMonthSum += $currentDaySum;
            }
            else
            {
                //echo 'За '.date('2016-'.$monthIncr.'-'.$dayIncr).' нет заказов :('.$currentDaySum.'<br>';
            }
        }
        if ($currentMonthSum !== 0)
        {
            //echo 'За '.$monthIncr.' месяц есть НЕНУЛЕВОЙ оборот на '.$currentMonthSum . '<br>';
            array_push($arrayCoolestMonthMoney, $currentMonthSum);
            array_push($arrayCoolestMonthName, $arrayMonthNames[$monthIncr - 1]);
        }
        else
        {
            //echo 'За '.$monthIncr.' месяц ПУСТО:( '.$currentMonthSum . '<br>';
        }
    }

    array_multisort($arrayCoolestDayMoney, SORT_NUMERIC, SORT_DESC, $arrayCoolestDayDate);
    for ($i = 0; $i < count($arrayCoolestDayMoney); $i++)
    {
        array_push($mainArrayCoolestDay, [$arrayCoolestDayDate[$i],
            $arrayCoolestDayMoney[$i]]);
    }


    array_multisort($arrayCoolestMonthMoney, SORT_NUMERIC, SORT_DESC, $arrayCoolestMonthName);
    for ($i = 0; $i < count($arrayCoolestMonthMoney); $i++)
    {
        array_push($mainArrayCoolestMonth, [$arrayCoolestMonthName[$i],
            $arrayCoolestMonthMoney[$i]]);
    }
    echo json_encode($mainArrayCoolestDay);
}

if ($command == "getBestMonths")
{
    //lets work with best day in 2016
    /* $a_date = date('Y-m-d');

      $time2explodeDate = explode("-", $a_date);

      $time2 = $time2explodeDate[2] . "." . $time2explodeDate[1] . "." . $time2explodeDate[0];

      echo date(" t", strtotime($time2)); */

    $arrayMonthNames = ["Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь"];

    $arrayCoolestDayMoney = [];
    $arrayCoolestDayDate  = [];
    $mainArrayCoolestDay  = [];


    $arrayCoolestMonthMoney = [];
    $arrayCoolestMonthName  = [];
    $mainArrayCoolestMonth  = [];

    $currentDaySum   = 0;
    $currentMonthSum = 0;


//cycle that we use for months
    for ($monthIncr = 1; $monthIncr <= 12; $monthIncr++)
    {
        $dayInMonth      = date('Y-' . $monthIncr . '-d');
        $dayInMonth      = date("t", strtotime($dayInMonth));
        $currentMonthSum = 0;
        for ($dayIncr = 1; $dayIncr <= $dayInMonth; $dayIncr++)
        {
            $currentDaySum = (int)R::getCell("SELECT

((SELECT SUM(diary_orders.count_hp*diary_worker_task.money_all_diary_worker_task) FROM diary_orders

INNER JOIN diary_worker_task ON diary_worker_task.id_diary_worker_task = diary_orders.type

WHERE diary_orders.date = '2016-{$monthIncr}-{$dayIncr}')

 +

(SELECT IFNULL(SUM(dashboard_productsOrderGroup.totalSumOfOrder),0) FROM dashboard_productsOrderGroup

WHERE dashboard_productsOrderGroup.dateOfOrder = '2016-{$monthIncr}-{$dayIncr}')) AS totalByThisDay");
            if ($currentDaySum !== 0)
            {
                if (count($arrayCoolestDayMoney) < 20)
                {
                    array_push($arrayCoolestDayMoney, $currentDaySum);
                    array_push($arrayCoolestDayDate, '2016-' . $monthIncr . '-' . $dayIncr);
                }

                //echo 'За '.date('2016-'.$monthIncr.'-'.$dayIncr).' есть НЕНУЛЕВОЙ оборот на '.$currentDaySum . '<br>';
                $currentMonthSum += $currentDaySum;
            }
            else
            {
                //echo 'За '.date('2016-'.$monthIncr.'-'.$dayIncr).' нет заказов :('.$currentDaySum.'<br>';
            }
        }
        if ($currentMonthSum !== 0)
        {
            //echo 'За '.$monthIncr.' месяц есть НЕНУЛЕВОЙ оборот на '.$currentMonthSum . '<br>';
            array_push($arrayCoolestMonthMoney, $currentMonthSum);
            array_push($arrayCoolestMonthName, $arrayMonthNames[$monthIncr - 1]);
        }
        else
        {
            //echo 'За '.$monthIncr.' месяц ПУСТО:( '.$currentMonthSum . '<br>';
        }
    }

    array_multisort($arrayCoolestDayMoney, SORT_NUMERIC, SORT_DESC, $arrayCoolestDayDate);
    for ($i = 0; $i < count($arrayCoolestDayMoney); $i++)
    {
        array_push($mainArrayCoolestDay, [$arrayCoolestDayDate[$i],
            $arrayCoolestDayMoney[$i]]);
    }


    array_multisort($arrayCoolestMonthMoney, SORT_NUMERIC, SORT_DESC, $arrayCoolestMonthName);
    for ($i = 0; $i < count($arrayCoolestMonthMoney); $i++)
    {
        array_push($mainArrayCoolestMonth, [$arrayCoolestMonthName[$i],
            $arrayCoolestMonthMoney[$i]]);
    }
    echo json_encode($mainArrayCoolestMonth);
}


if ($command == "add_new_company")
{
    $getNameOfNewCompany = htmlentities($_POST['text']);
    R::exec("INSERT INTO dashboard_companies (companyName, logotype, dashboard_companies_mobile, dashboard_companies_phone, dashboard_companies_any_phone, dashboard_companies_email, dashboard_companies_address, byWhomAdding) VALUES(?, NULL, ?, ?, ?, ?, ?, {$_COOKIE["userId"]})", [$getNameOfNewCompany, $_POST["mobile"], $_POST["phone"], $_POST["anyPhone"], $_POST["email"], $_POST["address"]]);
    $lastId = R::getInsertID();

    $lastIdCompany = R::getRow("SELECT id FROM dashboard_companies ORDER BY id DESC");

    $idCompany = $lastIdCompany["id"];

    $filenameCompany = "/disk/customers/company$idCompany";

    if (file_exists($filenameCompany)) {
        $filenameCompany = "/disk/customers/company_$idCompany";
    } else {
        mkdir( __DIR__ . $filenameCompany, 0777);
    }

    R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd, dashboard_peoples_whoisit, dashboard_peoples_id_client) VALUES(?, ?, ?, 2, ?)", ["company" . $idCompany, "_op" . rand(1111,9999) . "tyi_", $_COOKIE["userId"], $idCompany]);

    $fileName        = $lastId . '.jpg';
    $filePath        = '/images/companys/';
    $filePathAddToDB = "";

    if (0 < $_FILES['file']['error'])
    {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else
    {
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName))
        {
            echo 'file exist' . "<br>";
        }
        else
        {
            move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName);
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName))
            {
                $filePathAddToDB = $filePath . $fileName;
                //adding logo path to db
                R::exec("UPDATE dashboard_companies SET logotype='{$filePathAddToDB}' WHERE id = {$lastId}");
                //echo "UPDATE dashboard_companies SET logotype='{$filePathAddToDB}'";

                echo 'file' . $_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName . ' uploading SUCCESS' . "<br>";
            }
            else
            {
                echo 'file' . $_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName . ' uploading FAIL' . "<br>";
            }

        }
    }


    /*$filename = "/disk/customers/company$id";

    if (file_exists($filename))
    {
        $filename = "/disk/customers/company_$id";
    }
    else
    {
        mkdir(__DIR__ . $filename, 0777);
    }

    R::exec("INSERT INTO dashboard_peoples(dashboard_peoples_login, dashboard_peoples_password, dashboard_peoples_whoadd) VALUES(?, ?, ?)", ["company$id",
                                                                                                                                             "_op" . rand(1111, 9999) . "tyi_",
                                                                                                                                             $_COOKIE["userId"]]);
*/
    //echo "Успешно добавлен! ".$_POST['formData'];// . $filename;
}


if ($command == "edit_company")
{
    $fileName = $_GET['id'] . '.jpg';
    echo $fileName;
    $filePath        = '/images/companys/';
    $filePathAddToDB = "";

    if (0 < $_FILES['file']['error'])
    {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else
    {
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName))
        {
            echo 'file exist';
            if (unlink($_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName))
            {
                echo 'file delete';

            }
            else
            {
                echo 'file cant to delete now';
            }
        }
        if(isset($_FILES['file']['tmp_name']))
        {
            move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName);
            echo $_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName;
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName))
            {
                $filePathAddToDB = $filePath . $fileName;
                R::exec("UPDATE dashboard_companies SET companyName = ?, dashboard_companies_mobile = ?, dashboard_companies_phone = ?, dashboard_companies_any_phone = ?, dashboard_companies_email = ?, dashboard_companies_address = ?, logotype = ? WHERE id = ?", [$_POST['companyName'], $_POST['mobile'], $_POST['phone'], $_POST['anyPhone'], $_POST['email'], $_POST['address'], $filePathAddToDB, $_GET['id']]);
                echo 'file' . $_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName . ' uploading SUCCESS' . "<br>";
            }
            else
            {
                echo 'file' . $_SERVER["DOCUMENT_ROOT"] . $filePath . $fileName . ' uploading FAIL' . "<br>";
            }
        }
        else
        {
            R::exec("UPDATE dashboard_companies SET companyName = ?, dashboard_companies_mobile = ?, dashboard_companies_phone = ?, dashboard_companies_any_phone = ?, dashboard_companies_email = ?, dashboard_companies_address = ? WHERE id = ?", [$_POST['companyName'], $_POST['mobile'], $_POST['phone'], $_POST['anyPhone'], $_POST['email'], $_POST['address'], $_GET['id']]);
        }
    }
}

function checkPost($params)
{
    if (isset($_POST[$params]))
    {
        return htmlspecialchars($_POST[$params]);
    }
    return false;
}
function returnCurrentTimeFromBootstrap($time)
{
    $explode = explode(" ", $time);
    $explodeMinus = explode(".", $explode[0]);

    $date = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];
    $time = $explode[1] . ":00";

    $allDate = $date . " " . $time;

    return $allDate;
}