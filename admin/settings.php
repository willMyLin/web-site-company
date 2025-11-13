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
    'home_banner_title' => '首页Banner标题'
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
                <div class="card" style="max-width:600px;margin:auto;">
                    <div class="card-header">
                        <h3>基本信息</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($msg)): ?>
                        <div class="alert alert-success"><?= $msg ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="form-group">
                                <label for="site_title">网站标题</label>
                                <input type="text" name="site_title" id="site_title" class="form-control" value="<?= htmlspecialchars($setting_map['site_title']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="site_description">网站描述</label>
                                <textarea name="site_description" id="site_description" class="form-control" rows="2" required><?= htmlspecialchars($setting_map['site_description']) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="company_name">公司名称</label>
                                <input type="text" name="company_name" id="company_name" class="form-control" value="<?= htmlspecialchars($setting_map['company_name']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="company_phone">公司电话</label>
                                <input type="text" name="company_phone" id="company_phone" class="form-control" value="<?= htmlspecialchars($setting_map['company_phone']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="company_email">公司邮箱</label>
                                <input type="email" name="company_email" id="company_email" class="form-control" value="<?= htmlspecialchars($setting_map['company_email']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="company_address">公司地址</label>
                                <input type="text" name="company_address" id="company_address" class="form-control" value="<?= htmlspecialchars($setting_map['company_address']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="home_banner_title">首页Banner标题</label>
                                <input type="text" name="home_banner_title" id="home_banner_title" class="form-control" value="<?= htmlspecialchars($setting_map['home_banner_title']) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">保存设置</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
