<?php
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

    echo($out);