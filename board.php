<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/session.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/db.inc.php'); // تأكد من استدعاء ملف الداتا

// 1. التحقق من وجود ID في الرابط
if (!isset($_GET['b']) || empty($_GET['b'])) {
    die('Error: No board ID specified. Go back to <a href="/">Home</a>.');
}

$board_id = (int)$_GET['b'];

// 2. جلب بيانات البورد والكاتيجوري بخبطة واحدة (JOIN)
$sql = "SELECT b.*, c.name AS cat_name 
            FROM boards b 
            JOIN category c ON b.c_id = c.id 
            WHERE b.id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $board_id]);
$board = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. لو مفيش بورد بالـ ID ده
if (!$board) {
    die('Error: This board does not exist or has been deleted.');
}

// 4. جلب إحصائيات سريعة (اختياري عشان نخلي الـ Welcome مسطرة)
$countPosts = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE b_id = ?");
$countPosts->execute([$board_id]);
$total_posts = $countPosts->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>サンタロソ | Santaroso VER3.1 OG</title>
    <link rel="icon" type="image/png" href="./assets/small-Logo.webp"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" id="theme-stylesheet" href="css/<?= $_COOKIE['user_theme'] ?? 'style' ?>.css">
    <style>
        body {
            background: var(--body-bg) url('./uploads/<?= $board['bg'] ?>');
            background-size: cover;
            background-attachment: fixed;
        }
    </style>
</head>

<body>

<div class="container">
    <header>
        <div class="title">
            <img src="./uploads/<?= $board['icon'] ?>" alt="logo" width="150">
            <a href="./home.php"><img class="primarylogo" src="./assets/Santaroso-Logo.webp" alt="logo" width="300"></a>
        </div>
        <div class="title">
            <h1><?= htmlspecialchars($board['cat_name']) ?> &gt; <?= htmlspecialchars($board['name']) ?></h1>
        </div>
        <div class="title">
            <p>Welcome to <span class="highlight"><?= htmlspecialchars($board['name']) ?></span> Board!
                We have <span class="highlight"><?= $total_posts ?></span> posts!
                <a href="../report.php">want to send a report?</a>
            </p>
        </div>
        <p><?= htmlspecialchars($board['description']) ?></p>
    </header>
    <nav>
        ->
        <?php
        $all_boards = $pdo->query("SELECT id, name FROM boards")->fetchAll();
        foreach ($all_boards as $b_nav) {
            echo '<a href="board.php?b=' . $b_nav['id'] . '">/' . $b_nav['name'] . '</a> ';
        }
        ?>
        -
    </nav>

    <?php
    $css_directory = $_SERVER['DOCUMENT_ROOT'] . '/css/';
    $theme_files = [];

    if (is_dir($css_directory)) {
    // جلب كل ملفات .css وتجاهل المجلدات والنقاط
    $files = scandir($css_directory);
    foreach ($files as $file) {
    $name = pathinfo($file, PATHINFO_FILENAME);
    $theme_files[$name] = $file;
    }
    }

    // 2. جلب الثيم الحالي من الكوكيز
    $current_theme = $_COOKIE['user_theme'] ?? 'style'; // 'style' هو الافتراضي
    ?>

    <div class="theme-selector-container">
        <form action="/requests/setTheme.inc.php" method="POST">
            <label for="theme-select" >Select Style:</label>
            <select name="theme_name" id="theme-select">
                <?php foreach ($theme_files as $key => $file): ?>
                    <option value="<?= htmlspecialchars($key) ?>" <?= ($current_theme === $key) ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($key)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">
                Apply
            </button>
        </form>
    </div>

    <script>
        /**
         * اختياري: تغيير الثيم للمعاينة الفورية قبل الضغط على Apply
         */
        $(document).ready(function() {
            $('#theme-select').on('change', function() {
                const themeName = $(this).val();
                $('#theme-stylesheet').attr('href', 'css/' + themeName + '.css');
            });
        });
    </script>

    <div class="posts">

        <div class="toggles">
            <button id="form-toggle">form toggle</button>
        </div>

        <?php if (!empty($board['vid'])): ?>
            <div class="toggles">
                <button id="vid-toggle">video toggle</button>
            </div>

            <video id="vid" autoplay loop controls style="display: none;">
                <source src="<?= $board['vid'] ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        <?php endif; ?>

        <form id="post-form" style="display: none;" method="post" enctype="multipart/form-data" class="post-form"
              action="./requests/createPost.inc.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['CSRF_TOKEN']; ?>">
            <div>
                <input type="hidden" name="board" value="<?= $_GET['b']; ?>">
                <input type="text" name="name" id="name" placeholder="Anonymous (Default)"
                       value="<?= $_SESSION['name'] ?? ''; ?>">
                <input type="text" name="title" id="title" placeholder="Post Title *" required maxlength="250"
                       value="<?= $_SESSION['title'] ?? ''; ?>">
                <textarea id="content" name="content" placeholder="Post Body" maxlength="5000"
                          rows="7"><?= $_SESSION['content'] ?? ''; ?></textarea>
                <?php
                unset($_SESSION['content']);
                unset($_SESSION['title']);
                ?>
            </div>
            <div>
                <label for="media">Upload media:</label>
                <input type="file" name="media" id="media"
                       accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm">
                <small>Images: max 2MB (JPG, PNG, GIF, WebP) | Videos: max 5MB (MP4, WebM)</small>
            </div>
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/cooldown.php'); ?>
            <button type="submit" id="submit-btn" <?= ($remaining_time > 0) ? 'disabled' : '' ?>>
                <?= ($remaining_time > 0) ? "wait ($remaining_time)" : "post" ?>
            </button>
        </form>

        <script>
            $(document).ready(function () {
                $("#vid-toggle").click(function () {
                    $("#vid").toggle();
                })

                $("#form-toggle").click(function () {
                    $("#post-form").toggle();
                })

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
            })
        </script>

        <div id="postsList">

        </div>

        <div id="scroll-anchor" style="height: 20px;"></div>

        <div id="loading" style="display: none; text-align: center;">Loading...</div>


    </div>
</div>

<script>
    $(document).ready(function () {
        let currentPage = 1;
        let isLoading = false;
        let noMorePosts = false;

        function escapeHtml(text) {
            if (!text) return "";
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
        }

        function getPage(p) {
            if (isLoading || noMorePosts) return;

            isLoading = true;
            $('#loading').show();

            $.get('requests/getPosts.inc.php?b_id=' + <?= (int)$_GET['b'] ?> + '&page=' + p, function (data) {
                try {
                    data = JSON.parse(data);
                } catch(e) {
                    console.error("Invalid JSON", data);
                    return;
                }

                $('#loading').hide();
                isLoading = false;

                if (data.length === 0) {
                    if (p === 1) {
                        $('#postsList').html('<p>No posts yet.</p>');
                    } else {
                        if (!$('#no-more-msg').length) {
                            $('#postsList').append('<p id="no-more-msg" style="text-align:center">No more posts to show.</p>');
                        }
                    }
                    noMorePosts = true;
                    return;
                }

                let content = '';
                data.forEach(function (post) {
                    // 1. معالجة الميديا
                    let mediaHtml = '';
                    if (post.media) {
                        const isVideo = post.media.startsWith('VID_');
                        mediaHtml = `
                        <div class="media-container" style="position:relative">
                            <a href="./uploads/${post.media}" class="media-link" target="_blank">
                                <img src="./uploads/THUMB_${post.media}.jpg" class="media-thumbnail" alt="Post image">
                            </a>
                        </div>`;
                    }

                    // 2. معالجة آخر تعليق (Last Comment Preview)
                    let lastCommentHtml = '';
                    if (post.last_comment_body) {
                        lastCommentHtml = `
                        <div class="last-comment-preview" style="background: rgba(0,0,0,0.2); padding: 10px; margin-top: 10px;font-size: 0.9em; border-left: 2px solid var(--highlight);">
                            <small style="color: var(--highlight); font-weight: bold;">Last Reply by ${escapeHtml(post.last_comment_author)}:</small>
                            <p style="margin: 5px 0 0 0;">${escapeHtml(post.last_comment_body)}</p>
                        </div>`;
                    }

                    // 3. بناء هيكل البوست الكامل
                    content += `
                    <div class="post" id="post-${post.id}" style="margin-bottom: 20px; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                        <div class="post-header">
                            <span class="user">${escapeHtml(post.author)}</span>
                            <span class="timestamp"> • ${post.created_at}</span>
                            <span class="id">ID: ${post.id}</span>
                        </div>
                        <p class="post-titel" style="font-weight: bold; color: var(--highlight);">${escapeHtml(post.title)}</p>
                        <div class="post-content" style="margin: 10px 0;">
                            ${escapeHtml(post.body).replace(/\n/g, '<br>')}
                        </div>
                        ${mediaHtml}
                        ${lastCommentHtml}
                        <div style="margin-top: 10px;">
                            <a href="./post.php?id=${post.id}"><button>View Full Thread & Reply</button></a>
                        </div>
                    </div><hr style="opacity: 0.2;">`;
                });

                $('#postsList').append(content);
            });
        }

        // إعدادات الـ Scroll اللانهائي
        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
                if (!isLoading && !noMorePosts) {
                    currentPage++;
                    getPage(currentPage);
                }
            }
        });

        // تشغيل أول صفحة
        getPage(1);
    });
</script>

</body>

</html>