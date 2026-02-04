<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/session.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/db.inc.php');

// جلب الكاتيجوري ومعها البوردات التابعة لها
try {
    // نجلب الكاتيجوري أولاً
    $catStmt = $pdo->query("SELECT * FROM category ORDER BY id ASC");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

    // نجلب كل البوردات لنقوم بتوزيعها برمجياً أو عبر استعلام داخل اللوب
    // لسهولة الكود سنقوم بجلب البوردات لكل كاتيجوري داخل اللوب
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>サンタロソ | Santaroso Image Board</title>
    <meta name="description"
        content="Santaroso is a safe, Arab-friendly image board made with love in Egypt. No NSFW, no bots, just chill, honest, retro-style community fun." />
    <meta name="keywords"
        content="Santaroso, imageboard, Arab community, memes, anime, technology, Egypt forum, retro board, safe space, no NSFW" />
    <meta name="author" content="MinecraftPlayer44" />
    <link rel="icon" type="image/png" href="small-Logo.webp" />
    <style>
        :root {
            --primary-color: #a03c3c;
            --secondary-color: #ffe9c9;
            --bg-color: #fff4e0;
            --text-color: #333;
            --error-bg: #ffe2e2;
            --link-color: #a03c3c;
            --footer-color: #777;
        }

        body {
            background-color: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: auto;
            color: var(--text-color);
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
        }

        header,
        footer {
            text-align: center;
            padding: 30px 10px;
            padding: 0;
            margin: 0;
        }

        header * {
            margin: 0;
            padding: 0;
        }

        header img {
            width: 250px;
            max-width: 100%;
        }

        h1,
        h2,
        h3 {
            color: #222;
        }

        a {
            color: var(--link-color);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .button-link button {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            padding: 12px 20px;
            margin: 10px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-link button:hover {
            background-color: #c04c4c;
        }

        table,
        th {
            background-color: #ffffff;
            border: black solid 2px;
            border-collapse: collapse;
            padding: 5px;
            text-align: left;
        }

        table {
            width: 90%;
        }

        ul {
            padding-left: 20px;
        }

        section {
            margin-bottom: 40px;
        }

        footer {
            font-size: 14px;
            color: var(--footer-color);
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 0 50px;
            background-color: var(--bg-color);
        }

        .boards {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .boards * {
            margin: 5px;
            /* padding: 0; */
        }

        .board-th {
            width: 250px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }

        .annoucments * {
            margin: 3px;
        }
        .board-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .board-th { width: 200px; text-align: left; padding: 5px; }
        .boards hr { border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 0; }
        .board-icon { vertical-align: middle; margin-right: 5px; border-radius: 3px; }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <img src="./assets/Santaroso-Logo.webp" alt="Santaroso Logo" />
            <h1>サンタロソ </h1>
            <h2 data-i18n="welcome">Welcome to Santaroso Image Board</h2>
            <p data-i18n="subtitle">Created with LOVE in Egypt</p>
        </header>

        <main>
            <section>
                <h2 data-i18n="boards">Boards</h2>

                <?php foreach ($categories as $cat): ?>
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    <div class="boards">
                        <table class="board-table">
                            <?php
                            // جلب البوردات التابعة لهذه الكاتيجوري
                            $boardStmt = $pdo->prepare("SELECT * FROM boards WHERE c_id = :cid ORDER BY name ASC");
                            $boardStmt->execute(['cid' => $cat['id']]);
                            $boards = $boardStmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($boards as $board):
                                ?>
                                <tr>
                                    <th class="board-th">
                                        <?php if($board['icon']): ?>
                                            <img src="./uploads/<?= $board['icon'] ?>" class="board-icon" width="16" height="16">
                                        <?php endif; ?>
                                        <a href="board.php?b=<?= $board['id'] ?>">/<?= htmlspecialchars($board['name']) ?></a>
                                    </th>
                                    <th><?= htmlspecialchars($board['description']) ?></th>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <hr>
                <?php endforeach; ?>

            </section>

            <section>
                <h2 data-i18n="news_title">Community Annoucments</h2>
                <div class="annoucments">
                    <h3>Feb 4, 2026</h3>
                    <p data-i18n="news_desc">
                        now we are live again
                    </p>
                    <ul>
                        <li>new engine</li>
                        <li>better UI</li>
                        <li>with Humans</li>
                    </ul>
                </div>
            </section>
            <section>
                <div class="annoucments">
                    <h2 data-i18n="changelog">Changelog</h2>
                    <h3>Feb 4, 2026</h3>
                    <ul>
                        <li>Remade it!</li>
                    </ul>
                </div>
            </section>
        </main>

        <footer>
            <p>© 2025 Santaroso by someone | <span data-i18n="footer">Made with LOVE and memes</span></p>
        </footer>
    </div>
</body>

</html>