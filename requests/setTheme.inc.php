<?php
/**
 * صفحة معالجة تغيير الثيم وحفظ الكوكيز
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme_name'])) {
    $theme = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['theme_name']); // تنظيف المدخلات أمنياً

    // حفظ في الكوكيز لمدة سنة
    setcookie('user_theme', $theme, [
        'expires' => time() + (60 * 60 * 24 * 365),
        'path' => '/',
        'httponly' => false, // نتركه false إذا أردنا الوصول له عبر JS أيضاً
        'samesite' => 'Lax'
    ]);
}

// العودة للصفحة التي جاء منها المستخدم أو للرئيسية
$referer = $_SERVER['HTTP_REFERER'] ?? '/index.php';
header("Location: $referer");
exit();