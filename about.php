<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/Utils.php';

$db = Database::getInstance();

// 获取网站设置
$settings = [];
$settingsResult = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
foreach($settingsResult as $setting) {
    $settings[$setting['setting_key']] = $setting['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>关于我们 - <?php echo isset($settings['site_title']) ? $settings['site_title'] : '交个朋友'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- 页面头部 -->
    <!-- <div class="page-header">
        <div class="container">
            <h1 class="page-title">关于我们</h1>
            <div class="breadcrumb">
                <a href="/">首页</a> / 关于我们
            </div>
        </div>
    </div> -->

    <!-- 页面内容 -->
    <div class="page-content">
        <div class="container">
            <div class="content-wrapper">
                <div class="main-content">
                    <div class="section" style="padding: 0 0 40px 0;">
                        <h2 style="color: #0066cc; margin-bottom: 20px;">公司概况</h2>
                        <p style="line-height: 1.8; margin-bottom: 20px; font-size: 16px;">
                            厦门交个朋友有限公司成立于1995年，是一家专业从事智慧建筑、智慧园区、智慧民生等领域解决方案的高新技术企业。公司总部位于厦门，在全国30多个城市设有分公司及办事机构，业务覆盖26个省份，拥有100多个业务覆盖城市，累计实施项目超过4000个。
                        </p>
                        <p style="line-height: 1.8; margin-bottom: 20px; font-size: 16px;">
                            经过29年的发展，公司已成为中国智慧建筑行业的领军企业，公司资产达15亿元，拥有一支专业的技术团队和完善的服务体系。我们致力于为客户提供全方位的智慧化解决方案，助力城市数字化转型。
                        </p>
                    </div>

                    <div class="section" style="padding: 40px 0; border-top: 1px solid #eee;">
                        <h2 style="color: #0066cc; margin-bottom: 20px;">企业文化</h2>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                            <div style="text-align: center; padding: 20px;">
                                <h3 style="color: #333; margin-bottom: 15px;">使命愿景</h3>
                                <p style="line-height: 1.6; color: #666;">将智慧建筑融入智慧城市，为人类创造更美好的生活环境</p>
                            </div>
                            <div style="text-align: center; padding: 20px;">
                                <h3 style="color: #333; margin-bottom: 15px;">核心价值观</h3>
                                <p style="line-height: 1.6; color: #666;">创新、专业、诚信、共赢</p>
                            </div>
                            <div style="text-align: center; padding: 20px;">
                                <h3 style="color: #333; margin-bottom: 15px;">发展理念</h3>
                                <p style="line-height: 1.6; color: #666;">以技术创新为驱动，以客户需求为导向</p>
                            </div>
                        </div>
                    </div>

                    <div class="section" style="padding: 40px 0; border-top: 1px solid #eee;">
                        <h2 style="color: #0066cc; margin-bottom: 20px;">发展历程</h2>
                        <div style="position: relative;">
                            <div style="position: absolute; left: 20px; top: 0; bottom: 0; width: 2px; background: #0066cc;"></div>
                            
                            <div style="display: flex; margin-bottom: 30px;">
                                <div style="width: 80px; text-align: center; font-weight: bold; color: #0066cc;">1995</div>
                                <div style="flex: 1; padding-left: 30px;">
                                    <h4 style="margin-bottom: 10px; color: #333;">公司成立</h4>
                                    <p style="color: #666; line-height: 1.6;">厦门交个朋友有限公司正式成立，开始专注于智能化工程领域</p>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 30px;">
                                <div style="width: 80px; text-align: center; font-weight: bold; color: #0066cc;">2005</div>
                                <div style="flex: 1; padding-left: 30px;">
                                    <h4 style="margin-bottom: 10px; color: #333;">业务扩展</h4>
                                    <p style="color: #666; line-height: 1.6;">业务范围扩展至智慧建筑、智慧园区等多个领域</p>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 30px;">
                                <div style="width: 80px; text-align: center; font-weight: bold; color: #0066cc;">2015</div>
                                <div style="flex: 1; padding-left: 30px;">
                                    <h4 style="margin-bottom: 10px; color: #333;">技术创新</h4>
                                    <p style="color: #666; line-height: 1.6;">自主研发软硬件一体化工业级IoT操作系统</p>
                                </div>
                            </div>
                            
                            <div style="display: flex;">
                                <div style="width: 80px; text-align: center; font-weight: bold; color: #0066cc;">2024</div>
                                <div style="flex: 1; padding-left: 30px;">
                                    <h4 style="margin-bottom: 10px; color: #333;">行业领先</h4>
                                    <p style="color: #666; line-height: 1.6;">成为中国智慧建筑行业的领军企业，服务遍及全国</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section" style="padding: 40px 0; border-top: 1px solid #eee;">
                        <h2 style="color: #0066cc; margin-bottom: 20px;">资质荣誉</h2>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                                <h4 style="color: #333; margin-bottom: 10px;">高新技术企业</h4>
                                <p style="color: #666; font-size: 14px;">国家认定的高新技术企业</p>
                            </div>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                                <h4 style="color: #333; margin-bottom: 10px;">ISO9001认证</h4>
                                <p style="color: #666; font-size: 14px;">国际质量管理体系认证</p>
                            </div>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                                <h4 style="color: #333; margin-bottom: 10px;">行业领军企业</h4>
                                <p style="color: #666; font-size: 14px;">智慧建筑行业领军企业</p>
                            </div>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                                <h4 style="color: #333; margin-bottom: 10px;">专利技术</h4>
                                <p style="color: #666; font-size: 14px;">拥有多项自主知识产权</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar">
                    <h3>联系信息</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 15px; line-height: 1.6;">
                            <strong>公司地址：</strong><br>
                            <?php echo isset($settings['company_address']) ? $settings['company_address'] : '厦门市思明区软件园二期'; ?>
                        </li>
                        <li style="margin-bottom: 15px; line-height: 1.6;">
                            <strong>联系电话：</strong><br>
                            <?php echo isset($settings['company_phone']) ? $settings['company_phone'] : '400-123-4567'; ?>
                        </li>
                        <li style="margin-bottom: 15px; line-height: 1.6;">
                            <strong>电子邮箱：</strong><br>
                            <?php echo isset($settings['company_email']) ? $settings['company_email'] : 'info@vann.com.cn'; ?>
                        </li>
                    </ul>

                    <h3 style="margin-top: 30px;">快速导航</h3>
                    <ul>
                        <li><a href="/news/">新闻资讯</a></li>
                        <li><a href="solutions.php">解决方案</a></li>
                        <li><a href="products.php">产品中心</a></li>
                        <li><a href="contact.php">联系我们</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="/assets/js/main.js"></script>
</body>
</html>