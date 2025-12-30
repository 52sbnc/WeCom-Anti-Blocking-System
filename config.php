<?php
// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', '');// 自行修改数据库名字
define('DB_USER', '');// 自行修改数据库用户名
define('DB_PWD', '');// 自行修改数据库密码

// 上传配置
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/uploads/');

// 邮箱配置（预留，可替换为GitHub下载的邮件包）
define('SMTP_HOST', 'smtp.qq.com'); // 自行修改
define('SMTP_PORT', 465);
define('SMTP_USER', 'your_email@qq.com'); // 自行修改
define('SMTP_PASS', 'your_smtp_pass'); // 自行修改
define('SMTP_SECURE', 'ssl');
define('SMTP_FROM', 'your_email@qq.com'); // 自行修改
define('SMTP_FROM_NAME', '投诉系统');

// 初始化PDO
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('数据库连接失败：' . $e->getMessage());
}
?>