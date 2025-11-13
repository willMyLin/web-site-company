<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

// 检查登录
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 获取轮播图信息
$slider = $db->fetch("SELECT * FROM sliders WHERE id = ?", [$id]);
if(!$slider) {
    header('Location: sliders.php');
    exit;
}

// 处理表单提交
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $link = $_POST['link'];
    $sort_order = (int)$_POST['sort_order'];
    $status = isset($_POST['status']) ? 1 : 0;
    
    // 处理图片上传
    $image = $slider['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = UPLOAD_PATH;
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($file_ext, $allowed)) {
            $new_filename = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // 删除旧图片
                if($slider['image'] && file_exists($upload_dir . $slider['image'])) {
                    unlink($upload_dir . $slider['image']);
                }
                $image = $new_filename;
            }
        }
    }
    
    // 更新数据库
    $sql = "UPDATE sliders SET title = ?, subtitle = ?, image = ?, link = ?, sort_order = ?, status = ? WHERE id = ?";
    $db->query($sql, [$title, $subtitle, $image, $link, $sort_order, $status, $id]);
    
    header('Location: sliders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑轮播图 - 后台管理系统</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="content-header">
                <h1>编辑轮播图</h1>
                <a href="sliders.php" class="btn btn-secondary">返回列表</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>标题 *</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($slider['title']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>副标题</label>
                            <textarea name="subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($slider['subtitle']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>轮播图图片</label>
                            <?php if($slider['image']): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="<?php echo UPLOAD_URL . $slider['image']; ?>" alt="当前图片" style="max-width: 400px; height: auto; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="form-text">建议尺寸：1920x600px，不上传则保持原图</small>
                        </div>

                        <div class="form-group">
                            <label>链接地址</label>
                            <input type="url" name="link" class="form-control" value="<?php echo htmlspecialchars($slider['link']); ?>">
                        </div>

                        <div class="form-group">
                            <label>排序</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $slider['sort_order']; ?>" min="0">
                            <small class="form-text">数字越小越靠前</small>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="status" value="1" <?php echo $slider['status'] ? 'checked' : ''; ?>>
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
