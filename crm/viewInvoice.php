<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$outProducts = "";
$products = "";
$h2 = "";
$totalPriceOfAll = 0;
$countOfProducts = 0;
$companyData = "";

if (isset($_GET['productsOrderGroup']) && $_GET['productsOrderGroup'] != null)
{
    $products = R::getAll("SELECT companyName, dashboard_productsOrderGroup.id AS numberOfOrderGroup, dashboard_productsCategory.name AS productsCategoryName, dashboard_productsSubCategory.name AS productsSubCategoryName, dashboard_productsOrderGroup.dateOfOrder AS dateOfOrder, dashboard_customers.phone, dashboard_customers.companyId AS companyId, dashboard_customers.email, dashboard_customers.firstName, dashboard_customers.lastName, dashboard_customers.patronymicName, dashboard_products.name, dashboard_productsOrders.count, dashboard_productsOrders.cost, dashboard_productsOrders.information, ROUND(dashboard_productsOrders.count*dashboard_productsOrders.cost, 2) AS totalPricePerThisProduct FROM dashboard_productsOrders

INNER JOIN dashboard_productsOrderGroup ON dashboard_productsOrderGroup.id = dashboard_productsOrders.productsOrderGroupId
INNER JOIN dashboard_customers ON dashboard_customers.customerId = dashboard_productsOrderGroup.customerId
INNER JOIN dashboard_companies ON dashboard_companies.id = dashboard_customers.companyId
INNER JOIN dashboard_products ON dashboard_productsOrders.productId = dashboard_products.id
INNER JOIN dashboard_productsCategory ON dashboard_productsCategory.id = dashboard_products.categoryId
INNER JOIN dashboard_productsSubCategory ON dashboard_productsSubCategory.id = dashboard_products.subCategoryId

WHERE dashboard_productsOrderGroup.id = {$_GET['productsOrderGroup']}");

    $countOfProducts = count($products);

    if (isset($products[0]))
    {
        $h2 = '<h2 id="titleOfInvoice">СЧЕТ № ' . $products[0]["numberOfOrderGroup"] . ' от ' . $products[0]["dateOfOrder"] . '<br /><span class=\'mini\'>За услуги рекламы</span></h2>';
        $outProducts = "";
        foreach ($products as $key => $productsValue)
        {
            $nameCompany = "";

            if($productsValue["companyId"] != 1)
            {
                $nameCompany = "<br /><br />Компания: " . $productsValue["companyName"] . "<br />Ответственный: ";
            }
            $companyData =  $nameCompany . $productsValue["lastName"] . " " . $productsValue["firstName"] . " " . $productsValue["patronymicName"] . ", " . $productsValue["phone"] . ", " . $productsValue["email"];
            $key += 1;
            $outProducts .= <<<EOF
                  <tr>
                    <td>{$key}</td>
                    <td>{$productsValue["productsCategoryName"]}, {$productsValue["productsSubCategoryName"]}, {$productsValue['name']} {$productsValue['information']}</td>
                    <td>шт.</td>
                    <td>{$productsValue['count']}</td>
                    <td>{$productsValue['cost']}</td>
                    <td>{$productsValue['totalPricePerThisProduct']}</td>
                  </tr>
EOF;
            $totalPriceOfAll += $productsValue['totalPricePerThisProduct'];
        }
    }
    else
    {
        $h2 = '<h2 id="titleOfInvoice">СЧЕТ № 0 от 00.00.0000</h2>';
    }
}
else
{
    echo 'Нет товаров для накладной';
}

$table = "";

function num2str($num)
{
    $nul = 'ноль';
    $ten = array(
        array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
    );
    $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
    $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
    $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
    $unit = array(// Units
        array('копейка', 'копейки', 'копеек', 1),
        array('гривна', 'гривни', 'гривен', 0),
        array('тысяча', 'тысячи', 'тысяч', 1),
        array('миллион', 'миллиона', 'миллионов', 0),
        array('миллиард', 'милиарда', 'миллиардов', 0),
    );
    //
    list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub) > 0)
    {
        foreach (str_split($rub, 3) as $uk => $v)
        { // by 3 symbols
            if (!intval($v))
                continue;
            $uk = sizeof($unit) - $uk - 1; // unit key
            $gender = $unit[$uk][3];
            list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2 > 1)
                $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3];# 20-99
            else
                $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];# 10-19 | 1-9
            // units without rub & kop
            if ($uk > 1)
                $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
        } //foreach
    }
    else
        $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
    $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5)
{
    $n = abs(intval($n)) % 100;
    if ($n > 10 && $n < 20)
        return $f5;
    $n = $n % 10;
    if ($n > 1 && $n < 5)
        return $f2;
    if ($n == 1)
        return $f1;
    return $f5;
}
?>
<center><button class="btn btn-default" onclick="this.remove();window.print()">В печать <i class="fa fa-print" aria-hidden="true"></i></button></center>
<div class="container">
    <div class="row">
        <div class="col-md-12">
          <div id="header-invoice" class="fs-16">
            <img src="/images/Logo-B-05.svg" alt="" width="250">
            <p>Рекламное агентство "Champion Group"</p>
            <p>Украина, г. Одесса, Новосельского, 5</p>
            <p>Тел.: (048) 736-26-49, (095) 862-61-64, (096) 321-58-11, (093) 946-14-95</p>
          </div>
            <table border="1" class="table table-striped table-bordered fs-16">
              <tr>
                <td><p>Получатель</p><p>Рекламное агентство "Champion Group"</p></td>
                <td>5168 7420 2461 4384<br >Попович К.В.</td>
              </tr>
              <tr>
                <td><p>Банк получателя</p></td>
                <td><p>ПриватБанк</p></td>
              </tr>
            </table>
            <?php
            print($h2);
            print("<span class=\"fs-16\">Плательщик: " . $companyData . "</span><br /><br />");
            ?>
            <table class="table table-striped table-bordered fs-16">
                <thead>
                <th>№</th>
                <th>Наименование товара</th>
                <th>Единица</th>
                <th>Количество</th>
                <th>Цена, грн</th>
                <th>Сумма, грн</th>
                </thead>
                <tbody>
                    <?php print($outProducts); ?>
                <th colspan="4" style="border-left: 0px; border-bottom: 0px;"></th>
                <th>Итого, грн: </th>
                <th><?php print($totalPriceOfAll); ?></th>
                </tbody>
            </table>
            <br /><br />
            <h3 class="fs-20">Всего <?php print($countOfProducts); ?> продукт(а/ов), на сумму <?php print($totalPriceOfAll); ?> грн.</h3>
            <h3 class="fs-20"><?php print(num2str($totalPriceOfAll)); ?></h3><br />
            <h5 class="fs-20">Руководитель предприятия _________________________________(Попович К.В.)</h5><br />
            <h5 class="fs-20">Предоплата ___________________</h5>
        </div>
    </div>
</div>
<?php require_once(APP_DIR_INC . "footer.php"); ?>