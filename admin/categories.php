<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Utils.php';

Utils::checkAdminLogin();

$db = Database::getInstance();

// 删除分类
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $db->query('DELETE FROM categories WHERE id = ?', [$id]);
    header('Location: categories.php');
    exit;
}

// 修改分类
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $edit_name = isset($_POST['edit_name']) ? trim($_POST['edit_name']) : '';
    if ($edit_name !== '') {
        $db->query('UPDATE categories SET name = ? WHERE id = ?', [$edit_name, $edit_id]);
        header('Location: categories.php');
        exit;
    }
}

// 新增分类
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && !isset($_POST['edit_id'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $db->query('INSERT INTO categories (name) VALUES (?)', [$name]);
        header('Location: categories.php');
        exit;
    }
}

// 获取所有分类
$categories = $db->fetchAll('SELECT * FROM categories ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>分类管理 - 交个朋友CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script>
    function editCategory(id, name) {
        var newName = prompt('请输入新的分类名称：', name);
        if (newName !== null && newName.trim() !== '' && newName !== name) {
            var form = document.createElement('form');
            form.method = 'post';
            form.action = 'categories.php';
            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'edit_id';
            idInput.value = id;
            form.appendChild(idInput);
            var nameInput = document.createElement('input');
            nameInput.type = 'hidden';
            nameInput.name = 'edit_name';
            nameInput.value = newName;
            form.appendChild(nameInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
    function deleteCategory(id) {
        if (confirm('确定要删除该分类吗？')) {
            window.location = 'categories.php?action=delete&id=' + id;
        }
    }
    </script>
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
                <li><a href="categories.php" class="active">分类管理</a></li>
                <li><a href="media.php">图片管理</a></li>
                <li><a href="settings.php">网站设置</a></li>
                <li><a href="logout.php">退出登录</a></li>
            </ul>
        </aside>
        <!-- 主内容区 -->
        <main class="main-content">
            <header class="header">
                <h1 class="header-title">分类管理</h1>
                <div class="header-actions">
                    <form method="post" style="display:flex;gap:10px;align-items:center;">
                        <input type="text" name="name" class="form-control" placeholder="请输入新分类名称" required style="max-width:200px;">
                        <button type="submit" class="btn btn-success">新增分类</button>
                    </form>
                </div>
            </header>
            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <h3>分类列表 (共 <?php echo count($categories); ?> 个)</h3>
                    </div>
                    <div class="card-body">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr style="background:#f8f8f8;">
                                <th style="padding:12px 8px;border-bottom:1px solid #eee;">ID</th>
                                <th style="padding:12px 8px;border-bottom:1px solid #eee;">分类名称</th>
                                <th style="padding:12px 8px;border-bottom:1px solid #eee;">操作</th>
                            </tr>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td style="padding:10px 8px;border-bottom:1px solid #f0f0f0;"><?= htmlspecialchars($cat['id']) ?></td>
                                <td style="padding:10px 8px;border-bottom:1px solid #f0f0f0;"><?= htmlspecialchars($cat['name']) ?></td>
                                <td style="padding:10px 8px;border-bottom:1px solid #f0f0f0;">
                                    <button type="button" class="btn btn-warning btn-sm" onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>')">修改</button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteCategory(<?= $cat['id'] ?>)">删除</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
