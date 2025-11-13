<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Utils.php';

Utils::checkAdminLogin();

$db = Database::getInstance();

// 获取统计数据
$stats = [
    'articles' => $db->fetch("SELECT COUNT(*) as count FROM articles WHERE status = 1")['count'],
    'categories' => $db->fetch("SELECT COUNT(*) as count FROM categories WHERE status = 1")['count'],
    'media' => $db->fetch("SELECT COUNT(*) as count FROM media")['count'],
    'total_views' => $db->fetch("SELECT SUM(views) as total FROM articles")['total'] ?: 0
];

// 获取最新文章
$latestArticles = $db->fetchAll(
    "SELECT a.*, c.name as category_name 
     FROM articles a 
     LEFT JOIN categories c ON a.category_id = c.id 
     ORDER BY a.created_at DESC 
     LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - 交个朋友CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- 侧边栏 -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>交个朋友CMS</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active">控制台</a></li>
                <li><a href="articles.php">文章管理</a></li>
                <li><a href="categories.php">分类管理</a></li>
                <li><a href="media.php">图片管理</a></li>
                <li><a href="settings.php">网站设置</a></li>
                <li><a href="logout.php">退出登录</a></li>
            </ul>
        </aside>

        <!-- 主内容区 -->
        <main class="main-content">
            <header class="header">
                <h1 class="header-title">控制台</h1>
                <div class="header-actions">
                    <span>欢迎，<?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="logout.php" class="btn btn-sm">退出登录</a>
                </div>
            </header>

            <div class="content">
                <!-- 统计卡片 -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="color: #0066cc; font-size: 36px; margin-bottom: 10px;"><?php echo $stats['articles']; ?></h3>
                            <p style="color: #666;">文章总数</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="color: #28a745; font-size: 36px; margin-bottom: 10px;"><?php echo $stats['categories']; ?></h3>
                            <p style="color: #666;">分类总数</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="color: #ffc107; font-size: 36px; margin-bottom: 10px;"><?php echo $stats['media']; ?></h3>
                            <p style="color: #666;">图片总数</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="color: #dc3545; font-size: 36px; margin-bottom: 10px;"><?php echo number_format($stats['total_views']); ?></h3>
                            <p style="color: #666;">总浏览量</p>
                        </div>
                    </div>
                </div>

                <!-- 最新文章 -->
                <div class="card">
                    <div class="card-header">
                        <h3>最新文章</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>标题</th>
                                    <th>分类</th>
                                    <th>状态</th>
                                    <th>浏览量</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($latestArticles as $article): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(Utils::truncate($article['title'], 40)); ?></td>
                                    <td><?php echo htmlspecialchars($article['category_name'] ?: '未分类'); ?></td>
                                    <td>
                                        <span class="status <?php echo $article['status'] ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $article['status'] ? '已发布' : '草稿'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $article['views']; ?></td>
                                    <td><?php echo Utils::formatDate($article['created_at'], 'Y-m-d H:i'); ?></td>
                                    <td>
                                        <a href="article-edit.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-primary">编辑</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="articles.php" class="btn">查看全部文章</a>
                        </div>
                    </div>
                </div>

                <!-- 快捷操作 -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h4 style="margin-bottom: 15px; color: #333;">发布文章</h4>
                            <a href="article-add.php" class="btn btn-primary">新建文章</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h4 style="margin-bottom: 15px; color: #333;">图片管理</h4>
                            <a href="media.php" class="btn btn-success">管理图片</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h4 style="margin-bottom: 15px; color: #333;">分类管理</h4>
                            <a href="categories.php" class="btn btn-warning">管理分类</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h4 style="margin-bottom: 15px; color: #333;">网站设置</h4>
                            <a href="settings.php" class="btn btn-info">系统设置</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>