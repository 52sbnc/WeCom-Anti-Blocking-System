<?php
// 引入配置
require_once 'config.php';

// 获取客户端真实IP
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    return trim($ip);
}

// 安全上传验证
function secure_upload($file) {
    // 允许的后缀
    $allow_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    // 最大5MB
    $max_size = 5 * 1024 * 1024;
    
    // 检测文件大小
    if ($file['size'] > $max_size || $file['size'] <= 0) {
        return false;
    }
    
    // 检测后缀
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allow_ext)) {
        return false;
    }
    
    // 检测是否为真实图片
    if (!getimagesize($file['tmp_name'])) {
        return false;
    }
    
    return true;
}

// 发送企业微信通知
function send_wechat_notify($wechat_key, $complaint_id, $type, $content, $contact, $ip, $image_count) {
    if (empty($wechat_key)) return false;
    
    $url = "https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key={$wechat_key}";
    $msg = [
        "msgtype" => "markdown",
        "markdown" => [
            "content" => "## 🚨 新投诉通知\n\n" .
                         "**投诉ID**：`{$complaint_id}`\n" .
                         "**投诉类型**：{$type}\n" .
                         "**投诉内容**：{$content}\n" .
                         ($contact ? "**联系方式**：{$contact}\n" : "") .
                         "**投诉人IP**：{$ip}\n" .
                         "**图片证据**：" . ($image_count ? $image_count . '张' : '无') . "\n" .
                         "**投诉时间**：" . date('Y-m-d H:i:s')
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("微信通知失败：" . $error);
        return false;
    }
    return true;
}

// 发送邮箱通知（预留替换位置，修复use关键字错误）
function send_email_notify($to_email, $complaint_id, $type, $content, $contact, $ip, $image_count) {
    if (empty($to_email)) return false;
    
    // 此处为预留位置，可替换为GitHub下载的邮件包
    // 示例使用原生mail函数（建议替换为PHPMailer）
    $subject = '新投诉通知 - ' . date('Y-m-d H:i:s');
    $headers = [
        'From' => SMTP_FROM,
        'Content-Type' => 'text/html; charset=utf-8'
    ];
    
    $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center; color: white;'>
                <h1 style='margin: 0; font-size: 24px;'>🚨 新投诉通知</h1>
            </div>
            <div style='background: #f9fafb; padding: 20px;'>
                <p><strong>投诉ID</strong>：{$complaint_id}</p>
                <p><strong>投诉类型</strong>：{$type}</p>
                <p><strong>投诉内容</strong>：{$content}</p>
                " . ($contact ? "<p><strong>联系方式</strong>：{$contact}</p>" : "") . "
                <p><strong>投诉人IP</strong>：{$ip}</p>
                <p><strong>图片证据</strong>：" . ($image_count ? $image_count . '张' : '无') . "</p>
                <p><strong>投诉时间</strong>：" . date('Y-m-d H:i:s') . "</p>
            </div>
            <div style='background: #e5e7eb; padding: 15px; text-align: center; font-size: 12px; color: #6b7280;'>
                此邮件由系统自动发送，请勿回复。
            </div>
        </div>
    ";
    
    // 原生mail函数（建议替换为PHPMailer，替换方法见mail/README.md）
    $result = mail($to_email, $subject, $body, implode("\r\n", $headers));
    if (!$result) {
        error_log("邮箱通知失败：{$to_email}");
        return false;
    }
    return true;
}

// 生成唯一随机码
function generate_unique_code() {
    $code = md5(uniqid(mt_rand(), true));
    // 检测是否重复
    $stmt = $GLOBALS['pdo']->prepare("SELECT id FROM complaint_links WHERE unique_code = ?");
    $stmt->execute([$code]);
    if ($stmt->fetch()) {
        return generate_unique_code();
    }
    return $code;
}
?>