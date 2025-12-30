<?php
// 引入配置和函数（对接原有后端逻辑）
require_once 'config.php';
require_once 'functions.php';

// 初始化变量
$step = isset($_GET['step']) ? intval($_GET['step']) : 1; // 步骤：1=选择主类型 2=选择子类型 3=提交表单
$main_type = isset($_GET['type']) ? $_GET['type'] : '';
$sub_type = isset($_GET['subtype']) ? $_GET['subtype'] : '';
$submit_success = false;

// 处理表单提交（步骤3）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 3) {
    // 验证唯一码
    $code = isset($_GET['code']) ? trim($_GET['code']) : '';
    if (empty($code)) {
        $error_msg = '无效的投诉链接';
    } else {
        // 查询链接信息
        $stmt = $pdo->prepare("SELECT * FROM complaint_links WHERE unique_code = ? AND status = 1");
        $stmt->execute([$code]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$link) {
            $error_msg = '投诉链接不存在或已停用';
        } else {
            // 获取表单数据
            $content = trim($_POST['problem']);
            $contact = trim($_POST['contact'] ?? '');
            $ip = get_client_ip();
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $images = [];
            
            // 验证必填项 - 改为非必填（根据需求调整）
            if (false) {
                $error_msg = '请填写问题描述';
            } else {
                // 处理图片上传
                if (!empty($_FILES['images']['name'][0])) {
                    $uploaded_files = $_FILES['images'];
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                    
                    foreach ($uploaded_files['name'] as $key => $name) {
                        // 跳过空文件
                        if (empty($name)) continue;
                        
                        $file_tmp = $uploaded_files['tmp_name'][$key];
                        $file_size = $uploaded_files['size'][$key];
                        $file_error = $uploaded_files['error'][$key];
                        
                        // 验证文件
                        if ($file_error !== UPLOAD_ERR_OK || $file_size > $max_size || $file_size <= 0) {
                            $error_msg = '图片上传失败：文件大小超出限制';
                            break;
                        }
                        
                        // 验证后缀
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        if (!in_array($ext, $allowed_ext)) {
                            $error_msg = '图片格式错误，仅支持JPG/PNG/GIF/WEBP';
                            break;
                        }
                        
                        // 验证是否为图片
                        if (!getimagesize($file_tmp)) {
                            $error_msg = '请上传真实的图片文件';
                            break;
                        }
                        
                        // 生成唯一文件名
                        $new_name = uniqid('complaint_') . '.' . $ext;
                        $save_path = UPLOAD_PATH . $new_name;
                        
                        // 保存文件
                        if (move_uploaded_file($file_tmp, $save_path)) {
                            $images[] = UPLOAD_URL . $new_name;
                        } else {
                            $error_msg = '图片保存失败，请检查目录权限';
                            break;
                        }
                    }
                }
                
                // 无错误则保存投诉记录
                if (empty($error_msg)) {
                    try {
                        // 插入投诉记录
                        $stmt = $pdo->prepare("
                            INSERT INTO complaint_records 
                            (link_id, type, content, contact, images, ip, user_agent)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ");
                        $images_str = !empty($images) ? implode(',', $images) : '';
                        $full_type = $main_type . (empty($sub_type) ? '' : '-' . $sub_type);
                        $stmt->execute([
                            $link['id'], $full_type, $content, $contact,
                            $images_str, $ip, $user_agent
                        ]);
                        $complaint_id = $pdo->lastInsertId();
                        
                        // 发送通知
                        $image_count = count($images);
                        if (!empty($link['wechat_key'])) {
                            send_wechat_notify($link['wechat_key'], $complaint_id, $full_type, $content, $contact, $ip, $image_count);
                        }
                        if (!empty($link['email'])) {
                            send_email_notify($link['email'], $complaint_id, $full_type, $content, $contact, $ip, $image_count);
                        }
                        
                        $submit_success = true;
                    } catch (PDOException $e) {
                        $error_msg = '提交失败：' . $e->getMessage();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>企业微信投诉</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            background-color: #fff;
            height: 100vh;
            color: #333;
        }
        .container {
            max-width: 750px;
            margin: 0 auto;
            overflow: hidden;
            padding-bottom: 60px;
        }
        .header {
            padding: 16px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: center;
            position: relative;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 600;
            margin: 0 auto;
        }
        .back-btn {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .back-btn::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            border-left: 1px solid #333;
            border-bottom: 1px solid #333;
            transform: rotate(45deg);
            margin-right: 4px;
        }
        h2 {
            font-size: 14px;
            margin-bottom: 5px;
            margin-top: 20px;
            color: #999;
            padding-inline: 16px;
            font-weight: 500;
            opacity: 0.9;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            font-size: 16px;
            padding: 14px 16px;
            background-color: #fff;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        li>a {
            color: #333;
            text-decoration: none;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        li::before {
            content: '';
            width: calc(100% - 16px);
            height: 1px;
            background-color: #f5f5f5;
            position: absolute;
            bottom: 0;
            left: 16px;
        }
        li::after {
            content: '';
            display: inline-block;
            width: 10px;
            height: 10px;
            border-top: 1px solid #ccc;
            border-right: 1px solid #ccc;
            box-sizing: border-box;
            transform: rotate(45deg);
        }
        li:last-child::before {
            display: none;
        }
        p {
            font-size: 14px;
            color: #007bff;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
        }
        p>a {
            text-decoration: none;
            color: #007bff;
        }
        /* 表单样式 */
        .required-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .star {
            display: inline-block;
            vertical-align: middle;
            font-size: 16px;
            color: red;
            margin-right: 4px;
            display: none; /* 隐藏必填星号，因为所有字段改为选填 */
        }
        textarea {
            font-size: 16px;
            width: 100%;
            height: 120px;
            border: 1px solid #eee;
            border-radius: 4px;
            resize: none;
            padding: 12px;
            transition: border-color 0.3s;
        }
        textarea:focus {
            outline: none;
            border-color: #427ce8;
        }
        textarea::placeholder {
            font-size: 16px;
            color: #ccc;
        }
        .word-count {
            color: #ccc;
            font-size: 12px;
            position: absolute;
            right: 16px;
            bottom: 16px;
        }
        .imgbox {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .imgbox .img-item {
            position: relative;
            width: 80px;
            height: 80px;
        }
        .imgbox img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }
        .imgbox .delete-img {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            color: white;
            font-size: 14px;
            text-align: center;
            line-height: 20px;
            cursor: pointer;
            display: none;
        }
        .imgbox .img-item:hover .delete-img {
            display: block;
        }
        .update_btn {
            width: 80px;
            height: 80px;
            background-color: #f5f5f5;
            border: 1px dashed #ddd;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .update_btn:hover {
            background-color: #eee;
        }
        .update_btn::after {
            content: '';
            width: 30px;
            height: 2px;
            background-color: #ccc;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .update_btn::before {
            content: '';
            height: 30px;
            width: 2px;
            background-color: #ccc;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .update_btn input {
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        /* 图片上传区域文字样式修改 */
        .imgnumber {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .imgnumber-left {
            font-size: 13px; /* 缩小字号 */
            color: #000; /* 黑色 */
        }
        .img-count {
            font-size: 10px; /* 超小字号 */
            color: #999; /* 灰色 */
        }
        .img-tips {
            font-size: 10px; /* 超小字号 */
            color: #999; /* 灰色 */
            margin-top: 8px; /* 另起一行，增加间距 */
            line-height: 1.2; /* 行高，避免拥挤 */
            display: block; /* 确保独占一行 */
        }
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 16px;
            padding: 20px 16px;
            background-color: #fff;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 750px;
            margin: 0 auto;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }
        button {
            flex: 1;
            height: 44px;
            line-height: 44px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        button:hover {
            opacity: 0.9;
        }
        .cancel-btn {
            background-color: #f5f5f5;
            color: #333;
        }
        .submit-btn {
            background-color: #427ce8;
            color: #fff;
        }
        .submit-btn:disabled {
            background-color: #c9d5f0;
            cursor: not-allowed;
            opacity: 1;
        }
        .toast {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 14px;
            display: none;
            z-index: 9999;
        }
        /* 加载动画 */
        .submit-loading {
            position: relative;
            pointer-events: none;
        }
        .submit-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.5);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .submit-loading span {
            visibility: hidden;
        }
        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        /* 联系方式输入框 */
        .contact-input {
            font-size: 16px;
            width: 100%;
            padding: 12px;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-top: 8px;
        }
        .contact-input:focus {
            outline: none;
            border-color: #427ce8;
        }
        /* 成功页面 */
        .success-page {
            text-align: center;
            padding: 40px 20px;
        }
        .success-icon {
            font-size: 60px;
            color: #427ce8;
            margin-bottom: 20px;
        }
        .success-text {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .success-desc {
            font-size: 14px;
            color: #999;
            margin-bottom: 30px;
        }
        .error-msg {
            color: red;
            font-size: 14px;
            text-align: center;
            padding: 10px;
            margin: 10px 0;
        }
        /* 响应式调整 */
        @media (max-width: 480px) {
            .btn-group {
                padding: 16px;
            }
            button {
                height: 40px;
                line-height: 40px;
                font-size: 15px;
            }
            .imgbox .img-item, .update_btn {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 错误提示 -->
        <?php if (isset($error_msg)): ?>
            <div class="error-msg"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <!-- 步骤1：选择主投诉类型 -->
        <?php if ($step == 1 && !$submit_success): ?>
            <h2>请选择投诉该账号的原因：</h2>
            <ul>
                <li><a href="?step=2&type=发布不适当内容对我造成骚扰&code=<?php echo $_GET['code'] ?? ''; ?>">发布不适当内容对我造成骚扰</a></li>
                <li><a href="?step=2&type=存在欺诈骗钱行为&code=<?php echo $_GET['code'] ?? ''; ?>">存在欺诈骗钱行为</a></li>
                <li><a href="?step=2&type=此账号可能被盗用了&code=<?php echo $_GET['code'] ?? ''; ?>">此账号可能被盗用了</a></li>
                <li><a href="?step=2&type=存在侵权行为(侵犯知识产权、人身权)&code=<?php echo $_GET['code'] ?? ''; ?>">存在侵权行为(侵犯知识产权、人身权)</a></li>
                <li><a href="?step=2&type=侵犯未成年人权益&code=<?php echo $_GET['code'] ?? ''; ?>">侵犯未成年人权益</a></li>
                <li><a href="?step=2&type=粉丝无底线追星行为&code=<?php echo $_GET['code'] ?? ''; ?>">粉丝无底线追星行为</a></li>
            </ul>
            <p><a href="./tip.html">投诉须知</a></p>

        <!-- 步骤2：选择子类型 -->
        <?php elseif ($step == 2 && !$submit_success): ?>
            <?php if ($main_type == '发布不适当内容对我造成骚扰'): ?>
                <h2>请选择哪一类违法内容：</h2>
                <ul>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=色情&code=<?php echo $_GET['code'] ?? ''; ?>">色情</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=违法犯罪及违禁品&code=<?php echo $_GET['code'] ?? ''; ?>">违法犯罪及违禁品</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=赌博&code=<?php echo $_GET['code'] ?? ''; ?>">赌博</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=政治谣言&code=<?php echo $_GET['code'] ?? ''; ?>">政治谣言</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=暴恐血腥&code=<?php echo $_GET['code'] ?? ''; ?>">暴恐血腥</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=其他违规内容&code=<?php echo $_GET['code'] ?? ''; ?>">其他违规内容</a></li>
                </ul>
            <?php elseif ($main_type == '存在欺诈骗钱行为'): ?>
                <h2>请选择哪一类诈骗内容：</h2>
                <ul>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=金融诈骗(贷款/提额/代开/套现等)&code=<?php echo $_GET['code'] ?? ''; ?>">金融诈骗(贷款/提额/代开/套现等)</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=网络兼职刷单诈骗&code=<?php echo $_GET['code'] ?? ''; ?>">网络兼职刷单诈骗</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=返利诈骗&code=<?php echo $_GET['code'] ?? ''; ?>">返利诈骗</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=网络交友诈骗&code=<?php echo $_GET['code'] ?? ''; ?>">网络交友诈骗</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=虚假投资理财诈骗&code=<?php echo $_GET['code'] ?? ''; ?>">虚假投资理财诈骗</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=赌博诈骗&code=<?php echo $_GET['code'] ?? ''; ?>">赌博诈骗</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=收款不发货&code=<?php echo $_GET['code'] ?? ''; ?>">收款不发货</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=仿冒他人诈骗&code=<?php echo $_GET['code'] ?? ''; ?>">仿冒他人诈骗</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=免费送诈骗&code=<?php echo $_GET['code'] ?? ''; ?>">免费送诈骗</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=游戏相关诈骗(代练/充值等)&code=<?php echo $_GET['code'] ?? ''; ?>">游戏相关诈骗(代练/充值等)</a></li>
                    <li><a href="?step=3&type=<?php echo $main_type; ?>&subtype=其他诈骗行为&code=<?php echo $_GET['code'] ?? ''; ?>">其他诈骗行为</a></li>
                </ul>
            <?php else: ?>
                <!-- 其他主类型的二级页面直接跳转到三级表单 -->
                <script>
                    window.location.href = '?step=3&type=<?php echo $main_type; ?>&code=<?php echo $_GET['code'] ?? ''; ?>';
                </script>
            <?php endif; ?>
            <p><a href="./tip.html">投诉须知</a></p>

        <!-- 步骤3：提交表单 -->
        <?php elseif ($step == 3 && !$submit_success): ?>
            <form method="post" enctype="multipart/form-data" id="complaint-form">
                <ul>
                    <!-- 联系方式（选填） -->
                    <li>
                       
                            <span>联系方式（选填）</span>
                       
                        <input type="text" class="contact-input" name="contact" placeholder="请填写手机号/微信号，方便后续沟通" value="<?php echo $_POST['contact'] ?? ''; ?>">
                    </li>

                    <!-- 图片证据（选填） -->
                    <li>
                      <span>图片证据（选填）</span>
                        <div class="imgbox" id="img-container">
                            <div class="update_btn">
                                <input type="file" id="image-upload" name="images[]" accept="image/*" multiple />
                            </div>
                        </div>
                        <br>
                        <span class="img-tips">支持JPG、PNG格式，最多上传6张，单张不超过5MB</span>
                    </li>

                    <!-- 投诉内容（选填） -->
                    <li style="padding-bottom: 40px; position: relative;">
                        <div class="required-label">
                            <span>投诉内容（选填）</span>
                        </div>
                        <textarea placeholder="请详细描述你遇到的问题，有助于我们快速处理" maxlength="200" class="textarea" id="problem-description" name="problem"><?php echo $_POST['problem'] ?? ''; ?></textarea>
                        <span class="word-count" id="textlength">0/200</span>
                    </li>
                </ul>

                <!-- 底部按钮组 -->
                <div class="btn-group">
                    <button type="button" class="cancel-btn" onclick="history.back()">取消</button>
                    <button type="submit" class="submit-btn" id="submit">
                        <span>提交投诉</span>
                    </button>
                </div>
            </form>

        <!-- 提交成功页面 -->
        <?php elseif ($submit_success): ?>
            <div class="success-page">
                <div class="success-icon">✓</div>
                <div class="success-text">投诉提交成功！</div>
                <div class="success-desc">我们已收到你的投诉，会尽快处理并反馈结果</div>
                <button class="submit-btn" onclick="window.location.reload()">返回首页</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- 提示框 -->
    <div class="toast" id="toast"></div>

    <script>
        // 仅在步骤3时初始化表单脚本
        <?php if ($step == 3 && !$submit_success): ?>
        const textarea = document.getElementById('problem-description');
        const textLength = document.getElementById('textlength');
        const submitBtn = document.getElementById('submit');
        const imgUpload = document.getElementById('image-upload');
        const imgContainer = document.getElementById('img-container');
        const toast = document.getElementById('toast');
        
        let uploadedImages = [];
        const MAX_IMAGES = 6;
        const MAX_FILE_SIZE = 5 * 1024 * 1024;

        // 初始化文本计数
        if (textarea.value) {
            textLength.textContent = `${textarea.value.trim().length}/200`;
        }

        // 文本框输入事件
        textarea.addEventListener('input', function(e) {
            const length = e.target.value.trim().length;
            textLength.textContent = `${length}/200`;
        });

        // 图片上传事件
        imgUpload.addEventListener('change', function(e) {
            const files = e.target.files;
            if (!files.length) return;

            if (uploadedImages.length + files.length > MAX_IMAGES) {
                showToast(`最多只能上传${MAX_IMAGES}张图片`);
                return;
            }

            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) {
                    showToast('请上传图片格式文件');
                    return;
                }

                if (file.size > MAX_FILE_SIZE) {
                    showToast('单张图片不能超过5MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgItem = document.createElement('div');
                    imgItem.className = 'img-item';
                    imgItem.dataset.index = uploadedImages.length;

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = '上传的图片';

                    const deleteBtn = document.createElement('div');
                    deleteBtn.className = 'delete-img';
                    deleteBtn.innerHTML = '×';
                    deleteBtn.onclick = function() {
                        imgItem.remove();
                        const index = parseInt(imgItem.dataset.index);
                        uploadedImages.splice(index, 1);
                        updateImageIndexes();
                    };

                    imgItem.appendChild(img);
                    imgItem.appendChild(deleteBtn);
                    imgContainer.insertBefore(imgItem, imgContainer.lastChild);

                    uploadedImages.push(file);
                    // 隐藏上传按钮（达到最大数量时）
                    if (uploadedImages.length >= MAX_IMAGES) {
                        imgContainer.lastChild.style.display = 'none';
                    } else {
                        imgContainer.lastChild.style.display = 'block';
                    }
                };

                reader.readAsDataURL(file);
            });

            imgUpload.value = '';
        });

        // 重新设置图片索引
        function updateImageIndexes() {
            const imgItems = document.querySelectorAll('.img-item');
            imgItems.forEach((item, index) => {
                item.dataset.index = index;
            });
            // 更新上传按钮显示状态
            if (uploadedImages.length >= MAX_IMAGES) {
                imgContainer.lastChild.style.display = 'none';
            } else {
                imgContainer.lastChild.style.display = 'block';
            }
        }

        // 显示提示框
        function showToast(message) {
            toast.textContent = message;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 2000);
        }

        // 提交按钮加载状态
        document.getElementById('complaint-form').addEventListener('submit', function() {
            submitBtn.classList.add('submit-loading');
            submitBtn.disabled = true;
            showToast('提交中...');
        });
        
        // 移除表单验证（所有字段改为选填）
        submitBtn.disabled = false;
        <?php endif; ?>
    </script>
</body>
</html>