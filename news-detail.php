<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/Utils.php';

$db = Database::getInstance();

// 获取文章ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: news.php');
    exit;
}

// 获取文章详情
$article = $db->fetch(
    "SELECT a.*, c.name as category_name 
     FROM articles a 
     LEFT JOIN categories c ON a.category_id = c.id 
     WHERE a.id = ? AND a.status = 1",
    [$id]
);

if (!$article) {
    header('Location: news.php');
    exit;
}

// 更新浏览量
$db->query("UPDATE articles SET views = views + 1 WHERE id = ?", [$id]);

// 获取相关文章
$relatedArticles = $db->fetchAll(
    "SELECT * FROM articles 
     WHERE category_id = ? AND id != ? AND status = 1 
     ORDER BY created_at DESC 
     LIMIT 5",
    [$article['category_id'], $id]
);

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
    <title><?php echo htmlspecialchars($article['title']); ?> - <?php echo isset($settings['site_title']) ? $settings['site_title'] : '交个朋友'; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($article['excerpt'] ? $article['excerpt'] : Utils::truncate(Utils::cleanHtml($article['content']), 150)); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- 页面头部 -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="breadcrumb">
                <a href="/">首页</a> / 
                <a href="/news/">新闻资讯</a> / 
                <a href="/news/?category=<?php echo $article['category_id']; ?>"><?php echo htmlspecialchars($article['category_name']); ?></a> / 
                <?php echo htmlspecialchars(Utils::truncate($article['title'], 30)); ?>
            </div>
        </div>
    </div>

    <!-- 页面内容 -->
    <div class="page-content">
        <div class="container">
            <div class="content-wrapper">
                <div class="main-content">
                    <article>
                        <div class="article-meta" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; color: #666;">
                            <span>分类：<?php echo htmlspecialchars($article['category_name']); ?></span> |
                            <span>发布时间：<?php echo Utils::formatDate($article['created_at'], 'Y年m月d日'); ?></span> |
                            <span>浏览量：<?php echo $article['views']; ?></span>
                        </div>
                        
                        <?php if($article['featured_image']): ?>
                        <div class="article-image" style="margin-bottom: 30px; text-align: center;">
                            <img src="<?php echo UPLOAD_URL . $article['featured_image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" style="max-width: 100%; height: auto; border-radius: 8px;">
                        </div>
                        <?php endif; ?>

                        <div class="article-content" style="line-height: 1.8; font-size: 16px; white-space: pre-wrap; word-wrap: break-word;">
                            <?php echo $article['content']; ?>
                        </div>
                    </article>

                    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                        <a href="/news/" class="btn">返回新闻列表</a>
                    </div>
                </div>

                <div class="sidebar">
                    <?php if(!empty($relatedArticles)): ?>
                    <h3>相关文章</h3>
                    <ul>
                        <?php foreach($relatedArticles as $related): ?>
                        <li><a href="/news/<?php echo $related['id']; ?>.html"><?php echo htmlspecialchars(Utils::truncate($related['title'], 40)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <h3 style="margin-top: 30px;">最新资讯</h3>
                    <ul>
                        <?php
                        $latestNews = $db->fetchAll(
                            "SELECT a.*, c.name as category_name 
                             FROM articles a 
                             LEFT JOIN categories c ON a.category_id = c.id 
                             WHERE a.status = 1 AND c.type = 'news' AND a.id != ? 
                             ORDER BY a.created_at DESC 
                             LIMIT 5",
                            [$id]
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