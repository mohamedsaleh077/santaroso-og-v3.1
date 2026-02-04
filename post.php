<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/session.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/global.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/db.inc.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: /");
    exit();
}

$post_id = (int)$_GET['id'];

// جلب بيانات البوست والبورد
$sql = "SELECT p.*, b.name as board_name, b.bg as board_bg, b.icon as board_icon, c.name as cat_name 
        FROM posts p
        JOIN boards b ON p.b_id = b.id
        JOIN category c ON b.c_id = c.id
        WHERE p.id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die('Error: Post not found.');
}

// إحصائية سريعة للكومنتات
$countComments = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE p_id = ?");
$countComments->execute([$post_id]);
$total_comments = $countComments->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> | Santaroso</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" id="theme-stylesheet" href="css/<?= $_COOKIE['user_theme'] ?? 'style' ?>.css">

    <title>サンタロソ | Santaroso VER3.1 OG</title>
    <link rel="icon" type="image/png" href="./assets/small-Logo.webp"/>
    
    <style>
        body { background: var(--body-bg) url('./uploads/<?= $post['board_bg'] ?>') fixed no-repeat center; background-size: cover; }
        .media-container video, .media-container img { max-width: 100%; max-height: 500px; border-radius: 4px; }
        .comment { background: rgba(255, 255, 255, 0.1); margin: 10px 0; padding: 10px; border-left: 3px solid var(--highlight); animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        #load-more-btn { width: 100%; padding: 10px; margin: 20px 0; cursor: pointer; background: var(--highlight); border: none; color: white; border-radius: 4px; }
        #load-more-btn:disabled { background: #555; cursor: not-allowed; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="title">
            <img src="./uploads/<?= $post['board_icon'] ?>" alt="logo" width="100">
            <h1><?= htmlspecialchars($post['cat_name']) ?> > <?= htmlspecialchars($post['board_name']) ?></h1>
        </div>
    </header>

    <div class="posts">
        <button onclick="window.history.back();">← Back</button>

        <div class="post" id="post-<?= $post['id'] ?>">
            <div class="post-header">
                <span class="user"><?= htmlspecialchars($post['author']) ?></span>
                <span class="timestamp"> • <?= $post['created_at'] ?></span>
                <span class="id">ID: <?= $post['id'] ?></span>
            </div>
            <p class="post-titel"><?= htmlspecialchars($post['title']) ?></p>

            <div class="media-container">
                <?php if ($post['media']): ?>
                    <?php if (str_starts_with($post['media'], 'VID_')): ?>
                        <video controls loop><source src="./uploads/<?= $post['media'] ?>" type="video/mp4"></video>
                    <?php else: ?>
                        <a href="./uploads/<?= $post['media'] ?>" target="_blank"><img src="./uploads/<?= $post['media'] ?>"></a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="post-content"><?= nl2br(htmlspecialchars($post['body'])) ?></div>
        </div>

        <div class="comments-section">
            <p>Total: <span id="comment-count"><?= $total_comments ?></span> comment(s)</p>

            <div id="commentsList">
                <!-- الكومنتات هتنزل هنا بالأجاكس -->
            </div>

            <div id="loading" style="display: none; text-align: center;">Loading...</div>
            <button id="load-more-btn">Load More Comments</button>

            <div class="comment-form">
                <form action="./requests/createComment.inc.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['CSRF_TOKEN'] ?? ''; ?>">
                    <input type="hidden" name="p_id" value="<?= $post['id']; ?>">
                    <input type="text" name="author" placeholder="Anonymous" maxlength="20" value="<?= $_SESSION['name'] ?? ''; ?>">
                    <textarea name="body" placeholder="Write a comment..." required maxlength="300"><?= $_SESSION['content'] ?? ''; ?></textarea>
                    <div class="form-actions">
                        <input type="file" name="media" accept="image/*">
                        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/cooldown.php'); ?>
                        <button type="submit" id="submit-btn" <?= ($remaining_time > 0) ? 'disabled' : '' ?>>
                            <?= ($remaining_time > 0) ? "wait ($remaining_time)" : "post" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let timeLeft = <?= (int)$remaining_time ?>;
        const btn = $('#submit-btn');

        if (timeLeft > 0) {
            const timer = setInterval(function() {
                timeLeft--;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    btn.prop('disabled', false);
                    btn.text('post');
                } else {
                    btn.text('wait (' + timeLeft + ')');
                }
            }, 1000);
        }

        let currentPage = 1;
        let isLoading = false;
        let postID = <?= $post['id'] ?>;

        function loadComments(page) {
            if (isLoading) return;
            isLoading = true;
            $('#loading').show();
            $('#load-more-btn').hide();

            $.get('requests/getComments.php', { p_id: postID, page: page }, function(data) {
                $('#loading').hide();
                isLoading = false;

                if (data.length === 0) {
                    if (page === 1) {
                        $('#commentsList').html('<p>No comments yet.</p>');
                    } else {
                        $('#load-more-btn').after('<p style="text-align:center">No more comments.</p>').remove();
                    }
                    return;
                }

                let html = '';
                data.forEach(function(comment) {
                    let mediaHtml = comment.media ? `<div class="media-container"><img src="./uploads/${comment.media}" style="max-width:200px"></div>` : '';
                    html += `
                <div class="comment" id="comment-${comment.id}">
                    <div class="post-header">
                        <span class="user">${escapeHtml(comment.author)}</span>
                        <span class="timestamp"> • ${comment.created_at}</span>
                        <span class="id">ID: ${comment.id}</span>
                    </div>
                    <div class="comment-body">${escapeHtml(comment.body).replace(/\n/g, '<br>')}</div>
                    ${mediaHtml}
                </div>`;
                });

                $('#commentsList').append(html);
                $('#load-more-btn').show();

                // لو الداتا اللي رجعت أقل من الـ Limit (20) يبقى مفيش تاني
                if (data.length < 20) {
                    $('#load-more-btn').after('<p style="text-align:center">No more comments.</p>').remove();
                }
            }).fail(function() {
                $('#loading').hide();
                alert('Failed to load comments.');
            });
        }

        function escapeHtml(text) {
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
        }

        $('#load-more-btn').click(function() {
            currentPage++;
            loadComments(currentPage);
        });

        // تحميل أول مجموعة
        loadComments(1);
    });
</script>
</body>
</html>