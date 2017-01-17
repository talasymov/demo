<?php
require_once("./main.php");
require_once(APP_VIEWS . "pos/BanquetteBillView.php");

$view = new BanquetteBillView();

$ViewBuilder->AddFromClassView($view);
$ViewBuilder->Display();