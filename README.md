# 交个朋友CMS系统

一个基于PHP+MySQL+HTML的企业级内容管理系统，仿照交个朋友官网设计，提供完整的前台展示和后台管理功能。

![交个朋友CMS](https://img.shields.io/badge/PHP-7.0+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-green.svg)
![License](https://img.shields.io/badge/License-MIT-yellow.svg)

## 🌟 系统特色

- **现代化设计**: 仿照交个朋友官网的专业UI设计
- **响应式布局**: 完美适配桌面端和移动端
- **模块化架构**: 采用MVC架构模式，代码结构清晰
- **安全可靠**: PDO数据库操作，防SQL注入
- **功能完整**: 文章发布、图片管理、分类管理等完整CMS功能

## 📋 功能模块

### 前台功能
- ✅ 首页展示（企业介绍、统计数据、轮播图）
- ✅ 新闻资讯（列表页、详情页、分类筛选）
- ✅ 解决方案展示
- ✅ 产品中心
- ✅ 关于我们
- ✅ 联系我们
- ✅ 搜索功能
- ✅ 面包屑导航

### 后台功能
- ✅ 管理员登录/退出
- ✅ 控制台（数据统计）
- ✅ 文章管理（新增、编辑、删除、状态管理）
- ✅ 分类管理
- ✅ 图片管理（上传、预览、删除）
- ✅ 网站设置
- ✅ 富文本编辑器
- ✅ 批量操作

## 🛠️ 技术栈

- **后端**: PHP 7.0+
- **数据库**: MySQL 5.7+
- **前端**: HTML5 + CSS3 + JavaScript
- **样式**: 响应式CSS Grid/Flexbox布局
- **架构**: MVC模式

## 📁 项目结构

```
vann/
├── admin/                  # 后台管理目录
│   ├── index.php          # 后台首页
│   ├── login.php          # 登录页面
│   ├── articles.php       # 文章管理
│   ├── article-add.php    # 添加文章
│   ├── article-edit.php   # 编辑文章
│   ├── media.php          # 图片管理
│   ├── categories.php     # 分类管理
│   ├── settings.php       # 系统设置
│   └── logout.php         # 退出登录
├── assets/                 # 静态资源
│   ├── css/
│   │   ├── style.css      # 前台样式
│   │   └── admin.css      # 后台样式
│   ├── js/
│   │   └── main.js        # 前台脚本
│   └── images/            # 图片资源
├── includes/               # 核心文件
│   ├── config.php         # 配置文件
│   ├── Database.php       # 数据库类
│   └── Utils.php          # 工具类
├── uploads/                # 上传文件目录
├── sql/
│   └── database.sql       # 数据库结构
├── index.php              # 网站首页
├── news.php              # 新闻列表
├── news-detail.php       # 新闻详情
├── about.php             # 关于我们
├── solutions.php         # 解决方案
├── products.php          # 产品中心
├── contact.php           # 联系我们
└── README.md             # 项目说明
```

## 🚀 安装部署

### 🐳 Docker快速部署（推荐）

最简单的部署方式，只需要安装Docker：

```bash
# 1. 进入项目目录
cd vann

# 2. 一键启动
./start.sh

# 3. 访问网站
# 前台：http://localhost:8080
# 后台：http://localhost:8080/admin
# 默认账号：admin / admin123
```

详细说明请查看 [Docker部署指南](DOCKER.md)

### 📋 传统部署方式

#### 环境要求

- PHP 7.0 或更高版本
- MySQL 5.7 或更高版本
- Apache/Nginx 网服务器
- 支持PDO扩展

#### 安装步骤

1. **下载项目**
   ```bash
   git clone https://github.com/your-repo/vann-cms.git
   cd vann-cms
   ```

2. **配置Web服务器**
   
   将项目文件放置到Web服务器根目录下，确保`uploads`目录有写入权限：
   ```bash
   chmod 755 uploads/
   ```

3. **创建数据库**
   
   创建MySQL数据库并导入结构：
   ```bash
   mysql -u root -p < sql/database.sql
   ```

4. **配置数据库连接**
   
   编辑 `includes/config.php` 文件，修改数据库连接信息：
   ```php
   define('DB_HOST', 'localhost');     # 数据库主机
   define('DB_NAME', 'vann_cms');      # 数据库名
   define('DB_USER', 'your_username'); # 数据库用户名
   define('DB_PASS', 'your_password'); # 数据库密码
   ```

5. **配置站点URL**
   
   修改 `includes/config.php` 中的站点URL：
   ```php
   define('SITE_URL', 'http://your-domain.com');
   ```

6. **访问网站**
   
   - 前台：`http://your-domain.com`
   - 后台：`http://your-domain.com/admin`
   - 默认账号：`admin` / `admin123`

## 📱 功能说明

### 前台功能详解

**首页模块**
- 企业横幅展示
- 公司统计数据动画
- 推荐新闻展示
- 解决方案概览
- 产品中心介绍

**新闻系统**
- 新闻列表分页显示
- 分类筛选功能
- 新闻详情页面
- 相关文章推荐
- 浏览量统计

**页面导航**
- 响应式顶部导航
- 面包屑导航
- 侧边栏分类导航
- 返回顶部按钮

### 后台功能详解

**文章管理**
- 富文本编辑器
- 文章分类管理
- 特色图片上传
- 发布状态控制
- 推荐文章设置
- 批量操作功能

**图片管理**
- 多图片批量上传
- 拖拽上传支持
- 图片预览功能
- 一键复制链接
- 图片信息显示

**系统设置**
- 网站基本信息
- 联系方式配置
- 横幅内容设置
- SEO信息管理

## 🎨 自定义配置

### 修改网站样式

1. **前台样式**: 编辑 `assets/css/style.css`
2. **后台样式**: 编辑 `assets/css/admin.css`
3. **主题色彩**: 修改CSS变量中的颜色值

### 添加新功能模块

1. 在数据库中创建相应表结构
2. 创建对应的PHP页面文件
3. 添加导航链接
4. 更新管理后台功能

### 配置网站信息

登录后台管理系统，进入"网站设置"页面修改：
- 网站标题和描述
- 公司联系信息
- 首页横幅内容
- 其他个性化设置

## 🔧 常见问题

**Q: 图片上传失败怎么办？**
A: 检查`uploads`目录权限，确保Web服务器有写入权限。

**Q: 如何修改默认管理员密码？**
A: 登录后台后，可以在数据库中直接修改`admins`表的密码字段。

**Q: 如何备份网站数据？**
A: 定期备份MySQL数据库和`uploads`目录中的文件。

**Q: 如何设置网站SEO？**
A: 在后台"网站设置"中配置网站标题、描述等SEO信息。

## 📄 开源协议

本项目采用 MIT 协议开源，详见 [LICENSE](LICENSE) 文件。

## 🤝 贡献指南

欢迎提交Issue和Pull Request来帮助改进项目！

1. Fork 本项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 创建 Pull Request

## 📞 技术支持

如果您在使用过程中遇到问题，可以：

- 查看项目文档
- 提交GitHub Issue
- 发送邮件到：support@example.com

## 🎯 更新日志

### v1.0.0 (2024-11-05)
- ✨ 完整的CMS功能实现
- 🎨 仿交个朋友官网设计
- 📱 响应式布局支持
- 🔐 安全的后台管理系统
- 📸 图片管理功能
- 📝 富文本编辑器
- 🔍 搜索和筛选功能

---

**Made with ❤️ by [Your Name]**