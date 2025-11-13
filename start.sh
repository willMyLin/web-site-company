#!/bin/bash

# 交个朋友CMS Docker快速启动脚本

echo "🚀 交个朋友CMS Docker环境启动脚本"
echo "=================================="

# 检查Docker是否安装
if ! command -v docker &> /dev/null; then
    echo "❌ Docker未安装，请先安装Docker"
    echo "访问 https://www.docker.com/get-started 下载安装"
    exit 1
fi

# 检查Docker Compose是否安装
if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose未安装，请先安装Docker Compose"
    echo "或者您的Docker版本可能太旧，请更新到最新版本"
    exit 1
fi

# 确定使用哪个compose命令
if command -v docker-compose &> /dev/null; then
    COMPOSE_CMD="docker-compose"
else
    COMPOSE_CMD="docker compose"
fi

echo "✅ Docker环境检查通过"

# 复制Docker配置文件
echo "📝 配置数据库连接..."
cp includes/config.docker.php includes/config.php

# 创建uploads目录并设置权限
echo "📁 创建上传目录..."
mkdir -p uploads
chmod 777 uploads

# 启动Docker容器
echo "🔄 启动Docker容器..."
$COMPOSE_CMD up -d

# 等待数据库启动
echo "⏳ 等待数据库初始化..."
sleep 10

# 检查容器状态
echo "📊 检查容器状态..."
$COMPOSE_CMD ps

echo ""
echo "🎉 启动完成！"
echo "=================================="
echo "📱 访问地址："
echo "   前台网站：http://localhost:8080"
echo "   后台管理：http://localhost:8080/admin"
echo "   数据库管理：http://localhost:8081"
echo ""
echo "🔑 默认账号："
echo "   管理员账号：admin / admin123"
echo "   数据库账号：root / root123456"
echo ""
echo "🛠️ 常用命令："
echo "   查看日志：$COMPOSE_CMD logs -f"
echo "   停止服务：$COMPOSE_CMD down"
echo "   重启服务：$COMPOSE_CMD restart"
echo "=================================="