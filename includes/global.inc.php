<?php
ob_start();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'].'/includes/session.inc.php');

function setLastRequest(){
    $_SESSION['lastSubmit'] = time();
}

function isAllowed(){
    if (!isset($_SESSION['lastSubmit'])) {
        setLastRequest();
        return true;
    }
    return time() - $_SESSION['lastSubmit'] > 30;
}

function timeLeft(){
    $diff = time() - $_SESSION['lastSubmit'];
    if ($diff > 30) {
        return 0;
    }

    return 30 - $diff;
}
function isPost(){
    if(!$_SERVER['REQUEST_METHOD'] === 'POST'){
        die("unsupported request method");
    }
}