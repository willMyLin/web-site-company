<!-- 头部 -->
<header class="header">
   <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="index.php" class="logo">交个朋友</a>
                <nav>
                    <ul class="nav">
                        <li><a href="/" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>首页</a></li>
                        <li><a href="/about/" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>关于我们</a></li>
                        <li><a href="/news/" <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' || basename($_SERVER['PHP_SELF']) == 'news-detail.php' ? 'class="active"' : ''; ?>>新闻资讯</a></li>
                        <!-- <li><a href="/news/">解决方案</a></li>
                        <li><a href="/news/">产品中心</a></li> -->
                        <li><a href="/about/">联系我们</a></li>
                    </ul>
                </nav>
            </div>
        </div>
</header>
