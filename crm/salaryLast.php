<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$dateFrom = date("1970-m-d");
$dateTo = date("Y-m-d");
$getWorkerId  = "";

if(isset($_GET["idWorker"]))
{
  $getWorkerId = $_GET["idWorker"];
}

if(isset($_GET["date_from"]) && $_GET["date_from"] != null)
{
  $dateFrom = $_GET["date_from"];
}
if(isset($_GET["date_to"]) && $_GET["date_to"] != null)
{
  $dateTo = $_GET["date_to"];
}


$data = R::getAll("SELECT * FROM dashboard_users");

$out = "";
$moneyPay = 0;

foreach ($data as $key => $value) {
  $link = "";
  $money = "<span class='good-line'>0</span>";

  if($value["dashboard_users_permissions"] == "supervisor")
  {
    $link = <<<EOF
    <a href="/crm/salarySuperVisor.php?supervisorId={$value["dashboard_users_id"]}"><button class="btn btn-default dropdown-toggle dib">
      <span class="status">
        <i class="fa fa-arrow-right" aria-hidden="true"></i>
      </span>
    </button></a>
EOF;

    $countDay = R::getAll("SELECT * FROM basic_day_registration WHERE basic_day_registration_status = 0 AND basic_day_registration_who = ?", [$value["dashboard_users_id"]]);
    $countOrder = R::getAll("SELECT * FROM diary_orders WHERE byWhomAdding = ? AND pay_super = 0 AND status = 4", [$value["dashboard_users_id"]]);

    $count = count($countDay) + count($countOrder);

    if($count > 0)
    {
      $money = "<span class='bad-line'>" . count($countDay) . " + " . count($countOrder) . "</span>";
    }
  }
  $out .= <<<EOF
  <tr>
    <td>{$value["dashboard_users_name"]}</td>
    <td>{$value["dashboard_users_email"]}</td>
    <td>{$value["dashboard_users_phone"]}</td>
    <td>{$value["dashboard_users_skype"]}</td>
    <td>{$value["dashboard_users_permissions"]}</td>
    <td>{$money}</td>
    <td>{$link}</td>
  </tr>
EOF;
}

$output = <<<EOF
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Сотрудники компании и оплата</h2>
            <a href="/crm/slalarySuper.php">
            <button class="btn btn-default mr-10" type="button" id="showThisDateEmployment">
                Зарплата работников&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-up" aria-hidden="true"></i>
            </button><br /><br />
            </a>
            <table class="table table-striped table-bordered dataTable no-footer">
                <thead>
                  <tr>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Skype</th>
                    <th>Должность</th>
                    <th>Расчет</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {$out}
                </tbody>
            </table>
        </div>
    </div>
</div>
EOF;
print($output);
?>

<?php require_once(APP_DIR_INC . "footer.php"); ?>
