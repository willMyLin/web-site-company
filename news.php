<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/Utils.php';

$db = Database::getInstance();

// 分页设置
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// 分类筛选
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$whereClause = "WHERE a.status = 1 AND c.type = 'news'";
$params = [];

if ($categoryId > 0) {
    $whereClause .= " AND a.category_id = ?";
    $params[] = $categoryId;
}

// 获取新闻总数
$totalSql = "SELECT COUNT(*) as total FROM articles a LEFT JOIN categories c ON a.category_id = c.id $whereClause";

$totalResult = $db->fetch($totalSql, $params);
$total = $totalResult['total'];
$totalPages = ceil($total / $perPage);

// 获取新闻列表
$newsSql = "SELECT a.*, c.name as category_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            $whereClause 
            ORDER BY a.created_at DESC 
            LIMIT $perPage OFFSET $offset";
$newsList = $db->fetchAll($newsSql, $params);

// 获取新闻分类
$categories = $db->fetchAll("SELECT * FROM categories WHERE type = 'news' AND status = 1 ORDER BY sort_order ASC");

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
    <title>新闻资讯 - <?php echo isset($settings['site_title']) ? $settings['site_title'] : '交个朋友'; ?></title>
        <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- 页面头部 -->
    <!-- <div class="page-header">
        <div class="container">
            <h1 class="page-title">新闻资讯</h1>
            <div class="breadcrumb">
                <a href="index.php">首页</a> / 新闻资讯
            </div>
        </div>
    </div> -->

    <!-- 页面内容 -->
    <div class="page-content">
        <div class="container">
            <div class="content-wrapper">
                <div class="main-content">
                    <div class="news-grid">
                        <?php foreach($newsList as $news): ?>
                        <div class="news-item">
                            <?php if($news['featured_image']): ?>
                                <img src="<?php echo UPLOAD_URL . $news['featured_image']; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="news-image">
                            <?php else: ?>
                                <div class="news-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                            <?php endif; ?>
                            <div class="news-content">
                                <h3 class="news-title">
                                    <a href="/news/<?php echo $news['id']; ?>.html"><?php echo htmlspecialchars($news['title']); ?></a>
                                </h3>
                                <p class="news-excerpt"><?php echo Utils::truncate($news['excerpt'] ? $news['excerpt'] : Utils::cleanHtml($news['content']), 150); ?></p>
                                <div class="news-meta">
                                    <span><?php echo $news['category_name']; ?></span>
                                    <span><?php echo Utils::formatDate($news['created_at'], 'Y.m.d'); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 分页 -->
                    <?php if($totalPages > 1): ?>
                    <div class="page-navi">
                        <div class="page-navi-inner">
                            <?php if($page > 1): ?>
                                <a href="?page=1<?php echo $categoryId ? '&category='.$categoryId : ''; ?>" class="page-btn page-first">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8.5 2.5L3 8l5.5 5.5M13 2.5L7.5 8l5.5 5.5" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
                                </a>
                                <a href="?page=<?php echo $page-1; ?><?php echo $categoryId ? '&category='.$categoryId : ''; ?>" class="page-btn page-prev">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            <?php endif; ?>
                            
                            <?php
                            // 智能分页逻辑
                            $range = 2;
                            $start = max(1, $page - $range);
                            $end = min($totalPages, $page + $range);
                            
                            if($start > 1) {
                                echo '<a href="?page=1' . ($categoryId ? '&category='.$categoryId : '') . '" class="page-num">1</a>';
                                if($start > 2) {
                                    echo '<span class="page-ellipsis">···</span>';
                                }
                            }
                            
                            for($i = $start; $i <= $end; $i++):
                                if($i == $page): ?>
                                    <span class="page-num active"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?><?php echo $categoryId ? '&category='.$categoryId : ''; ?>" class="page-num"><?php echo $i; ?></a>
                                <?php endif;
                            endfor;
                            
                            if($end < $totalPages) {
                                if($end < $totalPages - 1) {
                                    echo '<span class="page-ellipsis">···</span>';
                                }
                                echo '<a href="?page='.$totalPages.($categoryId ? '&category='.$categoryId : '').'" class="page-num">'.$totalPages.'</a>';
                            }
                            ?>
                            
                            <?php if($page < $totalPages): ?>
                                <a href="?page=<?php echo $page+1; ?><?php echo $categoryId ? '&category='.$categoryId : ''; ?>" class="page-btn page-next">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                                <a href="?page=<?php echo $totalPages; ?><?php echo $categoryId ? '&category='.$categoryId : ''; ?>" class="page-btn page-last">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M7.5 2.5L13 8l-5.5 5.5M3 2.5L8.5 8 3 13.5" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                        <!-- <div class="page-info">
                            共 <span class="page-total"><?php echo $totalPages; ?></span> 页 / 第 <span class="page-current"><?php echo $page; ?></span> 页
                        </div> -->
                    </div>
                    <?php endif; ?>
                </div>

                <div class="sidebar">
                    <h3>新闻分类</h3>
                    <ul>
                                    <li><a href="/news/" <?php echo $categoryId == 0 ? 'style="color: #0066cc; font-weight: bold;"' : ''; ?>>全部新闻</a></li>
                        <?php foreach($categories as $category): ?>
                                    <li><a href="/news/?category=<?php echo $category['id']; ?>" <?php echo $categoryId == $category['id'] ? 'style="color: #0066cc; font-weight: bold;"' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>

                    <h3 style="margin-top: 30px;">最新资讯</h3>
                    <ul>
                        <?php
                        $latestNews = $db->fetchAll(
                            "SELECT a.*, c.name as category_name 
                             FROM articles a 
                             LEFT JOIN categories c ON a.category_id = c.id 
                             WHERE a.status = 1 AND c.type = 'news' 
                             ORDER BY a.created_at DESC 
                             LIMIT 5"
                        );
                        foreach($latestNews as $news):
                        ?>
                                    <li><a href="/news/<?php echo $news['id']; ?>.html"><?php echo htmlspecialchars(Utils::truncate($news['title'], 40)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

                <script src="/assets/js/main.js"></script>
</body>
</html>

