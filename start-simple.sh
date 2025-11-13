#!/bin/bash

# 交个朋友CMS 简化部署脚本（适用于较旧的Docker版本）

echo "🚀 交个朋友CMS 简化部署"
echo "========================"

# 检查Docker是否安装
if ! command -v docker &> /dev/null; then
    echo "❌ Docker未安装，请先安装Docker"
    exit 1
fi

echo "✅ Docker环境检查通过"

# 复制配置文件
echo "📝 配置数据库连接..."
cp includes/config.docker.php includes/config.php

# 创建uploads目录
echo "📁 创建上传目录..."
mkdir -p uploads
chmod 777 uploads

# 创建Docker网络
echo "🌐 创建Docker网络..."
docker network create vann-network 2>/dev/null || true

# 启动MySQL容器
echo "🗄️ 启动MySQL数据库..."
docker run -d \
  --name vann-cms-db \
  --network vann-network \
  -e MYSQL_ROOT_PASSWORD=root123456 \
  -e MYSQL_DATABASE=vann_cms \
  -e MYSQL_USER=vann_user \
  -e MYSQL_PASSWORD=vann_pass \
  -p 3308:3306 \
  -v vann_mysql_data:/var/lib/mysql \
  mysql:8.0

# 等待数据库启动
echo "⏳ 等待数据库启动..."
sleep 15

# 导入数据库结构
echo "📊 导入数据库结构..."
docker exec -i vann-cms-db mysql -u root -proot123456 vann_cms < sql/database.sql

# 构建Web应用镜像
echo "🔨 构建Web应用..."
docker build -t vann-cms-web .

# 启动Web容器
echo "🌐 启动Web服务..."
docker run -d \
  --name vann-cms-web \
  --network vann-network \
  -p 8080:80 \
  -v "$(pwd)":/var/www/html \
  -v "$(pwd)/uploads":/var/www/html/uploads \
  vann-cms-web

# 启动phpMyAdmin
echo "🛠️ 启动phpMyAdmin..."
docker run -d \
  --name vann-cms-phpmyadmin \
  --network vann-network \
  -e PMA_HOST=vann-cms-db \
  -e PMA_PORT=3306 \
  -e PMA_USER=root \
  -e PMA_PASSWORD=root123456 \
  -p 8081:80 \
  phpmyadmin/phpmyadmin

echo ""
echo "🎉 部署完成！"
echo "========================"
echo "📱 访问地址："
echo "   前台网站：http://localhost:8080"
echo "   后台管理：http://localhost:8080/admin"
echo "   数据库管理：http://localhost:8081"
echo ""
echo "🔑 默认账号："
echo "   管理员：admin / admin123"
echo "   数据库：root / root123456"
echo ""
echo "🛠️ 管理命令："
echo "   查看容器：docker ps"
echo "   停止所有：./stop.sh"
echo "   查看日志：docker logs vann-cms-web"
echo "========================"