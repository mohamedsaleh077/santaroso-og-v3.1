<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/includes/global.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/upload.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php');

isPost();

if (empty($_POST['title']) || empty($_POST['board'])) {
    die('Not Allowed to post without title or board!');
}

$board = $_POST['board'];
$name = $_POST['name'] === '' ? $_POST['name'] : 'Anonymous' ;
$title = $_POST['title'];
$content = $_POST['content'] ?? null;
$csrf = $_POST['csrf_token'];
$media = $_FILES['media'] ?? null;

var_dump(isAllowed());
if (!isAllowed()){
    $_SESSION['name'] = $name ?? '';
    $_SESSION['title'] = $title ?? '';
    $_SESSION['content'] = $content ?? '';
    die('wait ' . timeLeft() . ' seconds!');
}

if (mb_strlen($title) < 10 || mb_strlen($title) > 250) {
    $_SESSION['name'] = $name ?? '';
    $_SESSION['title'] = $title ?? '';
    $_SESSION['content'] = $content ?? '';
    die('title is out of range 10-255');
}

if ( mb_strlen($content) > 5000 ){
    $_SESSION['name'] = $name ?? '';
    $_SESSION['title'] = $title ?? '';
    $_SESSION['content'] = $content ?? '';
    die('content is more than 5000');
}

if ($csrf !== $_SESSION['CSRF_TOKEN']) {
    $_SESSION['name'] = $name ?? '';
    $_SESSION['title'] = $title ?? '';
    $_SESSION['content'] = $content ?? '';
    die('CSRF validation failed');
}

$uploaded_file = null;
if(isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploaded_file =  upload();
}

$sql_user = "INSERT INTO users (ip) VALUES (:ip) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([':ip' => $_SERVER['REMOTE_ADDR']]);
$user_id = $pdo->lastInsertId();

$query = 'INSERT INTO posts (`userid`, `b_id`, `author`, `title`, `body`, `media`) 
VALUES (:userid, :b_id, :author, :title, :body, :media);';
$stmt = $pdo->prepare ( $query ) ;
$stmt -> bindParam ( ":userid" , $user_id  ) ;
$stmt -> bindParam ( ":b_id" , $board  ) ;
$stmt -> bindParam ( ":author" , $name  ) ;
$stmt -> bindParam ( ":title" , $title  ) ;
$stmt -> bindParam ( ":body" , $content  ) ;
$stmt -> bindParam ( ":media" , $uploaded_file) ;
$stmt -> execute () ;

$pdo = null ;
$stmt = null ;

setLastRequest();

header('location: /board.php?b=' . $board );
die();