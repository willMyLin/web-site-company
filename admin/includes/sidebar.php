<?php
// 获取当前页面文件名
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>交个朋友CMS</h3>
    </div>
    <ul class="sidebar-menu">
        <li><a href="index.php" <?php echo $current_page == 'index.php' ? 'class="active"' : ''; ?>>控制台</a></li>
        <li><a href="articles.php" <?php echo in_array($current_page, ['articles.php', 'article-add.php', 'article-edit.php']) ? 'class="active"' : ''; ?>>文章管理</a></li>
        <li><a href="categories.php" <?php echo $current_page == 'categories.php' ? 'class="active"' : ''; ?>>分类管理</a></li>
        <li><a href="sliders.php" <?php echo in_array($current_page, ['sliders.php', 'slider-add.php', 'slider-edit.php']) ? 'class="active"' : ''; ?>>轮播图管理</a></li>
        <li><a href="media.php" <?php echo $current_page == 'media.php' ? 'class="active"' : ''; ?>>图片管理</a></li>
        <li><a href="settings.php" <?php echo $current_page == 'settings.php' ? 'class="active"' : ''; ?>>网站设置</a></li>
        <li><a href="logout.php">退出登录</a></li>
    </ul>
</aside>
