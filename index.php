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

// 获取推荐新闻
$featuredNews = $db->fetchAll(
    "SELECT a.*, c.name as category_name 
     FROM articles a 
     LEFT JOIN categories c ON a.category_id = c.id 
     WHERE a.status = 1 AND a.is_featured = 1 AND c.type = 'news' 
     ORDER BY a.created_at DESC 
     LIMIT 6"
);


// 获取解决方案
$solutions = $db->fetchAll(
    "SELECT * FROM categories WHERE type = 'solution' AND status = 1 ORDER BY sort_order ASC LIMIT 4"
);

// 获取轮播图
$sliders = $db->fetchAll(
    "SELECT * FROM sliders WHERE status = 1 ORDER BY sort_order ASC LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($settings['site_title']) ? $settings['site_title'] : '交个朋友'; ?></title>
    <meta name="description" content="<?php echo isset($settings['site_description']) ? $settings['site_description'] : ''; ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- 轮播图 -->
    <section class="banner-slider">
        <div class="slider-container">
            <?php if(!empty($sliders)): ?>
                <?php foreach($sliders as $index => $slider): ?>
                <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                    <?php if($slider['image']): ?>
                        <img src="<?php echo UPLOAD_URL . $slider['image']; ?>" alt="<?php echo htmlspecialchars($slider['title']); ?>">
                    <?php else: ?>
                        <div class="slide-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php endif; ?>
                    <div class="slide-content">
                        <h2><?php echo htmlspecialchars($slider['title']); ?></h2>
                        <?php if($slider['description']): ?>
                            <p><?php echo htmlspecialchars($slider['description']); ?></p>
                        <?php endif; ?>
                        <?php if($slider['link']): ?>
                            <a href="<?php echo htmlspecialchars($slider['link']); ?>" class="btn btn-light">了解更多</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- 默认轮播图 -->
                <div class="slide active">
                    <div class="slide-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="placeholder-content">
                            <h2>全流程服务</h2>
                            <p>前期 1 对 1 沟通，免费出方案与原型图；建站中同步进度、支持修改，上线后提供操作培训。</p>
                            <a href="/about/" class="btn btn-light">了解更多</a>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide-placeholder" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                        <div class="placeholder-content">
                            <h2>靠谱团队</h2>
                            <p>核心成员 10 年 + 行业经验，部分小伙伴来自大厂</p>
                            <a href="/about/" class="btn btn-light">了解更多</a>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide-placeholder" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
                        <div class="placeholder-content">
                            <h2>贴心售后</h2>
                            <p>上线后 1 年免费技术维护，24 小时内响应需求；定期安全检测与性能优化，提前规避问题</p>
                            <a href="/about/" class="btn btn-light">了解更多</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- 轮播控制 -->
        <button class="slider-btn slider-prev" onclick="changeSlide(-1)">&#10094;</button>
        <button class="slider-btn slider-next" onclick="changeSlide(1)">&#10095;</button>
        
        <!-- 指示点 -->
        <div class="slider-dots">
            <?php 
            $slideCount = !empty($sliders) ? count($sliders) : 3;
            for($i = 0; $i < $slideCount; $i++): 
            ?>
                <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)"></span>
            <?php endfor; ?>
        </div>
    </section>

    <!-- 统计数据 -->
    <section class="stats">
        <div class="container">
            <div class="stats-layout">
                <div class="stats-left">
                    <h2 class="stats-title">厦门交个朋友有限公司</h2>
                    <div class="stats-subtitle">SINCE1995</div>
                    <p class="stats-desc">交个朋友成立于1995年，深耕企业数字化服务28年。我们专注于网站建设、品牌设计与数字营销，为客户提供从策划到落地的一站式互联网解决方案，助力企业在数字时代实现品牌价值提升与业务增长。</p>
                    
                    <div class="stats-grid-compact">
                        <div class="stat-compact">
                            <div class="stat-num" data-target="29"><span class="counter">0</span><span>年</span></div>
                            <div class="stat-text">公司成立</div>
                        </div>
                        <div class="stat-compact">
                            <div class="stat-num" data-target="15"><span class="counter">0</span><span>亿元</span></div>
                            <div class="stat-text">公司资产</div>
                        </div>
                        <div class="stat-compact">
                            <div class="stat-num" data-target="100"><span class="counter">0</span><span>+</span></div>
                            <div class="stat-text">业务覆盖城市</div>
                        </div>
                    </div>
                    
                    <div class="stats-grid-compact">
                        <div class="stat-compact">
                            <div class="stat-num" data-target="4000"><span class="counter">0</span><span>+</span></div>
                            <div class="stat-text">实施案例</div>
                        </div>
                        <div class="stat-compact">
                            <div class="stat-num" data-target="30"><span class="counter">0</span><span>+</span></div>
                            <div class="stat-text">分公司及办事机构</div>
                        </div>
                        <div class="stat-compact">
                            <div class="stat-num" data-target="26"><span class="counter">0</span><span></span></div>
                            <div class="stat-text">业务覆盖省份</div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 40px;">
                        <a href="/about/" class="btn-outline-primary">了解更多</a>
                    </div>
                </div>
                
                <div class="stats-right">
                    <div class="building-image">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=1000&fit=crop" alt="智能大楼">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 新闻资讯 -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>新闻资讯</h2>
                <p>了解交个朋友最新动态和行业资讯</p>
            </div>
            <div class="news-grid">
                <?php foreach($featuredNews as $news): ?>
                <div class="news-item" onclick="location.href='/news/<?php echo $news['id']; ?>.html'" style="cursor: pointer;">
                    <?php if($news['featured_image']): ?>
                        <img src="<?php echo UPLOAD_URL . $news['featured_image']; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="news-image">
                    <?php else: ?>
                        <div class="news-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php endif; ?>
                    <div class="news-content">
                        <h3 class="news-title">
                            <a href="/news/<?php echo $news['id']; ?>.html"><?php echo htmlspecialchars($news['title']); ?></a>
                        </h3>
                        <p class="news-excerpt"><?php echo Utils::truncate($news['excerpt'] ?: Utils::cleanHtml($news['content']), 100); ?></p>
                        <div class="news-meta">
                            <span><?php echo $news['category_name']; ?></span>
                            <span><?php echo Utils::formatDate($news['created_at'], 'Y.m.d'); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="news.php" class="btn">了解更多</a>
            </div>
        </div>
    </section>



    <!-- 产品中心 -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>产品中心</h2>
                <p>创新的产品技术，引领行业发展</p>
            </div>
            <div class="solutions-grid">
                <div class="solution-item">
                    <h3 class="solution-title">物联网</h3>
                    <p class="solution-desc">传感设备实时采集任何需要监控、连接、互动的物体的信息，然后通过有线或无线网络接入最终实现物与物、物与人的连接</p>
                </div>
                <div class="solution-item">
                    <h3 class="solution-title">人工智能</h3>
                    <p class="solution-desc">传感设备实时采集任何需要监控、连接、互动的物体的信息，然后通过有线或无线网络接入最终实现物与物、物与人的连接</p>
                </div>
                <div class="solution-item">
                    <h3 class="solution-title">电子政务</h3>
                    <p class="solution-desc">传感设备实时采集任何需要监控、连接、互动的物体的信息，然后通过有线或无线网络接入最终实现物与物、物与人的连接</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="news.php" class="btn">了解更多</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
    // 轮播图功能
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    function showSlide(n) {
        if (n >= slides.length) currentSlide = 0;
        if (n < 0) currentSlide = slides.length - 1;
        
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }
    
    function changeSlide(direction) {
        currentSlide += direction;
        showSlide(currentSlide);
    }
    
    function goToSlide(n) {
        currentSlide = n;
        showSlide(currentSlide);
    }
    
    // 自动轮播
    setInterval(() => {
        currentSlide++;
        showSlide(currentSlide);
    }, 5000);
    
    // 数字滚动动画
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start);
            }
        }, 16);
    }
    
    // 监听滚动，当统计区域进入视图时触发动画
    const statsSection = document.querySelector('.stats');
    let animated = false;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animated) {
                animated = true;
                const counters = document.querySelectorAll('.counter');
                counters.forEach(counter => {
                    const target = parseInt(counter.parentElement.getAttribute('data-target'));
                    animateCounter(counter, target);
                });
            }
        });
    }, { threshold: 0.3 });
    
    if (statsSection) {
        observer.observe(statsSection);
    }
    </script>
</body>
</html>