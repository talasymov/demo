<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
if (isset($_POST['dtpLeadsFrom']) && isset($_POST['dtpLeadsTo']))
{
    if ($_POST['dtpLeadsFrom'] == "0" || $_POST['dtpLeadsTo'] == "0" || $_POST['dtpLeadsFrom'] == "" || $_POST['dtpLeadsTo'] == "")
    {
        //echo "Пусто";
        $dtpLeadsFrom = date("Y-m-d");
        $dtpLeadsTo = date("Y-m-d");
    }
    else
    {
        /*
          $explode = explode(" ", $value->value);
          $explodeMinus = explode(".", $explode[0]);

          $orderBean["date"] = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];
         */

        $explode = explode(" ", $_POST['dtpLeadsFrom']);
        $explodeMinus = explode(".", $explode[0]);
        $dtpLeadsFrom = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];

        $explode = explode(" ", $_POST['dtpLeadsTo']);
        $explodeMinus = explode(".", $explode[0]);
        $dtpLeadsTo = $explodeMinus[2] . "-" . $explodeMinus[1] . "-" . $explodeMinus[0];
    }
}
else
{
    $dtpLeadsFrom = date("Y-m-d");
    $dtpLeadsTo = date("Y-m-d");
}
//echo date("Y-m-d");
//$company = R::getAll("SELECT * FROM dashboard_company");
//$clients = R::getAll("SELECT * FROM dashboard_clients");
//$clientsFrom = R::getAll("SELECT * FROM dashboard_money_from");
//update workers status if it is need now
//get worker and him/her status

$leadsThatAdd = R::getAll("SELECT dashboard_users.dashboard_users_id, dashboard_users.dashboard_users_name, COUNT(diary_leads.id) AS howMuchAdding FROM diary_leads
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = diary_leads.byWhomAdding
WHERE DATE(diary_leads.whenAdding) BETWEEN ? AND ? AND diary_leads.inTableCustomer = 0

GROUP BY dashboard_users.dashboard_users_name, dashboard_users.dashboard_users_id" ,[$dtpLeadsFrom, $dtpLeadsTo]);

$leadsThatTransfer = R::getAll("SELECT dashboard_users.dashboard_users_id, dashboard_users.dashboard_users_name, COUNT(diary_leads.id) AS howTransferToCustomer FROM diary_leads
INNER JOIN dashboard_users ON dashboard_users.dashboard_users_id = diary_leads.byWhomAdding

WHERE DATE(diary_leads.whenAdding) BETWEEN ? AND ? AND diary_leads.inTableCustomer = 1

GROUP BY dashboard_users.dashboard_users_name, dashboard_users.dashboard_users_id", [$dtpLeadsFrom, $dtpLeadsTo]);

$outTable = "";

/*
  echo "<pre>\n";
  print_r($leadsThatAdd);

  echo "<pre>\n";
  print_r($leadsThatTransfer);
 */

$arrayWithNededData = [];
foreach ($leadsThatAdd as $val)
{
    $arrayWithNededData[$val['dashboard_users_id']] = [];
    $arrayWithNededData[$val['dashboard_users_id']]['userName'] = $val['dashboard_users_name'];
    $arrayWithNededData[$val['dashboard_users_id']]['howMuchAdding'] = $val['howMuchAdding'];
}

foreach ($leadsThatTransfer as $val)
{
    $arrayWithNededData[$val['dashboard_users_id']]['howTransferToCustomer'] = $val['howTransferToCustomer'];
}

foreach ($arrayWithNededData as $key => $val)
{
    if (array_key_exists('howMuchAdding', $arrayWithNededData[$key]) == FALSE)
    {
        $arrayWithNededData[$key]['howMuchAdding'] = 0;
    }
    if (array_key_exists('howTransferToCustomer', $arrayWithNededData[$key]) == FALSE)
    {
        $arrayWithNededData[$key]['howTransferToCustomer'] = 0;
    }
}

/*
  echo "<pre>\n";
  print_r($arrayWithNededData);
 */


foreach ($arrayWithNededData as $key => $value)
{
    $outTable .= <<<EOF
  <tr>
  <td>{$value["userName"]}</td>
  <td>{$value["howMuchAdding"]}</td>
  <td>{$value["howTransferToCustomer"]}</td>
  <td>
  </tr>
EOF;
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Статистика по лидам</h2>
            <form action="/crm/statisticAllLeads.php" method="POST">
                <label for="input">С какого числа: </label>
                <div class="form-group">
                    <div class="input-group date" id="dtpLeadsFrom1">
                        <input type="text" name="dtpLeadsFrom" id="inputLeadsFrom" class="form-control" style="background: lightgray;">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    <script type="text/javascript">
                        $(function ()
                        {
                            $('#dtpLeadsFrom1').datetimepicker({
                                locale: 'ru'
                            });
                        });
                    </script>
                </div>
                <label for="input">По какое число: </label>
                <div class="form-group">
                    <div class="input-group date" id="dtpLeadsTo1">
                        <input type="text" name="dtpLeadsTo" id="inputLeadsTo" class="form-control" style="background: lightgray;">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    <script type="text/javascript">
                        $(function ()
                        {
                            $('#dtpLeadsTo1').datetimepicker({
                                locale: 'ru'
                            });
                        });
                    </script>
                </div>
                <button id="btnLeadsSort" class="btn btn-info" type="submit" style="width: 100%;">
                    Отсортировать
                </button>
            </form>
            <table class="table">
                <thead>
                    <tr>
                        <th>ФИО супервайзера</th>
                        <th>Кол-во добавленных лидов</th>
                        <th>Кол-во перенесенных в заказчики</th>
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
<?php require_once(APP_DIR_INC . "footer.php"); ?>