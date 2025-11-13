<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Utils.php';

Utils::checkAdminLogin();

$db = Database::getInstance();

// 需要支持的设置项
$setting_keys = [
    'site_title' => '网站标题',
    'site_description' => '网站描述',
    'company_name' => '公司名称',
    'company_phone' => '公司电话',
    'company_email' => '公司邮箱',
    'company_address' => '公司地址',
    'home_banner_title' => '首页Banner标题',
    'company_intro' => '公司简介',
    'stat_years' => '公司成立年数',
    'stat_assets' => '公司资产（亿元）',
    'stat_cities' => '业务覆盖城市数',
    'stat_cases' => '实施案例数',
    'stat_branches' => '分公司及办事机构数',
    'stat_provinces' => '业务覆盖省份数'
];

// 保存设置
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($setting_keys as $key => $label) {
        $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        // 使用 INSERT ON DUPLICATE KEY UPDATE 避免重复键错误
        $db->query(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
             ON DUPLICATE KEY UPDATE setting_value = ?', 
            [$key, $value, $value]
        );
    }
    $msg = '保存成功！';
}

// 获取所有设置
$settings = $db->fetchAll('SELECT setting_key, setting_value FROM settings WHERE setting_key IN ("' . implode('","', array_keys($setting_keys)) . '")');
$setting_map = [];
foreach ($settings as $row) {
    $setting_map[$row['setting_key']] = $row['setting_value'];
}
// 保证所有字段都有值
foreach ($setting_keys as $key => $label) {
    if (!isset($setting_map[$key])) $setting_map[$key] = '';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站设置 - 交个朋友CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>交个朋友CMS</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php">控制台</a></li>
                <li><a href="articles.php">文章管理</a></li>
                <li><a href="categories.php">分类管理</a></li>
                <li><a href="sliders.php">轮播图管理</a></li>
                <li><a href="media.php">图片管理</a></li>
                <li><a href="settings.php" class="active">网站设置</a></li>
                <li><a href="logout.php">退出登录</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="header">
                <h1 class="header-title">网站设置</h1>
            </header>
            <div class="content">
                <?php if(isset($msg)): ?>
                <div class="alert alert-success" style="max-width: 900px; margin: 0 auto 20px;"><?= $msg ?></div>
                <?php endif; ?>
                
                <div style="max-width: 900px; margin: 0 auto;">
                    <form method="post">
                        <!-- 基本信息卡片 -->
                        <div class="card" style="margin-bottom: 20px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <h3 style="margin: 0; font-size: 18px;">基本信息</h3>
                            </div>
                            <div class="card-body">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <div class="form-group">
                                        <label for="site_title">网站标题 *</label>
                                        <input type="text" name="site_title" id="site_title" class="form-control" value="<?= htmlspecialchars($setting_map['site_title']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="company_name">公司名称</label>
                                        <input type="text" name="company_name" id="company_name" class="form-control" value="<?= htmlspecialchars($setting_map['company_name']) ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="site_description">网站描述 *</label>
                                    <textarea name="site_description" id="site_description" class="form-control" rows="2" required><?= htmlspecialchars($setting_map['site_description']) ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="company_intro">公司简介</label>
                                    <textarea name="company_intro" id="company_intro" class="form-control" rows="4" placeholder="公司简介将显示在首页统计数据区域"><?= htmlspecialchars($setting_map['company_intro']) ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="home_banner_title">首页Banner标题</label>
                                    <input type="text" name="home_banner_title" id="home_banner_title" class="form-control" value="<?= htmlspecialchars($setting_map['home_banner_title']) ?>" placeholder="支持使用 \n 换行">
                                </div>
                            </div>
                        </div>

                        <!-- 联系方式卡片 -->
                        <div class="card" style="margin-bottom: 20px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                <h3 style="margin: 0; font-size: 18px;">联系方式</h3>
                            </div>
                            <div class="card-body">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <div class="form-group">
                                        <label for="company_phone">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="vertical-align: middle; margin-right: 5px;">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            公司电话
                                        </label>
                                        <input type="text" name="company_phone" id="company_phone" class="form-control" value="<?= htmlspecialchars($setting_map['company_phone']) ?>" placeholder="400-123-4567">
                                    </div>
                                    <div class="form-group">
                                        <label for="company_email">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="vertical-align: middle; margin-right: 5px;">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <polyline points="22,6 12,13 2,6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            公司邮箱
                                        </label>
                                        <input type="email" name="company_email" id="company_email" class="form-control" value="<?= htmlspecialchars($setting_map['company_email']) ?>" placeholder="info@company.com">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="company_address">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="vertical-align: middle; margin-right: 5px;">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        公司地址
                                    </label>
                                    <input type="text" name="company_address" id="company_address" class="form-control" value="<?= htmlspecialchars($setting_map['company_address']) ?>" placeholder="详细地址">
                                </div>
                            </div>
                        </div>

                        <!-- 统计数据卡片 -->
                        <div class="card" style="margin-bottom: 20px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                <h3 style="margin: 0; font-size: 18px;">首页统计数据</h3>
                            </div>
                            <div class="card-body">
                                <p style="color: #666; margin-bottom: 20px; font-size: 14px;">这些数据将显示在首页的统计展示区域</p>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                                    <div class="form-group">
                                        <label for="stat_years">公司成立年数</label>
                                        <input type="number" name="stat_years" id="stat_years" class="form-control" value="<?= htmlspecialchars($setting_map['stat_years']) ?>" min="0" placeholder="29">
                                    </div>
                                    <div class="form-group">
                                        <label for="stat_assets">公司资产（亿元）</label>
                                        <input type="number" name="stat_assets" id="stat_assets" class="form-control" value="<?= htmlspecialchars($setting_map['stat_assets']) ?>" min="0" placeholder="15">
                                    </div>
                                    <div class="form-group">
                                        <label for="stat_cities">业务覆盖城市数</label>
                                        <input type="number" name="stat_cities" id="stat_cities" class="form-control" value="<?= htmlspecialchars($setting_map['stat_cities']) ?>" min="0" placeholder="100">
                                    </div>
                                    <div class="form-group">
                                        <label for="stat_cases">实施案例数</label>
                                        <input type="number" name="stat_cases" id="stat_cases" class="form-control" value="<?= htmlspecialchars($setting_map['stat_cases']) ?>" min="0" placeholder="4000">
                                    </div>
                                    <div class="form-group">
                                        <label for="stat_branches">分公司及办事机构数</label>
                                        <input type="number" name="stat_branches" id="stat_branches" class="form-control" value="<?= htmlspecialchars($setting_map['stat_branches']) ?>" min="0" placeholder="30">
                                    </div>
                                    <div class="form-group">
                                        <label for="stat_provinces">业务覆盖省份数</label>
                                        <input type="number" name="stat_provinces" id="stat_provinces" class="form-control" value="<?= htmlspecialchars($setting_map['stat_provinces']) ?>" min="0" placeholder="26">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="text-align: center; padding: 20px 0;">
                            <button type="submit" class="btn btn-primary" style="padding: 12px 40px; font-size: 16px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="vertical-align: middle; margin-right: 5px;">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <polyline points="17 21 17 13 7 13 7 21" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <polyline points="7 3 7 8 15 8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                保存所有设置
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
