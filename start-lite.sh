#!/bin/bash

# 交个朋友CMS 轻量级部署（使用Alpine镜像，更快）

echo "🚀 交个朋友CMS 轻量级部署"
echo "========================"

# 检查Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker未安装"
    exit 1
fi

echo "✅ Docker环境检查通过"

# 停止现有容器
echo "🛑 清理现有容器..."
docker stop vann-cms-web vann-cms-db vann-cms-phpmyadmin 2>/dev/null || true
docker rm vann-cms-web vann-cms-db vann-cms-phpmyadmin 2>/dev/null || true

# 配置文件
echo "📝 配置数据库连接..."
cp includes/config.docker.php includes/config.php

# 创建目录
mkdir -p uploads
chmod 777 uploads

# 创建网络
docker network create vann-network 2>/dev/null || true

# 启动MySQL数据库
echo "🗄️ 启动MySQL数据库..."
docker run -d \
  --name vann-cms-db \
  --network vann-network \
  -e MYSQL_ROOT_PASSWORD=root123456 \
  -e MYSQL_DATABASE=vann_cms \
  -e MYSQL_USER=vann_user \
  -e MYSQL_PASSWORD=vann_pass \
  -p 3306:3306 \
  mysql:5.7

# 等待数据库
echo "⏳ 等待数据库启动..."
sleep 20

# 导入数据
echo "📊 导入数据库结构..."
docker exec -i vann-cms-db mysql -u root -proot123456 vann_cms < sql/database.sql 2>/dev/null || echo "数据导入可能需要手动执行"

# 启动简单的PHP服务器（不需要构建镜像）
echo "🌐 启动PHP服务器..."
docker run -d \
  --name vann-cms-web \
  --network vann-network \
  -p 8080:80 \
  -v "$(pwd)":/var/www/html \
  -w /var/www/html \
  php:7.4-apache

# 安装PHP扩展
echo "🔧 安装PHP扩展..."
docker exec vann-cms-web docker-php-ext-install pdo pdo_mysql

# 启用Apache模块
docker exec vann-cms-web a2enmod rewrite

# 重启Web容器应用配置
docker restart vann-cms-web

echo ""
echo "🎉 部署完成！"
echo "========================"
echo "📱 访问地址："
echo "   前台网站：http://localhost:8080"
echo "   后台管理：http://localhost:8080/admin"
echo ""
echo "🔑 默认账号："
echo "   管理员：admin / admin123"
echo ""
echo "⚠️  如果网站无法访问，请等待1-2分钟让服务完全启动"
echo ""
echo "🛠️ 管理命令："
echo "   查看状态：docker ps"
echo "   查看日志：docker logs vann-cms-web"
echo "   停止服务：./stop.sh"
echo "========================"