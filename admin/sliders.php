<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

// 检查登录
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();

// 处理删除
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $db->query("DELETE FROM sliders WHERE id = ?", [$id]);
    header('Location: sliders.php?msg=deleted');
    exit;
}

// 处理排序
if(isset($_POST['update_order'])) {
    foreach($_POST['order'] as $id => $order) {
        $db->query("UPDATE sliders SET sort_order = ? WHERE id = ?", [(int)$order, (int)$id]);
    }
    header('Location: sliders.php?msg=updated');
    exit;
}

// 获取所有轮播图
$sliders = $db->fetchAll("SELECT * FROM sliders ORDER BY sort_order ASC, id DESC");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>轮播图管理 - 后台管理系统</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="content-header">
                <h1>轮播图管理</h1>
                <a href="slider-add.php" class="btn btn-primary">添加轮播图</a>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success">
                    <?php 
                    if($_GET['msg'] == 'deleted') echo '删除成功！';
                    if($_GET['msg'] == 'updated') echo '排序已更新！';
                    ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="post">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="80">排序</th>
                                    <th width="120">预览</th>
                                    <th>标题</th>
                                    <th>副标题</th>
                                    <th>链接</th>
                                    <th width="80">状态</th>
                                    <th width="150">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($sliders as $slider): ?>
                                <tr>
                                    <td>
                                        <input type="number" name="order[<?php echo $slider['id']; ?>]" 
                                               value="<?php echo $slider['sort_order']; ?>" 
                                               class="form-control" style="width: 60px;">
                                    </td>
                                    <td>
                                        <?php if($slider['image']): ?>
                                            <img src="<?php echo UPLOAD_URL . $slider['image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($slider['title']); ?>" 
                                                 style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 100px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999;">
                                                无图片
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($slider['title']); ?></td>
                                    <td><?php echo htmlspecialchars($slider['subtitle'] ?: '-'); ?></td>
                                    <td>
                                        <?php if($slider['link']): ?>
                                            <a href="<?php echo htmlspecialchars($slider['link']); ?>" target="_blank" style="color: #0066cc;">
                                                <?php echo htmlspecialchars(substr($slider['link'], 0, 30)) . (strlen($slider['link']) > 30 ? '...' : ''); ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $slider['status'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $slider['status'] ? '启用' : '禁用'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="slider-edit.php?id=<?php echo $slider['id']; ?>" class="btn btn-sm btn-info">编辑</a>
                                        <a href="sliders.php?action=delete&id=<?php echo $slider['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('确定要删除这个轮播图吗？')">删除</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($sliders)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                                        暂无轮播图，<a href="slider-add.php">点击添加</a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if(!empty($sliders)): ?>
                        <div style="margin-top: 20px;">
                            <button type="submit" name="update_order" class="btn btn-success">保存排序</button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
