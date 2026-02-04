<?php
//const MESSAGES = [
//    UPLOAD_ERR_OK => 'File uploaded successfully',
//    UPLOAD_ERR_INI_SIZE => 'File is too big to upload',
//    UPLOAD_ERR_FORM_SIZE => 'File is too big to upload',
//    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
//    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
//    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server',
//    UPLOAD_ERR_CANT_WRITE => 'File is failed to save to disk.',
//    UPLOAD_ERR_EXTENSION => 'File is not allowed to upload to this server',
//];
//
//const MAX_SIZE  = 10 * 1024 * 1024; //  10MB
//
//const ALLOWED_FILES = [
//    'image/png' => 'png',
//    'image/jpeg' => 'jpg',
//    'image/gif' => 'gif',
//    'image/webp' => 'webp',
//    'video/mp4' => 'mp4',
//    'video/webm' => 'webm'
//];
//
//const UPLOAD_DIR = __DIR__ . '/../uploads';
//
//function get_mime_type(string $filename)
//{
//    $info = finfo_open(FILEINFO_MIME_TYPE);
//    if (!$info) {
//        return false;
//    }
//
//    $mime_type = finfo_file($info, $filename);
//    finfo_close($info);
//
//    return $mime_type;
//}
//
//
//function upload()
//{
//    $file = $_FILES['media']['tmp_name'];
//    if (filesize($file) > MAX_SIZE) {
//        die('File is too big');
//    }
//
//    // validate the file type
//    $mime_type = get_mime_type($file);
//    if (!in_array($mime_type, array_keys(ALLOWED_FILES))) {
//        die('The file type is not allowed to upload');
//    }
//
//    $extension = ALLOWED_FILES[$mime_type];
//
//    if ($mime_type === 'video/mp4' || $mime_type === 'video/webm') {
//        $prefix = 'VID_';
//    } else {
//        $prefix = 'IMG_';
//    }
//
//    $uploaded_file = $prefix . bin2hex(random_bytes(10)) . '.' . $extension;
////    $uploaded_file = pathinfo($file, PATHINFO_FILENAME) . '.' . ALLOWED_FILES[$mime_type];
//
//    $filepath = UPLOAD_DIR . '/' . $uploaded_file;
//
//    $success = move_uploaded_file($file, $filepath);
//
//    if (!$success) {
//        die('Error moving the file to the upload folder.');
//    }
//
//    generateThumbnail($filepath, $uploaded_file,400, 400);
//    return $uploaded_file;
//}
//
//function generateThumbnail($img, $img_name , $width, $height, $quality = 90)
//{
//
//    $thumb_path = UPLOAD_DIR . '/' . 'THUMB_' . $img_name . '.jpg';
//    $mime_type = get_mime_type($img);
//    if ($mime_type === 'video/mp4' || $mime_type === 'video/webm') {
//        $command = "/usr/local/bin/ffmpeg -i " . escapeshellarg($img) . " -ss 00:00:01 -vframes 1 -vf scale=400:-1 " . escapeshellarg($thumb_path) . " 2>&1";
//        system($command);
//
//        if (file_exists($thumb_path)) {
//            $imagick = new Imagick($thumb_path);
//
//            // 2. الحصول على أبعاد التمبينيل الفعلية (عشان لو مش مربع)
//            $w = $imagick->getImageWidth();
//            $h = $imagick->getImageHeight();
//
//            // 3. حساب نقطة المركز
//            $centerX = $w / 2;
//            $centerY = $h / 2;
//            $size = 30; // حجم المثلث (نصف القاعدة والارتفاع)
//
//            $draw = new ImagickDraw();
//            $draw->setFillColor('white');
//            $draw->setFillOpacity(0.7);
//
//            // 4. رسم المثلث بناءً على السنتر
//            $points = [
//                ['x' => $centerX - ($size * 0.8), 'y' => $centerY - $size], // فوق شمال
//                ['x' => $centerX - ($size * 0.8), 'y' => $centerY + $size], // تحت شمال
//                ['x' => $centerX + $size,         'y' => $centerY]          // بوز المثلث يمين
//            ];
//
//            $draw->polygon($points);
//            $imagick->drawImage($draw);
//            $imagick->writeImage($thumb_path);
//
//            $imagick->clear();
//            $imagick->destroy();
//        }
//
//        return true;
//    }
//
//    if (is_file($img)) {
//        $imagick = new Imagick(realpath($img));
//        $imagick->setImageFormat('jpeg');
//        $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
//        $imagick->setImageCompressionQuality($quality);
//        $imagick->thumbnailImage($width, $height, true, false);
//        if (!$imagick->writeImage( UPLOAD_DIR . '/' . 'THUMB_' . $img_name . '.jpg')) {
//            die("Could not put contents.");
//        }
//        $imagick->clear();
//        $imagick->destroy();
//        return true;
//    }
//    else {
//        die("No valid image provided with {$img}.");
//    }
//}

// I love shit


const MESSAGES = [
    UPLOAD_ERR_OK => 'File uploaded successfully',
    UPLOAD_ERR_INI_SIZE => 'File is too big to upload',
    UPLOAD_ERR_FORM_SIZE => 'File is too big to upload',
    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server',
    UPLOAD_ERR_CANT_WRITE => 'File is failed to save to disk.',
    UPLOAD_ERR_EXTENSION => 'File is not allowed to upload to this server',
];

const MAX_SIZE = 10 * 1024 * 1024; // 10MB

const ALLOWED_FILES = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    'video/mp4' => 'mp4',
    'video/webm' => 'webm'
];

const UPLOAD_DIR = __DIR__ . '/../uploads';

function get_mime_type(string $filename)
{
    $info = finfo_open(FILEINFO_MIME_TYPE);
    if (!$info) {
        return false;
    }

    $mime_type = finfo_file($info, $filename);
    finfo_close($info);

    return $mime_type;
}

function upload()
{
    // التأكد من وجود المجلد
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $file = $_FILES['media']['tmp_name'];
    if (filesize($file) > MAX_SIZE) {
        die('File is too big');
    }

    // validate the file type
    $mime_type = get_mime_type($file);
    if (!in_array($mime_type, array_keys(ALLOWED_FILES))) {
        die('The file type is not allowed to upload');
    }

    $extension = ALLOWED_FILES[$mime_type];

    if (str_starts_with($mime_type, 'video/')) {
        $prefix = 'VID_';
    } else {
        $prefix = 'IMG_';
    }

    $uploaded_file = $prefix . bin2hex(random_bytes(10)) . '.' . $extension;
    $filepath = UPLOAD_DIR . '/' . $uploaded_file;

    $success = move_uploaded_file($file, $filepath);

    if (!$success) {
        die('Error moving the file to the upload folder.');
    }

    // توليد التمبينيل باستخدام GD
    generateThumbnail($filepath, $uploaded_file, 400, 400);
    return $uploaded_file;
}
function generateThumbnail($img, $img_name, $width, $height, $quality = 90)
{
    $thumb_path = UPLOAD_DIR . '/' . 'THUMB_' . $img_name . '.jpg';
    $mime_type = get_mime_type($img);

    // ---------------------------------------------------------
    // 1. معالجة الفيديو (نظام الألوان الذكي)
    // ---------------------------------------------------------
    if (str_starts_with($mime_type, 'video/')) {
        $canvas = imagecreatetruecolor($width, $height);

        // توليد لون عشوائي بس ثابت لنفس الملف (بناءً على هاش الاسم)
        // بناخد أول 6 حروف من الـ MD5 ونحولهم لأرقام RGB
        $hash = md5($img_name);
        $r = hexdec(substr($hash, 0, 2));
        $g = hexdec(substr($hash, 2, 2));
        $b = hexdec(substr($hash, 4, 2));

        // جعل الألوان هادية شوية (Pastel) عشان متبقاش فاقعة
        // بنعمل Mix مع اللون الأبيض
        $r = (int)(($r + 255) / 2);
        $g = (int)(($g + 255) / 2);
        $b = (int)(($b + 255) / 2);

        $bgColor = imagecolorallocate($canvas, $r, $g, $b);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $darkOverlay = imagecolorallocatealpha($canvas, 0, 0, 0, 40); // ضل خفيف

        // ملء الخلفية باللون المولد
        imagefill($canvas, 0, 0, $bgColor);

        // رسم دائرة شفافة خلف المثلث (زرار التشغيل)
        $centerX = $width / 2;
        $centerY = $height / 2;
        imagefilledellipse($canvas, $centerX, $centerY, 140, 140, $darkOverlay);

        // رسم دائرة حدود بيضاء
        imageellipse($canvas, $centerX, $centerY, 140, 140, $white);

        // رسم المثلث
        $size = 35;
        $points = [
            $centerX - ($size * 0.5), $centerY - $size,     // Top Left
            $centerX - ($size * 0.5), $centerY + $size,     // Bottom Left
            $centerX + ($size * 0.8), $centerY              // Right Tip
        ];
        imagefilledpolygon($canvas, $points, 3, $white);

        // كتابة نوع الملف تحت (MP4 / WEBM)
        // 5 هو حجم الفونت (أكبر حاجة في الـ Built-in)
        $ext = strtoupper(pathinfo($img_name, PATHINFO_EXTENSION));
        $fontX = $centerX - (imagefontwidth(5) * strlen($ext) / 2);
        imagestring($canvas, 5, $fontX, $centerY + 80, $ext, $white);

        imagejpeg($canvas, $thumb_path, $quality);
        imagedestroy($canvas);
        return true;
    }

    // ---------------------------------------------------------
    // 2. معالجة الصور (زي ما هي)
    // ---------------------------------------------------------
    if (is_file($img)) {
        switch ($mime_type) {
            case 'image/jpeg': $source = imagecreatefromjpeg($img); break;
            case 'image/png':  $source = imagecreatefrompng($img); break;
            case 'image/gif':  $source = imagecreatefromgif($img); break;
            case 'image/webp': $source = imagecreatefromwebp($img); break;
            default: return false;
        }

        if (!$source) return false;

        list($orig_w, $orig_h) = getimagesize($img);
        $ratio = $orig_w / $orig_h;

        if ($width / $height > $ratio) {
            $new_w = $height * $ratio;
            $new_h = $height;
        } else {
            $new_h = $width / $ratio;
            $new_w = $width;
        }

        $thumb = imagecreatetruecolor((int)$new_w, (int)$new_h);
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb, 0, 0, (int)$new_w, (int)$new_h, $white);

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, (int)$new_w, (int)$new_h, $orig_w, $orig_h);

        if (!imagejpeg($thumb, $thumb_path, $quality)) {
            die("Could not save thumbnail.");
        }

        imagedestroy($source);
        imagedestroy($thumb);
        return true;
    }

    return false;
}

