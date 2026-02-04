<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/includes/global.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php');

$b_id = $_GET['b_id'] ?? null;
$page = $_GET['page'] ?? null;

if ($b_id === null && $page === null) {
    die('GET OUT MF');
}

$limit = 50;
$offset = ((int)$page - 1) * $limit;

$query = "
WITH RankedComments AS (
    SELECT *, 
           ROW_NUMBER() OVER (PARTITION BY p_id ORDER BY id DESC) as rn
    FROM comments
)
SELECT p.*, rc.body as last_comment_body, rc.author as last_comment_author
FROM posts p
LEFT JOIN RankedComments rc ON p.id = rc.p_id AND rc.rn = 1
WHERE p.b_id = :b_id
ORDER BY p.id DESC
LIMIT :offset, :limit;
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(":b_id", $b_id, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($posts);