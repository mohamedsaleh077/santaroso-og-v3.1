<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/includes/global.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php');

// تأمين المدخلات
$p_id = isset($_GET['p_id']) ? (int)$_GET['p_id'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($p_id === null) {
    http_response_code(400);
    die(json_encode(['error' => 'Post ID is required.']));
}

$limit = 20; // عدد الكومنتات في كل مرة
$offset = ($page - 1) * $limit;

try {
    // الاستعلام مع الـ Limit والـ Offset لعمل الـ Infinity Scroll/Load More
    $query = "
        SELECT 
            id, 
            p_id, 
            author, 
            body, 
            media, 
            created_at 
        FROM comments 
        WHERE p_id = :p_id 
        ORDER BY created_at ASC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":p_id", $p_id, PDO::PARAM_INT);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($comments);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database failure.']);
}