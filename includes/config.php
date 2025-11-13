<?php
// 数据库配置 - Docker环境
define('DB_HOST', 'vann-cms-db');  // Docker服务名
define('DB_NAME', 'vann_cms');
define('DB_USER', 'vann_user');
define('DB_PASS', 'vann_pass');

// 网站配置
// 自动检测当前域名和端口
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8080';
define('SITE_URL', $protocol . '://' . $host);
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// 安全设置
define('SESSION_NAME', 'vann_cms_session');
define('PASSWORD_SALT', 'vann_cms_2024_salt');

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 错误报告（生产环境应关闭）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 启动会话
session_name(SESSION_NAME);
session_start();
?>