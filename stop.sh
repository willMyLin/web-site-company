#!/bin/bash

# 停止并删除所有相关容器

echo "🛑 停止交个朋友CMS容器..."

# 停止容器
docker stop vann-cms-web vann-cms-db vann-cms-phpmyadmin 2>/dev/null || true

# 删除容器
docker rm vann-cms-web vann-cms-db vann-cms-phpmyadmin 2>/dev/null || true

# 删除网络
docker network rm vann-network 2>/dev/null || true

echo "✅ 所有容器已停止并删除"
echo ""
echo "💡 如需完全清理："
echo "   docker volume rm vann_mysql_data  # 删除数据库数据"
echo "   docker rmi vann-cms-web          # 删除Web镜像"