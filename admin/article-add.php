<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Utils.php';

Utils::checkAdminLogin();

$db = Database::getInstance();
$error = '';
$success = '';

// 获取文章ID（编辑模式）
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

// 初始化文章数据
$article = [
    'title' => '',
    'content' => '',
    'excerpt' => '',
    'category_id' => 0,
    'featured_image' => '',
    'status' => 1,
    'is_featured' => 0
];

// 如果是编辑模式，获取文章信息
if ($isEdit) {
    $existingArticle = $db->fetch("SELECT * FROM articles WHERE id = ?", [$id]);
    if (!$existingArticle) {
        header('Location: articles.php');
        exit;
    }
    $article = array_merge($article, $existingArticle);
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $excerpt = trim($_POST['excerpt']);
    $categoryId = (int)$_POST['category_id'];
    $status = (int)$_POST['status'];
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    
    if (empty($title)) {
        $error = '请输入文章标题';
    } elseif (empty($content)) {
        $error = '请输入文章内容';
    } else {
        try {
            $featuredImage = $article['featured_image'];
            
            // 处理图片上传
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == UPLOAD_ERR_OK) {
                // 删除旧图片
                if ($featuredImage) {
                    Utils::deleteFile($featuredImage);
                }
                $featuredImage = Utils::uploadFile($_FILES['featured_image']);
            }
            
            if ($isEdit) {
                // 更新文章
                $db->query(
                    "UPDATE articles SET 
                     title = ?, content = ?, excerpt = ?, category_id = ?, 
                     featured_image = ?, status = ?, is_featured = ?, updated_at = NOW() 
                     WHERE id = ?",
                    [$title, $content, $excerpt, $categoryId, $featuredImage, $status, $isFeatured, $id]
                );
                $success = '文章更新成功！';
            } else {
                // 创建新文章
                $db->query(
                    "INSERT INTO articles 
                     (title, content, excerpt, category_id, featured_image, status, is_featured) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$title, $content, $excerpt, $categoryId, $featuredImage, $status, $isFeatured]
                );
                $success = '文章创建成功！';
                $id = $db->lastInsertId();
                $isEdit = true;
            }
            
            // 更新文章数据
            $article = array_merge($article, [
                'title' => $title,
                'content' => $content,
                'excerpt' => $excerpt,
                'category_id' => $categoryId,
                'featured_image' => $featuredImage,
                'status' => $status,
                'is_featured' => $isFeatured
            ]);
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// 获取分类列表
$categories = $db->fetchAll("SELECT * FROM categories ORDER BY type, sort_order");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? '编辑文章' : '新建文章'; ?> - 交个朋友CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .editor-container {
            position: relative;
        }
        .editor-toolbar {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-bottom: none;
            padding: 10px;
            border-radius: 4px 4px 0 0;
        }
        .editor-toolbar button {
            background: #fff;
            border: 1px solid #ccc;
            padding: 5px 10px;
            margin-right: 5px;
            cursor: pointer;
            border-radius: 3px;
        }
        .editor-toolbar button:hover {
            background: #e6e6e6;
        }
        #content {
            border-radius: 0 0 4px 4px;
            font-family: 'Microsoft YaHei', Arial, sans-serif;
        }
    </style>
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
                <h1 class="header-title"><?php echo $isEdit ? '编辑文章' : '新建文章'; ?></h1>
                <div class="header-actions">
                    <a href="articles.php" class="btn">返回列表</a>
                </div>
            </header>

            <div class="content">
                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-col" style="flex: 2;">
                                    <label for="title">文章标题 *</label>
                                    <input type="text" id="title" name="title" class="form-control" required 
                                           value="<?php echo htmlspecialchars($article['title']); ?>">
                                </div>
                                <div class="form-col">
                                    <label for="category_id">分类</label>
                                    <select id="category_id" name="category_id" class="form-control">
                                        <option value="0">请选择分类</option>
                                        <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo $article['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label for="excerpt">文章摘要</label>
                                    <textarea id="excerpt" name="excerpt" class="form-control" rows="3" 
                                              placeholder="如果不填写，将自动截取文章内容前150字"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label for="featured_image">特色图片</label>
                                    <?php if($article['featured_image']): ?>
                                        <div style="margin-bottom: 10px;">
                                            <img src="<?php echo UPLOAD_URL . $article['featured_image']; ?>" 
                                                 alt="当前图片" style="max-width: 200px; max-height: 150px; border-radius: 4px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
                                    <small style="color: #666;">支持 JPG、PNG、GIF 格式，建议尺寸 800x600px</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label for="content">文章内容 *</label>
                                    <div class="editor-container">
                                        <div class="editor-toolbar">
                                            <button type="button" onclick="insertTag('h2', '标题')">标题</button>
                                            <button type="button" onclick="insertTag('p', '段落')">段落</button>
                                            <button type="button" onclick="insertTag('strong', '粗体')">粗体</button>
                                            <button type="button" onclick="insertTag('em', '斜体')">斜体</button>
                                            <button type="button" onclick="insertList('ul')">无序列表</button>
                                            <button type="button" onclick="insertList('ol')">有序列表</button>
                                            <button type="button" onclick="insertLink()">链接</button>
                                        </div>
                                        <textarea id="content" name="content" class="form-control" rows="15" required 
                                                  style="font-family: monospace;"><?php echo htmlspecialchars($article['content']); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label for="status">发布状态</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="0" <?php echo $article['status'] == 0 ? 'selected' : ''; ?>>草稿</option>
                                        <option value="1" <?php echo $article['status'] == 1 ? 'selected' : ''; ?>>发布</option>
                                    </select>
                                </div>
                                <div class="form-col">
                                    <label>&nbsp;</label>
                                    <div style="padding-top: 10px;">
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" name="is_featured" value="1" 
                                                   <?php echo $article['is_featured'] ? 'checked' : ''; ?> 
                                                   style="margin-right: 8px;">
                                            设为推荐文章
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr style="margin: 30px 0;">

                            <div style="text-align: center;">
                                <button type="submit" class="btn btn-primary" style="padding: 12px 40px;">
                                    <?php echo $isEdit ? '更新文章' : '发布文章'; ?>
                                </button>
                                <a href="articles.php" class="btn" style="padding: 12px 40px; margin-left: 10px;">取消</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function insertTag(tag, defaultText) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end) || defaultText;
            
            const beforeText = textarea.value.substring(0, start);
            const afterText = textarea.value.substring(end);
            
            const newText = `<${tag}>${selectedText}</${tag}>`;
            textarea.value = beforeText + newText + afterText;
            
            textarea.focus();
            textarea.setSelectionRange(start + tag.length + 2, start + tag.length + 2 + selectedText.length);
        }
        
        function insertList(listType) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            
            const beforeText = textarea.value.substring(0, start);
            const afterText = textarea.value.substring(end);
            
            const newText = `<${listType}>\n<li>列表项1</li>\n<li>列表项2</li>\n</${listType}>`;
            textarea.value = beforeText + newText + afterText;
            
            textarea.focus();
        }
        
        function insertLink() {
            const url = prompt('请输入链接地址:');
            if (url) {
                const text = prompt('请输入链接文字:') || url;
                insertTag('a href="' + url + '"', text);
            }
        }
    </script>
</body>
</html>