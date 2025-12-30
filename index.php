<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title>诺辞企业微信防投诉系统V6.0</title>
    <meta name="keywords" content="诺辞企业微信防投诉系统,诺辞,企业微信防投诉,企微护航,企微护航官网,信息流投放页面制作,域名防封解决方案,企业微信恶意投诉处理,企业微信投诉假入口，企业微信投诉拦截,企业微信投诉，企微内部投诉通道，防封拦截系统"/>
    <meta name="description" content="诺辞企业微信防投诉系统,诺辞,企业微信防投诉,企微护航为您提供高转化率信息流页面制作、域名防封解决方案、企业微信恶意投诉处理、员工离职客户继承、对外名片优化等服务。10年互联网运营经验，专业团队量身定制解决方案，助力企业高效运营，提升品牌形象！立即咨询，获取免费方案！"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- 自定义 CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            scroll-behavior: smooth;
        }
        .navigation {
            background-color: #165DFF;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navigation-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .navigation-container-logo {
            height: 40px;
        }
        .navigation-container-menu ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .navigation-container-menu li {
            margin-left: 25px;
        }
        .navigation-container-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .navigation-container-menu a:hover {
            color: #FFD700;
        }
        .index-banner {
            background: linear-gradient(135deg, #165DFF 0%, #0640a5 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        .index-banner-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }
        .index-banner .left {
            flex: 1;
            min-width: 300px;
            padding: 20px;
        }
        .index-banner .right {
            flex: 1;
            min-width: 300px;
            padding: 20px;
        }
        .index-banner h3 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .index-banner h5 {
            font-size: 1.2rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .index-banner-botton a {
            background-color: #FFD700;
            color: #0640a5;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }
        .index-banner-botton a:hover {
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .section {
            padding: 80px 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .title {
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 15px;
            color: #165DFF;
        }
        .describe {
            text-align: center;
            color: #666;
            margin-bottom: 50px;
            font-size: 1.1rem;
        }
        .featured-box {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .featured-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .featured-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        .featured-icon i {
            font-size: 3rem;
            color: #165DFF;
        }
        .featured-content h4 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #333;
        }
        .featured-content p {
            color: #666;
            line-height: 1.7;
        }
        .plan-box {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .plan-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .plan-box .card-body {
            padding: 30px;
        }
        .plan-box h5 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #333;
        }
        .plan-box .text-muted {
            color: #666;
            margin-bottom: 20px;
        }
        .plan-features {
            margin-top: 20px;
        }
        .product-price-items {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #666;
        }
        .product-price-items i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .product-price-items .text-success {
            color: #28a745;
        }
        .product-price-items .text-danger {
            color: #dc3545;
        }
        #copyright {
            background-color: #f8f9fa;
            padding: 30px 0;
            margin-top: 50px;
        }
        .site-info p {
            color: #666;
            margin: 0;
        }
        .nav-inline .nav-link {
            color: #666;
            padding: 0;
            margin-left: 15px;
        }
        .nav-inline .nav-link:hover {
            color: #165DFF;
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .navigation-container-menu {
                display: none;
            }
            .index-banner h3 {
                font-size: 2rem;
            }
            .index-banner h5 {
                font-size: 1rem;
            }
            .title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="navigation">
        <div class="navigation-container">
            <a href="">
                <img src="/admin/img/logo.png" class="navigation-container-logo" id="logo_dark" alt="诺辞企业微信防投诉系统">
            </a>
            <div class="navigation-container-menu">
                <ul class="navigation-container-lists">
                    <li>
                        <a href="">首页</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="index-banner" id="topbar">
        <div class="index-banner-container">
            <div class="left">
                <h3>诺辞企业微信防投诉系统V6.0</h3>
                <h5>为您提供信息流页面制作、域名防封解决方案、企业微信防封拦截系统<br>企微客户继承、对外名片优化等服务，10年互联网运营经验，量身定制解决方案</h5>
                <div class="index-banner-botton">
                    <a href="" target="_blank">
                        <font>立即前往咨询</font>
                    </a>
                </div>
            </div>
            <div class="right">
                <img src="/admin/img/ts.png" alt="企业微信防投诉系统" class="img-fluid">
            </div>
        </div>
    </div>

    <div id="service" class="section bg-white">
        <div class="container">
            <div class="title">企微护航产品功能</div>
            <div class="describe">域名防封解决方案、企业微信恶意投诉处理、员工离职客户继承、对外名片优化等服务</div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="featured-box">
                        <div class="featured-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="featured-content">
                            <h4>信息流投放效果不佳</h4>
                            <p>信息流广告投放后，点击率低，落地页设计不符合用户需求，吸引力不足，页面加载速度慢，用户体验差，缺乏数据分析和优化策略。</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="featured-box">
                        <div class="featured-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="featured-content">
                            <h4>域名频繁被封</h4>
                            <p>提供防封域名解决方案，采用知名企业域名，降低被封风险，实时监控域名状态，及时发现并处理异常情况。</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="featured-box">
                        <div class="featured-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="featured-content">
                            <h4>企微防封拦截系统</h4>
                            <p>建立投诉预警机制，及时发现并处理潜在风险。制定标准化假投诉入口，确保快速响应和解决用户问题。主动与用户沟通化解矛盾，提升用户满意度。</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="featured-box">
                        <div class="featured-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="featured-content">
                            <h4>员工离职，客户资源流失</h4>
                            <p>用企业微信的在职或者离职继承，避免客户流失，一键式客户继承功能，快速转移客户资源，制定员工离职客户交接流程，确保业务连续性。</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="featured-box">
                        <div class="featured-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="featured-content">
                            <h4>企业微信对外展示</h4>
                            <p>利用企业微信名片功能，展示企业介绍、产品服务、成功案例等。展示客户评价和合作伙伴，增强信任感。</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="featured-box">
                        <div class="featured-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="featured-content">
                            <h4>企业微信功能使用难题</h4>
                            <p>无论您遇到任何企业微信相关问题，我们都能为您提供专业的解决方案！</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section" id="price">
        <div class="container">
            <div class="title">产品服务</div>
            <div class="describe">拥有10年互联网运营经验的专家团队为您提供一站式解决方案！</div>
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card plan-box">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5>域名防封解决方案</h5>
                                    <p class="text-muted">大厂域名及空间</p>
                                </div>
                                <div class="flex-shrink-0 ms-3">
                                    <i class="fas fa-shield-alt h1 text-primary"></i>
                                </div>
                            </div>
                            <div class="plan-features mt-5">
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>网站https加密防护</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>CDN加速</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>域名接入WAF</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>DDos攻击防护</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>网页防篡改</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>域名被封检测</span>
                                </p>
                                <p class="product-price-items">
                                     <i class="fas fa-check text-success"></i>
                                    <span>专属人工客服</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card plan-box">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5>企业微信投诉拦截系统</h5>
                                    <p class="text-muted">设置假投诉入口</p>
                                </div>
                                <div class="flex-shrink-0 ms-3">
                                    <i class="fas fa-ban h1 text-primary"></i>
                                </div>
                            </div>
                            <div class="plan-features mt-5">
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>创建假的投诉入口</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>提供防封域名</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>提供可视化后台</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>域名被封检测</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>同一体企微成员可用</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>统一配置无需员工操作</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>专属人工客服</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card plan-box">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5>信息流投放页面制作</h5>
                                    <p class="text-muted">提供源码</p>
                                </div>
                                <div class="flex-shrink-0 ms-3">
                                    <i class="fas fa-code h1 text-primary"></i>
                                </div>
                            </div>
                            <div class="plan-features mt-5">
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>H5广告页面制作</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>优化投放端页面</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>加微信个性化定制</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>加微信按钮延迟出现</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>多域名跳转</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>域名防封</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>专属人工客服</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card plan-box">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5>图片及投放页面设计</h5>
                                    <p class="text-muted">提供PSD源文件</p>
                                </div>
                                <div class="flex-shrink-0 ms-3">
                                    <i class="fas fa-palette h1 text-primary"></i>
                                </div>
                            </div>
                            <div class="plan-features mt-5">
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>入口图设计</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>信息流页面设计</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>主图设计</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>详情页设计</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>主图设计</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>网站设计</span>
                                </p>
                                <p class="product-price-items">
                                    <i class="fas fa-check text-success"></i>
                                    <span>专属人工客服</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="site-info text-center text-md-left">
                        <p>诺辞企业微信防投诉系统V6.0©️企微护航2026</p>
                    </div>
                </div>
                <div class="col-md-8 col-sm-12">
                    <div class="text-md-right">
                        <ul class="nav nav-inline justify-content-md-end justify-content-center">
                            <li class="nav-item" onclick="window.open('https://beian.miit.gov.cn/')">
                                <span class="nav-link">京ICP备2020037952号</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>