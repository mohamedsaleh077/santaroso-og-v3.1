<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/includes/global.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/upload.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php');

// التأكد من أن الطلب POST
isPost();

// التحقق من الحقول الأساسية (رقم البوست ومحتوى الكومنت)
if (empty($_POST['p_id']) || empty($_POST['body'])) {
    die('Error: Comment body and Post ID are required!');
}

$post_id = (int)$_POST['p_id'];
$author  = $_POST['author'] ?: 'Anonymous';
$body    = $_POST['body'];
$csrf    = $_POST['csrf_token'] ?? '';
$media   = $_FILES['media'] ?? null;

// التحقق من الـ CSRF لحماية الفورم
if ($csrf !== $_SESSION['CSRF_TOKEN']) {
    $_SESSION['name'] = $author ?? '';
    $_SESSION['content'] = $body ?? '';
    die('CSRF validation failed');
}

// منع السبام (التأكد من مرور وقت كافٍ بين الطلبات)
if (!isAllowed()){
    $_SESSION['name'] = $author ?? '';
    $_SESSION['content'] = $body ?? '';
    die('Please wait ' . timeLeft() . ' Seconds.');
}

// التحقق من طول محتوى الكومنت
if (mb_strlen($body) > 1000) {
    $_SESSION['name'] = $author ?? '';
    $_SESSION['content'] = $body ?? '';
    die('Comment is too long! Max 1000 characters.');
}

// معالجة رفع الملفات إن وجدت
$uploaded_file = null;
if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
    // سيقوم التابع upload بمعالجة الملف وإرجاع الاسم الجديد
    $uploaded_file = upload();
}

try {
    // 1. التعامل مع المستخدم: إدخال الـ IP أو تحديثه لجلب الـ ID
    $sql_user = "INSERT INTO users (ip) VALUES (:ip) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([':ip' => $_SERVER['REMOTE_ADDR']]);
    $user_id = $pdo->lastInsertId();

    // 2. إدخال الكومنت في جدول comments
    $query = 'INSERT INTO comments (`p_id`, `userid`, `author`, `body`, `media`) 
              VALUES (:p_id, :userid, :author, :body, :media);';

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':p_id'   => $post_id,
        ':userid' => $user_id,
        ':author' => $author,
        ':body'   => $body,
        ':media'  => $uploaded_file
    ]);

    // تحديث وقت آخر طلب للمستخدم لمنع السبام
    setLastRequest();

    // العودة لصفحة البوست بعد النجاح
    header('Location: ../post.php?id=' . $post_id);
    exit();

} catch (PDOException $e) {
    // في حالة الإنتاج يفضل تسجيل الخطأ في ملف بدلاً من عرضه
    die("Database Error: " . $e->getMessage());
}