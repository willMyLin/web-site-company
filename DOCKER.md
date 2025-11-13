# 交个朋友CMS - Docker部署指南

## 🐳 Docker快速部署

### 前提条件

确保您的系统已安装：
- [Docker](https://www.docker.com/get-started) (版本 20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (版本 2.0+)

### 🚀 一键启动

#### 方式一：Docker Compose（推荐，适用于新版Docker）

1. **克隆项目**（如果还没有）
   ```bash
   git clone <your-repo-url>
   cd vann
   ```

2. **运行启动脚本**
   ```bash
   ./start.sh
   ```

3. **访问网站**
   - 前台：http://localhost:8080
   - 后台：http://localhost:8080/admin
   - 数据库管理：http://localhost:8081

#### 方式二：简化部署（适用于旧版Docker）

如果上述方式不工作，使用简化部署：

```bash
# 启动服务
./start-simple.sh

# 停止服务
./stop.sh
```

### 📋 手动部署步骤

如果不使用启动脚本，可以手动执行以下步骤：

1. **配置数据库连接**
   ```bash
   cp includes/config.docker.php includes/config.php
   ```

2. **创建上传目录**
   ```bash
   mkdir -p uploads
   chmod 777 uploads
   ```

3. **启动Docker容器**
   ```bash
   docker-compose up -d
   ```

4. **等待初始化完成**
   ```bash
   docker-compose logs -f
   ```

### 🔧 Docker服务说明

#### Web服务 (Apache + PHP 8.1)
- **端口**: 8080
- **容器名**: vann-cms-web
- **文档根目录**: /var/www/html

#### MySQL数据库
- **端口**: 3306
- **容器名**: vann-cms-db
- **数据库**: vann_cms
- **用户**: vann_user / vann_pass
- **Root密码**: root123456

#### phpMyAdmin
- **端口**: 8081
- **容器名**: vann-cms-phpmyadmin
- **用于**: 数据库可视化管理

### 🎯 默认账号信息

**管理员账号**
- 用户名: `admin`
- 密码: `admin123`

**数据库账号**
- Root用户: `root` / `root123456`
- 应用用户: `vann_user` / `vann_pass`

### 🛠️ 常用Docker命令

```bash
# 查看容器状态
docker-compose ps

# 查看日志
docker-compose logs -f

# 停止所有服务
docker-compose down

# 重启服务
docker-compose restart

# 重新构建并启动
docker-compose up --build -d

# 进入Web容器
docker exec -it vann-cms-web bash

# 进入数据库容器
docker exec -it vann-cms-db mysql -u root -p

# 备份数据库
docker exec vann-cms-db mysqldump -u root -proot123456 vann_cms > backup.sql

# 恢复数据库
docker exec -i vann-cms-db mysql -u root -proot123456 vann_cms < backup.sql
```

### 📁 目录结构

```
vann/
├── docker-compose.yml      # Docker Compose配置
├── Dockerfile             # Web服务Docker配置
├── start.sh              # 一键启动脚本
├── .dockerignore         # Docker忽略文件
├── includes/
│   ├── config.php        # 运行时配置
│   └── config.docker.php # Docker环境配置模板
└── uploads/              # 文件上传目录（自动创建）
```

### 🔧 自定义配置

#### 修改端口

编辑 `docker-compose.yml` 文件：

```yaml
services:
  web:
    ports:
      - "8080:80"  # 修改为其他端口，如 "9000:80"
```

#### 数据库配置

编辑 `docker-compose.yml` 中的环境变量：

```yaml
services:
  db:
    environment:
      MYSQL_ROOT_PASSWORD: your_root_password
      MYSQL_DATABASE: your_database_name
      MYSQL_USER: your_username
      MYSQL_PASSWORD: your_password
```

### 🚨 故障排除

#### 容器启动失败
```bash
# 查看详细日志
docker-compose logs

# 重新构建容器
docker-compose build --no-cache
docker-compose up -d
```

#### 权限问题
```bash
# 修复uploads目录权限
sudo chmod -R 777 uploads/
sudo chown -R www-data:www-data uploads/
```

#### 数据库连接失败
```bash
# 检查数据库容器状态
docker-compose ps

# 查看数据库日志
docker-compose logs db

# 重启数据库服务
docker-compose restart db
```

#### 端口被占用
```bash
# 查看端口占用
lsof -i :8080

# 修改docker-compose.yml中的端口映射
```

### 🔄 数据备份与恢复

#### 备份
```bash
# 备份数据库
docker exec vann-cms-db mysqldump -u root -proot123456 vann_cms > backup_$(date +%Y%m%d).sql

# 备份上传文件
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz uploads/
```

#### 恢复
```bash
# 恢复数据库
docker exec -i vann-cms-db mysql -u root -proot123456 vann_cms < backup_20241105.sql

# 恢复上传文件
tar -xzf uploads_backup_20241105.tar.gz
```

### 🚀 生产环境部署

1. **修改安全配置**
   - 更改默认密码
   - 关闭错误显示
   - 使用HTTPS

2. **性能优化**
   - 启用PHP OPcache
   - 配置数据库连接池
   - 使用反向代理

3. **监控和日志**
   - 配置日志轮转
   - 添加健康检查
   - 设置监控告警

### 📞 技术支持

如果遇到问题，请：
1. 查看本文档的故障排除部分
2. 检查Docker和Docker Compose版本
3. 查看容器日志排查问题
4. 提交GitHub Issue

---

**🎉 现在您可以通过Docker快速部署交个朋友CMS系统了！**