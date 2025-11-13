<?php
/**
 * 工具类
 */
class Utils {
    
    /**
     * 上传文件
     */
    public static function uploadFile($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('文件上传失败');
        }
        
        // 检查文件类型
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('不支持的文件类型');
        }
        
        // 生成唯一文件名
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $uploadPath = UPLOAD_PATH . $fileName;
        
        // 移动文件
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('文件保存失败');
        }
        
        return $fileName;
    }
    
    /**
     * 删除文件
     */
    public static function deleteFile($fileName) {
        $filePath = UPLOAD_PATH . $fileName;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }
    
    /**
     * 格式化日期
     */
    public static function formatDate($date, $format = 'Y-m-d H:i:s') {
        return date($format, strtotime($date));
    }
    
    /**
     * 截取文字
     */
    public static function truncate($text, $length = 100, $suffix = '...') {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $suffix;
    }
    
    /**
     * 清理HTML标签
     */
    public static function cleanHtml($html) {
        return strip_tags($html);
    }
    
    /**
     * 生成随机字符串
     */
    public static function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
    }
    
    /**
     * 检查管理员登录状态
     */
    public static function checkAdminLogin() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . ADMIN_URL . '/login.php');
            exit;
        }
    }
    
    /**
     * 管理员登录
     */
    public static function adminLogin($username, $password) {
        $db = Database::getInstance();
        $hashedPassword = md5($password . PASSWORD_SALT);
        
        $admin = $db->fetch(
            "SELECT * FROM admins WHERE username = ? AND password = ? AND status = 1", 
            [$username, $hashedPassword]
        );
        
        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
        
        return false;
    }
    
    /**
     * 管理员退出登录
     */
    public static function adminLogout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        session_destroy();
    }
}
?>