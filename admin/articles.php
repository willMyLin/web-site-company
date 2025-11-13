<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Utils.php';

Utils::checkAdminLogin();

$db = Database::getInstance();

// 处理删除操作
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // 获取文章信息以删除相关图片
    $article = $db->fetch("SELECT featured_image FROM articles WHERE id = ?", [$id]);
    if ($article && $article['featured_image']) {
        Utils::deleteFile($article['featured_image']);
    }
    
    $db->query("DELETE FROM articles WHERE id = ?", [$id]);
    header('Location: articles.php?msg=deleted');
    exit;
}

// 分页设置
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

// 搜索和筛选
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$whereClause = "WHERE 1=1";
$params = [];

if ($search) {
    $whereClause .= " AND a.title LIKE ?";
    $params[] = "%$search%";
}

if ($categoryFilter > 0) {
    $whereClause .= " AND a.category_id = ?";
    $params[] = $categoryFilter;
}

if ($statusFilter !== '') {
    $whereClause .= " AND a.status = ?";
    $params[] = (int)$statusFilter;
}

// 获取文章总数
$totalSql = "SELECT COUNT(*) as total FROM articles a $whereClause";
$totalResult = $db->fetch($totalSql, $params);
$total = $totalResult['total'];
$totalPages = ceil($total / $perPage);

// 获取文章列表
$articlesSql = "SELECT a.*, c.name as category_name 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                $whereClause 
                ORDER BY a.created_at DESC 
                LIMIT $perPage OFFSET $offset";
$articles = $db->fetchAll($articlesSql, $params);

// 获取分类列表
$categories = $db->fetchAll("SELECT * FROM categories ORDER BY type, sort_order");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理 - 交个朋友CMS</title>
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
                <li><a href="index.php">控制台</a></li>
                <li><a href="articles.php" class="active">文章管理</a></li>
                <li><a href="categories.php">分类管理</a></li>
                <li><a href="media.php">图片管理</a></li>
                <li><a href="settings.php">网站设置</a></li>
                <li><a href="logout.php">退出登录</a></li>
            </ul>
        </aside>

        <!-- 主内容区 -->
        <main class="main-content">
            <header class="header">
                <h1 class="header-title">文章管理</h1>
                <div class="header-actions">
                    <a href="article-add.php" class="btn btn-primary">新建文章</a>
                </div>
            </header>

            <div class="content">
                <?php if(isset($_GET['msg'])): ?>
                    <?php if($_GET['msg'] == 'saved'): ?>
                        <div class="alert alert-success">文章保存成功！</div>
                    <?php elseif($_GET['msg'] == 'deleted'): ?>
                        <div class="alert alert-success">文章删除成功！</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3>文章列表 (共 <?php echo $total; ?> 篇)</h3>
                    </div>
                    <div class="card-body">
                        <!-- 搜索和筛选 -->
                        <div class="toolbar">
                            <div class="toolbar-left">
                                <form method="get" class="search-box">
                                    <input type="text" name="search" placeholder="搜索文章标题..." value="<?php echo htmlspecialchars($search); ?>">
                                    <select name="category">
                                        <option value="">全部分类</option>
                                        <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="status">
                                        <option value="">全部状态</option>
                                        <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>已发布</option>
                                        <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>草稿</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">搜索</button>
                                    <a href="articles.php" class="btn">重置</a>
                                </form>
                            </div>
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>标题</th>
                                    <th>分类</th>
                                    <th>状态</th>
                                    <th>推荐</th>
                                    <th>浏览量</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($articles as $article): ?>
                                <tr>
                                    <td><?php echo $article['id']; ?></td>
                                    <td>
                                        <a href="../news-detail.php?id=<?php echo $article['id']; ?>" target="_blank" style="color: #333; text-decoration: none;">
                                            <?php echo htmlspecialchars(Utils::truncate($article['title'], 50)); ?>
                                        </a>
                                        <?php if($article['featured_image']): ?>
                                            <span style="color: #0066cc; font-size: 12px;">📷</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($article['category_name'] ?: '未分类'); ?></td>
                                    <td>
                                        <span class="status <?php echo $article['status'] ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $article['status'] ? '已发布' : '草稿'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($article['is_featured']): ?>
                                            <span style="color: #ffc107;">⭐</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo number_format($article['views']); ?></td>
                                    <td><?php echo Utils::formatDate($article['created_at'], 'm-d H:i'); ?></td>
                                    <td>
                                        <a href="article-edit.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-primary">编辑</a>
                                        <a href="articles.php?action=delete&id=<?php echo $article['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('确定要删除这篇文章吗？')">删除</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- 分页 -->
                        <?php if($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if($page > 1): ?>
                                <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $categoryFilter; ?>&status=<?php echo $statusFilter; ?>">&laquo; 上一页</a>
                            <?php endif; ?>
                            
                            <?php for($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                                <?php if($i == $page): ?>
                                    <span class="current"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $categoryFilter; ?>&status=<?php echo $statusFilter; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if($page < $totalPages): ?>
                                <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $categoryFilter; ?>&status=<?php echo $statusFilter; ?>">下一页 &raquo;</a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>