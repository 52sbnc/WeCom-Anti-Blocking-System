<?php
// 检测是否已安装
if (file_exists('config.php') && filesize('config.php') > 0) {
    die('<h1>系统已安装！</h1><a href="admin/login.php">点击进入后台</a>');
}

$install_success = false;
$error_msg = '';

// 处理安装提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = trim($_POST['db_host']);
    $db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pwd = trim($_POST['db_pwd']);
    $admin_user = trim($_POST['admin_user']) ?: 'admin';
    $admin_pwd = trim($_POST['admin_pwd']) ?: '123456';
    
    // 1. 检测数据库连接
    try {
        $pdo = new PDO("mysql:host={$db_host};charset=utf8mb4", $db_user, $db_pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $error_msg = '数据库连接失败：' . $e->getMessage();
    }
    
    if (empty($error_msg)) {
        // 2. 创建数据库（如果不存在）
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pwd);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 3. 创建数据表（彻底修复所有TEXT字段默认值问题）
            $sqls = [
                // 管理员表
                "CREATE TABLE IF NOT EXISTS `admin_users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `username` varchar(50) NOT NULL COMMENT '管理员账号',
                    `password` varchar(100) NOT NULL COMMENT '管理员密码（明文）',
                    `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `username` (`username`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';",
                
                // 投诉链接表
                "CREATE TABLE IF NOT EXISTS `complaint_links` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `unique_code` varchar(32) NOT NULL COMMENT '投诉链接唯一码',
                    `wechat_key` varchar(100) DEFAULT '' COMMENT '企业微信机器人Key',
                    `email` varchar(100) DEFAULT '' COMMENT '收件邮箱',
                    `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1启用 0停用',
                    `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_code` (`unique_code`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投诉链接表';",
                
                // 投诉记录表（修复images、user_agent字段默认值）
                "CREATE TABLE IF NOT EXISTS `complaint_records` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `link_id` int(11) NOT NULL COMMENT '关联投诉链接ID',
                    `type` varchar(50) NOT NULL COMMENT '投诉类型',
                    `content` text NOT NULL COMMENT '投诉内容',
                    `contact` varchar(100) DEFAULT '' COMMENT '联系方式',
                    `images` text COMMENT '图片地址，逗号分隔',
                    `ip` varchar(50) DEFAULT '' COMMENT '提交IP',
                    `user_agent` text COMMENT '用户代理',
                    `submit_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `link_id` (`link_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投诉记录表';"
            ];
            
            foreach ($sqls as $sql) {
                $pdo->exec($sql);
            }
            
            // 4. 初始化管理员账号
            $stmt = $pdo->prepare("INSERT INTO `admin_users` (`username`, `password`) VALUES (?, ?)");
            $stmt->execute([$admin_user, $admin_pwd]);
            
            // 5. 生成配置文件
            $config_content = "<?php
// 数据库配置
define('DB_HOST', '{$db_host}');
define('DB_NAME', '{$db_name}');
define('DB_USER', '{$db_user}');
define('DB_PWD', '{$db_pwd}');

// 上传配置
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'http://' . \$_SERVER['HTTP_HOST'] . dirname(\$_SERVER['SCRIPT_NAME']) . '/uploads/');

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
    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PWD);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException \$e) {
    die('数据库连接失败：' . \$e->getMessage());
}
?>";
            
            file_put_contents('config.php', $config_content);
            
            // 6. 检测uploads目录权限
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }
            if (!is_writable('uploads')) {
                $error_msg = 'uploads目录无写入权限，请手动设置755权限！';
            } else {
                $install_success = true;
            }
            
        } catch (PDOException $e) {
            $error_msg = '数据表创建失败：' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>诺辞企业微信防投诉系统V6.0安装</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;}
        body {font-family:Arial, sans-serif;background:#f5f5f5;padding:20px;}
        .install-box {max-width:600px;margin:0 auto;background:#fff;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        h1 {text-align:center;margin-bottom:30px;color:#333;}
        .form-group {margin-bottom:20px;}
        label {display:block;margin-bottom:8px;color:#666;font-weight:bold;}
        input {width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;font-size:14px;}
        .btn {width:100%;padding:12px;background:#007bff;color:#fff;border:none;border-radius:4px;font-size:16px;cursor:pointer;}
        .btn:hover {background:#0056b3;}
        .error {color:red;margin-bottom:20px;padding:10px;background:#f8d7da;border-radius:4px;}
        .success {color:green;margin-bottom:20px;padding:10px;background:#d4edda;border-radius:4px;}
        .tips {color:#999;font-size:12px;margin-top:5px;}
    </style>
</head>
<body>
    <div class="install-box">
        <h1>诺辞企业微信防投诉系统V6.0安装</h1>
        
        <?php if ($error_msg): ?>
            <div class="error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <?php if ($install_success): ?>
            <div class="success">
                安装成功！<br>
                管理员账号：<?php echo $_POST['admin_user'] ?: 'admin'; ?><br>
                管理员密码：<?php echo $_POST['admin_pwd'] ?: '123456'; ?><br>
                <a href="admin/login.php" style="color:green;">点击进入后台</a>
            </div>
        <?php else: ?>
            <form method="post">
                <div class="form-group">
                    <label>数据库主机</label>
                    <input type="text" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label>数据库名称</label>
                    <input type="text" name="db_name" value="complaint_system" required>
                </div>
                <div class="form-group">
                    <label>数据库账号</label>
                    <input type="text" name="db_user" required>
                </div>
                <div class="form-group">
                    <label>数据库密码</label>
                    <input type="password" name="db_pwd">
                    <div class="tips">无密码请留空</div>
                </div>
                <div class="form-group">
                    <label>管理员账号（默认admin）</label>
                    <input type="text" name="admin_user" value="admin">
                </div>
                <div class="form-group">
                    <label>管理员密码（默认123456）</label>
                    <input type="password" name="admin_pwd" value="123456">
                </div>
                <button type="submit" class="btn">开始安装</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>