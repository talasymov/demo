<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");

$output = "";

$days = R::getAll("
SELECT * FROM basic_day_registration
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = basic_day_registration.basic_day_registration_who
WHERE DATE(basic_day_registration_date_start) = ?", [date("Y-m-d")]);

foreach ($days as $key => $value)
{
    $startTime = strtotime($userDay["basic_day_registration_date_start"]);
    $endTime = strtotime($userDay["basic_day_registration_date_stop"]);

    $allTime = $endTime - $startTime;

    $hour = (int) ($allTime / 3600);
    $minutes = ($allTime / 60 % 60);
    $seconds = ($allTime % 60);

    $workingTime = $hour . " часов " . $minutes . " минут " . $seconds . " секунд";

    $h1 = ($hour + ($minutes / 60));
    $salary = $value["salaryPerDay"] / 9;

    $moneyForWorker = round($h1 * $salary) . " грн";

    $output .= "<tr><td>" . $value["basic_day_registration_date_start"] . "</td><td>" . $value["basic_day_registration_date_stop"] . "</td><td>" . $workingTime . "</td><td>" . $moneyForWorker . "</td><td>" . $value["dashboard_users_name"] . "</td></tr>";
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Время работы сотрудников</h2>
            <table class="table exampleDataTable">
                <thead>
                <tr>
                    <th>Начало рабочего дня</th>
                    <th>Конец рабочего дня</th>
                    <th>Всего проработано часов</th>
                    <th>Всего заработано</th>
                    <th>ФИО сотрудника</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    echo $output;
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>
