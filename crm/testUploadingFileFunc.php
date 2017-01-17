<?php
ini_set('display_errors', '1');
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");

$filePath = "";

if (0 < $_FILES['file']['error'])
{
    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
}
else
{
    $fileName = $_GET['text'].'.jpg';
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/images/companys/' . $fileName))
    {
        echo 'file exist';
    }
    else
    {
        $filePath = $_SERVER["DOCUMENT_ROOT"] . '/images/companys/';
        move_uploaded_file($_FILES['file']['tmp_name'], $filePath.$fileName);
        if (file_exists($filePath.$fileName))
        {
            echo 'file' . $filePath.$fileName . ' uploading SUCCESS';
        }
        else
        {
            echo 'file' . $filePath.$fileName . ' uploading FAIL';
        }

    }
}
?>