<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/includes/global.inc.php');

isPost();

if (!isset($_FILES['title'])) {
    die('Not Allowed to post without title!');
}

$name = $_POST['name'] ?? 'Anonymous';
$title = $_POST['title'];
$content = $_POST['content'] ?? null;
$csrf = $_POST['csrf'];
$media = $_FILES['media'] ?? null;

if (mb_strlen($title) < 10 && mb_strlen($content) > 250) {
    die('title is out of range 10-255');
}

if ( mb_strlen($content) < 10 && mb_strlen($title) > 5000 ){
    die('content is out of range 10-5000');
}

if ($csrf !== $_POST['csrf']) {
    $_SESSION['name'] = $name ?? '';
    $_SESSION['title'] = $title ?? '';
    $_SESSION['content'] = $content ?? '';
    die('CSRF validation failed');
}
