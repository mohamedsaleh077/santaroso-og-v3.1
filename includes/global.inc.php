<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/includes/session.php');

function setLastRequest(){
    $_SESSION['lastSubmit'] = time();
}

function isAllowed(){
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

