# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a ThinkPHP 5.1 education management system (凯华吉祥教育) with Lin-CMS integration for content management. The application provides educational content management, user authentication, lesson management, activity management, and file upload functionality.

## Commands

### Development Commands
- `php think` - Access ThinkPHP console commands
- `php think migrate:run` - Run database migrations  
- `php think build` - Build application structure using build.php configuration

### Web Server
- Access via XAMPP: `http://localhost/yuecheng.jixiangjiaoyu.com/public/`
- Entry point: `public/index.php`

### Dependencies
- `composer install` - Install PHP dependencies
- Requires PHP >= 7.1.0

## Architecture

### Framework Structure
- **Framework**: ThinkPHP 5.1.36 with Lin-CMS base components
- **Database**: MySQL (configured via .env)
- **Entry Point**: `public/index.php`
- **Console**: `think` (CLI tool)

### Application Structure
```
application/
├── api/                    # API layer
│   ├── controller/
│   │   ├── cms/           # Content management controllers (Admin, User, Log, File)
│   │   └── v1/            # API v1 controllers (Lesson, Activity, News, etc.)
│   ├── model/             # Data models
│   ├── service/           # Business logic services
│   ├── validate/          # Form validation classes
│   └── behavior/          # Event behaviors
├── lib/                   # Core libraries
│   ├── authenticator/     # Authentication system
│   ├── exception/         # Custom exceptions
│   └── enum/              # Enumeration classes
└── http/middleware/       # HTTP middleware
```

### Key Components

#### Authentication System
- **Token-based**: Login tokens in `application/api/service/token/`
- **Permissions**: Role-based access control with groups and permissions
- **Middleware**: `Authentication` middleware handles auth verification
- **Scopes**: Login required, group required, admin required authenticators

#### API Architecture
- **CMS API**: Admin panel functionality (`/cms/*` routes)
- **Public API**: Client-facing endpoints (`/v1/*` routes) 
- **CORS**: Cross-domain requests enabled with credential support

#### Business Modules
- **Lessons**: Course management with categories, authors, and sections
- **Activities**: Event management with user registration
- **News**: News and announcements system
- **Psychology**: Psychologist management and appointments
- **Reports**: Reporting functionality
- **File Management**: Upload and file handling

### Configuration
- **Environment**: `.env` file for database and environment settings
- **Database**: MySQL connection configured in `config/database.php`
- **Routing**: RESTful routes defined in `route/route.php`
- **Logging**: Error logging configured in `config/log.php`

### Error Handling
- **Error Codes**: Documented in `error_msg.md` with standard error code ranges
- **Exception Handler**: Custom Lin-CMS exception handler
- **Debug Mode**: Controlled via `APP_DEBUG` environment variable

### Dependencies
- **Lin-CMS**: Content management system core (`lin-cms-tp5/base-core`)
- **WeChat**: WeChat SDK integration (`overtrue/wechat`)
- **Excel**: PhpSpreadsheet for Excel import/export
- **Utils**: Custom utility packages (`qinchen/web-utils`)

### Database
- **Prefix**: `kai_` (configurable via .env)
- **Connection**: Aliyun RDS MySQL instance
- **ORM**: ThinkPHP ORM with collection result sets