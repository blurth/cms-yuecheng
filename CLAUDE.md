# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 项目概述

越城综治中心管理系统 - 基于 ThinkPHP 5.1 + Vue 3 的综合治理中心管理平台。本项目改编自凯华吉祥教育系统，保持核心逻辑和功能架构不变，基于 Lin-CMS 框架构建。

## 开发环境要求

### 后端
- PHP >= 7.1.0, < 7.4.0
- MySQL 数据库
- Composer
- XAMPP 或其他 Web 服务器

### 前端
- Node.js 8.11.0+
- npm 或 yarn

## 常用命令

### 后端开发
```bash
# 安装依赖
composer install

# 数据库迁移
php think migrate:run

# 构建应用结构
php think build

# 访问控制台
php think
```

### 前端开发
```bash
# 进入前端目录
cd frontend

# 安装依赖
npm install
# 或
yarn

# 开发环境启动
npm run serve
# 或
yarn serve

# 构建生产版本
npm run build

# 代码检查
npm run lint

# 运行测试
npm run test:unit
```

### 本地访问
- 后端API: `http://localhost/yuecheng.jixiangjiaoyu.com/public/`
- 前端界面: `http://localhost:8080` (开发模式)

## 项目架构

### 整体结构
```
cms-yuecheng/
├── backend/          # ThinkPHP 5.1 后端
│   ├── application/  # 应用代码
│   │   ├── api/     # API层
│   │   ├── lib/     # 核心库
│   │   └── http/    # HTTP中间件
│   ├── public/      # Web入口
│   ├── config/      # 配置文件
│   └── database/    # 数据库文件
└── frontend/         # Vue 3 前端
    ├── src/         # 源代码
    │   ├── component/ # 通用组件
    │   ├── view/    # 页面视图
    │   ├── router/  # 路由配置
    │   ├── store/   # 状态管理
    │   └── utils/   # 工具函数
    └── public/      # 静态资源
```

### 后端架构 (ThinkPHP 5.1)

#### 核心组件
- **Lin-CMS**: 基于 Lin-CMS 的内容管理框架
- **认证系统**: Token 认证 + RBAC 权限控制
- **API设计**: RESTful API，分为 CMS 管理端 (`/cms/*`) 和客户端 (`/v1/*`)

#### 主要业务模块
- **课程管理**: 课程分类、讲师、课程章节
- **活动管理**: 活动发布、报名管理
- **心理咨询**: 心理咨询师和预约管理
- **新闻系统**: 新闻发布和公告
- **用户管理**: 用户注册、登录、权限管理
- **文件管理**: 图片、视频等文件上传
- **横幅管理**: Banner 轮播图管理
- **反馈系统**: 用户反馈收集

#### 数据库
- **前缀**: `kai_` (通过 .env 配置)
- **连接**: MySQL (阿里云 RDS)
- **数据库名**: `kai_center`
- **ORM**: ThinkPHP ORM

### 前端架构 (Vue 3 + Lin-CMS)

#### 技术栈
- **Vue 3.2.24**: 主框架
- **Element Plus 2.1.4**: UI 组件库
- **Vue Router 4**: 路由管理
- **Vuex 4**: 状态管理
- **Axios 0.24.0**: HTTP 请求库
- **TinyMCE**: 富文本编辑器
- **G2Plot**: 数据可视化

#### 核心功能
- **用户认证**: 登录、权限验证
- **动态路由**: 基于权限的菜单生成
- **文件上传**: 支持图片裁剪、富文本编辑
- **数据图表**: 基于 G2Plot 的数据可视化

## 配置文件

### 后端配置
- **环境配置**: `backend/.env` - 数据库连接、调试模式等
- **数据库**: `backend/config/database.php`
- **路由**: `backend/route/route.php`

### 前端配置
- **环境变量**: `frontend/.env`, `frontend/.env.production`
- **构建配置**: `frontend/vue.config.js`
- **包管理**: `frontend/package.json`

## 开发规范

### 后端开发
- 遵循 ThinkPHP 5.1 规范
- 使用 Lin-CMS 认证和权限系统
- API 返回统一的 JSON 格式
- 错误处理使用自定义异常类

### 前端开发
- 遵循 Vue 3 Composition API 风格
- 使用 ESLint + Airbnb 规范
- 组件采用单文件组件(.vue)格式
- 状态管理使用 Vuex 模块化

### 代码提交
- 使用 commitizen 规范提交信息: `npm run commit`
- 提交前自动执行 lint 检查
- 遵循语义化版本控制

## 注意事项

### 后端
- PHP 版本必须 >= 7.1.0 且 < 7.4.0
- 确保数据库连接配置正确
- 检查目录权限，特别是 runtime 目录

### 前端
- Node.js 版本需要 >= 8.11.0
- 如遇到依赖安装问题，尝试删除 node_modules 重新安装
- 开发时后端 API 跨域已配置
