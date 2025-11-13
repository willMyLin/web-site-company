<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

// 检查登录
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();

// 处理表单提交
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $link = $_POST['link'];
    $sort_order = (int)$_POST['sort_order'];
    $status = isset($_POST['status']) ? 1 : 0;
    
    // 处理图片上传
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = UPLOAD_PATH;
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($file_ext, $allowed)) {
            $new_filename = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_filename;
            }
        }
    }
    
    // 插入数据库
    $sql = "INSERT INTO sliders (title, subtitle, image, link, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)";
    $db->query($sql, [$title, $subtitle, $image, $link, $sort_order, $status]);
    
    header('Location: sliders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加轮播图 - 后台管理系统</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="content-header">
                <h1>添加轮播图</h1>
                <a href="sliders.php" class="btn btn-secondary">返回列表</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>标题 *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>副标题</label>
                            <textarea name="subtitle" class="form-control" rows="3" placeholder="轮播图副标题（选填）"></textarea>
                        </div>

                        <div class="form-group">
                            <label>轮播图图片 *</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                            <small class="form-text">建议尺寸：1920x600px，支持 JPG、PNG、GIF、WebP 格式</small>
                        </div>

                        <div class="form-group">
                            <label>链接地址</label>
                            <input type="url" name="link" class="form-control" placeholder="点击轮播图跳转的链接（选填）">
                        </div>

                        <div class="form-group">
                            <label>排序</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                            <small class="form-text">数字越小越靠前</small>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="status" value="1" checked>
                                启用
                            </label>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">保存</button>
                            <a href="sliders.php" class="btn btn-secondary">取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
