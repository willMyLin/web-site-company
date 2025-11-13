-- 交个朋友CMS数据库结构
-- 创建数据库
CREATE DATABASE IF NOT EXISTS vann_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vann_cms;

-- 管理员表
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态 1-启用 0-禁用',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- 分类表
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '分类名称',
  `slug` varchar(100) NOT NULL COMMENT '分类别名',
  `type` varchar(20) NOT NULL COMMENT '分类类型 news-新闻 solution-解决方案 product-产品',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态 1-启用 0-禁用',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分类表';

-- 文章表
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `slug` varchar(255) DEFAULT NULL COMMENT '文章别名',
  `content` longtext NOT NULL COMMENT '内容',
  `excerpt` text DEFAULT NULL COMMENT '摘要',
  `featured_image` varchar(255) DEFAULT NULL COMMENT '特色图片',
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `views` int(11) DEFAULT 0 COMMENT '浏览量',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态 1-发布 0-草稿',
  `is_featured` tinyint(1) DEFAULT 0 COMMENT '是否推荐',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`),
  KEY `is_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章表';

-- 图片库表
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL COMMENT '文件名',
  `original_name` varchar(255) NOT NULL COMMENT '原始文件名',
  `file_size` int(11) NOT NULL COMMENT '文件大小',
  `mime_type` varchar(100) NOT NULL COMMENT '文件类型',
  `alt_text` varchar(255) DEFAULT NULL COMMENT 'Alt文本',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='图片库表';

-- 网站设置表
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL COMMENT '设置键',
  `setting_value` longtext DEFAULT NULL COMMENT '设置值',
  `setting_type` varchar(20) DEFAULT 'text' COMMENT '设置类型',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='网站设置表';

-- 轮播图表
CREATE TABLE `sliders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `subtitle` varchar(255) DEFAULT NULL COMMENT '副标题',
  `image` varchar(255) NOT NULL COMMENT '图片',
  `link` varchar(255) DEFAULT NULL COMMENT '链接',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='轮播图表';

-- 插入默认管理员用户 (用户名: admin, 密码: admin123)
INSERT INTO `admins` (`username`, `password`, `email`) VALUES 
('admin', MD5(CONCAT('admin123', 'vann_cms_2024_salt')), 'admin@vann.com');

-- 插入默认分类
INSERT INTO `categories` (`name`, `slug`, `type`, `sort_order`) VALUES
('公司新闻', 'company-news', 'news', 1),
('行业资讯', 'industry-news', 'news', 2),
('智慧建筑', 'smart-building', 'solution', 1),
('智慧园区', 'smart-park', 'solution', 2),
('智慧民生', 'smart-livelihood', 'solution', 3),
('智慧文旅', 'smart-tourism', 'solution', 4),
('物联网产品', 'iot-products', 'product', 1),
('人工智能', 'ai-products', 'product', 2),
('电子政务', 'e-government', 'product', 3);

-- 插入默认网站设置
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_title', '交个朋友 - 智慧建筑解决方案', '网站标题'),
('site_description', '交个朋友专注于智慧建筑、智慧园区、智慧民生等领域的解决方案', '网站描述'),
('company_name', '厦门交个朋友有限公司', '公司名称'),
('company_phone', '400-123-4567', '公司电话'),
('company_email', 'info@vann.com.cn', '公司邮箱'),
('company_address', '厦门市思明区软件园二期', '公司地址'),
('home_banner_title', 'SMART BUILDING\nHEALTHY PEOPLE', '首页横幅标题'),
('home_banner_subtitle', '将智慧建筑融入智慧城市', '首页横幅副标题'),
('company_intro', '交个朋友成立于1995年，深耕企业数字化服务28年。我们专注于网站建设、品牌设计与数字营销，为客户提供从策划到落地的一站式互联网解决方案，助力企业在数字时代实现品牌价值提升与业务增长。', '公司简介'),
('stat_years', '29', '公司成立年数'),
('stat_assets', '15', '公司资产（亿元）'),
('stat_cities', '100', '业务覆盖城市数'),
('stat_cases', '4000', '实施案例数'),
('stat_branches', '30', '分公司及办事机构数'),
('stat_provinces', '26', '业务覆盖省份数');

-- 插入示例文章
INSERT INTO `articles` (`title`, `content`, `excerpt`, `category_id`, `status`, `is_featured`) VALUES
('交个朋友打造数智化可视平台 赋能智慧校园"统一网管"', 
'<p>交个朋友凭借多年的技术积累和创新实践，成功打造了数智化可视平台，为智慧校园建设提供了强有力的技术支撑。该平台通过统一的网络管理系统，实现了校园内各类设备的智能化管理和可视化监控。</p><p>平台具备以下核心功能：</p><ul><li>设备状态实时监控</li><li>故障预警和自动处理</li><li>能耗数据分析</li><li>安全管控一体化</li></ul>', 
'交个朋友凭借多年的技术积累和创新实践，成功打造了数智化可视平台，为智慧校园建设提供了强有力的技术支撑。', 
1, 1, 1),

('医院可视化后勤运维管理平台：降能耗、提效率、易管理', 
'<p>医院作为能耗密集型建筑，其后勤运维管理面临着巨大挑战。交个朋友推出的医院可视化后勤运维管理平台，通过先进的物联网技术和大数据分析，为医院提供了全面的解决方案。</p><p>平台主要特点：</p><ul><li>全方位设备监控</li><li>智能化运维调度</li><li>精细化能耗管理</li><li>预测性维护保障</li></ul>', 
'交个朋友推出的医院可视化后勤运维管理平台，通过先进的物联网技术和大数据分析，为医院提供了全面的解决方案。', 
1, 1, 1),

('智慧建筑解决方案', 
'<p>交个朋友基于20余年智能化工程的项目积累，自主研发的软硬件一体化工业级IoT操作系统，为智慧建筑提供全方位的解决方案。</p><p>解决方案包括：</p><ul><li>楼宇自控系统</li><li>安防监控系统</li><li>消防报警系统</li><li>能耗管理系统</li><li>环境监测系统</li></ul>', 
'交个朋友基于20余年智能化工程的项目积累，自主研发的软硬件一体化工业级IoT操作系统。', 
3, 1, 1);