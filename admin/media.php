<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Utils.php';

Utils::checkAdminLogin();

$db = Database::getInstance();
$error = '';
$success = '';

// 处理图片上传
if (isset($_FILES['images'])) {
    $uploadedCount = 0;
    $errors = [];
    
    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['images']['error'][$key] == UPLOAD_ERR_OK) {
            try {
                $fileInfo = [
                    'name' => $_FILES['images']['name'][$key],
                    'tmp_name' => $tmpName,
                    'size' => $_FILES['images']['size'][$key],
                    'error' => $_FILES['images']['error'][$key]
                ];
                
                $fileName = Utils::uploadFile($fileInfo);
                
                // 获取文件信息
                $originalName = $_FILES['images']['name'][$key];
                $fileSize = $_FILES['images']['size'][$key];
                $mimeType = mime_content_type(UPLOAD_PATH . $fileName);
                
                // 保存到数据库
                $db->query(
                    "INSERT INTO media (filename, original_name, file_size, mime_type) VALUES (?, ?, ?, ?)",
                    [$fileName, $originalName, $fileSize, $mimeType]
                );
                
                $uploadedCount++;
                
            } catch (Exception $e) {
                $errors[] = $_FILES['images']['name'][$key] . ': ' . $e->getMessage();
            }
        }
    }
    
    if ($uploadedCount > 0) {
        $success = "成功上传 $uploadedCount 张图片！";
    }
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}

// 处理删除操作
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $media = $db->fetch("SELECT filename FROM media WHERE id = ?", [$id]);
    if ($media) {
        Utils::deleteFile($media['filename']);
        $db->query("DELETE FROM media WHERE id = ?", [$id]);
        header('Location: media.php?msg=deleted');
        exit;
    }
}

// 分页设置
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// 获取图片总数
$total = $db->fetch("SELECT COUNT(*) as total FROM media")['total'];
$totalPages = ceil($total / $perPage);

// 获取图片列表
$mediaList = $db->fetchAll(
    "SELECT * FROM media ORDER BY created_at DESC LIMIT $perPage OFFSET $offset"
);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>图片管理 - 交个朋友CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .media-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .media-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .media-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
        }
        .media-info {
            padding: 10px;
        }
        .media-title {
            font-size: 12px;
            margin-bottom: 5px;
            word-break: break-all;
        }
        .media-meta {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }
        .media-actions {
            display: flex;
            gap: 5px;
        }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .upload-area:hover {
            border-color: #0066cc;
            background: #f8f9fa;
        }
        .upload-area.dragover {
            border-color: #0066cc;
            background: #e3f2fd;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
        }
        .modal-content {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
        }
        .modal-image {
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
        }
        .modal-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 30px;
            cursor: pointer;
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
                <li><a href="articles.php">文章管理</a></li>
                <li><a href="categories.php">分类管理</a></li>
                <li><a href="sliders.php">轮播图管理</a></li>
                <li><a href="media.php" class="active">图片管理</a></li>
                <li><a href="settings.php">网站设置</a></li>
                <li><a href="logout.php">退出登录</a></li>
            </ul>
        </aside>

        <!-- 主内容区 -->
        <main class="main-content">
            <header class="header">
                <h1 class="header-title">图片管理</h1>
                <div class="header-actions">
                    <span>共 <?php echo $total; ?> 张图片</span>
                </div>
            </header>

            <div class="content">
                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-success">图片删除成功！</div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3>上传图片</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data" id="uploadForm">
                            <div class="upload-area" id="uploadArea">
                                <input type="file" name="images[]" id="imageInput" multiple accept="image/*" style="display: none;">
                                <div class="upload-text">
                                    <p style="font-size: 18px; margin-bottom: 10px;">📸 选择或拖拽图片文件</p>
                                    <p style="color: #666; margin-bottom: 20px;">支持 JPG、PNG、GIF 格式，可同时上传多张图片</p>
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('imageInput').click()">选择图片</button>
                                </div>
                            </div>
                            <div id="selectedFiles"></div>
                            <button type="submit" class="btn btn-success" id="uploadBtn" style="display: none;">开始上传</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>图片库</h3>
                    </div>
                    <div class="card-body">
                        <div class="media-grid">
                            <?php foreach($mediaList as $media): ?>
                            <div class="media-item">
                                <img src="<?php echo UPLOAD_URL . $media['filename']; ?>" 
                                     alt="<?php echo htmlspecialchars($media['original_name']); ?>" 
                                     class="media-image"
                                     onclick="showModal('<?php echo UPLOAD_URL . $media['filename']; ?>')">
                                <div class="media-info">
                                    <div class="media-title"><?php echo htmlspecialchars(Utils::truncate($media['original_name'], 20)); ?></div>
                                    <div class="media-meta">
                                        <?php echo number_format($media['file_size'] / 1024, 1); ?> KB<br>
                                        <?php echo Utils::formatDate($media['created_at'], 'm-d H:i'); ?>
                                    </div>
                                    <div class="media-actions">
                                        <button class="btn btn-sm btn-primary" onclick="copyUrl('<?php echo UPLOAD_URL . $media['filename']; ?>')">复制链接</button>
                                        <a href="media.php?action=delete&id=<?php echo $media['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('确定要删除这张图片吗？')">删除</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 分页 -->
                        <?php if($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if($page > 1): ?>
                                <a href="?page=<?php echo $page-1; ?>">&laquo; 上一页</a>
                            <?php endif; ?>
                            
                            <?php for($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                                <?php if($i == $page): ?>
                                    <span class="current"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if($page < $totalPages): ?>
                                <a href="?page=<?php echo $page+1; ?>">下一页 &raquo;</a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 图片预览模态框 -->
    <div id="imageModal" class="modal">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="modalImage" class="modal-image" src="" alt="">
        </div>
    </div>

    <script>
        // 文件选择处理
        document.getElementById('imageInput').addEventListener('change', function(e) {
            showSelectedFiles(e.target.files);
        });

        // 拖拽上传
        const uploadArea = document.getElementById('uploadArea');
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            document.getElementById('imageInput').files = files;
            showSelectedFiles(files);
        });

        function showSelectedFiles(files) {
            const container = document.getElementById('selectedFiles');
            const uploadBtn = document.getElementById('uploadBtn');
            
            if (files.length > 0) {
                let html = '<h4>已选择 ' + files.length + ' 个文件:</h4><ul>';
                for (let i = 0; i < files.length; i++) {
                    html += '<li>' + files[i].name + ' (' + (files[i].size / 1024).toFixed(1) + ' KB)</li>';
                }
                html += '</ul>';
                container.innerHTML = html;
                uploadBtn.style.display = 'inline-block';
            } else {
                container.innerHTML = '';
                uploadBtn.style.display = 'none';
            }
        }

        function showModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        function copyUrl(url) {
            navigator.clipboard.writeText(url).then(function() {
                alert('图片链接已复制到剪贴板');
            }, function() {
                prompt('请复制以下链接:', url);
            });
        }

        // 点击模态框背景关闭
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // ESC键关闭模态框
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>