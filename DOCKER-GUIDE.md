# 🐳 交个朋友CMS - Docker部署完整指南

## 📋 部署选项总览

我们提供了多种Docker部署方式，适应不同的环境和需求：

### 🎯 选择适合您的部署方式

| 部署方式 | 适用场景 | 特点 | 推荐度 |
|---------|---------|------|--------|
| **Docker Compose** | 新版Docker环境 | 完整功能，一键部署 | ⭐⭐⭐⭐⭐ |
| **简化部署** | 旧版Docker环境 | 手动容器管理 | ⭐⭐⭐⭐ |
| **轻量级部署** | 快速测试 | 最小资源占用 | ⭐⭐⭐ |

## 🚀 快速开始

### 步骤1: 检查Docker环境

```bash
# 检查Docker版本
docker --version

# 检查Docker Compose（新版）
docker compose version

# 或检查Docker Compose（旧版）
docker-compose --version
```

### 步骤2: 选择并运行部署脚本

```bash
# 进入项目目录
cd vann

# 方式一：Docker Compose（推荐）
./start.sh

# 方式二：简化部署（兼容性好）
./start-simple.sh

# 方式三：轻量级部署（快速测试）
./start-lite.sh
```

### 步骤3: 访问网站

- **前台**: http://localhost:8080
- **后台**: http://localhost:8080/admin
- **管理员账号**: admin / admin123

## 📦 各部署方式详解

### 🎯 方式一：Docker Compose部署

**优点**: 完整功能，易于管理，生产环境推荐  
**文件**: `docker-compose.yml`, `start.sh`

```bash
# 启动
./start.sh

# 管理命令
docker-compose ps          # 查看状态
docker-compose logs -f     # 查看日志
docker-compose down        # 停止服务
docker-compose restart     # 重启服务
```

**服务包含**:
- Web服务器 (Apache + PHP 8.1)
- MySQL 8.0 数据库
- phpMyAdmin 数据库管理

### 🔧 方式二：简化部署

**优点**: 兼容旧版Docker，手动控制强  
**文件**: `start-simple.sh`, `stop.sh`

```bash
# 启动
./start-simple.sh

# 停止
./stop.sh

# 管理命令
docker ps                    # 查看容器
docker logs vann-cms-web     # 查看Web日志
docker logs vann-cms-db      # 查看数据库日志
```

### ⚡ 方式三：轻量级部署

**优点**: 快速启动，资源占用少  
**文件**: `start-lite.sh`

```bash
# 启动
./start-lite.sh

# 停止（使用通用停止脚本）
./stop.sh
```

## 🔧 自定义配置

### 修改端口

编辑对应的启动脚本，修改端口映射：

```bash
# 例如修改Web端口为9000
-p 9000:80  # 替换 -p 8080:80
```

### 修改数据库密码

编辑启动脚本中的环境变量：

```bash
-e MYSQL_ROOT_PASSWORD=your_password
-e MYSQL_PASSWORD=your_password
```

### 持久化数据

轻量级部署默认不持久化数据库，如需持久化：

```bash
# 在启动数据库时添加卷映射
-v vann_mysql_data:/var/lib/mysql
```

## 🛠️ 常用管理命令

### 查看服务状态

```bash
# 查看所有容器
docker ps

# 查看特定容器
docker ps | grep vann-cms
```

### 查看日志

```bash
# Web服务日志
docker logs vann-cms-web

# 数据库日志
docker logs vann-cms-db

# 实时查看日志
docker logs -f vann-cms-web
```

### 进入容器

```bash
# 进入Web容器
docker exec -it vann-cms-web bash

# 进入数据库容器
docker exec -it vann-cms-db mysql -u root -p
```

### 备份与恢复

```bash
# 备份数据库
docker exec vann-cms-db mysqldump -u root -proot123456 vann_cms > backup.sql

# 恢复数据库
docker exec -i vann-cms-db mysql -u root -proot123456 vann_cms < backup.sql

# 备份上传文件
tar -czf uploads_backup.tar.gz uploads/
```

## 🚨 故障排除

### 端口被占用

```bash
# 查看端口占用
lsof -i :8080

# 或者修改脚本中的端口
-p 9000:80  # 使用9000端口
```

### 容器启动失败

```bash
# 查看详细错误
docker logs container_name

# 重新启动容器
docker restart container_name

# 完全重建
docker stop container_name
docker rm container_name
# 然后重新运行启动脚本
```

### 数据库连接失败

```bash
# 检查数据库容器状态
docker ps | grep mysql

# 查看数据库日志
docker logs vann-cms-db

# 测试数据库连接
docker exec vann-cms-db mysql -u root -proot123456 -e "SHOW DATABASES;"
```

### 权限问题

```bash
# 修复uploads目录权限
chmod 777 uploads/

# 在容器内修复权限
docker exec vann-cms-web chown -R www-data:www-data /var/www/html/uploads
```

## 🔄 完全清理

如果需要完全删除所有相关容器和数据：

```bash
# 停止并删除容器
./stop.sh

# 删除数据卷（注意：会丢失所有数据）
docker volume rm vann_mysql_data

# 删除自建镜像
docker rmi vann-cms-web

# 删除网络
docker network rm vann-network
```

## 🚀 生产环境建议

### 安全配置

1. **修改默认密码**
   ```bash
   # 修改数据库密码
   -e MYSQL_ROOT_PASSWORD=strong_password
   
   # 登录后台修改admin密码
   ```

2. **关闭调试模式**
   ```php
   // 编辑 includes/config.php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

3. **使用HTTPS**
   ```bash
   # 配置反向代理（Nginx/Apache）
   # 或使用Let's Encrypt证书
   ```

### 性能优化

1. **启用PHP OPcache**
   ```dockerfile
   # 在Dockerfile中添加
   RUN docker-php-ext-install opcache
   ```

2. **配置数据库优化**
   ```bash
   # 增加MySQL配置
   -e MYSQL_INNODB_BUFFER_POOL_SIZE=256M
   ```

3. **使用数据卷**
   ```bash
   # 持久化数据和配置
   -v ./data:/var/lib/mysql
   -v ./config:/etc/mysql/conf.d
   ```

## 📞 获取帮助

如果遇到问题：

1. 查看本文档的故障排除部分
2. 检查Docker版本兼容性
3. 查看容器日志定位问题
4. 提交GitHub Issue

---

**🎉 现在您可以轻松使用Docker部署交个朋友CMS了！**

选择适合您环境的部署方式，几分钟内即可拥有一个完整的CMS系统。