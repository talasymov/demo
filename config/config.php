<?php
session_start();

$first_time = false;

if (!isset($_COOKIE["infoBlock2"])) {
	$first_time = true;
	setcookie("infoBlock2", "1", time() + 3600*24*10, "/");
	/* помнить десять дней, куки действуют на всех страницах сайта */
}

// if( ! ini_get('date.timezone') )
// {
//     date_default_timezone_set('Europe/Kiev');
// }
define( "APP_DIR", $_SERVER["DOCUMENT_ROOT"] . "/" );
define( "APP_VIEWS", APP_DIR . "application/views/" );
define( "APP_DIR_IMAGE" , APP_DIR . "images/" );
define( "APP_DIR_STYLES" , APP_DIR . "styles/" );
define( "APP_DIR_SCRIPTS" , APP_DIR . "js/" );
define( "APP_DIR_INCLUDES" , APP_DIR . "includes/" );
define( "APP_DIR_INC" , APP_DIR . "inc/" );
define( "APP_DIR_LIBS" , APP_DIR . "libs/" );
define( "APP_DIR_RB" , APP_DIR_LIBS . "redbeans/rb.php" );

define("DB_SERVER", "localhost");	//	HOST
define("DB_USER", "root");	// USERNAME
define("DB_PASS", "swe1309ane");	// PASSWORD
define("DB_NAME", "crm");	// DATABASE NAME

require_once( APP_DIR_RB );

R::setup( 'mysql:host='.DB_SERVER.';dbname='.DB_NAME, DB_USER, DB_PASS );

R::ext('xdispense', function( $type ){
		return R::getRedBean()->dispense( $type );
});

//Based functions
startWorkingDay();
function startWorkingDay()
{
    if($_COOKIE["userId"])
    {
        $cookieUserId = intval($_COOKIE["userId"]);

        $checkDay = R::getRow("SELECT * FROM basic_day_registration WHERE DATE(basic_day_registration_date_start) = ? AND basic_day_registration_who = ?", [date("Y-m-d"), $cookieUserId]);

        if (!isset($checkDay["basic_day_registration_id"]) || $checkDay["basic_day_registration_id"] == null)
        {
            R::exec("INSERT INTO basic_day_registration(basic_day_registration_date_start, basic_day_registration_date_stop, basic_day_registration_who) VALUES(?, ?, ?)", [date("Y-m-d H:i:s"), "1970-10-10 00:00:00", $cookieUserId]);
        }
    }
}

function EchoDiv()
{
	$outEchoDiv = <<<EOF
	<script>
		$(document).ready(function(){
			div = "Что было сделано?<br />" +
				"<ul>" +
					"<li>Алгоритм создания заказа изменен, редактирование добавлено!</li>" +
					"<li>Поиск по клиентам на 80% завершен!</li>" +
					"<li>Оптимизированы страницы. Теперь загрузка стала быстрее! Выполнено на 70%</li>" +
					"<li>Рабочий день начинается при открытии программы!</li>" +
				"</ul>";
			bootbox.alert({
				size: "large",
				title: "ВНИМАНИЕ!",
				message: div ,
				callback: function(){
					setTimeout(modalOpen, 1000);
				}
			});
		});
	</script>
EOF;

	echo $outEchoDiv;
}

function GetTarifsDesigner($data)
{
	if($data == 1)
	{
		return "Простой";
	}
	else if($data == 2)
	{
		return "Средний";
	}
	else if($data == 3)
	{
		return "Сложный";
	}
}

function GetOrientationDesigner($data)
{
	if($data == 1)
	{
		return "Горизонтальная";
	}
	else if($data == 2)
	{
		return "Вертикальная";
	}
}

function RTO($query, $blackList = null, $type = null)
{
	$diaryOrders = R::getAll($query);

	foreach($diaryOrders as $key => $value)
	{
		foreach($value as $subkey => $subvalue)
		{
			if(!in_array($subkey, $blackList))
			{
				echo $subkey . "<br />";
			}
		}
	}
}
function getCompany($option)
{
	$companys = R::getAll("SELECT * FROM dashboard_companies
	INNER JOIN dashboard_customers ON dashboard_customers.companyId = dashboard_companies.id
	WHERE dashboard_customers.companyId <> 1 AND dashboard_customers.companyId <> 0");

	if($option == "option")
	{
		$result = "<option value=\"0\">Выберите компанию</option><option value=\"1\">Физическое лицо</option>";

		foreach($companys as $key => $value)
		{
			$nameCompany = $value["companyName"];
			$idCompany = $value["id"];

			$result .= "<option value=\"{$idCompany}\">{$nameCompany}</option>";
		}
		return $result;
	}
}
function getLeftMenu()
{
	$outCalls = "";

	$calls = R::getAll("SELECT * FROM dashboard_calls WHERE dashboard_calls_whoadd = ? AND dashboard_calls_status = ? AND DATE(dashboard_calls_date) = ?", [$_COOKIE["userId"], 0, date("Y-m-d")]);

	foreach($calls as $key => $value) {
		$date = explode(" ", $value["dashboard_calls_date"]);
		$time = substr($date[1], 0, 5);
		$hours = explode(":", $time);
		$classCall = "default";

		if ($hours[0] <= date("H")) {
			$classCall = "green";
		}

		$comment = $value["dashboard_calls_comment"];
		$name = $value["dashboard_calls_name"];
		$phone = $value["dashboard_calls_phone"];

		$outCalls .= <<<EOF
        <div class="quick-calls {$classCall}" data-toggle="tooltip" data-placement="bottom" data-html="true" data-title="ФИО: {$name}<br />Телефон: {$phone}<br />Комментарий: {$comment}"><strong>{$time}</strong><br />{$name}</div>
EOF;
	}
	$out = <<<EOF
    <div id="left-quick-menu">
        <div class="dropdown">
            <button class="btn-full-blue dropdown-toggle" type="button" data-toggle="dropdown">Создать <i class="fa fa-plus-circle" aria-hidden="true"></i></button>
            <ul class="dropdown-menu">
              <li><a href="#"><i class="fa fa-user" aria-hidden="true"></i> Компанию</a></li>
              <li><a href="#"><i class="fa fa-user" aria-hidden="true"></i> Клиента</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="#"><i class="fa fa-bell" aria-hidden="true"></i> Лид</a></li>
              <li><a href="#"><i class="fa fa-phone" aria-hidden="true"></i> Звонок</a></li>
              <li><a href="#"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Заказ</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="#"><i class="fa fa-life-ring" aria-hidden="true"></i> Предложение</a></li>
            </ul>
        </div>
        <h3>Звонки на сегодня</h3>
        {$outCalls}
    </div>
EOF;

	return $out;
}

//function ButtonTypeOrderSupervisor($title, $dataName, $idInput, $baseId = null, $baseText = null) // $title - button text; $dataName - name database; $idInput - element id or class, where print result
//{
//    $button = <<<EOF
//    <input type="hidden" id="{$idInput}" value="{$baseId}">
//    <button class="btn btn-primary select-client-modal" data-name-result="#{$idInput}" data-name-db="{$dataName}">
//       {$title}&nbsp;&nbsp;<i class="fa fa-database" aria-hidden="true"></i>
//    </button>
//    <span id="{$idInput}printResult" class="text-near-button">{$baseText}</span>
//EOF;
//
//    return $button;
//}

function ButtonSelectSupervisorWorkers($title, $dataName, $idInput, $baseId = null, $baseText = null) // $title - button text; $dataName - name database; $idInput - element id or class, where print result
{
	$button = <<<EOF
    <input type="hidden" id="{$idInput}" value="{$baseId}">
    <button class="btn btn-primary select-super-worker-modal" data-name-result="#{$idInput}" data-name-db="{$dataName}">
       {$title}&nbsp;&nbsp;<i class="fa fa-database" aria-hidden="true"></i>
    </button>
    <span id="{$idInput}printResult" class="text-near-button">{$baseText}</span>
EOF;

	return $button;
}

function ButtonSupervisorType($title, $dataName, $idInput, $baseId = null, $baseText = null) // $title - button text; $dataName - name database; $idInput - element id or class, where print result
{
	$button = <<<EOF
    <input type="hidden" id="{$idInput}" value="{$baseId}">
    <button class="btn btn-primary select-super-type-modal" data-name-result="#{$idInput}" data-name-db="{$dataName}">
       {$title}&nbsp;&nbsp;<i class="fa fa-database" aria-hidden="true"></i>
    </button>
    <span id="{$idInput}printResult" class="text-near-button">{$baseText}</span>
EOF;

	return $button;
}

function PrintButtonSelect($title, $dataName, $idInput, $baseId = null, $baseText = null) // $title - button text; $dataName - name database; $idInput - element id or class, where print result
{
	$button = <<<EOF
    <input type="hidden" id="{$idInput}" value="{$baseId}">
    <button class="btn btn-primary select-client-modal" data-name-result="#{$idInput}" data-name-db="{$dataName}">
       {$title}&nbsp;&nbsp;<i class="fa fa-database" aria-hidden="true"></i>
    </button>
    <span id="{$idInput}printResult" class="text-near-button">{$baseText}</span>
EOF;

	return $button;
}

function PrintButtonSelectCompany($title, $dataName, $idInput, $baseId = null, $baseText = null) // $title - button text; $dataName - name database; $idInput - element id or class, where print result
{
    $button = <<<EOF
    <input type="hidden" id="{$idInput}" value="{$baseId}">
    <button class="btn btn-primary select-company-modal" data-name-result="#{$idInput}" data-name-db="{$dataName}">
       {$title}&nbsp;&nbsp;<i class="fa fa-database" aria-hidden="true"></i>
    </button>
    <span id="{$idInput}printResult" class="text-near-button">{$baseText}</span>
EOF;

    return $button;
}

function PrintButtonSelectProduct($title, $idInput, $baseId = null, $baseText = null, $idOrder = null) // $title - button text; $dataName - name database; $idInput - element id or class, where print result
{
	$button = <<<EOF
    <input type="hidden" id="{$idInput}" class="listOrders" data-id="{$idOrder}" value="{$baseId}">
    <button class="btn btn-primary select-product-modal" data-name-result="#{$idInput}">
       {$title}&nbsp;&nbsp;<i class="fa fa-archive" aria-hidden="true"></i>
    </button>
    <span id="{$idInput}printResult" class="text-near-button">{$baseText}</span>
EOF;

	return $button;
}

function StyleInput($prefix, $name, $value = null, $class = null)
{
    $outInput = <<<EOF
    <div class="input-label">
        <input id="{$prefix}_{$name}" type="text" class="form-control {$class}" name="{$name}"
               value="{$value}" placeholder="">
        <span class="line-input"></span>
    </div>
EOF;
    return $outInput;
}

function StyleTextArea($prefix, $name, $value = null, $class = null)
{
    $outTextArea = <<<EOF
    <textarea id="{$prefix}_{$name}" type="text" class="form-control {$class}" rows="1" name="name" placeholder="">{$value}</textarea>
EOF;
    return $outTextArea;
}

function SelectorDate($idDate, $value = null)
{
    $outDate = <<<EOF
    <div class='input-group date dateFromTo' id=''>
        <input type='text' class="form-control" id="{$idDate}" value="{$value}" />
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>

    <script type="text/javascript">
      $(function () {
        $('#{$idDate}').datetimepicker({
          format: 'YYYY-MM-DD',
          locale: 'ru'
        });
      });
    </script>
EOF;
    return $outDate;
}
?>
