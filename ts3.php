<?php
require_once 'config.php';

// 获取投诉链接代码
$code = isset($_GET['code']) ? trim($_GET['code']) : '';
if (empty($code)) {
    die('无效的投诉链接');
}

// 验证投诉链接是否有效
try {
    $stmt = $pdo->prepare("SELECT * FROM complaint_links WHERE unique_code = ? AND status = 1");
    $stmt->execute([$code]);
    $link = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$link) {
        die('投诉链接不存在或已失效');
    }
} catch (PDOException $e) {
    die('系统错误：' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title>投诉</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.1/style/weui.min.css">
    <link rel="stylesheet" href="http://cdn.bootcss.com/jquery-weui/1.0.1/css/jquery-weui.min.css">
    <script src="http://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/jquery-weui/1.0.1/js/jquery-weui.min.js"></script>
    <script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript"></script>
    <style>
        .loading{
            display:none;		
            position:fixed;
            width:680px;
            height:20px;
            left: 50%;
            top: 10%;
            transform: translate(-50%,-50%);
            text-align: center;
        }
        .loading li{
        	display: inline-block;
        	width: 12px;
        	height: 12px;
        	background: black;
        	border-radius:50%;
        }
        .loading-ani{
        	animation: loading-ani 1s infinite linear;
        }
        @keyframes loading-ani{
        	0%{transform: scale(0.5);}
        	100%{transform: scale(1);}
        }
        .loading-ani1{animation-delay: 0.5s;}
        .loading-ani2{animation-delay: 0.2s;}
        .loading-ani3{animation-delay: 0s;}
        .weui-uploader__file {
            position: relative;
            margin-right: 9px;
            margin-bottom: 9px;
            width: 79px;
            height: 79px;
            background: no-repeat center center;
            background-size: cover;
            border-radius: 4px;
        }
        .weui-uploader__file img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }
        .weui-uploader__file .delete-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: rgba(0,0,0,0.6);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
            cursor: pointer;
            display: none;
        }
        .weui-uploader__file:hover .delete-btn {
            display: block;
        }
    </style>
</head>
<body right-click>
    <div class="weui-cells__title" id="tstitle">请选择投诉该账号的原因:</div>
    <div class="weui-cells" id="tspage_1">
        <a class="weui-cell weui-cell_access" href="javascript: subTSinfo('inappropriate');">
            <div class="weui-cell__bd"><p>发布不适当内容对我造成骚扰</p></div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="javascript: subTSinfo('fraud');">
            <div class="weui-cell__bd"><p>存在欺诈骗钱行为</p></div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="javascript: subTSinfo('stolen');">
            <div class="weui-cell__bd"><p>此账号可能被盗用了</p></div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="javascript: subTSinfo('infringement');">
            <div class="weui-cell__bd"><p>存在侵权行为(侵犯知识产权、人身权)</p></div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="javascript: subTSinfo('minor');">
            <div class="weui-cell__bd"><p>侵犯未成年人权益</p></div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="javascript: subTSinfo('fans');">
            <div class="weui-cell__bd"><p>粉丝无底线追星行为</p></div>
            <div class="weui-cell__ft"></div>
        </a>
    </div>
    <div class="weui-cells" id="tspage_2">
        <div id="inappropriate_sub">
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('色情');">
                <div class="weui-cell__bd"><p>色情</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('违法犯罪及违禁品');">
                <div class="weui-cell__bd"><p>违法犯罪及违禁品</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('赌博');">
                <div class="weui-cell__bd"><p>赌博</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('政治谣言');">
                <div class="weui-cell__bd"><p>政治谣言</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('暴恐血腥');">
                <div class="weui-cell__bd"><p>暴恐血腥</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('其他违规内容');">
                <div class="weui-cell__bd"><p>其他违规内容</p></div>
                <div class="weui-cell__ft"></div>
            </a>
        </div>
        <div id="fraud_sub" style="display:none;">
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('金融诈骗(贷款/提额/代开/套现等)');">
                <div class="weui-cell__bd"><p>金融诈骗(贷款/提额/代开/套现等)</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('网络兼职刷单诈骗');">
                <div class="weui-cell__bd"><p>网络兼职刷单诈骗</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('返利诈骗');">
                <div class="weui-cell__bd"><p>返利诈骗</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('网络交友诈骗');">
                <div class="weui-cell__bd"><p>网络交友诈骗</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('虚假投资理财诈骗');">
                <div class="weui-cell__bd"><p>虚假投资理财诈骗</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('赌博诈骗');">
                <div class="weui-cell__bd"><p>赌博诈骗</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('收款不发货');">
                <div class="weui-cell__bd"><p>收款不发货</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('仿冒他人诈骗');">
                <div class="weui-cell__bd"><p>仿冒他人诈骗</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('免费送诈骗');">
                <div class="weui-cell__bd"><p>免费送诈骗</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('游戏相关诈骗(代练/充值等)');">
                <div class="weui-cell__bd"><p>游戏相关诈骗(代练/充值等)</p></div>
                <div class="weui-cell__ft"></div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript: subSHinfo('其他诈骗行为');">
                <div class="weui-cell__bd"><p>其他诈骗行为</p></div>
                <div class="weui-cell__ft"></div>
            </a>
        </div>
        <div id="other_sub" style="display:none;">
            <div class="weui-cells weui-cells_form">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" placeholder="联系方式（选填）" id="contact_info" name="contact">
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <div class="weui-uploader">
                            <div class="weui-uploader__hd">
                                <p class="weui-uploader__title">图片证据（选填）</p>
                                <div class="weui-uploader__info"><span id="imageCount">0</span>/9</div>
                            </div>
                            <div class="weui-uploader__bd">
                                <ul class="weui-uploader__files" id="uploaderFiles">
                                </ul>
                                <div class="weui-uploader__input-box">
                                    <input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" multiple onchange="handleImageSelect(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <textarea class="weui-textarea" placeholder="投诉内容（选填）" rows="3" id="complaint_detail" name="content"></textarea>
                    </div>
                </div>
            </div>
            <div class="weui-btn-area">
                <a class="weui-btn weui-btn_primary" href="javascript: submitOtherComplaint();">提交投诉</a>
            </div>
        </div>
    </div>
    <div class="loading">
        <ul>
            <li class="loading-ani loading-ani1"></li>
            <li class="loading-ani loading-ani2"></li>
            <li class="loading-ani loading-ani3"></li>
        </ul>
    </div> 
    <div class="weui-msg" id="shpage_3">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">投诉已提交</h2>
            <p class="weui-msg__desc">微信团队会尽快核实，并通过“微信团队”通知你的审核结果。感谢你的支持。</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                 <a href="javascript:wx.closeWindow();" class="weui-btn weui-btn_primary">关闭</a>
            </p>
        </div>
    </div>
    <!--<div class="complain-btn" style="position: fixed;right:10px;bottom:10%;width: 30px;height: 30px;font-size:12px;text-align: center;line-height: 30px;border-radius: 50%;background: rgba(0,0,0,.2);color: #fff;"><a href="http://baidu.com" style="color: #Fff;">投诉</a></div>-->

    <script type="text/javascript" defer="defer">
    var selectedType = '';
    var selectedSubtype = '';
    var selectedImages = [];
    
    $(document).ready(function(){
      $("#tspage_2").hide();
      $("#shpage_3").hide();
    });
    
    function subTSinfo(type) {
      selectedType = type;
      $("#tspage_1").hide();
      $("#tspage_2").show();
      $("#inappropriate_sub").hide();
      $("#fraud_sub").hide();
      $("#other_sub").hide();
      
      if (type === 'inappropriate') {
        $("#inappropriate_sub").show();
        $("#tstitle").html("请选择哪一类违法内容：");
      } else if (type === 'fraud') {
        $("#fraud_sub").show();
        $("#tstitle").html("请选择哪一类诈骗内容:");
      } else {
        $("#other_sub").show();
        $("#tstitle").html("请补充投诉信息：");
      }
    }
    
    function subSHinfo(subtype) {
      selectedSubtype = subtype;
      $("#tspage_1").hide();
      $("#tspage_2").hide();
      $("#tstitle").hide();
      $('.loading').show()
      submitComplaintData();
      setTimeout(showResult,800)
    }
    
    function handleImageSelect(input) {
      var files = input.files;
      var fileList = document.getElementById('uploaderFiles');
      var imageCount = document.getElementById('imageCount');
      
      for (var i = 0; i < files.length; i++) {
        if (selectedImages.length >= 9) {
          alert('最多只能上传9张图片');
          break;
        }
        
        var file = files[i];
        if (!file.type.match('image.*')) {
          continue;
        }
        
        selectedImages.push(file);
        var reader = new FileReader();
        reader.onload = function(e) {
          var li = document.createElement('li');
          li.className = 'weui-uploader__file';
          li.innerHTML = '<img src="' + e.target.result + '" alt="预览"><span class="delete-btn" onclick="removeImage(' + (selectedImages.length - 1) + ')">×</span>';
          fileList.appendChild(li);
          imageCount.textContent = selectedImages.length;
        };
        reader.readAsDataURL(file);
      }
      input.value = '';
    }
    
    function removeImage(index) {
      selectedImages.splice(index, 1);
      refreshImagePreview();
    }
    
    function refreshImagePreview() {
      var fileList = document.getElementById('uploaderFiles');
      var imageCount = document.getElementById('imageCount');
      fileList.innerHTML = '';
      
      selectedImages.forEach(function(file, index) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var li = document.createElement('li');
          li.className = 'weui-uploader__file';
          li.innerHTML = '<img src="' + e.target.result + '" alt="预览"><span class="delete-btn" onclick="removeImage(' + index + ')">×</span>';
          fileList.appendChild(li);
        };
        reader.readAsDataURL(file);
      });
      
      imageCount.textContent = selectedImages.length;
    }
    
    function submitOtherComplaint() {
      var contact = $('#contact_info').val();
      var content = $('#complaint_detail').val();
      
      $("#tspage_1").hide();
      $("#tspage_2").hide();
      $("#tstitle").hide();
      $('.loading').show();
      
      submitComplaintDataWithImages(contact, content);
      setTimeout(showResult,800);
    }
    
    // 提交带图片的投诉
    function submitComplaintDataWithImages(contact, content) {
      var formData = new FormData();
      formData.append('type', selectedType);
      formData.append('content', content);
      formData.append('contact', contact);
      formData.append('link_code', '<?php echo $code; ?>');
      
      for (var i = 0; i < selectedImages.length; i++) {
        formData.append('images[]', selectedImages[i]);
      }
      
      fetch('api/submit_complaint.php', {
        method: 'POST',
        body: formData
      }).then(response => response.json())
        .then(data => console.log('提交结果:', data))
        .catch(error => console.log('提交失败:', error));
    }
    
    // 提交普通投诉
    function submitComplaintData() {
      var formData = new FormData();
      formData.append('type', selectedType);
      formData.append('content', selectedSubtype);
      formData.append('link_code', '<?php echo $code; ?>');
      
      fetch('api/submit_complaint.php', {
        method: 'POST',
        body: formData
      }).then(response => response.json())
        .then(data => console.log('提交结果:', data))
        .catch(error => console.log('提交失败:', error));
    }
    
    function showResult(){
    	 $('.loading').hide()
    	$("#shpage_3").show();
    }
    </script>
</body>
</html>