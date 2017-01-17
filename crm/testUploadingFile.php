<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

?>

<html>
<head>
</head>
<body>
<input id="text" type="text" name="text"/>
<input id="sortpicture" type="file" name="sortpic"/>
<button id="upload"> Upload</button>
</body>
</html>